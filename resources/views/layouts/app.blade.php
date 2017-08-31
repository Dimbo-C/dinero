<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Global Dinero Object -->
    <script>
      window.Dinero = {!! json_encode(array_merge(
            Dinero::scriptVariables(), []
        )) !!};
    </script>
</head>
<body>
    <div id="app" :class="{'with-sidebar': showSidebar && user}" v-cloak>
        <page-sidebar v-if="user" :version="version" :updated-at="updatedAt" :user="user" :window-width="windowWidth"></page-sidebar>
        <div class="page-container">

            <navbar :user="user" :window-width="windowWidth"></navbar>

            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
