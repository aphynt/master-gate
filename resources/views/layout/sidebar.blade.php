{{-- <div class="sidenav-menu" style="
    background-color: #001932;
    background-image: linear-gradient(#213d58, rgba(0,0,0,0));
"> --}}

<div class="sidenav-menu" style="
    background-color: #001932;
">

    <!-- Brand Logo -->
    <a href="#" class="logo" aria-label="Master Gate logo" style="display: flex; align-items: center; gap: 12px;">
        <svg aria-hidden="true" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="img" focusable="false" width="40" height="40">
            <rect width="64" height="64" rx="12" ry="12" fill="#20c997"/>
            <path fill="#0a1614" d="M20 44h6v-6h-6v6zm0-14h16v-8h-8v-6h-8v14zM40 20h-6v14h6v-14zm4 14h6v10a8 8 0 01-16 0v-10h6v8a2 2 0 004 0v-8z"/>
        </svg>
        Main
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-sm-hover">
        <i class="ti ti-circle align-middle"></i>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-fullsidebar">
        <i class="ti ti-x align-middle"></i>
    </button>

    <div data-simplebar>

        <!--- Sidenav Menu -->
        <ul class="side-nav">
            <li class="side-nav-title">Navigation</li>

            <li class="side-nav-item">
                <a href="{{ route('dashboard.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/dashboard.png" style="width: 20px"></span>
                    <span class="menu-text"> Dashboard </span>
                    {{-- <span class="badge bg-success rounded-pill">5</span> --}}
                </a>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#masterData" aria-expanded="false"
                    aria-controls="masterData" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/master-data.png" style="width: 20px"></span>
                    <span class="menu-text"> Reference </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="masterData">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('listActivity.index') }}" class="side-nav-link">
                                <span class="menu-text">List Activity</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('listDescriptionProblem.index') }}" class="side-nav-link">
                                <span class="menu-text">List Description Problem</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('listActionProblem.index') }}" class="side-nav-link">
                                <span class="menu-text">List Action Problem</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('listStatus.index') }}" class="side-nav-link">
                                <span class="menu-text">List Status</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('listRequestAt.index') }}" class="side-nav-link">
                                <span class="menu-text">List Request At</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('listArea.index') }}" class="side-nav-link">
                                <span class="menu-text">List Area</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('listTower.index') }}" class="side-nav-link">
                                <span class="menu-text">List Tower</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('listAccessPoint.index') }}" class="side-nav-link">
                                <span class="menu-text">List Access Point</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('listUnit.index') }}" class="side-nav-link">
                                <span class="menu-text">List Unit</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-title">Activity</li>

             <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#summaryReport" aria-expanded="false"
                    aria-controls="summaryReport" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/interpretation.png" style="width: 20px"></span>
                    <span class="menu-text"> Report </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="summaryReport">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('dailyActivity.index') }}" class="side-nav-link">
                                <span class="menu-text">Summary Daily</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('weeklyActivity.index') }}" class="side-nav-link">
                                <span class="menu-text">Summary Weekly</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="#" onclick="Swal.fire({ icon: 'info', title: 'Upps!', text: 'Maaf, menu ini belum berfungsi.', confirmButtonText: 'OK' })" class="side-nav-link">
                                <span class="menu-text">Summary Monthly</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('maintenanceTower.index') }}" class="side-nav-link">
                                <span class="menu-text">Maintenance Tower</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('maintenanceUnit.index') }}" class="side-nav-link">
                                <span class="menu-text">Maintenance Unit</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('summaryRitation.index') }}" class="side-nav-link">
                                <span class="menu-text">Summary Ritation</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('weeklyPlan.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/planning.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Weekly Plan </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('ritationPerHour.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/exchange.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Ritation Per Hour </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('activityTower.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/eiffel-tower.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Tower </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('activityUnit.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/dump-truck.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Unit </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('activityGenset.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/switch.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Turn On/Off Genset </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('activityAdditional.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/book.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Additional </span>
                </a>
            </li>

            <li class="side-nav-title">Inventory</li>

            <li class="side-nav-item">
                <a href="{{ route('barang.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/boxes.png" style="width: 20px"></i></span>
                    <span class="menu-text"> List Barang </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('barangMasuk.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/inbound.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Barang Masuk </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('barangKeluar.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/outbound.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Barang Keluar </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('activityPergantianBarang.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/alter.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Pergantian Barang </span>
                </a>
            </li>

            <li class="side-nav-title">Safety</li>
            <li class="side-nav-item">
                <a href="#" onclick="Swal.fire({ icon: 'info', title: 'Upps!', text: 'Maaf, menu ini belum berfungsi.', confirmButtonText: 'OK' })" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/businessman.png" style="width: 20px"></i></span>
                    <span class="menu-text"> P5M </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="#" onclick="Swal.fire({ icon: 'info', title: 'Upps!', text: 'Maaf, menu ini belum berfungsi.', confirmButtonText: 'OK' })" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/safety.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Pemahaman JSA </span>
                </a>
            </li>
             {{-- <li class="side-nav-item">
                <a href="{{ route('activityHarianTower.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/5g.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Activity Harian Tower </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('activityHarianUnit.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/excavator.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Activity Harian Unit </span>
                </a>
            </li> --}}
            <li class="side-nav-item">
                <a href="#" onclick="Swal.fire({ icon: 'info', title: 'Upps!', text: 'Maaf, menu ini belum berfungsi.', confirmButtonText: 'OK' })" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/radio.png" style="width: 20px"></i></span>
                    <span class="menu-text"> KLKH Tower </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="#" onclick="Swal.fire({ icon: 'info', title: 'Upps!', text: 'Maaf, menu ini belum berfungsi.', confirmButtonText: 'OK' })" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/exca.png" style="width: 20px"></i></span>
                    <span class="menu-text"> KLKH Unit </span>
                </a>
            </li>

            <li class="side-nav-title">Authentication</li>

             <li class="side-nav-item">
                <a href="{{ route('profile.index') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/profile.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Profile </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('logout') }}" class="side-nav-link">
                    <span class="menu-icon"><img src="{{ asset('dashboard') }}/assets/images/sidebar/logout.png" style="width: 20px"></i></span>
                    <span class="menu-text"> Logout </span>
                </a>
            </li>

        </ul>

        <div class="clearfix"></div>
    </div>
</div>
