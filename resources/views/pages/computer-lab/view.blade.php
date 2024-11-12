@extends('layouts.master')

@section('content')
    <!-- Breadcrumb -->
    <div class="page-breadcrumb mb-3">
        <div class="row align-items-center">
            <!-- Breadcrumb Title and Navigation -->
            <div class="col-12 col-md-9 d-flex align-items-center">
                <div class="breadcrumb-title pe-3">Pengurusan Makmal Komputer</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('computer-lab') }}">Senarai Makmal Komputer</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Maklumat {{ $computerLab->name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-12 col-md-3 d-flex justify-content-center justify-content-md-end mt-2 mt-md-0">
                <a href="{{ route('computer-lab.edit', $computerLab->id) }}">
                    <button type="button" class="btn btn-primary mt-2 mt-lg-0">Kemaskini Maklumat</button>
                </a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->


    <h6 class="mb-0 text-uppercase">Maklumat {{ $computerLab->name }}</h6>
    <hr />

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Kod</th>
                            <td>{{ $computerLab->code }}</td>
                        </tr>
                        <tr>
                            <th>Nama Makmal Komputer</th>
                            <td>{{ $computerLab->name }}</td>
                        </tr>
                        <tr>
                            <th>Kampus</th>
                            <td>{{ $computerLab->campus->name }}</td>
                        </tr>
                        <tr>
                            <th>Pemilik</th>
                            <td>{{ $computerLab->pemilik->name }}</td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td>{{ $computerLab->username }}</td>
                        </tr>
                        <tr>
                            <th>Kata Laluan</th>
                            <td>
                                <div class="password-container d-flex align-items-center">
                                    <span class="password me-3" data-password="{{ $computerLab->password }}">****</span>
                                    <button class="btn btn-sm btn-outline-info toggle-password" type="button">
                                        <i class="bx bx-show"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Perisian</th>
                            <td colspan="2">
                                <table style="width: 100%;">
                                    <tr>
                                        @php $count = 0; @endphp
                                        @foreach ($softwareList as $software)
                                            @if (!empty($computerLab->software_id) && in_array($software->id, $computerLab->software_id))
                                                <td style="width: 50%;">
                                                    <span style="margin-right: 5px;">&#10004;</span> <!-- Tick icon -->
                                                    {{ $software->title }}
                                                </td>
                                                @php $count++; @endphp

                                                <!-- Start a new row after every two columns -->
                                                @if ($count % 2 == 0)
                                    </tr>
                                    <tr>
                                        @endif
                                        @endif
                                        @endforeach

                                        <!-- If there's an odd number of items, fill the last cell to keep layout intact -->
                                        @if ($count % 2 != 0)
                                            <td style="width: 50%;"></td>
                                        @endif
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th>Bilangan Komputer</th>
                            <td>{{ $computerLab->no_of_computer }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $computerLab->publish_status }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Wrapper -->
    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const passwordSpan = this.previousElementSibling;
                const icon = this.querySelector('i');
                if (passwordSpan.textContent === '****') {
                    passwordSpan.textContent = passwordSpan.getAttribute('data-password');
                    icon.classList.replace('bx-show', 'bx-hide');
                } else {
                    passwordSpan.textContent = '****';
                    icon.classList.replace('bx-hide', 'bx-show');
                }
            });
        });
    </script>
@endsection
