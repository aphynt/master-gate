<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Log In | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">

    <!-- Vendor css -->
    <link href="{{ asset('dashboard') }}/assets/css/vendor.min.css" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="{{ asset('dashboard') }}/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="{{ asset('dashboard') }}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <!-- Theme Config Js -->
    <script src="{{ asset('dashboard') }}/assets/js/config.js"></script>

    {{-- Sweetalert 2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<style>
    .form-control{
        font-size:12px;
    }
    .uppercase-input {
        text-transform: uppercase;
    }

    .uppercase-input::placeholder {
        text-transform: none;
    }
    .logo {
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 700;
      font-size: 1.8rem;
      color: #1bb99a;
    }
    .logo svg {
      width: 40px;
      height: 40px;
      fill: #1bb99a;
      flex-shrink: 0;
    }

</style>

<body class="authentication-bg" style="
    background-image: linear-gradient(rgba(0, 0, 0, 0.5), #001932), url('{{ asset('dashboard/assets/images/background.jpg') }}');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    width: 100vw;
    height: 100vh;
    margin: 0;
    overflow: hidden;
">
    @include('notification.sweet')
    <div class="account-pages pt-10 my-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5" style="--bs-box-shadow:  0px 0px 0px 0 #dadee8">
                    <div class="account-card-box bg-light rounded-2 p-2">
                        <div class="card mb-0 border border-4">
                            <div class="card-body p-4">

                                <div class="text-center">
                                    <div class="my-3">
                                        <a href="#">
                                            <span><img src="{{ asset('dashboard') }}/assets/images/sims.png" alt="" height="50"></span>
                                        </a>
                                    </div>
                                    <h5 class="text-muted text-uppercase py-3 font-16">sign in</h5>
                                </div>

                                <form action="{{ route('login.post') }}" method="POST" class="mt-2">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <input class="form-control uppercase-input" type="text" required name="nrp" placeholder="Masukkan NIK/NRP">
                                    </div>

                                    <div class="form-group mb-3">
                                        <input class="form-control" type="password" required="" name="password" id="password" placeholder="Masukkan Password">
                                    </div>

                                    <div class="form-group mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="checkbox-signin" name="remember" checked>
                                            <label class="form-check-label ms-1" for="checkbox-signin">Ingat Saya!</label>
                                        </div>
                                    </div>

                                    <div class="form-group text-center mb-3">
                                        <button class="btn btn-success btn-block waves-effect waves-light w-100" type="submit"> Masuk </button>
                                    </div>

                                    {{-- <a href="#" class="text-muted"><i class="mdi mdi-lock me-1"></i> Lupa password?</a> --}}

                                </form>
                                <div class="text-center" style="display: flex; justify-content: center; align-items: center; flex-direction: column;">
                                    <div class="my-3">
                                        <a href="#" class="logo" aria-label="Master Gate logo" style="display: flex; align-items: center; gap: 12px;">
                                            <svg aria-hidden="true" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="img" focusable="false" width="40" height="40">
                                                <rect width="64" height="64" rx="12" ry="12" fill="#20c997"/>
                                                <path fill="#0a1614" d="M20 44h6v-6h-6v6zm0-14h16v-8h-8v-6h-8v14zM40 20h-6v14h6v-14zm4 14h6v10a8 8 0 01-16 0v-10h6v8a2 2 0 004 0v-8z"/>
                                            </svg>
                                            Master Gate
                                        </a>
                                    </div>
                                </div>
                            </div> <!-- end card-body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end row -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>

    <!-- Vendor js -->
    <script src="{{ asset('dashboard') }}/assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="{{ asset('dashboard') }}/assets/js/app.js"></script>

</body>
</html>
