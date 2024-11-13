@extends('layouts.app')

@section('content')
<!--wrapper-->
<div class="wrapper">
    <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
        <div class="container-fluid">
            <div class="text-center">
                <div class="d-flex align-items-center justify-content-center flex-column flex-md-row mb-4">
                    <img src="{{ asset('public/assets/images/putih.png') }}" class="logo-icon-login" alt="logo icon">
                    <div class="ms-3">
                        <h4 class="logo-text-login mb-0">COLMAS</h4>
                        <h6 class="logo-subtitle-login mb-0">Computer Lab Management System</h6>
                    </div>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
                <div class="col mx-auto">
                    <div class="card shadow-none">
                        <div class="card-body">
                            <div class="border p-4 rounded">
                                <div class="text-center mb-4">
                                    <h3 class="">Link Tamat Tempoh</h3>
                                </div>
                                <div class="text-center mb-4">
                                    <p>Maaf, pautan reset kata laluan anda telah tamat tempoh.</p>
                                    <p>Sila buat permohonan reset kata laluan baru.</p>
                                </div>
                                <div class="col-12 text-center">
                                    <a href="{{ route('password.request') }}" class="btn btn-primary">
                                        <i class="bx bxs-lock-open"></i> Permohonan Reset Baru
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end wrapper-->
@endsection
