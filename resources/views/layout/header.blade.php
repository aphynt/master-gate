<style>
.shake-alert {
    display: inline-block;
    animation: shake 1s infinite;
    color: #dc3545; /* Merah ala alert */
}

@keyframes shake {
    0%   { transform: translateX(0); }
    20%  { transform: translateX(-3px); }
    40%  { transform: translateX(3px); }
    60%  { transform: translateX(-3px); }
    80%  { transform: translateX(3px); }
    100% { transform: translateX(0); }
}
</style>
<header class="app-topbar"
    style="background-image: url('{{ asset('dashboard') }}/assets/images/bg-header.png'); background-size: cover; background-repeat: no-repeat; background-position: center;">

    <div class="page-container topbar-menu">
        <div class="d-flex align-items-center gap-2">

            <!-- Brand Logo -->
            <a href="#" class="logo">
                <span class="logo-light">
                    <span class="logo-lg"><img src="{{ asset('dashboard') }}/assets/images/logo-light.png"
                            alt="logo"></span>
                    <span class="logo-sm"><img src="{{ asset('dashboard') }}/assets/images/logo-sm-light.png"
                            alt="small logo"></span>
                </span>

                <span class="logo-dark">
                    <span class="logo-lg"><img src="{{ asset('dashboard') }}/assets/images/logo-dark.png"
                            alt="dark logo"></span>
                    <span class="logo-sm"><img src="{{ asset('dashboard') }}/assets/images/logo-sm.png"
                            alt="small logo"></span>
                </span>
            </a>

            <!-- Sidebar Menu Toggle Button -->
            <button class="sidenav-toggle-button px-2">
                <i class="mdi mdi-menu font-24"></i>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="topnav-toggle-button px-2" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="mdi mdi-menu font-22"></i>
            </button>

            <div class="d-none d-md-flex align-items-center">
                <img src="{{ asset('dashboard') }}/assets/images/sims.png" height="30px">

                <!-- Garis penengah -->
                <div style="width:1px; height:30px; background-color:#3a3a3a; margin: 0 10px;"></div>

                <a href="#" class="logo" aria-label="Master Gate logo" style="display: flex; align-items: center; gap: 12px;">
                    <svg aria-hidden="true" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="img" focusable="false" width="40" height="40">
                        <rect width="64" height="64" rx="12" ry="12" fill="#20c997"/>
                        <path fill="#0a1614" d="M20 44h6v-6h-6v6zm0-14h16v-8h-8v-6h-8v14zM40 20h-6v14h6v-14zm4 14h6v10a8 8 0 01-16 0v-10h6v8a2 2 0 004 0v-8z"/>
                    </svg>
                    Master Gate
                </a>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Light/Dark Toggle Button  -->
            <div class="topbar-item d-none d-sm-flex">
                <button class="topbar-link" id="light-dark-mode" type="button">
                    <i class="ti ti-moon font-22"></i>
                </button>
            </div>

            <!-- Language Dropdown -->
            <div class="topbar-item">
                <div class="dropdown">
                    <button class="topbar-link" data-bs-toggle="dropdown" data-bs-offset="0,25" type="button"
                        aria-haspopup="false" aria-expanded="false">
                        <img src="{{ asset('dashboard') }}/assets/images/flags/idn.png" alt="user-image"
                            class="w-100 rounded" height="18" id="selected-language-image">
                    </button>

                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item" data-translator-lang="en">
                            <img src="{{ asset('dashboard') }}/assets/images/flags/idn.png" alt="user-image"
                                class="me-1 rounded" height="18" data-translator-image> <span
                                class="align-middle">Indonesia</span>
                        </a>

                    </div>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="topbar-item nav-user">
                <div class="dropdown">
                    <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown"
                        data-bs-offset="0,25" type="button" aria-haspopup="false" aria-expanded="false">
                        <img src="{{ asset('avatar') }}/{{ Auth::user()->avatar }}"
                            width="32" height="32"
                            style="object-fit: cover"
                            class="rounded-circle me-lg-2 d-flex"
                            alt="user-image">
                        <span class="d-lg-flex flex-column gap-1 d-none">
                            <h6 class="my-0" style="color: white">Hi, {{ Auth::user()->name }}</h6>
                        </span>
                        <i class="mdi mdi-chevron-down d-none d-lg-block align-middle ms-2" style="color: white"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <div class="dropdown-header bg-primary mt-n3 rounded-top-2">
                            <h6 class="text-overflow text-white m-0">Semangat pagi!</h6>
                        </div>

                        <!-- item-->
                        <a href="{{ route('profile.index') }}" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-outline"></i>
                            <span>Profile</span>
                        </a>
                        {{-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="mdi mdi-grease-pencil"></i>
                            <span>Ubah Password</span>
                        </a> --}}

                        <div class="dropdown-divider"></div>

                        <!-- item-->
                        <a href="{{ route('logout') }}" class="dropdown-item notify-item">
                            <i class="mdi mdi-logout-variant"></i>
                            <span>Logout</span>
                        </a>

                    </div>
                </div>
            </div>

            <!-- Button Trigger Customizer Offcanvas -->
            <div class="topbar-item d-none d-sm-flex" >
                <button class="topbar-link" data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas"
                    type="button" style="color: white">
                    <i class="mdi mdi-cog-outline font-22"></i>
                </button>
            </div>
        </div>
    </div>
</header>
