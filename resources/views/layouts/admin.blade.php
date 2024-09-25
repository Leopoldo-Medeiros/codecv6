<!-- resources/views/layouts/admin.blade.php -->
@include('includes.admin.header')
@include('includes.admin.menu.navbar')

<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Stylesheets -->
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">

<!-- Begin page content -->
<div class="container-fluid p-0 flex-shrink-0">
    <div class="d-grid gap-6" style="grid-template-columns: 1fr 5fr;">
        @role('client')
        @include('includes.admin.menu.client')
        @else
            @include('includes.admin.menu.admin')
            @endrole
            <div class="p-3">
                @yield('content')
            </div>
    </div>
</div>

@include('includes.admin.footer')

<!-- External Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> <!-- Use only one version -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> <!-- Use only one version -->
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>

<!-- JavaScript for Theme Switching -->
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const htmlElement = document.documentElement;
        const bodyElement = document.body;
        const darkModeSwitch = document.getElementById('darkModeSwitch');
        const lightModeSwitch = document.getElementById('lightModeSwitch');

        const currentTheme = localStorage.getItem('bsTheme') || 'dark';
        htmlElement.setAttribute('data-bs-theme', currentTheme);
        bodyElement.classList.toggle('dark-mode', currentTheme === 'dark');
        darkModeSwitch.style.display = currentTheme === 'dark' ? 'none' : 'inline-block';
        lightModeSwitch.style.display = currentTheme === 'light' ? 'none' : 'inline-block';

        darkModeSwitch.addEventListener('click', function () {
            htmlElement.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem('bsTheme', 'dark');
            bodyElement.classList.add('dark-mode');
            darkModeSwitch.style.display = 'none';
            lightModeSwitch.style.display = 'inline-block';
        });

        lightModeSwitch.addEventListener('click', function () {
            htmlElement.setAttribute('data-bs-theme', 'light');
            localStorage.setItem('bsTheme', 'light');
            bodyElement.classList.remove('dark-mode');
            darkModeSwitch.style.display = 'inline-block';
            lightModeSwitch.style.display = 'none';
        });
    });
</script>


