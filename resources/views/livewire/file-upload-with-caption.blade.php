<div>
    @filepondScripts
    @filepondStyles

    <form wire:submit="save" class="space-y-4">
        <div>
            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">انتخاب فایل</label>
            <div class="mt-1">
                <input
                    type="file"
                    wire:model="file"
                    class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-600 file:text-white
                        hover:file:bg-indigo-700"
                />
            </div>
            @error('file') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="caption" class="block text-sm font-medium text-gray-700">توضیحات</label>
            <textarea
                wire:model="caption"
                id="caption"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="توضیحات خود را اینجا وارد کنید..."
            ></textarea>
            @error('caption') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <button
                type="submit"
                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                آپلود فایل
            </button>
        </div>
    </form>

    @if (session()->has('message'))
        <div class="mt-4 p-4 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    @if ($uploadedFile)
        <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900">فایل آپلود شده:</h3>
            <p class="mt-1 text-sm text-gray-500">{{ $uploadedFile }}</p>
        </div>
    @endif
</div>
