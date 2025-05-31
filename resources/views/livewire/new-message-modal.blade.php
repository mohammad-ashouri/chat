<div>
    <button
        title="پیام جدید"
        wire:click="$set('showModal', true)"
        class="flex gap-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        <svg fill="#FFFFFF" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
             xmlns:xlink="http://www.w3.org/1999/xlink"
             class="w-5 h-5" viewBox="0 0 400 400" xml:space="preserve">
        <g>
            <g>
                <path d="M199.995,0C89.716,0,0,89.72,0,200c0,110.279,89.716,200,199.995,200C310.277,400,400,310.279,400,200
                    C400,89.72,310.277,0,199.995,0z M199.995,373.77C104.182,373.77,26.23,295.816,26.23,200c0-95.817,77.951-173.77,173.765-173.77
                    c95.817,0,173.772,77.953,173.772,173.77C373.769,295.816,295.812,373.77,199.995,373.77z"/>
                <path d="M279.478,186.884h-66.363V120.52c0-7.243-5.872-13.115-13.115-13.115s-13.115,5.873-13.115,13.115v66.368h-66.361
                    c-7.242,0-13.115,5.873-13.115,13.115c0,7.243,5.873,13.115,13.115,13.115h66.358v66.362c0,7.242,5.872,13.114,13.115,13.114
                    c7.242,0,13.115-5.872,13.115-13.114v-66.365h66.367c7.241,0,13.114-5.873,13.114-13.115
                    C292.593,192.757,286.72,186.884,279.478,186.884z"/>
            </g>
        </g>
        </svg>
        پیام جدید
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
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">انتخاب کاربر</h3>

                    <div class="mt-2 px-7 py-3">
                        <input type="text" wire:model.live="search" placeholder="جستجوی کاربر..."
                               class="w-full px-3 py-2 border rounded-md">

                        <div class="mt-4 max-h-[400px] overflow-y-auto">
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
