<?php

namespace App\Support;

use App\Models\EmailLog;
use App\Services\MatterEmailBodyCleanupService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Resolve the most recent checklist-style sent email for a client matter
 * (sheet Email Reminder popup prefill).
 */
final class LastChecklistEmailForMatter
{
    public function __construct(
        private readonly MatterEmailBodyCleanupService $bodyCleanup
    ) {}

    /**
     * @return array{id: int, subject: string, message: string, template_id: int|null}|null
     */
    public function find(int $clientMatterId, ?int $firstTemplateId = null): ?array
    {
        if ($clientMatterId <= 0 || ! Schema::hasTable('email_logs')) {
            return null;
        }

        $base = EmailLog::query()
            ->where('client_matter_id', $clientMatterId)
            ->where(function ($q): void {
                $q->where('mail_body_type', 'sent')
                    ->orWhere('mail_type', 1)
                    ->orWhereNull('mail_body_type');
            });

        $candidates = (clone $base)
            ->where(function ($q) use ($firstTemplateId): void {
                $q->where(function ($inner): void {
                    $inner->whereNotNull('attachments')
                        ->where('attachments', '!=', '')
                        ->where('attachments', '!=', '[]')
                        ->where('attachments', '!=', 'null');
                });
                if ($firstTemplateId !== null && $firstTemplateId > 0) {
                    $q->orWhere('template_id', $firstTemplateId);
                }
                $q->orWhere('subject', 'like', '%Checklist%');
            })
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        if ($candidates->isEmpty()) {
            $candidates = (clone $base)
                ->orderByDesc('id')
                ->limit(5)
                ->get();
        }

        foreach ($candidates as $email) {
            $message = $this->resolveMessageHtml($email);
            if ($message === '') {
                continue;
            }

            return [
                'id' => (int) $email->id,
                'subject' => (string) ($email->subject ?? ''),
                'message' => $message,
                'template_id' => $email->template_id !== null ? (int) $email->template_id : null,
            ];
        }

        return null;
    }

    private function resolveMessageHtml(EmailLog $email): string
    {
        foreach (['message', 'enhanced_html', 'rendered_html'] as $column) {
            if (! Schema::hasColumn('email_logs', $column)) {
                continue;
            }
            $value = trim((string) ($email->{$column} ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        $s3Key = $this->bodyCleanup->resolveArchivedBodyS3Key($email);
        if ($s3Key === null) {
            return '';
        }

        try {
            $content = Storage::disk('s3')->get($s3Key);
        } catch (\Throwable) {
            return '';
        }

        $content = trim((string) $content);
        if ($content === '') {
            return '';
        }

        // MatterEmailBodyCleanupService wraps bodies with Subject/From/To then <hr>.
        $hrPos = strripos($content, '<hr>');
        if ($hrPos === false) {
            $hrPos = strripos($content, '<hr ');
        }
        if ($hrPos !== false) {
            $after = trim(substr($content, $hrPos));
            $after = preg_replace('/^<hr[^>]*>/i', '', $after) ?? $after;
            $after = trim($after);
            if ($after !== '') {
                return $after;
            }
        }

        return $content;
    }
}
