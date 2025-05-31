<div class="search-box bg-white p-4 mb-4 rounded-lg shadow-md">
    <script>
        jalaliDatepicker.startWatch({
            minDate: "attr",
            maxDate: "attr"
        });
    </script>

    <form
        wire:submit.prevent="search"
        class="space-y-4"
    >
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <!-- فیلدهای متنی -->
            @foreach($inputs as $input)
                <div class="input-group">
                    <label
                        for="input_{{ $input['name'] }}"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        {{ $input['label'] ?? ucfirst($input['name']) }}
                    </label>
                    <input
                        @if(isset($input['type']))
                            type="{{ $input['type'] }}"
                        @else
                            data-jdp
                        @endif
                        id="input_{{ $input['name'] }}"
                        name="{{ $input['name'] }}"
                        value="{{ old($input['name'], request($input['name'])) }}"
                        placeholder="{{ $input['placeholder'] ?? '' }}"
                        wire:model="{{ $input['name'] }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    >
                </div>
            @endforeach

            <!-- فیلدهای انتخابی -->
            @foreach($selects as $select)
                <div class="select-group">
                    <label
                        for="select_{{ $select['name'] }}"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        {{ $select['label'] ?? ucfirst($select['name']) }}
                    </label>
                    <select
                        id="select_{{ $select['name'] }}"
                        wire:model="{{ $select['name'] }}"
                        name="{{ $select['name'] }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        @if(isset($select['placeholder']))
                            <option value="">{{ $select['placeholder'] }}</option>
                        @endif
                        @foreach($select['options'] as $value => $label)
                            <option
                                value="{{ $value }}"
                                {{ old($select['name'], request($select['name'])) == $value ? 'selected' : '' }}
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endforeach
            @if($errors->any())
                <div
                    class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300">
                    <h3 class="font-bold">خطاهای موجود:</h3>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="flex justify-end space-x-3 space-x-reverse mt-4">
            <button
                type="button"
                wire:click="resetForm"
                href="{{ $action }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
            >
                بازنشانی
            </button>

            <button
                type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                {{ $buttonLabel }}
            </button>
        </div>
    </form>
</div>
