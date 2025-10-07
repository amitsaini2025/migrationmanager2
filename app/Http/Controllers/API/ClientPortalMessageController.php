<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Broadcast;
use App\Events\MessageSent;
use App\Events\MessageReceived;
use App\Events\MessageDeleted;
use App\Events\MessageUpdated;
use App\Events\UnreadCountUpdated;

class ClientPortalMessageController extends Controller
{
    /**
     * Send Message
     * POST /api/messages/send
     * 
     * Sends a message and broadcasts it in real-time
     */
    public function sendMessage(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            // Validate request
            $validator = Validator::make($request->all(), [
                'message' => 'nullable|string|max:5000',
                'client_matter_id' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $message = $request->input('message');
            $clientMatterId = $request->input('client_matter_id');

            // Get sender information (optional)
            $sender = null;
            if ($clientId) {
                $sender = DB::table('admins')
                    ->select('id', 'first_name', 'last_name', 'email')
                    ->where('id', $clientId)
                    ->first();
            }



            // Create message record
            $messageData = [
                'message' => $message,
                'sender' => $sender ? $sender->first_name . ' ' . $sender->last_name : null,
                'sender_id' => $clientId,
                'recipient_id' => null,
                'sent_at' => now(),
                'is_read' => false,
                'client_matter_id' => $clientMatterId,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $messageId = DB::table('messages')->insertGetId($messageData);

            if ($messageId) {
                // Prepare message for broadcasting
                $messageForBroadcast = [
                    'id' => $messageId,
                    'message' => $message,
                    'sender' => $sender ? $sender->first_name . ' ' . $sender->last_name : null,
                    'sender_id' => $clientId,
                    'recipient_id' => null,
                    'sent_at' => now()->toISOString(),
                    'is_read' => false
                ];

                // Broadcast message (no specific recipient)
                broadcast(new MessageSent($messageForBroadcast, null));

                // Send notifications to migration agent, person responsible, person assisting, and superadmins
                $clientMatter = DB::table('client_matters')
                    ->where('id', $clientMatterId)
                    ->first();

                $notificationUsers = [];

                // Get users from client matter (migration agent, person responsible, person assisting)
                if ($clientMatter) {
                    $matterUsers = [
                        $clientMatter->sel_migration_agent,
                        $clientMatter->sel_person_responsible,
                        $clientMatter->sel_person_assisting
                    ];

                    // Remove null values and add to notification list
                    $matterUsers = array_filter($matterUsers, function($userId) {
                        return $userId !== null;
                    });

                    $notificationUsers = array_merge($notificationUsers, $matterUsers);
                }

                // Get superadmin users (role=1)
                $superadmins = DB::table('admins')
                    ->where('role', 1)
                    ->where('status', 1) // Active superadmins only
                    ->pluck('id')
                    ->toArray();

                $notificationUsers = array_merge($notificationUsers, $superadmins);

                // Remove duplicates and current user from notification list
                $notificationUsers = array_unique($notificationUsers);
                $notificationUsers = array_filter($notificationUsers, function($userId) use ($clientId) {
                    return $userId != $clientId;
                });

                // Send notifications to each user
                foreach ($notificationUsers as $userId) {
                    DB::table('notifications')->insert([
                        'sender_id' => $clientId,
                        'receiver_id' => $userId,
                        'module_id' => $clientMatterId,
                        'url' => '/admin/messages',
                        'notification_type' => 'message',
                        'message' => 'New message received by Client Portal Mobile App from ' . ($sender ? $sender->first_name . ' ' . $sender->last_name : 'Client') . ' for matter ' . ($clientMatter ? $clientMatter->client_unique_matter_no : 'ID: ' . $clientMatterId),
                        'created_at' => now(),
                        'updated_at' => now(),
                        'sender_status' => 1,
                        'receiver_status' => 0,
                        'seen' => 0
                    ]);
                }

                // Create activity log
                DB::table('activities_logs')->insert([
                    'client_id' => $clientId,
                    'created_by' => $clientId,
                    'subject' => 'Message sent',
                    'description' => 'Message sent by Client Portal Mobile App for matter ID: ' . $clientMatterId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'data' => [
                        'message_id' => $messageId,
                        'message' => $messageForBroadcast,
                        'sent_at' => now()->toISOString()
                    ]
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Send Message API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'client_matter_id' => $request->input('client_matter_id'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Messages
     * GET /api/messages
     * 
     * Retrieves messages for the authenticated user
     */
    public function getMessages(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            // Validate required parameters
            $validator = Validator::make($request->all(), [
                'client_matter_id' => 'required|integer|min:1',
                'page' => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get query parameters
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);
            $clientMatterId = $request->get('client_matter_id');

            // Build query
            $query = DB::table('messages')
                ->where(function ($q) use ($clientId) {
                    $q->where('sender_id', $clientId)
                      ->orWhere('recipient_id', $clientId);
                })
                ->where('client_matter_id', $clientMatterId); // Required filter

            // Get total count
            $totalMessages = $query->count();

            // Get messages with pagination
            $messages = $query->orderBy('created_at', 'asc')
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get()
                ->map(function ($msg) use ($clientId) {
                    return [
                        'id' => $msg->id,
                        'message' => $msg->message,
                        'sender' => $msg->sender,
                        'recipient' => $msg->recipient,
                        'sender_id' => $msg->sender_id,
                        'recipient_id' => $msg->recipient_id,
                        'is_sender' => $msg->sender_id == $clientId,
                        'is_recipient' => $msg->recipient_id == $clientId,
                        'sent_at' => $msg->sent_at,
                        'read_at' => $msg->read_at,
                        'is_read' => $msg->is_read,
                        'client_matter_id' => $msg->client_matter_id,
                        'created_at' => $msg->created_at,
                        'updated_at' => $msg->updated_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'messages' => $messages,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $limit,
                        'total' => $totalMessages,
                        'last_page' => ceil($totalMessages / $limit)
                    ],
                    'filters' => [
                        'client_matter_id' => $clientMatterId
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get Messages API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Message Details
     * GET /api/messages/{id}
     * 
     * Retrieves details of a specific message
     */
    public function getMessageDetails(Request $request, $id)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            $message = DB::table('messages')
                ->where('id', $id)
                ->where(function ($q) use ($clientId) {
                    $q->where('sender_id', $clientId)
                      ->orWhere('recipient_id', $clientId);
                })
                ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender' => $message->sender,
                    'recipient' => $message->recipient,
                    'sender_id' => $message->sender_id,
                    'recipient_id' => $message->recipient_id,
                    'is_sender' => $message->sender_id == $clientId,
                    'is_recipient' => $message->recipient_id == $clientId,
                    'sent_at' => $message->sent_at,
                    'read_at' => $message->read_at,
                    'is_read' => $message->is_read,
                    'client_matter_id' => $message->client_matter_id,
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get Message Details API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'message_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch message details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark Message as Read
     * PUT /api/messages/{id}/read
     * 
     * Marks a message as read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            // First check if message exists
            $message = DB::table('messages')
                ->where('id', $id)
                ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found'
                ], 404);
            }

            // Check if user is authorized to mark this message as read (must be the recipient)
            if ($message->recipient_id != $clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized for mark as read'
                ], 403);
            }

            if (!$message->is_read) {
                DB::table('messages')
                    ->where('id', $id)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                        'updated_at' => now()
                    ]);

                // Get updated message for broadcast
                $updatedMessage = DB::table('messages')
                    ->where('id', $id)
                    ->first();

                // Broadcast message update
                broadcast(new MessageUpdated([
                    'id' => $updatedMessage->id,
                    'message' => $updatedMessage->message,
                    'sender' => $updatedMessage->sender,
                    'recipient' => $updatedMessage->recipient,
                    'is_read' => true,
                    'read_at' => $updatedMessage->read_at,
                    'sent_at' => $updatedMessage->sent_at
                ], $clientId));

                // Broadcast read status to sender
                broadcast(new MessageReceived($id, $message->sender_id));
                
                // Broadcast unread count update for current user
                $unreadCount = DB::table('messages')
                    ->where('recipient_id', $clientId)
                    ->where('is_read', false)
                    ->count();
                broadcast(new UnreadCountUpdated($clientId, $unreadCount));
            }

            return response()->json([
                'success' => true,
                'message' => 'Message marked as read'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Mark Message as Read API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'message_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark message as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Unread Count
     * GET /api/messages/unread-count
     * 
     * Gets the count of unread messages
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            $unreadCount = DB::table('messages')
                ->where('recipient_id', $clientId)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $unreadCount
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get Unread Count API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get unread count',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
