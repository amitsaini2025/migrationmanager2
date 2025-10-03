           <!-- AI Tab -->
           <div class="tab-pane" id="artificialintelligences-tab">
                <div class="AIS-container">
                    <div class="row">
                        <!-- Left Side: Chat History -->
                        <div class="col-md-4">
                            <div class="AIS-header">
                                <h3><i class="fas fa-history"></i> Chat History</h3>
                                <button class="btn new-chat-btn">New Chat</button>
                            </div>
                            <div class="chat-history-list">
                                <div class="loading-history text-center">
                                    <p>Loading chat history...</p>
                                </div>
                                <div class="chat-history-content" style="display: none;">
                                    <!-- Chat history will be populated dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Chat Area -->
                        <div class="col-md-8">
                            <div class="chat-area">
                                <div class="loading-data text-center">
                                    <p id="client-data-loading">Client data are loading...</p>
                                    <p id="client-notes-loading">Client notes are loading...</p>
                                    <p id="client-details-loading">Client personal details are loading...</p>
                                </div>
                                <div class="ai-ready-message text-center" style="display: none;">
                                    <p>AI is ready to answer your query.</p>
                                </div>
                                <div class="chat-content" style="display: none;">
                                    <div class="chat-messages" style="height: 400px; overflow-y: auto; border: 1px solid #e9ecef; padding: 15px; border-radius: 8px;">
                                        <!-- Messages will be populated dynamically -->
                                    </div>
                                    <div class="chat-input mt-3">
                                        <textarea class="form-control" id="chat-input" rows="3" placeholder="Type your question here..."></textarea>
                                        <button class="btn btn-primary mt-2 send-chat-btn" style="background-color: #4a90e2 !important;">Send</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .chat-history-list {
                    max-height: 500px;
                    overflow-y: auto;
                    border: 1px solid #e9ecef;
                    border-radius: 8px;
                    padding: 10px;
                }

                .chat-history-item {
                    padding: 10px;
                    border-bottom: 1px solid #e9ecef;
                    cursor: pointer;
                    transition: background-color 0.2s ease;
                }

                .chat-history-item:hover {
                    background-color: #f1f5f9;
                }

                .chat-history-item.active {
                    background-color: #e9ecef;
                    font-weight: 600;
                }

                .chat-history-item h5 {
                    margin: 0;
                    font-size: 1rem;
                    color: #2c3e50;
                }

                .chat-history-item p {
                    margin: 0;
                    font-size: 0.85rem;
                    color: #6c757d;
                }

                .chat-messages .message {
                    margin-bottom: 15px;
                }

                .chat-messages .message.user {
                    text-align: right;
                }

                .chat-messages .message.ai {
                    text-align: left;
                }

                .chat-messages .message .message-content {
                    display: inline-block;
                    padding: 10px 15px;
                    border-radius: 8px;
                    max-width: 70%;
                }

                .chat-messages .message.user .message-content {
                    background-color: #4a90e2;
                    color: #fff;
                }

                .chat-messages .message.ai .message-content {
                    background-color: #f1f5f9;
                    color: #2c3e50;
                }

                .chat-input textarea {
                    resize: none;
                }
            </style>