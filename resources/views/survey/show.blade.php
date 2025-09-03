<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $survey->name }} - SurfsUp</title>
    <meta name="description" content="{{ $survey->description }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('/img/favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://kit.fontawesome.com/d251d3e9b0.js" crossorigin="anonymous"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-900 text-white antialiased min-h-screen flex flex-col" style="background-image: url('{{ asset('img/surfsup-hero.png') }}'); background-size: cover; background-position: center; background-attachment: fixed;">
    @include('partials.header')

    <main class="container mx-auto px-4 py-8 flex-grow bg-white/60 backdrop-blur-sm rounded-lg shadow-xl mt-20 mb-4 text-center">
        <h1 class="text-3xl font-bold mb-4 text-gray-800">{{ $survey->name }}</h1>
        <p class="text-gray-600 mb-8">{{ $survey->description }}</p>

        @auth
            @if($hasSubmitted)
                <p class="text-gray-800 font-bold">You have already submitted this survey. Thank you for your feedback!</p>
            @else
                <form action="{{ route('survey.store', $survey) }}" method="POST">
                    @csrf
                    @foreach($survey->sections as $section)
                        <div class="mb-8 p-6 bg-gray-100 rounded-lg shadow-md">
                            <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ $section->name }}</h2>
                            @foreach($section->questions as $question)
                                <div class="mb-6">
                                    <label class="block text-gray-800 font-bold mb-2 text-center">{{ $question->content }}</label>
                                    @if($question->type === 'text')
                                        <input type="text" name="questions[{{ $question->id }}]" class="w-full px-4 py-2 border rounded-md text-gray-700">
                                    @elseif($question->type === 'textarea')
                                        <textarea name="questions[{{ $question->id }}]" class="w-full px-4 py-2 border rounded-md text-gray-700"></textarea>
                                    @elseif($question->type === 'radio')
                                        <div class="flex flex-col items-center">
                                        @foreach($question->options as $option)
                                            <div class="flex items-center mb-2">
                                                <input type="radio" name="questions[{{ $question->id }}]" value="{{ $option }}" class="mr-2">
                                                <label class="text-gray-800">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                        </div>
                                    @elseif($question->type === 'multiselect')
                                        <div class="flex flex-col items-center">
                                        @foreach($question->options as $option)
                                            <div class="flex items-center mb-2">
                                                <input type="checkbox" name="questions[{{ $question->id }}][]" value="{{ $option }}" class="mr-2">
                                                <label class="text-gray-800">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    {{-- Questions without a section --}}
                    @foreach($survey->questions->whereNull('section_id') as $question)
                        <div class="mb-6">
                            <label class="block text-gray-800 font-bold mb-2 text-center">{{ $question->content }}</label>
                            @if($question->type === 'text')
                                <input type="text" name="questions[{{ $question->id }}]" class="w-full px-4 py-2 border rounded-md text-gray-700">
                            @elseif($question->type === 'textarea')
                                <textarea name="questions[{{ $question->id }}]" class="w-full px-4 py-2 border rounded-md text-gray-700"></textarea>
                            @elseif($question->type === 'radio')
                                <div class="flex flex-col items-center">
                                @foreach($question->options as $option)
                                    <div class="flex items-center mb-2">
                                        <input type="radio" name="questions[{{ $question->id }}]" value="{{ $option }}" class="mr-2">
                                        <label class="text-gray-800">{{ $option }}</label>
                                    </div>
                                @endforeach
                                </div>
                            @elseif($question->type === 'multiselect')
                                <div class="flex flex-col items-center">
                                @foreach($question->options as $option)
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" name="questions[{{ $question->id }}][]" value="{{ $option }}" class="mr-2">
                                        <label class="text-gray-800">{{ $option }}</label>
                                    </div>
                                @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Submit
                    </button>
                </form>
            @endif
        @endauth

        @guest
            <div class="text-center">
                <p class="text-gray-800 font-bold mb-4">Please log in to participate in this survey.</p>
                <a href="{{ route('auth.steam') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Login with Steam
                </a>
            </div>
        @endguest
    </main>

    @include('partials.footer')
</body>
</html>
