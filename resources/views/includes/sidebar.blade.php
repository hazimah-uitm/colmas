<div class="sidebar-header">
    <div>
        <img src="{{ asset('public/assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
    </div>
    <div>
        <h4 class="logo-text">COLMAS</h4>
        <h6 class="logo-subtitle">Computer Lab Maintenance System</h6>
    </div>
    <div class="toggle-icon ms-auto" id="toggle-icon"><i class='bx bx-arrow-to-left'></i></div>
</div>

<!--navigation-->
<ul class="metismenu" id="menu">
    <li class="{{ Request::routeIs('home') ? 'mm-active' : '' }}">
        <a href="{{ route('home') }}">
            <div class="parent-icon"><i class='bx bx-home-circle'></i></div>
            <div class="menu-title">Dashboard</div>
        </a>
    </li>
    @hasanyrole('Superadmin|admin|Pegawai Penyemak')
    <li class="{{ Request::is('announcement*') ? 'mm-active' : '' }}">
        <a href="{{ route('announcement') }}">
            <div class="parent-icon"><i class='bx bxs-megaphone'></i>
            </div>
            <div class="menu-title">Makluman</div>
        </a>
    </li>
    @endhasanyrole

    @role('Superadmin')
    <li class="{{ Request::routeIs('activity-log') ? 'mm-active' : '' }}">
        <a href="{{ route('activity-log') }}">
            <div class="parent-icon"><i class='bx bx-history'></i></div>
            <div class="menu-title">Log Aktiviti</div>
        </a>
    </li>

    <li class="menu-label">Pengurusan Pengguna</li>

    <li class="{{ Request::is('user*') && !Request::is('user-role*') ? 'mm-active' : '' }}">
        <a href="{{ route('user') }}">
            <div class="parent-icon"><i class='bx bx-user-circle'></i></div>
            <div class="menu-title">Pengguna</div>
        </a>
    </li>

    <li class="{{ Request::is('user-role*') ? 'mm-active' : '' }}">
        <a href="{{ route('user-role') }}">
            <div class="parent-icon"><i class='bx bx-shield'></i></div>
            <div class="menu-title">Peranan Pengguna</div>
        </a>
    </li>
    @endrole

    <li class="menu-label">Pengurusan Maklumat</li>
    <li class="{{ Request::is('lab-management*') ? 'mm-active' : '' }}">
        <a href="{{ route('lab-management') }}">
            <div class="parent-icon"><i class='bx bx-wrench'></i>
            </div>
            <div class="menu-title">Rekod Selenggara</div>
        </a>
    </li>
    <li class="{{ request()->routeIs('report*', 'yearly-report*') ? 'mm-active' : '' }}">
        <a class="has-arrow">
            <div class="parent-icon"><i class='bx bxs-report'></i></div>
            <div class="menu-title">Laporan</div>
        </a>
        <ul>
            <li class="{{ request()->routeIs('report*') ? 'mm-active' : '' }}">
                <a href="{{ route('report') }}"><i class="bx bx-right-arrow-alt"></i>Laporan Selenggara</a>
            </li>
            <li class="{{ request()->routeIs('yearly-report*') ? 'mm-active' : '' }}">
                <a href="{{ route('yearly-report') }}"><i class="bx bx-right-arrow-alt"></i>Laporan Tahunan</a>
            </li>
            <li class="{{ request()->routeIs('computer-lab-report*') ? 'mm-active' : '' }}">
                <a href="{{ route('computer-lab-report') }}"><i class="bx bx-right-arrow-alt"></i>Senarai Makmal Komputer</a>
            </li>
        </ul>
    </li>
    <li class="{{ Request::is('schedule*') ? 'mm-active' : '' }}">
        <a href="{{ route('schedule') }}">
            <div class="parent-icon"><i class='bx bx-calendar'></i>
            </div>
            <div class="menu-title">Jadual Kuliah</div>
        </a>
    </li>

    <li class="menu-label">Tetapan</li>
    @hasanyrole('Superadmin|admin')
    <li class="{{ Request::is('campus*') ? 'mm-active' : '' }}">
        <a class="has-arrow" href="#">
            <div class="parent-icon"><i class='bx bx-location-plus'></i></div>
            <div class="menu-title">Lokasi</div>
        </a>
        <ul>
            <li class="{{ Request::is('campus*') ? 'mm-active' : '' }}">
                <a href="{{ route('campus') }}"><i class="bx bx-right-arrow-alt"></i>Kampus</a>
            </li>
        </ul>
    </li>
    @endhasanyrole

    <li class="{{ Request::is('computer-lab*') && !Request::is('computer-lab-report') ? 'mm-active' : '' }}">
        <a href="{{ route('computer-lab') }}">
            <div class="parent-icon"><i class='bx bx-desktop'></i>
            </div>
            <div class="menu-title">Makmal Komputer</div>
        </a>
    </li>
    <li class="{{ request()->routeIs('software*', 'work-checklist*', 'lab-checklist*') ? 'mm-active' : '' }}">
        <a class="has-arrow">
            <div class="parent-icon"><i class='bx bx-briefcase-alt'></i></div>
            <div class="menu-title">Proses Selenggara</div>
        </a>
        <ul>
            <li class="{{ request()->routeIs('software*') ? 'mm-active' : '' }}">
                <a href="{{ route('software') }}"><i class="bx bx-right-arrow-alt"></i>Perisian</a>
            </li>
            <li class="{{ request()->routeIs('work-checklist*') ? 'mm-active' : '' }}">
                <a href="{{ route('work-checklist') }}"><i class="bx bx-right-arrow-alt"></i>Proses Kerja</a>
            </li>
            <li class="{{ request()->routeIs('lab-checklist*') ? 'mm-active' : '' }}">
                <a href="{{ route('lab-checklist') }}"><i class="bx bx-right-arrow-alt"></i>Senarai Semak Makmal</a>
            </li>
        </ul>
    </li>

    @hasanyrole('Superadmin|admin')
    <li class="{{ Request::is('position*') ? 'mm-active' : '' }}">
        <a class="has-arrow" href="#">
            <div class="parent-icon"><i class="bx bx-cog"></i></div>
            <div class="menu-title">Tetapan Umum</div>
        </a>
        <ul>
            <li class="{{ Request::is('position*') ? 'mm-active' : '' }}">
                <a href="{{ route('position') }}"><i class="bx bx-right-arrow-alt"></i>Jawatan</a>
            </li>
        </ul>
    </li>
    @endhasanyrole
    @role('Superadmin')
    <li class="{{ Request::routeIs('logs.debug') ? 'mm-active' : '' }}">
        <a href="{{ route('logs.debug') }}">
            <div class="parent-icon"><i class='bx bxs-bug'></i></div>
            <div class="menu-title">Debug Log</div>
        </a>
    </li>
    @endrole
    <li class="{{ Request::is('manual') ? 'mm-active' : '' }}">
        <a href="{{ route('manual') }}" target="_blank" >
            <div class="parent-icon"><i class='bx bxs-bookmark'></i></div>
            <div class="menu-title">Manual Pengguna</div>
        </a>
    </li>
</ul>
<!--end navigation-->