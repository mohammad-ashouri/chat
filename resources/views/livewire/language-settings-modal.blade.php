<div>
    @if($showModal)
        <div
            class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-50 overflow-y-auto h-full w-full z-50"
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
            <div
                class="relative top-20 mx-auto p-5 border dark:border-gray-700 w-[500px] shadow-lg rounded-md bg-white dark:bg-gray-800 z-50"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95">
                <div class="mt-3">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">تنظیمات زبان</h3>

                    <div class="mt-2 px-7 py-3">
                        <div class="mb-4">
                            <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300">زبان</label>
                            <select id="language" wire:model="language"
                                    class="mt-1 block w-full px-3 py-2 border dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="fa">فارسی</option>
                                <option value="en">English</option>
                                <option value="ar">العربية</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="dateFormat" class="block text-sm font-medium text-gray-700 dark:text-gray-300">فرمت
                                تاریخ</label>
                            <select id="dateFormat" wire:model="dateFormat"
                                    class="mt-1 block w-full px-3 py-2 border dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="jalali">شمسی</option>
                                <option value="gregorian">میلادی</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="timeFormat" class="block text-sm font-medium text-gray-700 dark:text-gray-300">فرمت
                                ساعت</label>
                            <select id="timeFormat" wire:model="timeFormat"
                                    class="mt-1 block w-full px-3 py-2 border dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="24">۲۴ ساعته</option>
                                <option value="12">۱۲ ساعته</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button wire:click="save"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                            ذخیره
                        </button>
                        <button wire:click="$set('showModal', false)"
                                class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded mr-2">
                            انصراف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
