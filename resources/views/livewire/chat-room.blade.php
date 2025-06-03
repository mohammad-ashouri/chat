<div>
    <div class="h-[calc(100vh-65px)] bg-gray-100 dark:bg-gray-900">
        <div class="flex h-full">
            <!-- Sidebar -->
            <div class="w-80 bg-white dark:bg-gray-800 shadow-lg overflow-y-auto">
                <div class="p-4 space-y-2">
                    <div class="flex space-x-2 rtl:space-x-reverse">
                        <livewire:new-message-modal/>
                        <livewire:create-group-modal/>
                    </div>
                </div>

                <div class="mt-4">
                    <h3 class="px-4 text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">چت‌های اخیر</h3>
                    <div class="mt-2">
                        <!-- Chat List -->
                        <div class="flex-1 overflow-y-auto">
                            @foreach($chats as $chat)
                                <div wire:click="selectChat({{ $chat->id }})"
                                     wire:key="chat-{{ $chat->id }}"
                                     class="p-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer {{ $selectedChat && $selectedChat->id === $chat->id ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                                    <div class="flex items-center space-x-3">
                                        @if($chat->is_group)
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $chat->name }}</p>
                                                    @if($chat->lastMessage)
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $chat->lastMessage->created_at->format('H:i') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                    @if($chat->lastMessage)
                                                        @if($chat->lastMessage->user_id === auth()->id())
                                                            شما:
                                                        @else
                                                            {{ $chat->lastMessage->user->name }}:
                                                        @endif
                                                        {{ $chat->lastMessage->content }}
                                                    @else
                                                        بدون پیام
                                                    @endif
                                                </p>
                                            </div>
                                            @if($chat->unreadMessagesCount() > 0)
                                                <div class="flex-shrink-0">
                                                    <span
                                                        class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-blue-500 text-xs font-medium text-white">
                                                        {{ $chat->unreadMessagesCount() }}
                                                    </span>
                                                </div>
                                            @endif
                                        @else
                                            @php
                                                $otherUser = $chat->users->where('id', '!=', auth()->id())->first();
                                            @endphp
                                            <div class="flex-shrink-0 relative">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                    <span
                                                        class="text-gray-600 dark:text-gray-300">{{ substr($otherUser->name, 0, 1) }}</span>
                                                </div>
                                                @if($otherUser->isOnline())
                                                    <div
                                                        class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $otherUser->name }}</p>
                                                    @if($chat->lastMessage)
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $chat->lastMessage->created_at->format('H:i') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                    @if($chat->lastMessage)
                                                        @if($chat->lastMessage->user_id === auth()->id())
                                                            شما:
                                                        @endif
                                                        {{ $chat->lastMessage->content }}
                                                    @else
                                                        بدون پیام
                                                    @endif
                                                </p>
                                            </div>
                                            @if($chat->unreadMessagesCount() > 0)
                                                <div class="flex-shrink-0">
                                                    <span
                                                        class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-blue-500 text-xs font-medium text-white">
                                                        {{ $chat->unreadMessagesCount() }}
                                                    </span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="flex-1 flex flex-col bg-gray-50 dark:bg-gray-900">
                @if($selectedChat)
                    <!-- Chat Header -->
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-center">
                            @if($selectedChat->is_group)
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white ml-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $selectedChat->name }}</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $selectedChat->users->count() }}
                                        عضو</p>
                                </div>
                                <div class="mr-auto">
                                    <livewire:group-management :group="$selectedChat"/>
                                </div>
                            @else
                                <div
                                    class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-300 ml-3">
                                    {{ substr($selectedChat->users->where('id', '!=', auth()->id())->first()->name, 0, 1) }}
                                </div>
                                <div>
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $selectedChat->users->where('id', '!=', auth()->id())->first()->name }}</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $selectedChat->users->where('id', '!=', auth()->id())->first()->isOnline() ? 'آنلاین' : 'آفلاین' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chat-messages">
                        @foreach($messages as $message)
                            @if($message->is_system)
                                <div class="flex justify-center my-2">
                                    <div
                                        class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm px-4 py-2 rounded-full">
                                        {{ $message->content }}
                                    </div>
                                </div>
                            @else
                                <div
                                    class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }} mb-2 message-animation"
                                    data-message-id="{{ $message->id }}">
                                    <div
                                        class="max-w-[55%] w-auto {{ $message->user_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100' }} rounded-lg p-3 relative group {{ in_array($message->id, $selectedMessages) ? ($message->user_id === auth()->id() ? 'ring-2 ring-blue-300 dark:ring-blue-600' : 'ring-2 ring-gray-400 dark:ring-gray-500') : '' }}"
                                        data-message-id="{{ $message->id }}">
                                        @if(in_array($message->id, $selectedMessages))
                                            <div
                                                class="absolute {{ $message->user_id === auth()->id() ? '-left-2' : '-left-2' }} -top-2 bg-blue-500 text-white rounded-full p-1 z-10">
                                                <i class="fas fa-check text-xs"></i>
                                            </div>
                                        @endif
                                        @if($selectedChat->is_group)
                                            <div
                                                class="text-xs mb-1 {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400' }}">
                                                {{ $message->user_id === auth()->id() ? 'شما' : $message->user->name }}
                                            </div>
                                        @endif
                                        @if($message->original_sender_id)
                                            <div
                                                class="mb-2 pb-2 border-b border-gray-300 dark:border-gray-600 text-sm break-words">
                                                <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                                </svg>
                                                پیام فوروارد شده از
                                                @if($message->original_sender_id === auth()->id())
                                                    <span class="font-semibold">شما</span>
                                                @else
                                                    <button
                                                        wire:click.stop="startChat({{ $message->original_sender_id }})"
                                                        class="text-sm hover:underline {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-blue-600 dark:text-blue-400' }}">
                                                        <span
                                                            class="font-semibold">{{ $message->originalSender->name }}</span>
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                        @if($message->replyTo)
                                            <div
                                                class="mb-2 pb-2 border-b border-gray-300 dark:border-gray-600 text-sm break-words">
                                                <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                </svg>
                                                در پاسخ به
                                                <button wire:click.stop="scrollToMessage({{ $message->replyTo->id }})"
                                                        class="text-sm hover:underline {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-blue-600 dark:text-blue-400' }}">
                                                    <span
                                                        class="font-semibold">{{ $message->replyTo->user->name }}</span>
                                                </button>
                                                <div class="text-gray-600 dark:text-gray-400 truncate">
                                                    {{ $message->replyTo->content }}
                                                </div>
                                            </div>
                                        @endif
                                        @if($message->file_path)
                                            <div class="mb-2 space-y-2">
                                                @php
                                                    $filePaths = json_decode($message->file_path) ?? [];
                                                    $fileNames = json_decode($message->file_name) ?? [];
                                                @endphp
                                                @foreach($filePaths as $index => $filePath)
                                                    <a href="{{ Storage::url($filePath) }}" target="_blank"
                                                       class="flex items-center text-sm hover:underline {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-blue-600 dark:text-blue-400' }}">
                                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                        </svg>
                                                        {{ $this->getFileTypeLabel($fileNames[$index] ?? '') }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($message->content)
                                            <div
                                                class="break-words text-sm leading-relaxed">{{ $message->content }}</div>
                                        @endif
                                        <div class="flex items-center justify-between mt-2">
                                            <div
                                                class="text-xs {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400' }}">
                                                {{ $message->created_at->format('H:i') }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button wire:click.stop="replyToMessage({{ $message->id }})"
                                                        class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200 {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                    </svg>
                                                </button>
                                                <button wire:click.stop="openForwardModal({{ $message->id }})"
                                                        class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200 {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Message Input -->
                    <div class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <livewire:message-input :chat="$selectedChat"/>
                    </div>
                @else
                    <div class="flex-1 flex items-center justify-center text-gray-500 dark:text-gray-400">
                        برای شروع گفتگو، یک کاربر را انتخاب کنید یا یک گروه جدید ایجاد کنید
                    </div>
                @endif
            </div>
        </div>
    </div>
    <livewire:forward-message-modal/>

    <script>
        // اسکرول به پایین هنگام بارگذاری چت یا ارسال پیام جدید
        window.scrollToBottom = function () {
            const chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }
    </script>

    @script
    <script>
        let scrollInterval = null;

        function scrollToBottom() {
            const chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        // اسکرول هنگام بارگذاری اولیه
        document.addEventListener('livewire:initialized', () => {
            // Load selected chat from localStorage
            const selectedChatId = localStorage.getItem('selectedChatId');
            if (selectedChatId) {
                $wire.handleChatSelected(selectedChatId);

                // Start polling for messages
                if (scrollInterval) {
                    clearInterval(scrollInterval);
                }

                scrollInterval = setInterval(() => {
                    const chatMessages = document.getElementById('chat-messages');
                    if (chatMessages && chatMessages.children.length > 0) {
                        scrollToBottom();
                        clearInterval(scrollInterval);
                    }
                }, 100);
            }
        });

        // اسکرول هنگام ارسال پیام یا تغییر چت
        document.addEventListener('livewire:updated', () => {
            scrollToBottom();
        });

        // Save selected chat to localStorage
        document.addEventListener('livewire:navigated', () => {
            const selectedChatId = localStorage.getItem('selectedChatId');
            if (selectedChatId) {
                $wire.handleChatSelected(selectedChatId);

                // Start polling for messages
                if (scrollInterval) {
                    clearInterval(scrollInterval);
                }

                scrollInterval = setInterval(() => {
                    const chatMessages = document.getElementById('chat-messages');
                    if (chatMessages && chatMessages.children.length > 0) {
                        scrollToBottom();
                        clearInterval(scrollInterval);
                    }
                }, 100);
            }
        });

        // Listen for saveSelectedChat event
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('saveSelectedChat', (event) => {
                localStorage.setItem('selectedChatId', event.chatId);
            });
        });

        // Listen for message sent event
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('message-sent', () => {
                scrollToBottom();
            });
        });

        // Clean up interval when component is destroyed
        document.addEventListener('livewire:destroyed', () => {
            if (scrollInterval) {
                clearInterval(scrollInterval);
            }
        });
    </script>
    @endscript

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('scroll-to-message', (data) => {
                    const messageElement = document.querySelector(`[data-message-id="${data.messageId}"]`);
                    if (messageElement) {
                        messageElement.scrollIntoView({behavior: 'smooth', block: 'center'});
                        messageElement.classList.add('highlight-message');
                        setTimeout(() => {
                            messageElement.classList.remove('highlight-message');
                        }, 2000);
                    }
                });

                Livewire.on('scroll-to-bottom', () => {
                    const chatMessages = document.getElementById('chat-messages');
                    if (chatMessages) {
                        setTimeout(() => {
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }, 100);
                    }
                });

                Livewire.on('focus-message-input', () => {
                    const input = document.querySelector('input[type="text"]');
                    if (input) {
                        input.focus();
                    }
                });
            });
        </script>
    @endpush

    <style>
        .highlight-message {
            animation: highlight 2s ease-out;
        }

        @keyframes highlight {
            0% {
                background-color: rgba(59, 130, 246, 0.2);
            }
            100% {
                background-color: transparent;
            }
        }

        /* Message animation styles */
        .message-animation {
            animation: messageAppear 0.3s ease-out forwards;
        }

        @keyframes messageAppear {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</div>
