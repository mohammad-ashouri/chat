<div class="h-[calc(100vh-65px)] bg-gray-100">
    <div class="flex h-full">
        <!-- Sidebar -->
        <div class="w-80 bg-white shadow-lg overflow-y-auto">
            <div class="p-4 space-y-2">
                <div class="flex space-x-2 rtl:space-x-reverse">
                    <livewire:new-message-modal/>
                    <livewire:create-group-modal/>
                </div>
            </div>

            <div class="mt-4">
                <h3 class="px-4 text-sm font-semibold text-gray-500 uppercase">چت‌های اخیر</h3>
                <div class="mt-2">
                    <!-- Chat List -->
                    <div class="flex-1 overflow-y-auto">
                        @foreach($chats as $chat)
                            <div wire:click="handleChatSelected({{ $chat->id }})"
                                 class="p-3 hover:bg-gray-100 cursor-pointer {{ $selectedChat && $selectedChat->id === $chat->id ? 'bg-gray-100' : '' }}">
                                <div class="flex items-center space-x-3">
                                    @if($chat->is_group)
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $chat->name }}</p>
                                                @if($chat->lastMessage)
                                                    <span class="text-xs text-gray-500">
                                                        {{ $chat->lastMessage->created_at->format('H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500 truncate">
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
                                                class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-600">{{ substr($otherUser->name, 0, 1) }}</span>
                                            </div>
                                            @if($otherUser->isOnline())
                                                <div
                                                    class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $otherUser->name }}</p>
                                                @if($chat->lastMessage)
                                                    <span class="text-xs text-gray-500">
                                                        {{ $chat->lastMessage->created_at->format('H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500 truncate">
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
        <div class="flex-1 flex flex-col">
            @if($selectedChat)
                <div class="p-4 border-b bg-white">
                    @if($selectedChat->is_group)
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-semibold ml-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-medium">{{ $selectedChat->name }}</h2>
                                <p class="text-sm text-gray-500">{{ $selectedChat->users->count() }} عضو</p>
                            </div>
                            @if($selectedChat->is_group)
                                <div class="mr-auto">
                                    <livewire:group-management :group="$selectedChat"/>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold ml-3">
                                {{ substr($selectedChat->otherUser()->name, 0, 1) }}
                            </div>
                            <div>
                                <h2 class="text-lg font-medium">{{ $selectedChat->otherUser()->name }}</h2>
                                <p class="text-sm text-gray-500">
                                    {{ $selectedChat->otherUser()->isOnline() ? 'آنلاین' : 'آفلاین' }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex-1 p-4 overflow-y-auto" id="chat-messages">
                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        @foreach($messages as $message)
                            <div
                                class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <div
                                    class="max-w-[70%] {{ $message->user_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }} rounded-lg px-4 py-2">
                                    <div class="text-sm">{{ $message->content }}</div>
                                    <div
                                        class="text-xs mt-1 {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-gray-500' }}">
                                        {{ $message->user->name }} - {{ $message->created_at->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t p-4">
                    <form wire:submit="sendMessage" class="flex space-x-2 rtl:space-x-reverse">
                        <input type="text"
                               wire:model.live.debounce.1000ms="message"
                               placeholder="پیام خود را بنویسید..."
                               class="flex-1 border rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                            ارسال
                        </button>
                    </form>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center text-gray-500">
                    برای شروع چت، یک کاربر را انتخاب کنید یا یک گروه جدید ایجاد کنید
                </div>
            @endif
        </div>
    </div>
</div>

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
    // اسکرول هنگام بارگذاری اولیه
    document.addEventListener('livewire:initialized', () => {
        window.scrollToBottom();

        // Load selected chat from localStorage
        const selectedChatId = localStorage.getItem('selectedChatId');
        if (selectedChatId) {
            $wire.handleChatSelected(selectedChatId);
        }
    });

    // اسکرول هنگام ارسال پیام یا تغییر چت
    document.addEventListener('livewire:updated', () => {
        window.scrollToBottom();
    });

    // Save selected chat to localStorage
    document.addEventListener('livewire:navigated', () => {
        const selectedChatId = localStorage.getItem('selectedChatId');
        if (selectedChatId) {
            $wire.handleChatSelected(selectedChatId);
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
            window.scrollToBottom();
        });
    });
</script>
@endscript
