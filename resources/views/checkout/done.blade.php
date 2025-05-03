<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {{ config('app.name') }}
    </title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css'])
</head>

<body>
    <div class="flex items-center justify-center min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
        <div
            class="w-full max-w-2xl p-12 mx-4 text-center transition-all transform bg-white shadow-lg rounded-xl hover:shadow-xl">
            <!-- Success Icon -->
            <div class="flex items-center justify-center w-24 h-24 mx-auto mb-8 bg-light-brand rounded-full">
                <svg class="w-12 h-12 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <!-- Main Content -->
            <h1 class="mb-6 text-4xl font-extrabold text-brand">
                Done!
            </h1>

            <p class="mb-8 text-xl text-gray-700">
                This process have been finished.
            </p>

            <div class="p-6 mb-8 rounded-lg bg-blue-50">
                <p class="text-lg font-medium text-blue-700">
                    If the payment done successfully, your subscription will be activated.
                </p>
            </div>

            <!-- Contact Information -->
            <!-- <div class="pt-8 mt-8 border-t border-gray-100">
                <p class="text-lg text-gray-700">
                    Have questions? Contact us at:
                </p>
                <a href="mailto:admin@eliteai.tools"
                    class="inline-block mt-2 text-xl font-medium text-blue-600 transition-colors duration-200 hover:text-blue-800">
                    admin@eliteai.tools
                </a>
            </div> -->

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