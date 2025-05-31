<div>
    <button
        title="گروه جدید"
        wire:click="$set('showModal', true)"
        class="flex gap-1 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-200 ease-in-out transform hover:scale-105">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        گروه جدید
    </button>

    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="$wire.set('showModal', false)"
             style="display: none;"
             id="modal">
            <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white z-50"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">ایجاد گروه جدید</h3>
                        <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mt-2 px-7 py-3">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="groupName">
                                نام گروه
                            </label>
                            <input type="text" wire:model="groupName" id="groupName"
                                   class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                                   placeholder="نام گروه را وارد کنید">
                            @error('groupName')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                انتخاب اعضا
                            </label>
                            <div class="relative">
                                <input type="text" wire:model="search" placeholder="جستجوی کاربر..."
                                       class="shadow appearance-none border rounded-lg w-full py-2 px-3 pr-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="mt-4 max-h-[400px] overflow-y-auto space-y-2">
                                @foreach($users as $user)
                                    <div wire:click="toggleUser({{ $user->id }})"
                                         class="p-3 hover:bg-green-50 cursor-pointer rounded-lg transition duration-200 flex items-center">
                                        <div class="flex items-center h-5 ml-3">
                                            <input type="checkbox"
                                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                                @checked(in_array($user->id, $selectedUsers))>
                                        </div>
                                        <div
                                            class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-semibold ml-3">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('selectedUsers')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button wire:click="createGroup"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-200 ease-in-out transform hover:scale-105">
                            ایجاد گروه
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    // پاک کردن ورودی‌ها هنگام بستن مودال
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('modalClosed', () => {
            @this.
            set('groupName', '');
            @this.
            set('selectedUsers', []);
            @this.
            set('search', '');
        });
    });
</script>
@endscript
