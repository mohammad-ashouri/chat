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
                    @foreach($chats as $chat)
                        <div wire:click="handleChatSelected({{ $chat->id }})"
                             class="px-4 py-2 hover:bg-gray-100 cursor-pointer {{ $selectedChat && $selectedChat->id === $chat->id ? 'bg-gray-100' : '' }}">
                            @if($chat->is_group)
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-semibold ml-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium truncate">{{ $chat->name }}</div>
                                        @if($chat->lastMessage)
                                            <div class="text-sm text-gray-500 truncate">
                                                {{ $chat->lastMessage->user_id === auth()->id() ? 'شما: ' : '' }}{{ $chat->lastMessage->content }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold ml-3">
                                        {{ substr($chat->otherUser()->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium truncate">{{ $chat->otherUser()->name }}</div>
                                        @if($chat->lastMessage)
                                            <div class="text-sm text-gray-500 truncate">
                                                {{ $chat->lastMessage->user_id === auth()->id() ? 'شما: ' : '' }}{{ $chat->lastMessage->content }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
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
                    @foreach($messages as $message)
                        <div class="mb-4 {{ $message->user_id === auth()->id() ? 'text-right' : 'text-left' }}">
                            <div
                                class="inline-block p-3 rounded-lg {{ $message->user_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">
                                {{ $message->content }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $message->user->name }} - {{ $message->created_at->format('H:i') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t p-4">
                    <form wire:submit="sendMessage" class="flex">
                        <input type="text" wire:model="message"
                               class="flex-1 border rounded-l-lg px-4 py-2 focus:outline-none focus:border-blue-500"
                               placeholder="پیام خود را بنویسید...">
                        <button type="submit"
                                class="bg-blue-500 text-white px-6 py-2 rounded-r-lg hover:bg-blue-600 focus:outline-none">
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

@script
<script>
    // اسکرول به پایین هنگام بارگذاری چت یا ارسال پیام جدید
    function scrollToBottom() {
        const chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    // اسکرول هنگام بارگذاری اولیه
    document.addEventListener('livewire:initialized', () => {
        scrollToBottom();
    });

    // اسکرول هنگام ارسال پیام یا تغییر چت
    document.addEventListener('livewire:updated', () => {
        scrollToBottom();
    });
</script>
@endscript
