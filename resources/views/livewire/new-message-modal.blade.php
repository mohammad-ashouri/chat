<div>
    <button wire:click="$set('showModal', true)"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        ارسال پیام جدید
    </button>

    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="modal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">انتخاب کاربر</h3>

                    <div class="mt-2 px-7 py-3">
                        <input type="text" wire:model.live="search" placeholder="جستجوی کاربر..."
                               class="w-full px-3 py-2 border rounded-md">

                        <div class="mt-4 max-h-60 overflow-y-auto">
                            @foreach($users as $user)
                                <div wire:click="selectUser({{ $user->id }})"
                                     class="p-2 hover:bg-gray-100 cursor-pointer rounded-md">
                                    {{ $user->name }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button wire:click="$set('showModal', false)"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                            انصراف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
