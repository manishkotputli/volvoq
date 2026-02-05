<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Mindora Admin Register</title>

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Nunito:300,400,600,700|Poppins:300,400,500,600,700" rel="stylesheet">

    <!-- Vendor CSS -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>

<body>

<div style="background-image:url('{{ asset('assets/media/login-bg.jpg') }}'); background-size:cover;">
<main>
    <div class="container">

        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                        <!-- Logo -->
                        <div class="d-flex justify-content-center py-4">
                            <a class="logo d-flex align-items-center w-auto">
                                <img src="{{ asset('assets/img/logo.png') }}" alt="Mindora">
                            </a>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">

                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-center pb-0 fs-4">Create Admin Account</h5>
                                    <p class="text-center small">Enter details to register</p>
                                </div>

                                {{-- VALIDATION ERRORS --}}
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- REGISTER FORM --}}
                                <form method="POST" action="{{ route('register') }}" class="row g-3">
                                    @csrf

                                    <!-- Name -->
                                    <div class="col-12">
                                        <label class="form-label">Name</label>
                                        <input type="text"
                                               name="name"
                                               class="form-control"
                                               value="{{ old('name') }}"
                                               required autofocus>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-12">
                                        <label class="form-label">Email</label>
                                        <input type="email"
                                               name="email"
                                               class="form-control"
                                               value="{{ old('email') }}"
                                               required>
                                    </div>

                                    <!-- Password -->
                                    <div class="col-12">
                                        <label class="form-label">Password</label>
                                        <input type="password"
                                               name="password"
                                               class="form-control"
                                               required>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="col-12">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password"
                                               name="password_confirmation"
                                               class="form-control"
                                               required>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary w-100" type="submit">
                                            Register
                                        </button>
                                    </div>

                                    <div class="col-12 text-center">
                                        <a href="{{ route('login') }}" class="small">
                                            Already registered? Login
                                        </a>
                                    </div>
                                </form>

                            </div>
                        </div>

                        <div class="credits text-white">
                            © {{ date('Y') }} Mindora Admin
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </div>
</main>
</div>

<!-- Vendor JS -->
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

</body>
</html>
