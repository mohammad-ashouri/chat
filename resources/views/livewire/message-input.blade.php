<div>
    @if($replyingTo)
        <div class="mb-2 p-2 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-between">
            <div class="flex-1">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    در پاسخ به {{ $replyingTo->user->name }}
                </div>
                <div class="text-sm text-gray-900 dark:text-gray-100 truncate">
                    {{ $replyingTo->content }}
                </div>
            </div>
            <button wire:click="cancelReply"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif
    @if(count($selectedMessages) > 0)
        <div class="mb-4 flex items-center justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                {{ count($selectedMessages) }} پیام انتخاب شده
            </div>
            <div class="flex gap-2">
                <button wire:click="$dispatch('forwardSelectedMessages')"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 dark:hover:bg-blue-400 transition-colors duration-200">
                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    فوروارد پیام‌های انتخاب شده
                </button>
                <button wire:click="clearSelectedMessages"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-400 transition-colors duration-200">
                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
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
                   wire:loading.attr="disabled" wire:target="sendMessage"
                   x-data
                   x-init="$el.focus()"
                   x-on:focus="$el.focus()"
                   autofocus>
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
                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
            @if($isUploading)
                <div class="flex items-center gap-1 text-blue-500 mr-2 mt-2">
                    <span class="flex items-center gap-1 text-blue-500 mr-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                             fill="none"
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
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $error }}
        </div>
    @endif
</div> 