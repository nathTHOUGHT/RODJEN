<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - RODJEN Technet' : 'RODJEN Technet' }}
</title>

<link rel="icon" href="{{ asset('brand/rodjen-icon.png') }}" type="image/png">
<link rel="apple-touch-icon" href="{{ asset('brand/rodjen-icon.png') }}">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
<script>window.Flux.applyAppearance('light')</script>
