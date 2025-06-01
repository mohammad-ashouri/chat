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
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">تنظیمات پیشرفته
                        تم</h3>

                    <div class="mt-2 px-7 py-3">
                        <div class="mb-4">
                            <label for="theme"
                                   class="block text-sm font-medium text-gray-700 dark:text-gray-300">تم</label>
                            <select id="theme" wire:model="theme"
                                    class="mt-1 block w-full px-3 py-2 border dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="light">روشن</option>
                                <option value="dark">تیره</option>
                                <option value="system">سیستمی</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="accentColor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">رنگ
                                اصلی</label>
                            <select id="accentColor" wire:model="accentColor"
                                    class="mt-1 block w-full px-3 py-2 border dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="blue">آبی</option>
                                <option value="green">سبز</option>
                                <option value="red">قرمز</option>
                                <option value="purple">بنفش</option>
                                <option value="orange">نارنجی</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="fontSize" class="block text-sm font-medium text-gray-700 dark:text-gray-300">اندازه
                                فونت</label>
                            <select id="fontSize" wire:model="fontSize"
                                    class="mt-1 block w-full px-3 py-2 border dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="small">کوچک</option>
                                <option value="medium">متوسط</option>
                                <option value="large">بزرگ</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="fontFamily" class="block text-sm font-medium text-gray-700 dark:text-gray-300">نوع
                                فونت</label>
                            <select id="fontFamily" wire:model="fontFamily"
                                    class="mt-1 block w-full px-3 py-2 border dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="vazir">وزیر</option>
                                <option value="iransans">ایران سنس</option>
                                <option value="yekan">یکان</option>
                                <option value="system">سیستمی</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center space-x-3 space-x-reverse">
                                <input type="checkbox" wire:model="compactMode"
                                       class="form-checkbox h-5 w-5 text-blue-600 dark:text-blue-500">
                                <span class="text-gray-700 dark:text-gray-300">حالت فشرده</span>
                            </label>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center space-x-3 space-x-reverse">
                                <input type="checkbox" wire:model="animations"
                                       class="form-checkbox h-5 w-5 text-blue-600 dark:text-blue-500">
                                <span class="text-gray-700 dark:text-gray-300">انیمیشن‌ها</span>
                            </label>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center space-x-3 space-x-reverse">
                                <input type="checkbox" wire:model="roundedCorners"
                                       class="form-checkbox h-5 w-5 text-blue-600 dark:text-blue-500">
                                <span class="text-gray-700 dark:text-gray-300">گوشه‌های گرد</span>
                            </label>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center space-x-3 space-x-reverse">
                                <input type="checkbox" wire:model="shadows"
                                       class="form-checkbox h-5 w-5 text-blue-600 dark:text-blue-500">
                                <span class="text-gray-700 dark:text-gray-300">سایه‌ها</span>
                            </label>
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
