<!DOCTYPE html>
<html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ 'پیام رسان شناخت | ' . $title ?? 'پیام رسان شناخت' }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"
          integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">

    <!-- Scripts -->
    <link rel="stylesheet" href="/build/plugins/tagify/tagify.css">
    <script src="/build/plugins/tagify/tagify.min.js"></script>
    <script src="/build/plugins/tagify/tagify.polyfills.min.js"></script>

    <link rel="stylesheet" href="/build/plugins/jalali-datepicker/jalali-datepicker.min.css"/>
    <script src="/build/plugins/jalali-datepicker/jalali-datepicker.js"></script>

    <x-head.tinymce-config/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('scripts')

    <script>
        // Check for saved dark mode preference
        if (localStorage.getItem('darkMode') === null) {
            localStorage.setItem('darkMode', 'true');
        }
    </script>
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <livewire:layout.navigation/>

    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <x-notification-modal name="success-notification">
        عملیات با موفقیت انجام شد!
    </x-notification-modal>

    <x-flash-messages/>

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>
</div>
@livewireScripts
@filepondScripts

<script>
    document.addEventListener('dark-mode-enabled', () => {
        document.documentElement.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
    });

    document.addEventListener('dark-mode-disabled', () => {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('darkMode', 'false');
    });
</script>
</body>
</html>
