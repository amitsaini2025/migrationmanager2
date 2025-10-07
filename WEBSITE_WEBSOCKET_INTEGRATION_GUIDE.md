# Website WebSocket Integration Guide

## Overview
This guide provides complete instructions for implementing real-time messaging on the Laravel website backend using WebSocket broadcasting with Pusher Channels. The website needs to broadcast messages to specific users and handle real-time updates for the messaging system.

## Prerequisites
- Laravel 8.x or higher
- Pusher Channels account and credentials
- Laravel Broadcasting configured
- Laravel Sanctum for authentication
- Redis or Queue driver configured

## 1. Environment Configuration

### Update `.env` file:
```env
# Broadcasting Configuration
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your-pusher-app-id
PUSHER_APP_KEY=your-pusher-app-key
PUSHER_APP_SECRET=your-pusher-app-secret
PUSHER_APP_CLUSTER=ap2

# Queue Configuration (for broadcasting)
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Broadcasting Authentication
BROADCASTING_AUTH_ENDPOINT=/api/broadcasting/auth
```

### ⚠️ **CONFIGURATION CONSISTENCY**

**CRITICAL**: The Pusher credentials in this `.env` file MUST match exactly with the mobile app configuration:
- `PUSHER_APP_KEY` must match `pusherAppKey` in mobile app
- `PUSHER_APP_CLUSTER` must match `pusherCluster` in mobile app
- Both must use the same Pusher account

## 2. Broadcasting Configuration

### Update `config/broadcasting.php`:
```php
<?php

return [
    'default' => env('BROADCAST_DRIVER', 'null'),

    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
                'encrypted' => true,
                'host' => '127.0.0.1',
                'port' => 6001,
                'scheme' => 'http',
                'curl_options' => [
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                ],
            ],
        ],
    ],
];
```

## 3. Event Classes

### Create `app/Events/MessageSent.php`:
```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $targetUserId;

    /**
     * Create a new event instance.
     *
     * @param array $message
     * @param int|null $targetUserId
     */
    public function __construct($message, $targetUserId = null)
    {
        $this->message = $message;
        $this->targetUserId = $targetUserId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->targetUserId) {
            // Broadcast to specific user channel
            return new PrivateChannel('user.' . $this->targetUserId);
        }
        
        // Broadcast to matter-specific channel
        if (isset($this->message['client_matter_id'])) {
            return new PrivateChannel('matter.' . $this->message['client_matter_id']);
        }
        
        // Fallback to general channel
        return new Channel('messages');
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'timestamp' => now()->toISOString(),
            'type' => 'message_sent'
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message.sent';
    }
}
```

### Create `app/Events/MessageUpdated.php`:
```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $targetUserId;

    /**
     * Create a new event instance.
     *
     * @param array $message
     * @param int|null $targetUserId
     */
    public function __construct($message, $targetUserId = null)
    {
        $this->message = $message;
        $this->targetUserId = $targetUserId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->targetUserId) {
            return new PrivateChannel('user.' . $this->targetUserId);
        }
        
        if (isset($this->message['client_matter_id'])) {
            return new PrivateChannel('matter.' . $this->message['client_matter_id']);
        }
        
        return new Channel('messages');
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'timestamp' => now()->toISOString(),
            'type' => 'message_updated'
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message.updated';
    }
}
```

### Create `app/Events/MessageReceived.php`:
```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;
    public $senderId;

    /**
     * Create a new event instance.
     *
     * @param int $messageId
     * @param int $senderId
     */
    public function __construct($messageId, $senderId)
    {
        $this->messageId = $messageId;
        $this->senderId = $senderId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->senderId);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message_id' => $this->messageId,
            'sender_id' => $this->senderId,
            'timestamp' => now()->toISOString(),
            'type' => 'message_received'
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message.received';
    }
}
```

### Create `app/Events/UnreadCountUpdated.php`:
```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UnreadCountUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $unreadCount;

    /**
     * Create a new event instance.
     *
     * @param int $userId
     * @param int $unreadCount
     */
    public function __construct($userId, $unreadCount)
    {
        $this->userId = $userId;
        $this->unreadCount = $unreadCount;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userId);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'user_id' => $this->userId,
            'unread_count' => $this->unreadCount,
            'timestamp' => now()->toISOString(),
            'type' => 'unread_count_updated'
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'unread.count.updated';
    }
}
```

## 4. Channel Classes

### Create `app/Broadcasting/UserChannel.php`:
```php
<?php

namespace App\Broadcasting;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class UserChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  int  $userId
     * @return array|bool
     */
    public function join(Authenticatable $user, $userId)
    {
        // Allow user to join their own channel
        return (int) $user->id === (int) $userId;
    }
}
```

### Create `app/Broadcasting/MatterChannel.php`:
```php
<?php

namespace App\Broadcasting;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class MatterChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  int  $matterId
     * @return array|bool
     */
    public function join(Authenticatable $user, $matterId)
    {
        // Check if user is associated with this matter
        $isAssociated = DB::table('client_matters')
            ->where('id', $matterId)
            ->where(function($query) use ($user) {
                $query->where('sel_migration_agent', $user->id)
                      ->orWhere('sel_person_responsible', $user->id)
                      ->orWhere('sel_person_assisting', $user->id);
            })
            ->exists();

        // Allow superadmins (role=1) to join any matter channel
        $isSuperAdmin = $user->role == 1;

        return $isAssociated || $isSuperAdmin;
    }
}
```

## 5. Controller Updates

### ⚠️ **CRITICAL FIX REQUIRED**

**IMPORTANT**: The current `ClientPortalMessageController.php` has incorrect broadcasting logic that will prevent real-time messaging from working. You MUST update the controller before implementing WebSocket features.

### Current Issue in Controller:
```php
// ❌ CURRENT (INCORRECT) - Line 87 in current controller:
broadcast(new MessageSent($messageForBroadcast, null));
```

### Update `app/Http/Controllers/API/ClientPortalMessageController.php`:
```php
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

            // Get sender information
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
                    'is_read' => false,
                    'client_matter_id' => $clientMatterId
                ];

                // Get matter details for broadcasting
                $clientMatter = DB::table('client_matters')
                    ->where('id', $clientMatterId)
                    ->first();

                // Get users associated with this matter
                $matterUsers = [];
                if ($clientMatter) {
                    $matterUsers = [
                        $clientMatter->sel_migration_agent,
                        $clientMatter->sel_person_responsible,
                        $clientMatter->sel_person_assisting
                    ];
                    $matterUsers = array_filter($matterUsers, function($userId) {
                        return $userId !== null;
                    });
                }

                // Get superadmin users
                $superadmins = DB::table('admins')
                    ->where('role', 1)
                    ->where('status', 1)
                    ->pluck('id')
                    ->toArray();

                // Combine all target users
                $targetUsers = array_unique(array_merge($matterUsers, $superadmins));
                $targetUsers = array_filter($targetUsers, function($userId) use ($clientId) {
                    return $userId != $clientId;
                });

                // Broadcast to each target user
                foreach ($targetUsers as $userId) {
                    broadcast(new MessageSent($messageForBroadcast, $userId));
                }

                // Send notifications to target users
                foreach ($targetUsers as $userId) {
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
     * Mark Message as Read
     * PUT /api/messages/{id}/read
     * 
     * Marks a message as read and broadcasts read status
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

            // Check if user is authorized to mark this message as read
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

                // Prepare message for broadcast
                $messageForBroadcast = [
                    'id' => $updatedMessage->id,
                    'message' => $updatedMessage->message,
                    'sender' => $updatedMessage->sender,
                    'recipient' => $updatedMessage->recipient,
                    'is_read' => true,
                    'read_at' => $updatedMessage->read_at,
                    'sent_at' => $updatedMessage->sent_at
                ];

                // Broadcast message update to sender
                broadcast(new MessageUpdated($messageForBroadcast, $message->sender_id));

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

    // ... other existing methods remain the same
}
```

## 6. Broadcasting Authentication

### Update `routes/api.php`:
```php
<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\ServiceAccountController;
use App\Http\Controllers\API\ClientPortalController;
use App\Http\Controllers\API\ClientPortalDashboardController;
use App\Http\Controllers\API\ClientPortalDocumentController;
use App\Http\Controllers\API\ClientPortalWorkflowController;
use App\Http\Controllers\API\ClientPortalMessageController;

// Broadcasting auth route for WebSocket authentication
Route::post('/broadcasting/auth', function (Request $request) {
    try {
        // Get the authorization header
        $authHeader = $request->header('Authorization');
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Extract token
        $token = substr($authHeader, 7);
        
        // Validate token using Sanctum
        $user = \Laravel\Sanctum\PersonalAccessToken::findToken($token)?->tokenable;
        
        if (!$user) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Get request data
        $socketId = $request->input('socket_id');
        $channelName = $request->input('channel_name');

        // Validate channel name format
        if (!preg_match('/^private-(user|matter)\.\d+$/', $channelName)) {
            return response()->json(['error' => 'Invalid channel'], 403);
        }

        // Generate auth response
        $pusher = new \Pusher\Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        $authResponse = $pusher->authorizeChannel($channelName, $socketId);

        return response()->json($authResponse);
        
    } catch (\Exception $e) {
        Log::error('Broadcasting auth error: ' . $e->getMessage());
        return response()->json(['error' => 'Authentication failed'], 500);
    }
});

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // ... existing routes
});
```

## 7. Frontend JavaScript Integration

### Create `public/js/websocket.js`:
```javascript
class WebSocketManager {
    constructor() {
        this.pusher = null;
        this.channels = new Map();
        this.isConnected = false;
        this.userId = null;
        this.authToken = null;
    }

    // Initialize Pusher connection
    async initialize(userId, authToken) {
        try {
            this.userId = userId;
            this.authToken = authToken;

            // Load Pusher from CDN if not already loaded
            if (typeof Pusher === 'undefined') {
                await this.loadPusher();
            }

            this.pusher = new Pusher('your-pusher-app-key', {
                cluster: 'ap2',
                encrypted: true,
                authEndpoint: '/api/broadcasting/auth',
                auth: {
                    headers: {
                        'Authorization': `Bearer ${this.authToken}`,
                        'Content-Type': 'application/json'
                    }
                }
            });

            this.pusher.connection.bind('connected', () => {
                console.log('Pusher connected');
                this.isConnected = true;
                this.subscribeToUserChannel();
            });

            this.pusher.connection.bind('disconnected', () => {
                console.log('Pusher disconnected');
                this.isConnected = false;
            });

            this.pusher.connection.bind('error', (error) => {
                console.error('Pusher connection error:', error);
            });

        } catch (error) {
            console.error('Failed to initialize Pusher:', error);
        }
    }

    // Load Pusher from CDN
    async loadPusher() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://js.pusher.com/8.2.0/pusher.min.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    // Subscribe to user-specific channel
    subscribeToUserChannel() {
        if (!this.userId) return;

        const channelName = `private-user.${this.userId}`;
        const channel = this.pusher.subscribe(channelName);

        // Listen for message events
        channel.bind('message.sent', (data) => {
            this.handleMessageSent(data);
        });

        channel.bind('message.updated', (data) => {
            this.handleMessageUpdated(data);
        });

        channel.bind('message.received', (data) => {
            this.handleMessageReceived(data);
        });

        channel.bind('unread.count.updated', (data) => {
            this.handleUnreadCountUpdated(data);
        });

        this.channels.set(channelName, channel);
        console.log(`Subscribed to channel: ${channelName}`);
    }

    // Handle new message
    handleMessageSent(data) {
        console.log('New message received:', data);
        
        // Update message list
        this.updateMessageList(data.message);
        
        // Show notification
        this.showNotification(data.message);
        
        // Update unread count
        this.updateUnreadCount();
    }

    // Handle message updated
    handleMessageUpdated(data) {
        console.log('Message updated:', data);
        
        // Update message in list
        this.updateMessageInList(data.message);
    }

    // Handle message received (read status)
    handleMessageReceived(data) {
        console.log('Message received:', data);
        
        // Update message read status
        this.updateMessageReadStatus(data.message_id);
    }

    // Handle unread count updated
    handleUnreadCountUpdated(data) {
        console.log('Unread count updated:', data);
        
        // Update unread count display
        this.updateUnreadCountDisplay(data.unread_count);
    }

    // Update message list
    updateMessageList(message) {
        // Find message container
        const messageContainer = document.querySelector('.messages-container');
        if (!messageContainer) return;

        // Create message element
        const messageElement = this.createMessageElement(message);
        
        // Add to container
        messageContainer.appendChild(messageElement);
        
        // Scroll to bottom
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }

    // Create message element
    createMessageElement(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message-item';
        messageDiv.dataset.messageId = message.id;
        
        const isOwnMessage = message.sender_id == this.userId;
        messageDiv.classList.add(isOwnMessage ? 'own-message' : 'other-message');
        
        messageDiv.innerHTML = `
            <div class="message-content">
                <div class="message-text">${message.message || ''}</div>
                <div class="message-time">${this.formatTime(message.sent_at)}</div>
            </div>
        `;
        
        return messageDiv;
    }

    // Update message in list
    updateMessageInList(message) {
        const messageElement = document.querySelector(`[data-message-id="${message.id}"]`);
        if (messageElement) {
            // Update message content
            const messageText = messageElement.querySelector('.message-text');
            if (messageText) {
                messageText.textContent = message.message || '';
            }
            
            // Update read status
            if (message.is_read) {
                messageElement.classList.add('read');
            }
        }
    }

    // Update message read status
    updateMessageReadStatus(messageId) {
        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
        if (messageElement) {
            messageElement.classList.add('read');
        }
    }

    // Update unread count
    async updateUnreadCount() {
        try {
            const response = await fetch('/api/messages/unread-count', {
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.updateUnreadCountDisplay(data.data.unread_count);
            }
        } catch (error) {
            console.error('Failed to update unread count:', error);
        }
    }

    // Update unread count display
    updateUnreadCountDisplay(count) {
        // Update badge in navigation
        const badge = document.querySelector('.unread-count-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'block' : 'none';
        }
        
        // Update page title if needed
        if (count > 0) {
            document.title = `(${count}) Messages - Client Portal`;
        } else {
            document.title = 'Messages - Client Portal';
        }
    }

    // Show notification
    showNotification(message) {
        // Check if notifications are supported
        if ('Notification' in window && Notification.permission === 'granted') {
            const notification = new Notification('New Message', {
                body: message.message || 'You have a new message',
                icon: '/favicon.ico'
            });
            
            // Auto-close after 5 seconds
            setTimeout(() => notification.close(), 5000);
        }
    }

    // Format time
    formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // Disconnect
    disconnect() {
        if (this.pusher) {
            this.pusher.disconnect();
            this.isConnected = false;
            this.channels.clear();
        }
    }

    // Get connection status
    getConnectionStatus() {
        return {
            isConnected: this.isConnected,
            userId: this.userId,
            channels: Array.from(this.channels.keys())
        };
    }
}

// Global instance
window.webSocketManager = new WebSocketManager();
```

## 8. Frontend Integration

### Update your main layout file (e.g., `resources/views/layouts/app.blade.php`):
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Portal</title>
    
    <!-- Pusher JavaScript SDK -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <!-- Custom WebSocket JavaScript -->
    <script src="{{ asset('js/websocket.js') }}"></script>
    
    <style>
        /* Message styles */
        .messages-container {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        
        .message-item {
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 8px;
            max-width: 70%;
        }
        
        .own-message {
            background-color: #007bff;
            color: white;
            margin-left: auto;
        }
        
        .other-message {
            background-color: #f8f9fa;
            color: #333;
        }
        
        .message-content {
            display: flex;
            flex-direction: column;
        }
        
        .message-text {
            margin-bottom: 4px;
        }
        
        .message-time {
            font-size: 12px;
            opacity: 0.7;
        }
        
        .read {
            opacity: 0.8;
        }
        
        .unread-count-badge {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            margin-left: 5px;
        }
        
        .connection-status {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            z-index: 1000;
        }
        
        .connected {
            background-color: #28a745;
            color: white;
        }
        
        .disconnected {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Connection Status Indicator -->
    <div id="connection-status" class="connection-status disconnected">
        Disconnected
    </div>
    
    <!-- Navigation -->
    <nav class="navbar">
        <a href="/dashboard">Dashboard</a>
        <a href="/messages">
            Messages
            <span class="unread-count-badge" style="display: none;">0</span>
        </a>
        <!-- ... other navigation items -->
    </nav>
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <script>
        // Initialize WebSocket connection when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Get user data and token from Laravel
            const userId = {{ auth()->id() ?? 'null' }};
            const authToken = '{{ auth()->user()?->createToken("web-socket")->plainTextToken ?? "" }}';
            
            if (userId && authToken) {
                window.webSocketManager.initialize(userId, authToken);
                
                // Update connection status
                setInterval(() => {
                    const status = window.webSocketManager.getConnectionStatus();
                    const statusElement = document.getElementById('connection-status');
                    
                    if (status.isConnected) {
                        statusElement.className = 'connection-status connected';
                        statusElement.textContent = 'Connected';
                    } else {
                        statusElement.className = 'connection-status disconnected';
                        statusElement.textContent = 'Disconnected';
                    }
                }, 1000);
            }
        });
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            window.webSocketManager.disconnect();
        });
    </script>
</body>
</html>
```

## 9. Message Management Page

### Create `resources/views/messages/index.blade.php`:
```html
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Messages</h2>
            
            <!-- Message List -->
            <div class="messages-container" id="messages-container">
                <!-- Messages will be loaded here -->
            </div>
            
            <!-- Message Input -->
            <div class="message-input">
                <form id="message-form">
                    <div class="input-group">
                        <input type="text" id="message-input" class="form-control" placeholder="Type your message...">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const messagesContainer = document.getElementById('messages-container');
    
    // Load initial messages
    loadMessages();
    
    // Handle message form submission
    messageForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;
        
        try {
            const response = await fetch('/api/messages/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${window.webSocketManager.authToken}`
                },
                body: JSON.stringify({
                    message: message,
                    client_matter_id: 1 // Replace with actual matter ID
                })
            });
            
            if (response.ok) {
                messageInput.value = '';
                // Message will be added via WebSocket
            } else {
                console.error('Failed to send message');
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    });
    
    // Load messages function
    async function loadMessages() {
        try {
            const response = await fetch('/api/messages?client_matter_id=1', {
                headers: {
                    'Authorization': `Bearer ${window.webSocketManager.authToken}`
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                displayMessages(data.data.messages);
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }
    
    // Display messages
    function displayMessages(messages) {
        messagesContainer.innerHTML = '';
        messages.forEach(message => {
            const messageElement = window.webSocketManager.createMessageElement(message);
            messagesContainer.appendChild(messageElement);
        });
        
        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
</script>
@endsection
```

## 10. Queue Configuration

### Update `config/queue.php`:
```php
<?php

return [
    'default' => env('QUEUE_CONNECTION', 'sync'),

    'connections' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for' => null,
        ],
    ],
];
```

### Update `config/database.php` (Redis configuration):
```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],
],
```

## 11. Broadcasting Service Provider

### Update `app/Providers/BroadcastServiceProvider.php`:
```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes(['middleware' => ['web']]);
        
        require base_path('routes/channels.php');
    }
}
```

## 12. Routes Configuration

### Update `routes/channels.php`:
```php
<?php

use Illuminate\Support\Facades\Broadcast;

// User-specific channels
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Matter-specific channels
Broadcast::channel('matter.{matterId}', function ($user, $matterId) {
    // Check if user is associated with this matter
    $isAssociated = DB::table('client_matters')
        ->where('id', $matterId)
        ->where(function($query) use ($user) {
            $query->where('sel_migration_agent', $user->id)
                  ->orWhere('sel_person_responsible', $user->id)
                  ->orWhere('sel_person_assisting', $user->id);
        })
        ->exists();

    // Allow superadmins (role=1) to join any matter channel
    $isSuperAdmin = $user->role == 1;

    return $isAssociated || $isSuperAdmin;
});
```

## 13. Testing

### Create `tests/Feature/BroadcastingTest.php`:
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Events\MessageSent;
use App\Events\MessageUpdated;
use App\Events\UnreadCountUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

class BroadcastingTest extends TestCase
{
    use RefreshDatabase;

    public function test_message_sent_event_broadcasts()
    {
        Event::fake();
        
        $message = [
            'id' => 1,
            'message' => 'Test message',
            'sender_id' => 1,
            'client_matter_id' => 1
        ];
        
        event(new MessageSent($message, 2));
        
        Event::assertDispatched(MessageSent::class);
    }

    public function test_message_updated_event_broadcasts()
    {
        Event::fake();
        
        $message = [
            'id' => 1,
            'message' => 'Updated message',
            'sender_id' => 1,
            'is_read' => true
        ];
        
        event(new MessageUpdated($message, 1));
        
        Event::assertDispatched(MessageUpdated::class);
    }

    public function test_unread_count_updated_event_broadcasts()
    {
        Event::fake();
        
        event(new UnreadCountUpdated(1, 5));
        
        Event::assertDispatched(UnreadCountUpdated::class);
    }
}
```

## 14. Testing WebSocket Integration

### ✅ **TESTING CHECKLIST**

After implementing all the above changes, test the following scenarios:

#### Test 1: Backend Broadcasting
```bash
# Test if events are being dispatched
php artisan queue:work --tries=3
# Send a message via API and check if events are queued
```

#### Test 2: Channel Authorization
```bash
# Test broadcasting auth endpoint
curl -X POST http://localhost:8000/api/broadcasting/auth \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"socket_id":"123.456","channel_name":"private-user.1"}'
```

#### Test 3: Real-time Message Flow
1. **Mobile App → Website**: Send message from mobile app, verify it appears on website
2. **Website → Mobile App**: Send message from website, verify it appears on mobile app
3. **Read Status**: Mark message as read, verify read receipt is received
4. **Unread Count**: Verify unread count updates in real-time

#### Test 4: Error Handling
1. **Invalid Token**: Test with invalid authentication token
2. **Wrong Channel**: Test subscribing to unauthorized channel
3. **Connection Drop**: Test reconnection logic
4. **Queue Failure**: Test when Redis/Queue is down

## 15. Implementation Checklist

### Phase 1: Configuration
- [ ] Update `.env` with Pusher credentials
- [ ] Configure `config/broadcasting.php`
- [ ] Set up Redis/Queue driver
- [ ] Update `config/queue.php`

### Phase 2: Events and Channels
- [ ] Create event classes (`MessageSent`, `MessageUpdated`, etc.)
- [ ] Create channel classes (`UserChannel`, `MatterChannel`)
- [ ] Update `routes/channels.php`
- [ ] Update `BroadcastServiceProvider`

### Phase 3: Controller Updates
- [ ] Update `ClientPortalMessageController`
- [ ] Add broadcasting logic to `sendMessage`
- [ ] Add broadcasting logic to `markAsRead`
- [ ] Update error handling

### Phase 4: Authentication
- [ ] Update broadcasting auth route
- [ ] Implement token validation
- [ ] Add channel authorization logic
- [ ] Test authentication flow

### Phase 5: Frontend Integration
- [ ] Create WebSocket JavaScript file
- [ ] Update main layout
- [ ] Create message management page
- [ ] Add real-time UI updates

### Phase 6: Testing
- [ ] Test WebSocket connections
- [ ] Test message broadcasting
- [ ] Test real-time updates
- [ ] Test error handling

## 15. Important Notes

### Broadcasting Requirements
- All events must implement `ShouldBroadcast` interface
- Events must be queued for reliable delivery
- Channel authorization must be properly implemented
- Authentication tokens must be validated

### Channel Naming Convention
- User channels: `private-user.{userId}`
- Matter channels: `private-matter.{matterId}`
- Channel names must match frontend and mobile app

### Error Handling
- Implement proper error handling for broadcasting failures
- Add logging for debugging WebSocket issues
- Handle connection drops gracefully
- Implement reconnection logic

### Performance Considerations
- Use Redis for queue management
- Implement proper channel filtering
- Avoid broadcasting to unnecessary users
- Monitor WebSocket connection limits

## 16. Troubleshooting

### Common Issues
1. **Broadcasting Not Working**: Check Pusher credentials and queue configuration
2. **Authentication Failed**: Verify token validation and channel authorization
3. **Events Not Received**: Check channel subscriptions and event names
4. **Connection Drops**: Implement reconnection logic and error handling

### Debug Steps
1. Check Pusher dashboard for connection status
2. Verify event broadcasting in Laravel logs
3. Test channel authorization manually
4. Monitor queue processing
5. Check browser console for JavaScript errors

This guide provides everything needed to implement real-time messaging on the Laravel website backend. The implementation will enable bidirectional real-time communication between the website and mobile app through WebSocket broadcasting.

## 🚨 **CRITICAL SUCCESS FACTORS**

### 1. **Controller Fix Required**
- **MUST** update `ClientPortalMessageController.php` broadcasting logic
- Current: `broadcast(new MessageSent($messageForBroadcast, null))`
- Required: Targeted broadcasting to specific users

### 2. **Configuration Consistency**
- Pusher credentials MUST match between website and mobile app
- Channel names MUST match exactly
- Event names MUST match exactly

### 3. **Testing Sequence**
1. Fix backend controller first
2. Test broadcasting auth endpoint
3. Test mobile app connection
4. Test bidirectional message flow

### 4. **Dependencies**
- Redis must be running for queue processing
- Pusher account must be active
- Laravel Sanctum tokens must be valid

**Without these fixes, the WebSocket integration will NOT work properly.**
