<!DOCTYPE html>
<html lang="en" data-sidenav-size="fullscreen">
<head>
    <meta charset="utf-8" />
    <title>{{ $title }} | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">

    <!-- Responsive Table css -->
    <link href="{{ asset('dashboard') }}/assets/libs/RWD-Table-Patterns/css/rwd-table.min.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{{ asset('dashboard') }}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('dashboard') }}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('dashboard') }}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('dashboard') }}/assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css">

    <!-- Plugins css -->
    <link href="{{ asset('dashboard') }}/assets/libs/spectrum-colorpicker2/spectrum.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('dashboard') }}/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('dashboard') }}/assets/libs/clockpicker/bootstrap-clockpicker.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('dashboard') }}/assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />

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
    <script src="https://cdn.lordicon.com/lordicon-1.1.0.js"></script>


</head>

<body>
    <style>
        input[readonly] {
            background-color: rgb(236, 236, 236);
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
    @include('notification.sweet')
    <!-- Begin page -->
    <div class="wrapper">
