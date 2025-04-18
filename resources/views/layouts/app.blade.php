<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="{{ auth()->id() }}">
    <title>Expense Tracker</title>

    <!-- Favicon Links -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

    <!--- jquery section---->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!--- font-awesome section---->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!--- apex charts section---->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!--- toast section---->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
</head>
<body class="font-sans antialiased flex flex-col min-h-screen text-white bg-gray-800">

<!-- Navigation bar -->
@include('navs.navbar')

<!-- Main Content Section -->
<main class="flex-grow">
    @yield('content')
</main>

<!-- Footer -->
@include('navs.footer')

<!-- Livewire Scripts -->
@livewireScripts

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('notification-sent', () => {
            console.log('Notification received event triggered!');
        });
        Livewire.on('global-toast', (event) => {
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            };
            if (event.details.type === 'success'){
                toastr.success(event.details.message);
            }
            if (event.details.type === 'error'){
                toastr.error(event.details.message);
            }
            if (event.details.type === 'warning'){
                toastr.warning(event.details.message);
            }
            if (event.details.type === 'info'){
                toastr.info(event.details.message);
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        @if(Session::has('success'))
            toastr.options = {
            "closeButton": true,
            "progressBar": true
        };
        toastr.success("{{ session('success') }}");
        @endif

            @if(Session::has('error'))
            toastr.options = {
            "closeButton": true,
            "progressBar": true
        };
        toastr.error("{{ session('error') }}");
        @endif

            @if(Session::has('info'))
            toastr.options = {
            "closeButton": true,
            "progressBar": true
        };
        toastr.info("{{ session('info') }}");
        @endif

            @if(Session::has('warning'))
            toastr.options = {
            "closeButton": true,
            "progressBar": true
        };
        toastr.warning("{{ session('warning') }}");
        @endif
    });
</script>

</body>
</html>
