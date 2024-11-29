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
        @hasanyrole('Superadmin|Admin')
        <div class="col-12 col-md-3 d-flex justify-content-center justify-content-md-end mt-2 mt-md-0">
            <a href="{{ route('computer-lab.edit', $computerLab->id) }}">
                <button type="button" class="btn btn-primary mt-2 mt-lg-0">Kemaskini Maklumat</button>
            </a>
        </div>
        @endhasanyrole
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
                        <th class="col-2">Kategori</th>
                        <td class="col-4">{{ ucwords(str_replace('_', ' ', $computerLab->category)) }}</td>
                    </tr>
                    <tr>
                        <th class="col-2">Kod Ruang</th>
                        <td class="col-4">{{ $computerLab->code }}</td>
                    </tr>
                    <tr>
                        <th class="col-2">Nama Ruang</th>
                        <td class="col-4">{{ $computerLab->name }}</td>
                    </tr>
                    <tr>
                        <th class="col-2">Lokasi</th>
                        <td class="col-4">{{ $computerLab->location }}</td>
                    </tr>
                    <tr>
                        <th class="col-2">Kampus</th>
                        <td class="col-4">{{ $computerLab->campus->name }}</td>
                    </tr>
                    <tr>
                        <th class="col-2">Pemilik</th>
                        <td class="col-4">{{ $computerLab->pemilik->name }}</td>
                    </tr>
                    <tr>
                        <th class="col-2">Akaun</th>
                        <td class="col-4">
                            @if(count($userCredentials) > 0)
                            @foreach ($userCredentials as $index => $credential)
                            @if(count($userCredentials) > 1)
                            <p class="badge bg-primary text-uppercase mb-1">Akaun {{ $index + 1 }}</p>
                            @endif
                            <p class="mb-0"><strong>Nama Pengguna:</strong> {{ $credential['username'] }}</p>
                            <p class="mb-0"><strong>Kata Laluan:</strong>
                                <span class="password" data-password="{{ $credential['password'] ?? '' }}">
                                    {!! $credential['password'] ? '****' : '<em>Tiada</em>' !!}
                                </span>
                                @if($credential['password'])
                                <button type="button" class="btn btn-link toggle-password" style="padding: 0; font-size: 1.1rem;">
                                    <i class="bx bx-show"></i>
                                </button>
                                @endif
                            </p>
                            @if($index
                            < count($userCredentials) - 1)
                                <hr class="my-1" />
                            @endif
                            @endforeach
                            @else
                            <p class="mb-0"><strong>Tiada Akaun</strong></p>
                            @endif
                        </td>

                    </tr>
                    <tr>
                        <th class="col-2">Perisian</th>
                        <td class="col-4">
                            <table style="width: 100%;">
                                <tr>
                                    @php $count = 0; @endphp
                                    @foreach ($computerLab->software as $software)
                                    <td style="width: 50%; padding-right: 5px;">
                                        <span style="margin-right: 5px;">&#10004;</span> <!-- Tick icon -->
                                        {{ $software->title }} {{ $software->version ? $software->version : '' }}
                                    </td>
                                    @php $count++; @endphp

                                    <!-- Start a new row after every two columns -->
                                    @if ($count % 2 == 0)
                                </tr>
                                <tr>
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
                        <th class="col-2">Bilangan Komputer</th>
                        <td class="col-4">{{ $computerLab->no_of_computer }}</td>
                    </tr>
                    <tr>
                        <th class="col-2">Status</th>
                        <td class="col-4">{{ $computerLab->publish_status }}</td>
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
            const passwordSpan = this.previousElementSibling; // The <span> containing the password
            const icon = this.querySelector('i'); // The <i> element for the icon
            if (passwordSpan.textContent === '****') {
                // Show the password and change the icon to hide
                passwordSpan.textContent = passwordSpan.getAttribute('data-password');
                icon.classList.replace('bx-show', 'bx-hide');
            } else {
                // Hide the password and change the icon to show
                passwordSpan.textContent = '****';
                icon.classList.replace('bx-hide', 'bx-show');
            }
        });
    });
</script>
@endsection