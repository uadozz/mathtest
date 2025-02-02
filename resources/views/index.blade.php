<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Multiplication Practice</title>
	<!-- <base href="{{ url('/') }}/"> -->

	@livewireStyles
    </head>
    <body class="antialiased">
	<livewire:multiplication-practice />
	@livewireScripts
    </body>
</html>
