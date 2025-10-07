# Mobile App WebSocket Integration Guide

## Overview
This guide provides complete instructions for implementing real-time messaging in the Flutter mobile app using WebSocket connections with the Laravel backend. The mobile app needs to establish WebSocket connections, subscribe to private channels, and handle real-time message events.

## Prerequisites
- Flutter SDK 3.x
- Dart SDK 3.x
- Pusher Channels Flutter SDK
- Valid authentication token from Laravel backend

## 1. Dependencies Setup

### Add to `pubspec.yaml`:
```yaml
dependencies:
  flutter:
    sdk: flutter
  pusher_channels_flutter: ^2.2.1
  socket_io_client: ^2.0.3+1
  http: ^1.1.0
  provider: ^6.1.1
  shared_preferences: ^2.2.2
```

### Run:
```bash
flutter pub get
```

## 2. Configuration Files

### ⚠️ **CRITICAL REQUIREMENT**

**IMPORTANT**: Before implementing the mobile app WebSocket integration, the Laravel backend controller MUST be updated. The current `ClientPortalMessageController.php` has incorrect broadcasting logic that will prevent real-time messaging from working.

### Required Backend Fix:
The current controller broadcasts to `null` instead of specific users. This must be fixed in the Laravel backend first.

### Create `lib/config/socket_config.dart`:
```dart
class SocketConfig {
  // Pusher Configuration - MUST match Laravel backend exactly
  static const String pusherAppKey = 'your-pusher-app-key';  // ← Must match PUSHER_APP_KEY in .env
  static const String pusherCluster = 'ap2';  // ← Must match PUSHER_APP_CLUSTER in .env
  static const bool pusherEncrypted = true;
  
  // API Configuration
  static const String baseUrl = 'http://127.0.0.1:8000';
  static const String broadcastingAuthUrl = '$baseUrl/api/broadcasting/auth';
  
  // Channel Names
  static String getUserChannel(int userId) => 'private-user.$userId';
  static String getMatterChannel(int matterId) => 'private-matter.$matterId';
  
  // Event Names
  static const String messageSentEvent = 'message.sent';
  static const String messageUpdatedEvent = 'message.updated';
  static const String messageReceivedEvent = 'message.received';
  static const String unreadCountUpdatedEvent = 'unread.count.updated';
  
  // Connection Settings
  static const Duration connectionTimeout = Duration(seconds: 30);
  static const Duration reconnectionDelay = Duration(seconds: 5);
  static const int maxReconnectionAttempts = 5;
}
```

### Update `lib/config/api_config.dart`:
```dart
class ApiConfig {
  static const String baseUrl = 'http://127.0.0.1:8000/api';
  static const Duration timeout = Duration(seconds: 30);
  
  // Add broadcasting authentication endpoint
  static const String broadcastingAuth = '$baseUrl/broadcasting/auth';
  
  // Existing endpoints...
  static const String login = '$baseUrl/login';
  static const String messages = '$baseUrl/messages';
  static const String sendMessage = '$baseUrl/messages/send';
  // ... other endpoints
}
```

## 3. Service Files

### Create `lib/services/pusher_service.dart`:
```dart
import 'package:pusher_channels_flutter/pusher_channels_flutter.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../config/socket_config.dart';
import '../models/socket_message.dart';
import '../controllers/message_controller.dart';

class PusherService {
  static final PusherService _instance = PusherService._internal();
  factory PusherService() => _instance;
  PusherService._internal();

  PusherChannelsFlutter? _pusher;
  bool _isConnected = false;
  String? _currentUserId;
  MessageController? _messageController;

  // Initialize Pusher connection
  Future<void> initialize() async {
    try {
      _pusher = PusherChannelsFlutter.getInstance();
      
      await _pusher!.init(
        apiKey: SocketConfig.pusherAppKey,
        cluster: SocketConfig.pusherCluster,
        onConnectionStateChange: _onConnectionStateChange,
        onError: _onError,
        onSubscriptionSucceeded: _onSubscriptionSucceeded,
        onEvent: _onEvent,
        onSubscriptionError: _onSubscriptionError,
        onDecryptionFailure: _onDecryptionFailure,
        onMemberAdded: _onMemberAdded,
        onMemberRemoved: _onMemberRemoved,
        onAuthorizer: _onAuthorizer,
      );

      await _pusher!.connect();
    } catch (e) {
      print('Pusher initialization error: $e');
    }
  }

  // Connect to user-specific channel
  Future<void> connectToUserChannel(int userId) async {
    if (_pusher == null) {
      await initialize();
    }

    _currentUserId = userId.toString();
    String channelName = SocketConfig.getUserChannel(userId);
    
    try {
      await _pusher!.subscribe(channelName: channelName);
      _isConnected = true;
      print('Connected to channel: $channelName');
    } catch (e) {
      print('Channel subscription error: $e');
    }
  }

  // Disconnect from channels
  Future<void> disconnect() async {
    if (_pusher != null) {
      await _pusher!.disconnect();
      _isConnected = false;
      _currentUserId = null;
    }
  }

  // Set message controller for handling events
  void setMessageController(MessageController controller) {
    _messageController = controller;
  }

  // Event handlers
  void _onConnectionStateChange(String currentState, String previousState) {
    print('Connection state changed: $previousState -> $currentState');
  }

  void _onError(String message, int? code, dynamic e) {
    print('Pusher error: $message, Code: $code');
  }

  void _onSubscriptionSucceeded(String channelName, dynamic data) {
    print('Subscribed to channel: $channelName');
  }

  void _onEvent(PusherEvent event) {
    print('Received event: ${event.eventName} on channel: ${event.channelName}');
    
    switch (event.eventName) {
      case SocketConfig.messageSentEvent:
        _handleMessageSent(event);
        break;
      case SocketConfig.messageUpdatedEvent:
        _handleMessageUpdated(event);
        break;
      case SocketConfig.messageReceivedEvent:
        _handleMessageReceived(event);
        break;
      case SocketConfig.unreadCountUpdatedEvent:
        _handleUnreadCountUpdated(event);
        break;
    }
  }

  void _onSubscriptionError(String message, dynamic e) {
    print('Subscription error: $message');
  }

  void _onDecryptionFailure(String event, String reason) {
    print('Decryption failure: $event, Reason: $reason');
  }

  void _onMemberAdded(String channelName, PusherMember member) {
    print('Member added to $channelName: ${member.userId}');
  }

  void _onMemberRemoved(String channelName, PusherMember member) {
    print('Member removed from $channelName: ${member.userId}');
  }

  // Authentication for private channels
  Future<Map<String, dynamic>?> _onAuthorizer(
    String channelName,
    String socketId,
    dynamic options,
  ) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      
      if (token == null) {
        throw Exception('No authentication token found');
      }

      final response = await http.post(
        Uri.parse(SocketConfig.broadcastingAuthUrl),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode({
          'socket_id': socketId,
          'channel_name': channelName,
        }),
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        throw Exception('Authentication failed: ${response.statusCode}');
      }
    } catch (e) {
      print('Authorization error: $e');
      return null;
    }
  }

  // Event handlers
  void _handleMessageSent(PusherEvent event) {
    try {
      final data = jsonDecode(event.data);
      final message = SocketMessage.fromJson(data['message']);
      _messageController?.handleNewMessage(message);
    } catch (e) {
      print('Error handling message sent: $e');
    }
  }

  void _handleMessageUpdated(PusherEvent event) {
    try {
      final data = jsonDecode(event.data);
      final message = SocketMessage.fromJson(data['message']);
      _messageController?.handleMessageUpdated(message);
    } catch (e) {
      print('Error handling message updated: $e');
    }
  }

  void _handleMessageReceived(PusherEvent event) {
    try {
      final data = jsonDecode(event.data);
      final messageId = data['message_id'];
      _messageController?.handleMessageReceived(messageId);
    } catch (e) {
      print('Error handling message received: $e');
    }
  }

  void _handleUnreadCountUpdated(PusherEvent event) {
    try {
      final data = jsonDecode(event.data);
      final userId = data['user_id'];
      final unreadCount = data['unread_count'];
      _messageController?.handleUnreadCountUpdated(userId, unreadCount);
    } catch (e) {
      print('Error handling unread count updated: $e');
    }
  }

  // Getters
  bool get isConnected => _isConnected;
  String? get currentUserId => _currentUserId;
}
```

### Create `lib/services/socket_service.dart`:
```dart
import 'package:shared_preferences/shared_preferences.dart';
import 'pusher_service.dart';
import '../config/socket_config.dart';

class SocketService {
  static final SocketService _instance = SocketService._internal();
  factory SocketService() => _instance;
  SocketService._internal();

  final PusherService _pusherService = PusherService();
  bool _isInitialized = false;

  // Initialize socket connection
  Future<void> initialize() async {
    if (_isInitialized) return;

    try {
      await _pusherService.initialize();
      _isInitialized = true;
      print('Socket service initialized');
    } catch (e) {
      print('Socket service initialization error: $e');
    }
  }

  // Connect to user channel after login
  Future<void> connectAfterLogin(int userId) async {
    try {
      await initialize();
      await _pusherService.connectToUserChannel(userId);
      print('Connected to user channel for user: $userId');
    } catch (e) {
      print('Error connecting to user channel: $e');
    }
  }

  // Disconnect on logout
  Future<void> disconnectOnLogout() async {
    try {
      await _pusherService.disconnect();
      _isInitialized = false;
      print('Disconnected from socket');
    } catch (e) {
      print('Error disconnecting from socket: $e');
    }
  }

  // Check connection status
  bool get isConnected => _pusherService.isConnected;

  // Get pusher service instance
  PusherService get pusherService => _pusherService;
}
```

## 4. Model Files

### Create `lib/models/socket_message.dart`:
```dart
class SocketMessage {
  final int id;
  final String? message;
  final String? sender;
  final String? recipient;
  final int? senderId;
  final int? recipientId;
  final DateTime? sentAt;
  final DateTime? readAt;
  final bool isRead;
  final int? clientMatterId;
  final DateTime createdAt;
  final DateTime updatedAt;

  SocketMessage({
    required this.id,
    this.message,
    this.sender,
    this.recipient,
    this.senderId,
    this.recipientId,
    this.sentAt,
    this.readAt,
    required this.isRead,
    this.clientMatterId,
    required this.createdAt,
    required this.updatedAt,
  });

  factory SocketMessage.fromJson(Map<String, dynamic> json) {
    return SocketMessage(
      id: json['id'],
      message: json['message'],
      sender: json['sender'],
      recipient: json['recipient'],
      senderId: json['sender_id'],
      recipientId: json['recipient_id'],
      sentAt: json['sent_at'] != null ? DateTime.parse(json['sent_at']) : null,
      readAt: json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
      isRead: json['is_read'] ?? false,
      clientMatterId: json['client_matter_id'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'message': message,
      'sender': sender,
      'recipient': recipient,
      'sender_id': senderId,
      'recipient_id': recipientId,
      'sent_at': sentAt?.toIso8601String(),
      'read_at': readAt?.toIso8601String(),
      'is_read': isRead,
      'client_matter_id': clientMatterId,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}
```

### Create `lib/models/broadcast_event.dart`:
```dart
class BroadcastEvent {
  final String type;
  final Map<String, dynamic> data;
  final DateTime timestamp;
  final String action;

  BroadcastEvent({
    required this.type,
    required this.data,
    required this.timestamp,
    required this.action,
  });

  factory BroadcastEvent.fromJson(Map<String, dynamic> json) {
    return BroadcastEvent(
      type: json['type'],
      data: json['data'] ?? {},
      timestamp: DateTime.parse(json['timestamp']),
      action: json['action'],
    );
  }
}

enum EventType {
  newMessage,
  messageUpdated,
  messageReceived,
  unreadCountUpdated,
}

enum ActionType {
  messageCreated,
  messageUpdated,
  messageReceived,
  unreadCountUpdated,
}
```

## 5. Controller Updates

### Update `lib/controllers/message_controller.dart`:
```dart
import 'package:flutter/foundation.dart';
import '../models/socket_message.dart';
import '../services/socket_service.dart';
import '../services/api_service.dart';

class MessageController extends ChangeNotifier {
  final SocketService _socketService = SocketService();
  final ApiService _apiService = ApiService();
  
  List<SocketMessage> _messages = [];
  int _unreadCount = 0;
  bool _isLoading = false;

  // Getters
  List<SocketMessage> get messages => _messages;
  int get unreadCount => _unreadCount;
  bool get isLoading => _isLoading;

  // Initialize socket connection
  Future<void> initializeSocket(int userId) async {
    try {
      _socketService.pusherService.setMessageController(this);
      await _socketService.connectAfterLogin(userId);
    } catch (e) {
      print('Error initializing socket: $e');
    }
  }

  // Handle new message from socket
  void handleNewMessage(SocketMessage message) {
    _messages.add(message);
    _unreadCount++;
    notifyListeners();
    
    // Show notification if app is in background
    _showNotification(message);
  }

  // Handle message updated from socket
  void handleMessageUpdated(SocketMessage message) {
    final index = _messages.indexWhere((m) => m.id == message.id);
    if (index != -1) {
      _messages[index] = message;
      notifyListeners();
    }
  }

  // Handle message received (read status)
  void handleMessageReceived(int messageId) {
    final index = _messages.indexWhere((m) => m.id == messageId);
    if (index != -1) {
      _messages[index] = _messages[index].copyWith(isRead: true, readAt: DateTime.now());
      notifyListeners();
    }
  }

  // Handle unread count updated
  void handleUnreadCountUpdated(int userId, int unreadCount) {
    _unreadCount = unreadCount;
    notifyListeners();
  }

  // Send message via API
  Future<bool> sendMessage(String message, int clientMatterId) async {
    try {
      _isLoading = true;
      notifyListeners();

      final response = await _apiService.sendMessage(message, clientMatterId);
      
      if (response['success'] == true) {
        // Message will be added via socket event
        return true;
      } else {
        return false;
      }
    } catch (e) {
      print('Error sending message: $e');
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  // Mark message as read
  Future<bool> markAsRead(int messageId) async {
    try {
      final response = await _apiService.markMessageAsRead(messageId);
      return response['success'] == true;
    } catch (e) {
      print('Error marking message as read: $e');
      return false;
    }
  }

  // Get messages from API
  Future<void> loadMessages(int clientMatterId, {int page = 1, int limit = 20}) async {
    try {
      _isLoading = true;
      notifyListeners();

      final response = await _apiService.getMessages(clientMatterId, page: page, limit: limit);
      
      if (response['success'] == true) {
        final messagesData = response['data']['messages'] as List;
        _messages = messagesData.map((json) => SocketMessage.fromJson(json)).toList();
        notifyListeners();
      }
    } catch (e) {
      print('Error loading messages: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  // Show notification
  void _showNotification(SocketMessage message) {
    // Implement local notification
    // This will show notification when app is in background
  }

  // Dispose
  @override
  void dispose() {
    _socketService.disconnectOnLogout();
    super.dispose();
  }
}
```

## 6. API Service Updates

### Update `lib/services/api_service.dart`:
```dart
// Add these methods to your existing ApiService class

// Send message
Future<Map<String, dynamic>> sendMessage(String message, int clientMatterId) async {
  final response = await _dio.post(
    '/messages/send',
    data: {
      'message': message,
      'client_matter_id': clientMatterId,
    },
  );
  return response.data;
}

// Mark message as read
Future<Map<String, dynamic>> markMessageAsRead(int messageId) async {
  final response = await _dio.put('/messages/$messageId/read');
  return response.data;
}

// Get messages
Future<Map<String, dynamic>> getMessages(int clientMatterId, {int page = 1, int limit = 20}) async {
  final response = await _dio.get(
    '/messages',
    queryParameters: {
      'client_matter_id': clientMatterId,
      'page': page,
      'limit': limit,
    },
  );
  return response.data;
}

// Get unread count
Future<Map<String, dynamic>> getUnreadCount() async {
  final response = await _dio.get('/messages/unread-count');
  return response.data;
}
```

## 7. Screen Updates

### Update `lib/screens/messages/messages_screen.dart`:
```dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../controllers/message_controller.dart';
import '../../models/socket_message.dart';

class MessagesScreen extends StatefulWidget {
  final int clientMatterId;
  
  const MessagesScreen({Key? key, required this.clientMatterId}) : super(key: key);

  @override
  _MessagesScreenState createState() => _MessagesScreenState();
}

class _MessagesScreenState extends State<MessagesScreen> {
  final TextEditingController _messageController = TextEditingController();
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final messageController = Provider.of<MessageController>(context, listen: false);
      messageController.loadMessages(widget.clientMatterId);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Messages'),
        actions: [
          Consumer<MessageController>(
            builder: (context, controller, child) {
              return Badge(
                label: Text('${controller.unreadCount}'),
                child: Icon(Icons.message),
              );
            },
          ),
        ],
      ),
      body: Column(
        children: [
          Expanded(
            child: Consumer<MessageController>(
              builder: (context, controller, child) {
                if (controller.isLoading && controller.messages.isEmpty) {
                  return Center(child: CircularProgressIndicator());
                }

                return ListView.builder(
                  controller: _scrollController,
                  reverse: true, // Show newest messages at bottom
                  itemCount: controller.messages.length,
                  itemBuilder: (context, index) {
                    final message = controller.messages[index];
                    return MessageBubble(message: message);
                  },
                );
              },
            ),
          ),
          MessageInput(
            controller: _messageController,
            onSend: _sendMessage,
          ),
        ],
      ),
    );
  }

  Future<void> _sendMessage() async {
    if (_messageController.text.trim().isEmpty) return;

    final messageController = Provider.of<MessageController>(context, listen: false);
    final success = await messageController.sendMessage(
      _messageController.text.trim(),
      widget.clientMatterId,
    );

    if (success) {
      _messageController.clear();
      _scrollToBottom();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to send message')),
      );
    }
  }

  void _scrollToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scrollController.hasClients) {
        _scrollController.animateTo(
          0,
          duration: Duration(milliseconds: 300),
          curve: Curves.easeOut,
        );
      }
    });
  }
}

class MessageBubble extends StatelessWidget {
  final SocketMessage message;

  const MessageBubble({Key? key, required this.message}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      child: Row(
        mainAxisAlignment: message.senderId == getCurrentUserId() 
            ? MainAxisAlignment.end 
            : MainAxisAlignment.start,
        children: [
          Container(
            constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.7),
            padding: EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: message.senderId == getCurrentUserId() 
                  ? Colors.blue 
                  : Colors.grey[300],
              borderRadius: BorderRadius.circular(12),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  message.message ?? '',
                  style: TextStyle(
                    color: message.senderId == getCurrentUserId() 
                        ? Colors.white 
                        : Colors.black,
                  ),
                ),
                SizedBox(height: 4),
                Text(
                  _formatTime(message.sentAt),
                  style: TextStyle(
                    fontSize: 12,
                    color: message.senderId == getCurrentUserId() 
                        ? Colors.white70 
                        : Colors.grey[600],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  String _formatTime(DateTime? dateTime) {
    if (dateTime == null) return '';
    return '${dateTime.hour.toString().padLeft(2, '0')}:${dateTime.minute.toString().padLeft(2, '0')}';
  }

  int getCurrentUserId() {
    // Get current user ID from your auth service
    // This should return the authenticated user's ID
    return 0; // Replace with actual user ID
  }
}

class MessageInput extends StatelessWidget {
  final TextEditingController controller;
  final VoidCallback onSend;

  const MessageInput({
    Key? key,
    required this.controller,
    required this.onSend,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.2),
            spreadRadius: 1,
            blurRadius: 3,
            offset: Offset(0, -1),
          ),
        ],
      ),
      child: Row(
        children: [
          Expanded(
            child: TextField(
              controller: controller,
              decoration: InputDecoration(
                hintText: 'Type a message...',
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(24),
                ),
                contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              ),
              maxLines: null,
              textCapitalization: TextCapitalization.sentences,
            ),
          ),
          SizedBox(width: 8),
          FloatingActionButton(
            onPressed: onSend,
            mini: true,
            child: Icon(Icons.send),
          ),
        ],
      ),
    );
  }
}
```

## 8. Main App Integration

### Update `lib/main.dart`:
```dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'controllers/message_controller.dart';
import 'services/socket_service.dart';

void main() {
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => MessageController()),
        // ... other providers
      ],
      child: MaterialApp(
        title: 'Client Portal',
        // ... rest of your app configuration
      ),
    );
  }
}
```

### Update `lib/screens/auth/login_screen.dart`:
```dart
// Add this after successful login
Future<void> _handleLoginSuccess(Map<String, dynamic> response) async {
  // ... existing login logic
  
  // Initialize socket connection
  final userId = response['data']['user']['id'];
  final messageController = Provider.of<MessageController>(context, listen: false);
  await messageController.initializeSocket(userId);
  
  // Navigate to dashboard
  Navigator.pushReplacementNamed(context, '/dashboard');
}
```

## 9. Environment Configuration

### Create `lib/config/env_config.dart`:
```dart
class EnvConfig {
  // Development
  static const String devPusherKey = 'your-dev-pusher-key';
  static const String devPusherCluster = 'ap2';
  static const String devBaseUrl = 'http://127.0.0.1:8000';
  
  // Production
  static const String prodPusherKey = 'your-prod-pusher-key';
  static const String prodPusherCluster = 'ap2';
  static const String prodBaseUrl = 'https://your-domain.com';
  
  // Get current environment
  static bool get isDevelopment => const bool.fromEnvironment('dart.vm.product') == false;
  
  // Get current configuration
  static String get pusherKey => isDevelopment ? devPusherKey : prodPusherKey;
  static String get pusherCluster => isDevelopment ? devPusherCluster : prodPusherCluster;
  static String get baseUrl => isDevelopment ? devBaseUrl : prodBaseUrl;
}
```

## 10. Error Handling

### Create `lib/utils/socket_error_handler.dart`:
```dart
class SocketErrorHandler {
  static void handleConnectionError(String error) {
    print('Socket connection error: $error');
    // Implement error handling logic
    // Show user-friendly error messages
    // Attempt reconnection
  }

  static void handleAuthenticationError(String error) {
    print('Socket authentication error: $error');
    // Handle authentication failures
    // Redirect to login if needed
  }

  static void handleSubscriptionError(String error) {
    print('Socket subscription error: $error');
    // Handle channel subscription failures
    // Retry subscription if needed
  }
}
```

## 11. Testing WebSocket Integration

### ✅ **TESTING CHECKLIST**

Before testing the mobile app, ensure the Laravel backend is properly configured and the controller broadcasting logic is fixed.

#### Test 1: Connection Establishment
```dart
// Test Pusher connection
final socketService = SocketService();
await socketService.initialize();
print('Connected: ${socketService.isConnected}');
```

#### Test 2: Channel Subscription
```dart
// Test user channel subscription
final userId = 1; // Replace with actual user ID
await socketService.connectAfterLogin(userId);
// Check if subscribed successfully
```

#### Test 3: Message Flow
1. **Send Message**: Test sending message via API
2. **Receive Message**: Verify message appears in real-time
3. **Read Status**: Test marking message as read
4. **Unread Count**: Verify unread count updates

#### Test 4: Error Handling
1. **Invalid Token**: Test with expired/invalid token
2. **Network Issues**: Test with poor network connection
3. **Channel Auth**: Test with unauthorized channel access
4. **Reconnection**: Test automatic reconnection logic

### Create `lib/utils/socket_test_utils.dart`:
```dart
class SocketTestUtils {
  // Test socket connection
  static Future<bool> testConnection() async {
    try {
      final socketService = SocketService();
      await socketService.initialize();
      return socketService.isConnected;
    } catch (e) {
      print('Socket connection test failed: $e');
      return false;
    }
  }

  // Test channel subscription
  static Future<bool> testChannelSubscription(int userId) async {
    try {
      final socketService = SocketService();
      await socketService.connectAfterLogin(userId);
      return socketService.isConnected;
    } catch (e) {
      print('Channel subscription test failed: $e');
      return false;
    }
  }
}
```

## 12. Implementation Checklist

### Phase 1: Setup
- [ ] Add dependencies to `pubspec.yaml`
- [ ] Create configuration files
- [ ] Set up Pusher credentials
- [ ] Create model classes

### Phase 2: Services
- [ ] Implement `PusherService`
- [ ] Implement `SocketService`
- [ ] Update `ApiService` with new methods
- [ ] Add error handling

### Phase 3: Controllers
- [ ] Update `MessageController`
- [ ] Add socket event handlers
- [ ] Implement real-time updates
- [ ] Add notification handling

### Phase 4: UI Integration
- [ ] Update message screens
- [ ] Add real-time message display
- [ ] Implement message input
- [ ] Add unread count display

### Phase 5: Testing
- [ ] Test socket connection
- [ ] Test channel subscription
- [ ] Test message sending/receiving
- [ ] Test real-time updates

## 13. Important Notes

### Authentication
- The mobile app must have a valid authentication token
- Private channels require proper authentication
- Token must be sent with broadcasting auth requests

### Channel Naming
- User channels: `private-user.{userId}`
- Matter channels: `private-matter.{matterId}` (if needed)
- Channel names must match backend implementation

### Event Handling
- Listen for `message.sent` events
- Listen for `message.updated` events
- Listen for `unread.count.updated` events
- Handle connection errors gracefully

### Error Handling
- Implement reconnection logic
- Handle authentication failures
- Show user-friendly error messages
- Log errors for debugging

## 14. Troubleshooting

### Common Issues
1. **Connection Failed**: Check Pusher credentials and network
2. **Authentication Failed**: Verify token validity and auth endpoint
3. **Channel Subscription Failed**: Check channel naming and permissions
4. **Events Not Received**: Verify event listeners and channel subscriptions

### Debug Steps
1. Check Pusher dashboard for connection status
2. Verify authentication token validity
3. Test channel subscriptions
4. Monitor event broadcasting
5. Check error logs for detailed information

This guide provides everything the mobile app developer needs to implement real-time messaging with WebSocket integration. The implementation will enable bidirectional real-time communication between the website and mobile app.

## 🚨 **CRITICAL SUCCESS FACTORS**

### 1. **Backend Prerequisites**
- **MUST** fix Laravel controller broadcasting logic first
- **MUST** have valid Pusher credentials configured
- **MUST** have Redis/Queue running for broadcasting

### 2. **Configuration Matching**
- `pusherAppKey` MUST match `PUSHER_APP_KEY` in Laravel .env
- `pusherCluster` MUST match `PUSHER_APP_CLUSTER` in Laravel .env
- Channel names MUST match exactly with backend

### 3. **Implementation Order**
1. **Backend First**: Fix Laravel controller and test broadcasting
2. **Mobile Second**: Implement WebSocket integration
3. **Testing**: Test bidirectional communication
4. **Production**: Deploy with matching configurations

### 4. **Dependencies**
- Valid authentication tokens from Laravel
- Active Pusher account
- Proper channel authorization setup

**The mobile app integration will NOT work without the backend fixes being implemented first.**
