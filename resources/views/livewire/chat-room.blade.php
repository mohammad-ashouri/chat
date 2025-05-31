<div class="flex h-screen bg-gray-100">
    <!-- سایدبار -->
    <div class="w-1/4 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">چت‌ها</h2>
            <input
                type="text"
                wire:model.live="search"
                placeholder="جستجو در چت‌ها..."
                class="w-full mt-2 p-2 border rounded-lg"
            >
        </div>

        <div class="flex-1 overflow-y-auto">
            @foreach($chats as $chat)
                <div
                    wire:click="selectChat({{ $chat->id }})"
                    class="p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer flex items-center {{ $activeChat && $activeChat->id == $chat->id ? 'bg-blue-50' : '' }}"
                >
                    <div class="flex-shrink-0">
                        <img
                            src="{{ $chat->isGroup() ? asset('images/group-icon.png') : $chat->otherUser()->avatar }}"
                            alt="avatar"
                            class="h-10 w-10 rounded-full"
                        >
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-medium">
                                {{ $chat->isGroup() ? $chat->name : $chat->otherUser()->name }}
                            </h3>
                            <span class="text-xs text-gray-500">
                                {{ $chat->lastMessage ? $chat->lastMessage->created_at->shortRelativeDiffForHumans() : '' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 truncate">
                            {{ $chat->lastMessage ? ($chat->lastMessage->user_id == auth()->id() ? 'شما: ' : '').$chat->lastMessage->content : 'هیچ پیامی وجود ندارد' }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- صفحه اصلی چت -->
    <div class="flex-1 flex flex-col">
        @if($activeChat)
            <!-- هدر چت -->
            <div class="p-4 border-b border-gray-200 bg-white flex items-center">
                <div class="flex-shrink-0">
                    <img
                        src="{{ $activeChat->isGroup() ? asset('images/group-icon.png') : $activeChat->otherUser()->avatar }}"
                        alt="avatar"
                        class="h-10 w-10 rounded-full"
                    >
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium">
                        {{ $activeChat->isGroup() ? $activeChat->name : $activeChat->otherUser()->name }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ $activeChat->isGroup() ? $activeChat->users->count().' عضو' : ($activeChat->otherUser()->isOnline() ? 'آنلاین' : 'آفلاین') }}
                    </p>
                </div>
            </div>

            <!-- محتوای چت -->
            <div
                class="flex-1 overflow-y-auto p-4 bg-gray-50"
                id="chat-messages"
                wire:ignore
            >
                @foreach($activeChat->messages as $message)
                    <div class="mb-4 flex {{ $message->user_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div
                            class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->user_id == auth()->id() ? 'bg-blue-500 text-white' : 'bg-white border border-gray-200' }}">
                            <div
                                class="text-xs text-{{ $message->user_id == auth()->id() ? 'blue-100' : 'gray-500' }} mb-1">
                                {{ $message->user->name }} - {{ $message->created_at->format('H:i') }}
                            </div>
                            <p>{{ $message->content }}</p>
                            @if($message->attachment)
                                <div class="mt-2">
                                    @if(str_starts_with($message->attachment_type, 'image/'))
                                        <img src="{{ asset('storage/'.$message->attachment) }}" alt="attachment"
                                             class="max-w-full h-auto rounded">
                                    @else
                                        <a href="{{ asset('storage/'.$message->attachment) }}" download
                                           class="text-blue-500 hover:underline flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                            </svg>
                                            دانلود فایل
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- فرم ارسال پیام -->
            <div class="p-4 border-t border-gray-200 bg-white">
                @if($attachment)
                    <div class="mb-2 flex items-center justify-between p-2 bg-gray-100 rounded">
                        <span class="text-sm">
                            فایل انتخاب شده: {{ $attachment->getClientOriginalName() }}
                        </span>
                        <button wire:click="$set('attachment', null)" class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @endif

                <div class="flex">
                    <label for="attachment"
                           class="flex items-center justify-center p-2 rounded-lg bg-gray-100 hover:bg-gray-200 cursor-pointer mr-2">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        <input type="file" id="attachment" wire:model="attachment" class="hidden">
                    </label>

                    <input
                        type="text"
                        wire:model="message"
                        wire:keydown.enter="sendMessage"
                        placeholder="پیام خود را بنویسید..."
                        class="flex-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                    <button
                        wire:click="sendMessage"
                        class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        ارسال
                    </button>
                </div>
            </div>
        @else
            <div class="flex-1 flex items-center justify-center bg-gray-50">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">چتی انتخاب نشده است</h3>
                    <p class="mt-1 text-gray-500">لطفاً یک چت را از لیست سمت راست انتخاب کنید.</p>
                </div>
            </div>
        @endif
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
