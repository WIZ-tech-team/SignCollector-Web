<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css'])
    @paddleJS
</head>

<body class="font-sans antialiased dark:bg-black dark:text-white/50">
    <div class="flex items-center justify-center min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
        <div
            class="w-full max-w-2xl p-12 mx-4 text-center transition-all transform bg-white shadow-lg rounded-xl hover:shadow-xl">
            <!-- Success Icon -->
            <div class="flex items-center justify-center w-24 h-24 mx-auto mb-8 bg-light-brand rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" class="w-12 h-12 text-brand" viewBox="0 0 16 16">
                    <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                    <path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2z" />
                </svg>
            </div>

            <!-- Main Content -->
            <h1 class="mb-6 text-4xl font-extrabold text-gray-600">
                {{$plan['name']}}
            </h1>

            <p class="mb-8 text-xl text-gray-700">
                {{$price['amount']/100}} {{$price['currency']}} / {{$price['interval']??'once'}}
            </p>

            <div class="p-6 mb-8 rounded-lg bg-light-brand">
                <p class="text-lg font-medium text-brand">
                    {{$plan['description']}}
                </p>
            </div>

            <x-paddle-button :checkout="$checkout" class="inline-block px-8 py-4 text-lg font-semibold text-white transition-colors duration-200 bg-brand rounded-lg hover:bg-active-brand">
                Continue to Checkout
            </x-paddle-button>

            <!-- Back to Home Button -->
            <div class="mt-12">
                <a href="{{config('app.url')}}"
                    class="inline-block px-8 py-4 text-lg font-semibold text-brand transition-colors duration-200 bg-light-brand rounded-lg hover:bg-brand hover:text-light-brand">
                    Home
                </a>
            </div>
        </div>
    </div>

</body>

</html>