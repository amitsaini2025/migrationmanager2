<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\ClientPortalDetailAudit;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

/**
 * ClientPortalController
 * 
 * Handles client portal user management including creating portal users,
 * activating/deactivating access, and sending portal credentials.
 * 
 * Maps to: resources/views/Admin/clients/tabs/client_portal.blade.php
 */
class ClientPortalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Toggle Client Portal Status and Send Email
     */
    public function toggleClientPortal(Request $request)
    {
        try {
            $request->validate([
                'client_id' => 'required|integer',
                'status' => 'required|in:true,false,1,0'
            ]);

            $clientId = $request->client_id;
            $status = ($request->status === true || $request->status === 'true' || $request->status === '1' || $request->status === 1) ? 1 : 0;

            // Update the client's cp_status
            $client = \App\Models\Admin::where('id', $clientId)->where('role', '7')->first();

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            $client->cp_status = $status;
            
            // Handle password based on status
            if ($status == 1) {
                // Generate and save password when activating client portal
                $randomPassword = Str::random(12);
                $hashedPassword = Hash::make($randomPassword);
                $client->password = $hashedPassword;
            } else {
                // Clear password when deactivating client portal for security
                $client->password = null;
            }
            
            $client->save();

            // Send appropriate email based on status change
            if ($status == 1) {
                // Status is being turned ON - send activation email with password
                $this->sendClientPortalActivationEmail($client, $randomPassword);
            } else {
                // Status is being turned OFF - send deactivation email
                $this->sendClientPortalDeactivationEmail($client);
            }

            return response()->json([
                'success' => true,
                'message' => $status ? 'Client Portal activated and email sent successfully' : 'Client Portal deactivated and email sent successfully',
                'status' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating client portal status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send Client Portal activation email
     */
    private function sendClientPortalActivationEmail($client, $password)
    {
        try {
            // Get client's email directly from admins table
            $emailAddress = $client->email;

            if (!$emailAddress) {
                throw new \Exception('No email address found for client');
            }

            // Email content
            $subject = 'Client Portal Access Activated - Bansal Immigration';
            $message = "
                <html>
                <body>
                    <h2>Client Portal Access Activated</h2>
                    <p>Dear {$client->first_name} {$client->last_name},</p>
                    <p>Your client portal has been activated successfully. Below are your login credentials:</p>
                    <p><strong>Email:</strong> {$client->email}</p>
                    <p><strong>Password:</strong> {$password}</p>
                    <p>You can now access your client portal using the mobile app with these credentials to view your case details.</p>
                    <p>Download the mobile app from the following link: <a href='https://play.google.com/store/apps/details?id=com.bansalimmigration.clientportal'>https://play.google.com/store/apps/details?id=com.bansalimmigration.clientportal</a></p>
                    <p><strong>Important:</strong> Please keep your login credentials secure and do not share them with anyone. After Login in mboile App you can chnage your password.</p>
                    <p>Please contact us if you have any questions.</p>
                    <br>
                    <p>Best regards,<br>Bansal Immigration Team</p>
                </body>
                </html>
            ";

            // Send email using Mail facade
            Mail::send('emails.client_portal_active_email', ['content' => $message], function($message) use ($emailAddress, $subject) {
                $message->to($emailAddress)
                       ->subject($subject)
                       ->from(config('mail.from.address'), config('mail.from.name'));
            });

        } catch (\Exception $e) {
            Log::error('Failed to send client portal activation email: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send Client Portal deactivation email
     */
    private function sendClientPortalDeactivationEmail($client)
    {
        try {
            // Get client's email directly from admins table
            $emailAddress = $client->email;

            if (!$emailAddress) {
                throw new \Exception('No email address found for client');
            }

            // Email content for deactivation
            $subject = 'Client Portal Access Deactivated - Bansal Immigration';
            $message = "
                <html>
                <body>
                    <h2>Client Portal Access Deactivated</h2>
                    <p>Dear {$client->first_name} {$client->last_name},</p>
                    <p>Your client portal access has been deactivated. Now you cannot access the client portal from mobile app.</p>
                    <p>Please contact the administrator for further assistance.</p>
                    <br>
                    <p>Best regards,<br>Bansal Immigration Team</p>
                </body>
                </html>
            ";

            // Send email using Mail facade
            Mail::send('emails.client_portal_active_email', ['content' => $message], function($message) use ($emailAddress, $subject) {
                $message->to($emailAddress)
                       ->subject($subject)
                       ->from(config('mail.from.address'), config('mail.from.name'));
            });

        } catch (\Exception $e) {
            Log::error('Failed to send client portal deactivation email: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Approve Audit Value
     * Updates the admins table with the audit value and removes the audit entry
     */
    public function approveAuditValue(Request $request)
    {
        try {
            $request->validate([
                'audit_id' => 'required|integer',
                'field_name' => 'required|string',
                'client_id' => 'required|integer',
                'client_matter_id' => 'required|integer'
            ]);

            $auditId = $request->audit_id;
            $fieldName = $request->field_name;
            $clientId = $request->client_id;
            $clientMatterId = $request->client_matter_id;

            // Get the audit entry
            $auditEntry = ClientPortalDetailAudit::find($auditId);

            if (!$auditEntry || $auditEntry->client_id != $clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Audit entry not found'
                ], 404);
            }

            // Get the client
            $client = Admin::find($clientId);
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            DB::beginTransaction();

            try {
                $newValue = $auditEntry->new_value;

                // Map field names to database columns
                $fieldMapping = [
                    'first_name' => 'first_name',
                    'last_name' => 'last_name',
                    'client_id' => 'client_id',
                    'dob' => 'dob',
                    'age' => 'age',
                    'gender' => 'gender',
                    'marital_status' => 'marital_status',
                ];

                if (!isset($fieldMapping[$fieldName])) {
                    throw new \Exception('Invalid field name');
                }

                $dbField = $fieldMapping[$fieldName];

                // Update the admins table
                $client->$dbField = $newValue;
                
                // If DOB is updated, recalculate age
                if ($fieldName === 'dob' && $newValue) {
                    try {
                        $dob = \Carbon\Carbon::parse($newValue);
                        $now = \Carbon\Carbon::now();
                        $age = $dob->diff($now)->format('%y years %m months');
                        $client->age = $age;
                    } catch (\Exception $e) {
                        // Age calculation failed, continue without updating age
                    }
                }
                
                $client->save();

                // Delete the audit entry
                $auditEntry->delete();

                // If DOB is approved, also remove age audit entry since age will be recalculated
                if ($fieldName === 'dob') {
                    $ageAuditEntry = ClientPortalDetailAudit::where('client_id', $clientId)
                        ->where('meta_key', 'age')
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    
                    if ($ageAuditEntry) {
                        $ageAuditEntry->delete();
                    }
                }
                
                // If age is approved, also remove DOB audit entry since they are related
                if ($fieldName === 'age') {
                    $dobAuditEntry = ClientPortalDetailAudit::where('client_id', $clientId)
                        ->where('meta_key', 'dob')
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    
                    if ($dobAuditEntry) {
                        $dobAuditEntry->delete();
                    }
                }

                // Get field label for message
                $fieldLabels = [
                    'first_name' => 'First Name',
                    'last_name' => 'Last Name',
                    'client_id' => 'Client ID',
                    'dob' => 'Date of Birth',
                    'age' => 'Age',
                    'gender' => 'Gender',
                    'marital_status' => 'Marital Status'
                ];

                $fieldLabel = $fieldLabels[$fieldName] ?? $fieldName;

                // Get sender (admin) information
                $sender = Auth::guard('admin')->user();
                $senderId = $sender ? $sender->id : null;
                $senderName = $sender ? ($sender->first_name . ' ' . $sender->last_name) : 'Admin';

                // Create approval message
                $message = "Your Basic Detail {$fieldLabel} related changes approved by Admin. Please check at your end.";

                // Create message record
                $messageData = [
                    'message' => $message,
                    'sender' => $senderName,
                    'sender_id' => $senderId,
                    'sent_at' => now(),
                    'client_matter_id' => $clientMatterId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $messageId = DB::table('messages')->insertGetId($messageData);

                if ($messageId) {
                    // Insert recipient into pivot table
                    DB::table('message_recipients')->insert([
                        'message_id' => $messageId,
                        'recipient_id' => $clientId,
                        'recipient' => $client->first_name . ' ' . $client->last_name,
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Broadcast message via Pusher if event exists
                    if (class_exists('\App\Events\MessageSent')) {
                        $messageForBroadcast = [
                            'id' => $messageId,
                            'message' => $message,
                            'sender' => $senderName,
                            'sender_id' => $senderId,
                            'sent_at' => now()->toISOString(),
                            'created_at' => now()->toISOString(),
                            'client_matter_id' => $clientMatterId,
                            'recipients' => [[
                                'recipient_id' => $clientId,
                                'recipient' => $client->first_name . ' ' . $client->last_name
                            ]]
                        ];

                        broadcast(new \App\Events\MessageSent($messageForBroadcast, $clientId));
                        if ($senderId) {
                            broadcast(new \App\Events\MessageSent($messageForBroadcast, $senderId));
                        }
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Change approved and updated successfully. Message sent to client.'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving change: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject Audit Value
     * Removes the audit entry and sends a message to the client
     */
    public function rejectAuditValue(Request $request)
    {
        try {
            $request->validate([
                'audit_id' => 'required|integer',
                'field_name' => 'required|string',
                'client_id' => 'required|integer',
                'client_matter_id' => 'required|integer'
            ]);

            $auditId = $request->audit_id;
            $fieldName = $request->field_name;
            $clientId = $request->client_id;
            $clientMatterId = $request->client_matter_id;

            // Get the audit entry
            $auditEntry = ClientPortalDetailAudit::find($auditId);

            if (!$auditEntry || $auditEntry->client_id != $clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Audit entry not found'
                ], 404);
            }

            // Get field label for message
            $fieldLabels = [
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'client_id' => 'Client ID',
                'dob' => 'Date of Birth',
                'age' => 'Age',
                'gender' => 'Gender',
                'marital_status' => 'Marital Status'
            ];

            $fieldLabel = $fieldLabels[$fieldName] ?? $fieldName;

            DB::beginTransaction();

            try {
                // Delete the audit entry
                $auditEntry->delete();

                // If DOB is rejected, also remove age audit entry since age depends on DOB
                if ($fieldName === 'dob') {
                    $ageAuditEntry = ClientPortalDetailAudit::where('client_id', $clientId)
                        ->where('meta_key', 'age')
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    
                    if ($ageAuditEntry) {
                        $ageAuditEntry->delete();
                    }
                }
                
                // If age is rejected, also remove DOB audit entry since they are related
                if ($fieldName === 'age') {
                    $dobAuditEntry = ClientPortalDetailAudit::where('client_id', $clientId)
                        ->where('meta_key', 'dob')
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    
                    if ($dobAuditEntry) {
                        $dobAuditEntry->delete();
                    }
                }

                // Get sender (admin) information
                $sender = Auth::guard('admin')->user();
                $senderId = $sender ? $sender->id : null;
                $senderName = $sender ? ($sender->first_name . ' ' . $sender->last_name) : 'Admin';

                // Get client information
                $client = Admin::find($clientId);
                if (!$client) {
                    throw new \Exception('Client not found');
                }

                // Create rejection message
                $message = "Your Basic Detail {$fieldLabel} related changes rejected by Admin. Please try again.";

                // Create message record
                $messageData = [
                    'message' => $message,
                    'sender' => $senderName,
                    'sender_id' => $senderId,
                    'sent_at' => now(),
                    'client_matter_id' => $clientMatterId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $messageId = DB::table('messages')->insertGetId($messageData);

                if ($messageId) {
                    // Insert recipient into pivot table
                    DB::table('message_recipients')->insert([
                        'message_id' => $messageId,
                        'recipient_id' => $clientId,
                        'recipient' => $client->first_name . ' ' . $client->last_name,
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Broadcast message via Pusher if event exists
                    if (class_exists('\App\Events\MessageSent')) {
                        $messageForBroadcast = [
                            'id' => $messageId,
                            'message' => $message,
                            'sender' => $senderName,
                            'sender_id' => $senderId,
                            'sent_at' => now()->toISOString(),
                            'created_at' => now()->toISOString(),
                            'client_matter_id' => $clientMatterId,
                            'recipients' => [[
                                'recipient_id' => $clientId,
                                'recipient' => $client->first_name . ' ' . $client->last_name
                            ]]
                        ];

                        broadcast(new \App\Events\MessageSent($messageForBroadcast, $clientId));
                        if ($senderId) {
                            broadcast(new \App\Events\MessageSent($messageForBroadcast, $senderId));
                        }
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Change rejected and message sent to client'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting change: ' . $e->getMessage()
            ], 500);
        }
    }
}

