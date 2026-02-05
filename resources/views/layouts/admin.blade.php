<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Mindora Admin')</title>

    <!-- Favicons -->
    <link href="{{ asset('assets/img/logo.png') }}" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">

    <!-- Vendor CSS -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- Main CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href="{{ route('admin.dashboard') }}" class="logo d-flex align-items-center">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Mindora">
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

   <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

        {{-- 🔔 Notifications --}}
        <li class="nav-item dropdown">

            <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-bell"></i>

                @if(auth()->user()->unreadNotifications->count())
                    <span class="badge bg-danger badge-number">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </a>

            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">

                <li class="dropdown-header">
                    You have {{ auth()->user()->unreadNotifications->count() }} new notifications
                </li>

                <li><hr class="dropdown-divider"></li>

                @forelse(auth()->user()->notifications->take(5) as $n)
                    <li class="notification-item">
                        <i class="bi 
                            {{ $n->data['action'] === 'approved' ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }}">
                        </i>

                        <div>
                            <h4 class="mb-0">{{ $n->data['title'] }}</h4>
                            <p class="mb-0 small text-muted">
                                {{ ucfirst($n->data['action']) }}
                                @if(!empty($n->data['reason']))
                                    – {{ Str::limit($n->data['reason'],40) }}
                                @endif
                            </p>
                            <p class="small text-muted">
                                {{ $n->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </li>

                    <li><hr class="dropdown-divider"></li>
                @empty
                    <li class="dropdown-item text-center text-muted">
                        No notifications
                    </li>
                @endforelse

                <li class="dropdown-footer">
                    <a href="{{ route('admin.notifications.index') }}">
                        View all notifications
                    </a>
                </li>

            </ul>
        </li>

        {{-- 👤 User Profile --}}
        <li class="nav-item dropdown pe-3">
            <a class="nav-link nav-profile d-flex align-items-center pe-0"
               href="#"
               data-bs-toggle="dropdown">
                <img src="{{ asset('assets/img/user.png') }}" class="rounded-circle">
                <span class="d-none d-md-block dropdown-toggle ps-2">
                    {{ auth()->user()->name }}
                </span>
            </a>

            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                <li class="dropdown-header">
                    <h6>{{ auth()->user()->email }}</h6>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <i class="bi bi-person"></i>
                        <span>My Profile</span>
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item d-flex align-items-center">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </li>

    </ul>
</nav>

</header>
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-grid"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
    <a class="nav-link collapsed"
       data-bs-target="#questions-nav"
       data-bs-toggle="collapse"
       href="#">
        <i class="bi bi-question-circle"></i>
        <span>Questions</span>
        <i class="bi bi-chevron-down ms-auto"></i>
    </a>

    <ul id="questions-nav"
        class="nav-content collapse
        {{ request()->routeIs('admin.questions.*') || request()->routeIs('admin.question_categories.*') ? 'show' : '' }}"
        data-bs-parent="#sidebar-nav">

        <li>
            <a href="{{ route('admin.questions.index') }}"
               class="{{ request()->routeIs('admin.questions.index') ? 'active' : '' }}">
                <i class="bi bi-circle"></i>
                <span>All Questions</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.questions.create') }}"
               class="{{ request()->routeIs('admin.questions.create') ? 'active' : '' }}">
                <i class="bi bi-circle"></i>
                <span>Add Question</span>
            </a>
        </li>

        
       
    {{-- In Review --}}
    <li>
        <a href="{{ route('admin.questions.index',['status'=>'review']) }}"
           class="{{ request('status') === 'review' ? 'active' : '' }}">
            <i class="bi bi-circle"></i>
            <span>In Review Question</span>
        </a>
    </li>

    {{-- Published --}}
    <li>
        <a href="{{ route('admin.questions.index',['status'=>'published']) }}"
           class="{{ request('status') === 'published' ? 'active' : '' }}">
            <i class="bi bi-circle"></i>
            <span>Published Question</span>
        </a>
    </li>

    <li>
        <a href="{{ route('admin.questions.index',['status'=>'approved']) }}"
           class="{{ request('status') === 'approved' ? 'active' : '' }}">
            <i class="bi bi-circle"></i>
            <span>Approved Question</span>
        </a>
    </li>

    <li>
        <a href="{{ route('admin.questions.index',['status'=>'rejected']) }}"
           class="{{ request('status') === 'rejected' ? 'active' : '' }}">
            <i class="bi bi-circle"></i>
            <span>Rejected Question</span>
        </a>
    </li>

        <li>
            <a href="{{ route('admin.categories.index') }}"
               class="{{ request()->routeIs('admin.question_categories.*') ? 'active' : '' }}">
                <i class="bi bi-circle"></i>
                <span>Categories</span>
            </a>
        </li>

    </ul>



        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-globe"></i> Scraping
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-robot"></i> AI Management
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-gear"></i> Settings
            </a>
        </li>

    </ul>
</aside>
<main id="main" class="main">
    @yield('content')
</main>
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
document.querySelector('.nav-icon')?.addEventListener('click', () => {
    fetch('{{ route('admin.notifications.readAll') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
});
</script>

<footer class="footer">
    <div class="copyright">
        © {{ date('Y') }} <strong>Mindora</strong>. All Rights Reserved
    </div>
</footer>
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

@stack('scripts')
</body>
</html>
