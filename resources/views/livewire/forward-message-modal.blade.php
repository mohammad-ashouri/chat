<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div
                    class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
                    aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-right w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                    id="modal-title">
                                    فوروارد پیام
                                </h3>

                                <div class="mt-4">
                                    <input type="text" wire:model.live="search" placeholder="جستجوی چت..."
                                           class="w-full px-3 py-2 border dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">

                                    <div class="mt-4 max-h-[400px] overflow-y-auto">
                                        @foreach($chats as $chat)
                                            <div wire:click="forwardMessage({{ $chat->id }})"
                                                 class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer rounded-md text-gray-900 dark:text-gray-100">
                                                @if($chat->is_group)
                                                    <div class="flex items-center">
                                                        <div
                                                            class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white mr-2">
                                                            <i class="fas fa-users"></i>
                                                        </div>
                                                        <span>{{ $chat->name }}</span>
                                                    </div>
                                                @else
                                                    <div class="flex items-center">
                                                        <div
                                                            class="w-8 h-8 rounded-full bg-gray-500 flex items-center justify-center text-white mr-2">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                        <span>{{ $chat->otherUser()->name }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="$set('showModal', false)"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm">
                            انصراف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
