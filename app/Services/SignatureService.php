<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Signer;
use App\Models\Admin;
use App\Models\Lead;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SignatureService
{
    /**
     * Send a document for signature
     *
     * @param Document $document
     * @param array $signers Array of ['email' => '', 'name' => '']
     * @param array $options Additional options
     * @return bool
     */
    public function send(Document $document, array $signers, array $options = []): bool
    {
        try {
            $createdSigners = [];

            foreach ($signers as $signerData) {
                $signer = $document->signers()->create([
                    'email' => $signerData['email'],
                    'name' => $signerData['name'],
                    'token' => Str::random(64),
                    'status' => 'pending',
                ]);

                $createdSigners[] = $signer;
            }

            // Update document status and tracking
            $document->update([
                'status' => 'sent',
                'primary_signer_email' => $signers[0]['email'] ?? null,
                'signer_count' => count($signers),
                'last_activity_at' => now(),
            ]);

            // Send emails to all signers
            foreach ($createdSigners as $signer) {
                $this->sendSigningEmail($document, $signer, $options);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send document for signature', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send signing email to a signer
     */
    protected function sendSigningEmail(Document $document, Signer $signer, array $options = []): void
    {
        $signingUrl = url("/sign/{$document->id}/{$signer->token}");
        
        $subject = $options['subject'] ?? 'Document Signature Request';
        $message = $options['message'] ?? "Please sign the document: {$document->display_title}";

        Mail::raw("{$message}\n\nClick here to sign: {$signingUrl}", function ($mail) use ($signer, $subject) {
            $mail->to($signer->email, $signer->name)
                 ->subject($subject);
        });
    }

    /**
     * Send reminder to a signer
     */
    public function remind(Signer $signer): bool
    {
        try {
            // Check reminder limits
            if ($signer->reminder_count >= 3) {
                throw new \Exception('Maximum reminders already sent');
            }

            if ($signer->last_reminder_sent_at && $signer->last_reminder_sent_at->diffInHours(now()) < 24) {
                throw new \Exception('Please wait 24 hours between reminders');
            }

            $document = $signer->document;
            $signingUrl = url("/sign/{$document->id}/{$signer->token}");

            Mail::raw("This is a reminder to sign your document: {$document->display_title}\n\nClick here to sign: {$signingUrl}", 
                function ($mail) use ($signer) {
                    $mail->to($signer->email, $signer->name)
                         ->subject('Reminder: Please Sign Your Document');
                }
            );

            // Update reminder tracking
            $signer->update([
                'last_reminder_sent_at' => now(),
                'reminder_count' => $signer->reminder_count + 1
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send reminder', [
                'signer_id' => $signer->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Void a document
     */
    public function void(Document $document, string $reason = null): bool
    {
        try {
            $document->update([
                'status' => 'voided',
                'last_activity_at' => now(),
            ]);

            // Optionally log the reason
            if ($reason) {
                Log::info('Document voided', [
                    'document_id' => $document->id,
                    'reason' => $reason
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to void document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Associate a document with an entity (Client or Lead)
     */
    public function associate(Document $document, string $entityType, int $entityId, string $note = null): bool
    {
        try {
            $documentableType = match($entityType) {
                'client' => Admin::class,
                'lead' => Lead::class,
                default => throw new \InvalidArgumentException("Invalid entity type: {$entityType}")
            };

            $document->update([
                'documentable_type' => $documentableType,
                'documentable_id' => $entityId,
                'origin' => $entityType,
            ]);

            // Log the association
            Log::info('Document associated', [
                'document_id' => $document->id,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'note' => $note
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to associate document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Detach a document from its association
     */
    public function detach(Document $document, string $reason = null): bool
    {
        try {
            $document->update([
                'documentable_type' => null,
                'documentable_id' => null,
                'origin' => 'ad_hoc',
            ]);

            // Log the detachment
            if ($reason) {
                Log::info('Document detached', [
                    'document_id' => $document->id,
                    'reason' => $reason
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to detach document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Auto-suggest association based on signer email
     */
    public function suggestAssociation(string $email): ?array
    {
        // Try to find matching client (Admin with role != 7)
        $client = Admin::where('email', $email)
            ->where('role', '!=', 7)
            ->first();

        if ($client) {
            return [
                'type' => 'client',
                'id' => $client->id,
                'name' => trim("{$client->first_name} {$client->last_name}"),
                'email' => $client->email
            ];
        }

        // Try to find matching lead
        $lead = Lead::where('email', $email)->first();

        if ($lead) {
            return [
                'type' => 'lead',
                'id' => $lead->id,
                'name' => trim("{$lead->first_name} {$lead->last_name}"),
                'email' => $lead->email
            ];
        }

        return null;
    }

    /**
     * Archive old drafts
     */
    public function archiveOldDrafts(int $daysOld = 30): int
    {
        $count = Document::where('status', 'draft')
            ->where('created_at', '<', now()->subDays($daysOld))
            ->whereNull('archived_at')
            ->update(['archived_at' => now()]);

        Log::info("Archived {$count} old draft documents");

        return $count;
    }

    /**
     * Get pending count for a user
     */
    public function getPendingCount(int $userId): int
    {
        return Document::forUser($userId)
            ->byStatus('sent')
            ->notArchived()
            ->count();
    }
}

