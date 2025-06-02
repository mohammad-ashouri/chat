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
                            <div wire:click="handleChatSelected({{ $chat->id }})"
                                 class="p-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer {{ $selectedChat && $selectedChat->id === $chat->id ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
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
                                <i class="fas fa-users"></i>
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
                                class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }} mb-2">
                                {{-- User avatar/initials can go here if needed for left-aligned messages --}}
                                <div
                                    class="max-w-[55%] w-auto {{ $message->user_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100' }} rounded-lg p-3 relative group {{ in_array($message->id, $selectedMessages) ? ($message->user_id === auth()->id() ? 'ring-2 ring-blue-300 dark:ring-blue-600' : 'ring-2 ring-gray-400 dark:ring-gray-500') : '' }}"
                                    wire:click="toggleMessageSelection({{ $message->id }})"
                                    style="cursor: pointer;">
                                    @if(in_array($message->id, $selectedMessages))
                                        <div
                                            class="absolute {{ $message->user_id === auth()->id() ? '-left-2' : '-left-2' }} -top-2 bg-blue-500 text-white rounded-full p-1 z-10">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                    @endif
                                    @if($message->original_sender_id)
                                        <div
                                            class="mb-2 pb-2 border-b border-gray-300 dark:border-gray-600 text-sm break-words">
                                            <button wire:click.stop="startChat({{ $message->original_sender_id }})"
                                                    class="text-sm hover:underline {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-blue-600 dark:text-blue-400' }}">
                                                <i class="fas fa-share-alt mr-1"></i>
                                                پیام فوروارد شده از <span
                                                    class="font-semibold">{{ $message->originalSender->name }}</span>
                                            </button>
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
                                        <div class="break-words text-sm leading-relaxed">{{ $message->content }}</div>
                                    @endif
                                    <div class="flex items-center justify-between mt-1">
                                        <div
                                            class="text-xs {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400' }}">
                                            {{ $message->created_at->format('H:i') }}
                                        </div>
                                        <button wire:click.stop="openForwardModal({{ $message->id }})"
                                                class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-sm hover:text-blue-300 {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400' }}">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Message Input -->
                <div class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                    @if(count($selectedMessages) > 0)
                        <div class="mb-4 flex items-center justify-between">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ count($selectedMessages) }} پیام انتخاب شده
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="forwardSelectedMessages"
                                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 dark:hover:bg-blue-400 transition-colors duration-200">
                                    <i class="fas fa-share-alt mr-1"></i>
                                    فوروارد پیام‌های انتخاب شده
                                </button>
                                <button wire:click="clearSelectedMessages"
                                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-400 transition-colors duration-200">
                                    <i class="fas fa-times mr-1"></i>
                                    لغو انتخاب
                                </button>
                            </div>
                        </div>
                    @endif
                    <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                        <div
                            class="flex-1 flex items-center bg-gray-50 dark:bg-gray-700 rounded-lg px-3 py-1 border border-gray-200 dark:border-gray-600 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent transition-all duration-200">
                            <label class="cursor-pointer group" wire:loading.attr="disabled" wire:target="newFile">
                                <input type="file" wire:model.live="newFile" multiple class="hidden"
                                       wire:loading.attr="disabled">
                                <svg
                                    class="w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors duration-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </label>
                            <input type="text" wire:model="message" placeholder="پیام خود را بنویسید..."
                                   class="flex-1 bg-transparent border-0 focus:ring-0 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
                                   wire:loading.attr="disabled" wire:target="sendMessage">
                        </div>
                        <button type="submit"
                                class="bg-blue-500 text-white w-12 h-12 rounded-lg hover:bg-blue-600 dark:hover:bg-blue-400 flex items-center justify-center transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled" wire:target="sendMessage, newFile">
                            <span wire:loading.remove wire:target="sendMessage, newFile">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </span>
                            <span wire:loading wire:target="sendMessage, newFile">
                                <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                     fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </form>
                    @if($files)
                        <div class="mt-2">
                            <div class="grid grid-cols-4 gap-2">
                                @foreach($files as $index => $file)
                                    <div
                                        class="flex items-center text-sm bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-2 rounded-lg">
                                        <svg class="w-5 h-5 ml-2 flex-shrink-0" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span
                                            class="flex-1 truncate text-xs">{{ $file->getClientOriginalName() }}</span>
                                        <button wire:click="removeFile({{ $index }})"
                                                class="text-red-500 hover:text-red-700 dark:hover:text-red-400 transition-colors duration-200 flex-shrink-0"
                                                wire:loading.attr="disabled" wire:target="files">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            @if($isUploading)
                                <div class="flex items-center gap-1 text-blue-500 mr-2 mt-2">
                                    <span class="flex items-center gap-1 text-blue-500 mr-2">
                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                             viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        در حال آپلود...
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif
                    @if($error)
                        <div x-data="{ show: true }"
                             x-show="show"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             x-init="setTimeout(() => { show = false; $wire.dismissError() }, 3000)"
                             class="mt-2 text-red-500 dark:text-red-400 text-sm bg-red-50 dark:bg-red-900/30 px-3 py-2 rounded-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $error }}
                        </div>
                    @endif
                </div>
            @else
                <div class="flex-1 flex items-center justify-center text-gray-500 dark:text-gray-400">
                    برای شروع گفتگو، یک کاربر را انتخاب کنید یا یک گروه جدید ایجاد کنید
                </div>
            @endif
        </div>
    </div>
    <livewire:forward-message-modal/>
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
