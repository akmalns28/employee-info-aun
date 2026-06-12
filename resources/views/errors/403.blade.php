<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>403 - Forbidden</title>
</head>

<body>
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="empty">
                <div class="empty-header">403</div>

                <p class="empty-title">Access Denied</p>

                <p class="empty-subtitle text-secondary">
                    Sorry, you do not have permission to access this page.
                </p>

                <div class="empty-action">
                    @if (auth()->check() && auth()->user()->can('dashboard.view'))
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-2">
                                <path d="M5 12l14 0"></path>
                                <path d="M5 12l6 6"></path>
                                <path d="M5 12l6 -6"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                    @else
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-4 mb-2">
                                Logout
                            </button>
                        </form>
                        <a href="javascript:history.back()">
                            Kembali
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>

</html>
