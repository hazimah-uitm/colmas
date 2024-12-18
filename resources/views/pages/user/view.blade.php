@extends('layouts.master')

@section('content')
    <!-- Breadcrumb -->
    <div class="page-breadcrumb mb-3">
        <div class="row align-items-center">
            <!-- Breadcrumb Title and Navigation -->
            <div class="col-12 col-md-9 d-flex align-items-center">
                <div class="breadcrumb-title pe-3">Pengurusan Pengguna</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('user') }}">Senarai Pengguna</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Maklumat {{ ucfirst($user->name) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-12 col-md-3 d-flex justify-content-center justify-content-md-end mt-2 mt-md-0">
                <a href="{{ route('user.edit', $user->id) }}">
                    <button type="button" class="btn btn-primary mt-2 mt-lg-0">Kemaskini Maklumat</button>
                </a>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <h6 class="mb-0 text-uppercase">Maklumat {{ ucfirst($user->name) }}</h6>
    <hr />

    <div class="container">
    <div class="main-body">
        <!-- Profile Layout -->
        <div class="row">
            <!-- Sidebar (User Info) -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center">
                            <!-- User Image -->
                            <img src="{{ $user->profile_image ? asset('public/storage/' . $user->profile_image) : 'https://via.placeholder.com/150' }}"
                                alt="Profile Image" class="rounded-circle p-1 bg-primary" width="150" height="150">
                            <!-- User Name and Position -->
                            <div class="d-flex flex-column align-items-center text-center">
                                <h5 class="mt-3">{{ $user->name }}</h5>
                                <p class="text-muted">{{ $user->position->title ?? 'Position' }}</p>
                            </div>
                        </div>
                        <hr class="my-4">
                        <table class="table table-borderless mt-2">
                            <tr class="border-bottom">
                                <th>Email</th>
                                <td>{{ $user->email ?? '-' }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <th>Staff ID</th>
                                <td>{{ $user->staff_id ?? '-' }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <th>Kampus</th>
                                <td>
                                    @foreach ($user->campus as $campus)
                                    {{ $campus->name }}<br>
                                    @endforeach
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <th>No. Telefon Pejabat</th>
                                <td>{{ $user->office_phone_no ?? '-' }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <th>Peranan</th>
                                <td>
                                    @if ($user->roles->count() === 1)
                                    {{ ucwords(str_replace('-', ' ', $user->roles->first()->name ?? '-')) }}
                                    @else
                                    <ul class="list-unstyled">
                                        @foreach ($user->roles as $role)
                                        <li><span
                                                class="badge bg-secondary">{{ ucwords(str_replace('-', ' ', $role->name ?? '-')) }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>{{ $user->publish_status ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Main Content -->
    </div>
</div>
    <!-- End Page Wrapper -->
@endsection
