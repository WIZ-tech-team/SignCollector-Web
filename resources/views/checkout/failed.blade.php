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
            <div class="flex items-center justify-center w-24 h-24 mx-auto mb-8 bg-red-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" class="w-12 h-12 text-red-600" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                </svg>
            </div>

            <!-- Main Content -->
            <h1 class="mb-6 text-4xl font-extrabold text-red-600">
                @if(isset($title))
                {{$title}}
                @else
                Error!
                @endif
            </h1>

            <p class="mb-8 text-xl text-gray-700">
                @if(isset($message))
                {{$message}}
                @else
                Unexpected error.
                @endif
            </p>

            <div class="p-6 mb-8 rounded-lg bg-red-50">
                <p class="text-lg font-medium text-red-700">
                    @if(isset($details))
                    {{$details}}
                    @else
                    Oops! Something went wrong.
                    @endif
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