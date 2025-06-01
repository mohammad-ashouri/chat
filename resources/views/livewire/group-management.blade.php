@props(['group'])

<div>
    @if($isAdmin)
        <button type="button"
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                wire:click="openModal">
            مدیریت گروه
        </button>
    @endif

    <x-modal name="group-management" :show="$showModal" maxWidth="2xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    مدیریت گروه
                </h2>
                <button type="button" class="text-gray-400 hover:text-gray-500" wire:click="closeModal">
                    <span class="sr-only">بستن</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($isAdmin)
                <div class="mb-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="flex-1">
                            <x-input-label for="group_name" value="نام گروه"/>
                            <x-text-input id="group_name" type="text" class="mt-1 block w-full" wire:model="groupName"
                                          placeholder="نام گروه را وارد کنید"/>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-6">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">اعضای گروه</h3>
                <div class="space-y-4">
                    @foreach($group->users as $user)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                @if($user->profile_photo_url)
                                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                         class="h-10 w-10 rounded-full">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                            @if($isAdmin && $user->id !== auth()->id())
                                <button wire:click="removeMember({{ $user->id }})"
                                        class="text-red-600 hover:text-red-800">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            @if($isAdmin)
                <div class="mb-6">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">افزودن عضو جدید</h3>
                    <div class="relative">
                        <input type="text" wire:model.live="search" placeholder="جستجوی کاربر..."
                               class="shadow appearance-none border rounded-lg w-full py-2 px-3 pr-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="mt-4 max-h-[400px] overflow-y-auto space-y-2">
                        @foreach($availableUsers as $user)
                            <div wire:click="toggleUser({{ $user->id }})"
                                 class="p-3 hover:bg-green-50 cursor-pointer rounded-lg transition duration-200 flex items-center">
                                <div class="flex items-center h-5 ml-3">
                                    <input type="checkbox"
                                           class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                        @checked(in_array($user->id, $selectedUsers))>
                                </div>
                                @if($user->profile_photo_url)
                                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                         class="h-10 w-10 rounded-full ml-3">
                                @else
                                    <div
                                        class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center ml-3">
                                        <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-200">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex justify-end">
                        <x-primary-button wire:click="addSelectedMembers">
                            افزودن اعضای انتخاب شده
                        </x-primary-button>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-4 gap-2">
                    <x-danger-button type="button" wire:click="openDeleteModal">
                        حذف گروه
                    </x-danger-button>
                    <x-primary-button wire:click="saveChanges">
                        ذخیره تغییرات
                    </x-primary-button>
                </div>
            @endif
        </div>
    </x-modal>

    <x-modal name="delete-group-modal" :show="$showDeleteModal" maxWidth="sm">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="rounded-full bg-red-100 p-3">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">حذف گروه</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">آیا از حذف این گروه اطمینان دارید؟ این عمل
                    غیرقابل بازگشت است.</p>
                <div class="flex justify-center space-x-4 gap-2">
                    <x-secondary-button wire:click="closeDeleteModal">
                        انصراف
                    </x-secondary-button>
                    <x-danger-button wire:click="deleteGroup">
                        حذف گروه
                    </x-danger-button>
                </div>
            </div>
        </div>
    </x-modal>
</div>
