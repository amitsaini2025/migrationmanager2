<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\EmailLogEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SesEventWebhookService
{
    /** @var array<string, int> Higher rank is more final. A later event must not downgrade status. */
    private const STATUS_RANK = [
        'pending' => 0,
        'processed' => 10,
        'deferred' => 20,
        'blocked' => 25,
        'delivered' => 30,
        'bounced' => 40,
        'dropped' => 45,
        'send_failed' => 50,
    ];

    public function __construct(private SnsMessageVerifier $verifier) {}

    /**
     * @return array{body: string, status: int}
     */
    public function handle(Request $request): array
    {
        if (! $this->tokenMatches($request)) {
            return ['body' => 'Unauthorized', 'status' => 401];
        }

        $envelope = json_decode($request->getContent(), true);
        if (! is_array($envelope)) {
            return ['body' => 'Invalid payload', 'status' => 400];
        }

        $topicArn = trim((string) config('services.ses.sns_topic_arn'));
        if ($topicArn === '') {
            Log::warning('SES event webhook rejected because SES_SNS_TOPIC_ARN is not set');

            return ['body' => 'Webhook is not configured', 'status' => 401];
        }

        $messageTopic = (string) ($envelope['TopicArn'] ?? '');
        if (! hash_equals($topicArn, $messageTopic)) {
            Log::warning('SES event webhook rejected because the topic ARN did not match');

            return ['body' => 'Unauthorized', 'status' => 401];
        }

        try {
            $this->verifier->verify($envelope);
        } catch (Throwable $e) {
            Log::warning('SES event webhook signature rejected', ['error' => $e->getMessage()]);

            return ['body' => 'Unauthorized', 'status' => 401];
        }

        $type = (string) ($envelope['Type'] ?? '');

        if ($type === 'SubscriptionConfirmation') {
            return $this->confirmSubscription($envelope);
        }

        if ($type === 'UnsubscribeConfirmation') {
            Log::info('SES SNS unsubscribe confirmation received');

            return ['body' => 'OK', 'status' => 200];
        }

        if ($type !== 'Notification') {
            return ['body' => 'Ignored', 'status' => 200];
        }

        $event = json_decode((string) ($envelope['Message'] ?? ''), true);
        if (! is_array($event)) {
            return ['body' => 'Invalid notification', 'status' => 400];
        }

        $this->applyEvent($event);

        return ['body' => 'OK', 'status' => 200];
    }

    /**
     * @param  array<string, mixed>  $event
     */
    public function applyEvent(array $event): void
    {
        $emailLogId = $this->resolveEmailLogId($event);
        if ($emailLogId === null) {
            Log::info('SES event ignored because email_log_id was missing');

            return;
        }

        $emailLog = EmailLog::find($emailLogId);
        if (! $emailLog) {
            Log::warning('SES event ignored because email_log was not found', ['email_log_id' => $emailLogId]);

            return;
        }

        if ((int) $emailLog->mail_type !== 1) {
            Log::warning('SES event ignored because the email_log is not outgoing', ['email_log_id' => $emailLogId]);

            return;
        }

        $eventType = $this->normalizeEventType($event);
        if ($eventType === null) {
            return;
        }

        if ($this->alreadyRecorded($emailLog, $eventType, $event)) {
            return;
        }

        $occurredAt = $this->eventTimestamp($event) ?? now();
        EmailLogEvent::create([
            'email_log_id' => $emailLog->id,
            'event_type' => $eventType,
            'occurred_at' => $occurredAt,
            'metadata' => $this->metadata($event, $eventType),
        ]);

        if (in_array($eventType, ['open', 'click', 'spamreport'], true)) {
            $this->applyEngagement($emailLog, $eventType, $occurredAt);
            $this->upgradeToDelivered($emailLog, $occurredAt);

            return;
        }

        $this->applyStatus($emailLog, $eventType, $this->reason($event, $eventType), $occurredAt);
    }

    private function tokenMatches(Request $request): bool
    {
        $token = config('services.ses.webhook_token');
        if (! is_string($token) || $token === '') {
            return true;
        }

        $provided = $request->query('token', $request->header('X-Webhook-Token'));

        return is_string($provided) && hash_equals($token, $provided);
    }

    /**
     * @param  array<string, mixed>  $envelope
     * @return array{body: string, status: int}
     */
    private function confirmSubscription(array $envelope): array
    {
        $url = (string) ($envelope['SubscribeURL'] ?? '');
        if (! $this->verifier->isAllowedSnsUrl($url)) {
            return ['body' => 'Invalid subscribe URL', 'status' => 400];
        }

        $response = Http::withOptions(['allow_redirects' => false])
            ->timeout(5)
            ->connectTimeout(3)
            ->get($url);

        if ($response->failed()) {
            Log::warning('SES SNS subscription confirmation failed', ['status' => $response->status()]);

            return ['body' => 'Confirm failed', 'status' => 502];
        }

        Log::info('SES SNS subscription confirmed', ['topic' => $envelope['TopicArn'] ?? null]);

        return ['body' => 'OK', 'status' => 200];
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function normalizeEventType(array $event): ?string
    {
        $raw = strtolower((string) ($event['eventType'] ?? $event['notificationType'] ?? ''));
        $raw = str_replace([' ', '_'], '', $raw);

        if ($raw === 'bounce') {
            $bounceType = strtolower((string) ($event['bounce']['bounceType'] ?? 'permanent'));

            return $bounceType === 'transient' ? 'deferred' : 'bounced';
        }

        return match ($raw) {
            'send' => 'processed',
            'delivery' => 'delivered',
            'deliverydelay' => 'deferred',
            'reject', 'renderingfailure' => 'dropped',
            'complaint' => 'spamreport',
            'open' => 'open',
            'click' => 'click',
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function resolveEmailLogId(array $event): ?int
    {
        $tagged = $event['mail']['tags']['email_log_id'] ?? null;
        if (is_array($tagged)) {
            $tagged = $tagged[0] ?? null;
        }

        $fromTag = $this->positiveInt($tagged);
        if ($fromTag !== null) {
            return $fromTag;
        }

        $headers = $event['mail']['headers'] ?? [];
        if (! is_array($headers)) {
            return null;
        }

        foreach ($headers as $header) {
            if (! is_array($header)) {
                continue;
            }

            $name = strtolower((string) ($header['name'] ?? ''));
            $value = (string) ($header['value'] ?? '');

            if ($name === 'x-crm-email-log-id') {
                return $this->positiveInt($value);
            }

            if ($name === 'x-ses-message-tags' && preg_match('/(?:^|[,\\s])email_log_id=(\\d+)/', $value, $matches) === 1) {
                return (int) $matches[1];
            }
        }

        return null;
    }

    private function positiveInt(mixed $value): ?int
    {
        if (is_int($value) && $value > 0) {
            return $value;
        }

        if (is_string($value) && ctype_digit($value) && (int) $value > 0) {
            return (int) $value;
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function alreadyRecorded(EmailLog $emailLog, string $eventType, array $event): bool
    {
        $messageId = (string) ($event['mail']['messageId'] ?? '');
        if ($messageId === '') {
            return false;
        }

        return EmailLogEvent::query()
            ->where('email_log_id', $emailLog->id)
            ->where('event_type', $eventType)
            ->where('metadata->ses_message_id', $messageId)
            ->exists();
    }

    /**
     * @param  array<string, mixed>  $event
     * @return array<string, mixed>
     */
    private function metadata(array $event, string $eventType): array
    {
        $metadata = array_filter([
            'ses_message_id' => $event['mail']['messageId'] ?? null,
            'reason' => $this->reason($event, $eventType),
            'smtp_response' => $event['delivery']['smtpResponse'] ?? null,
        ], static fn ($value) => is_string($value) && $value !== '');

        $recipient = $event['mail']['destination'][0] ?? null;
        if (is_string($recipient) && $recipient !== '') {
            $metadata['email'] = $recipient;
        }

        return $metadata;
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function reason(array $event, string $eventType): ?string
    {
        $reason = match ($eventType) {
            'bounced', 'deferred' => $this->recipientDiagnostic($event['bounce']['bouncedRecipients'] ?? $event['deliveryDelay']['delayedRecipients'] ?? null)
                ?? ($event['deliveryDelay']['delayType'] ?? null),
            'dropped' => $event['reject']['reason'] ?? $event['failure']['errorMessage'] ?? null,
            'spamreport' => $event['complaint']['complaintFeedbackType'] ?? null,
            default => null,
        };

        if (! is_string($reason) || trim($reason) === '') {
            return null;
        }

        return trim($reason);
    }

    private function recipientDiagnostic(mixed $recipients): ?string
    {
        if (! is_array($recipients) || ! isset($recipients[0]) || ! is_array($recipients[0])) {
            return null;
        }

        $diagnostic = $recipients[0]['diagnosticCode'] ?? $recipients[0]['status'] ?? null;

        return is_string($diagnostic) && $diagnostic !== '' ? $diagnostic : null;
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function eventTimestamp(array $event): ?Carbon
    {
        foreach (['delivery', 'bounce', 'complaint', 'deliveryDelay', 'open', 'click', 'reject'] as $key) {
            $stamp = $event[$key]['timestamp'] ?? null;
            if (is_string($stamp) && $stamp !== '') {
                return Carbon::parse($stamp);
            }
        }

        $mailStamp = $event['mail']['timestamp'] ?? null;
        if (is_string($mailStamp) && $mailStamp !== '') {
            return Carbon::parse($mailStamp);
        }

        return null;
    }

    private function applyStatus(EmailLog $emailLog, string $status, ?string $reason, Carbon $occurredAt): void
    {
        $emailLog->refresh();
        $current = $emailLog->delivery_status ?: 'pending';
        if (! $this->shouldUpdateStatus($current, $status)) {
            return;
        }

        $updates = ['delivery_status' => $status];
        if ($status === 'delivered') {
            $updates['delivered_at'] = $emailLog->delivered_at ?? $occurredAt;
            $updates['status_reason'] = null;
        } elseif ($reason !== null) {
            $updates['status_reason'] = mb_substr($reason, 0, 2000);
        }

        $emailLog->update($updates);
    }

    private function upgradeToDelivered(EmailLog $emailLog, Carbon $occurredAt): void
    {
        $this->applyStatus($emailLog, 'delivered', null, $occurredAt);
    }

    private function applyEngagement(EmailLog $emailLog, string $eventType, Carbon $occurredAt): void
    {
        $emailLog->refresh();
        $updates = [];

        if ($eventType === 'open' && $emailLog->opened_at === null) {
            $updates['opened_at'] = $occurredAt;
        }

        if ($eventType === 'click' && $emailLog->clicked_at === null) {
            $updates['clicked_at'] = $occurredAt;
        }

        if ($eventType === 'spamreport' && $emailLog->spam_reported_at === null) {
            $updates['spam_reported_at'] = $occurredAt;
        }

        if ($updates !== []) {
            $emailLog->update($updates);
        }
    }

    private function shouldUpdateStatus(string $current, string $next): bool
    {
        $currentRank = self::STATUS_RANK[$current] ?? 0;
        $nextRank = self::STATUS_RANK[$next] ?? 0;

        return $nextRank > $currentRank;
    }
}
