@extends('layouts.master')
@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Pengurusan Makmal Komputer</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Senarai Makmal Komputer</li>
            </ol>
        </nav>
    </div>
    @hasanyrole('Superadmin|Admin')
    <div class="ms-auto">
        <a href="{{ route('computer-lab.trash') }}">
            <button type="button" class="btn btn-primary mt-2 mt-lg-0">Senarai Rekod Dipadam</button>
        </a>
    </div>
    @endhasanyrole
</div>
<!--end breadcrumb-->
<h6 class="mb-0 text-uppercase">Senarai Makmal Komputer</h6>
<hr />

<div class="card">
    <div class="card-body">

        <div class="d-lg-flex align-items-center mb-4 gap-3">
            <div class="position-relative">
                <form action="{{ route('computer-lab.search') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control search-input" placeholder="Carian..." name="search">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary search-button">
                                <i class="bx bx-search"></i>
                            </button>
                        </span>
                    </div>
                </form>
            </div>
            @hasanyrole('Superadmin|Admin')
            <div class="ms-auto">
                <a href="{{ route('computer-lab.create') }}" class="btn btn-primary radius-30 mt-2 mt-lg-0">
                    <i class="bx bxs-plus-square"></i> Tambah Makmal Komputer
                </a>
            </div>
            @endhasanyrole
        </div>

        <!-- Note Section -->
        <p class="text-primary mt-4 fst-italic">Sebarang perubahan/pertambahan maklumat boleh maklum kepada moderator (Hazimah).</p>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kod</th>
                        <th>Makmal Komputer</th>
                        <th>Kampus</th>
                        <th>Pemilik</th>
                        <th>Bil. Komputer</th>
                        <th>Username</th>
                        <th>Kata Laluan</th>
                        @hasanyrole('Superadmin|Admin')
                        <th>Status</th>
                        <th>Tindakan</th>
                        @endhasanyrole
                    </tr>
                </thead>
                <tbody>
                    @if (count($computerLabList) > 0)
                    @foreach ($computerLabList as $computerLab)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $computerLab->code }}</td>
                        <td>{{ $computerLab->name }}</td>
                        <td>{{ $computerLab->campus->name }}</td>
                        <td>{{ $computerLab->pemilik->name }}</td>
                        <td>{{ $computerLab->no_of_computer }}</td>
                        <td>{{ $computerLab->username }}</td>
                        <td>
                            <div class="password-container d-flex align-items-center">
                                <span class="password me-3" data-password="{{ $computerLab->password }}">****</span>
                                <button class="btn btn-sm btn-outline-info toggle-password" type="button">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </td>
                        @hasanyrole('Superadmin|Admin')
                        <td>
                            @if ($computerLab->publish_status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('computer-lab.edit', $computerLab->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kemaskini">
                                <i class="bx bxs-edit"></i>
                            </a>
                            <a href="{{ route('computer-lab.show', $computerLab->id) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Papar">
                                <i class="bx bx-show"></i>
                            </a>
                                            <a href="{{ route('computer-lab.history', $computerLab->id) }}"
                                                class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="Sejarah">
                                                <i class="bx bx-history"></i>
                                            </a>
                            <a type="button" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Padam">
                                <span class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $computerLab->id }}"><i class="bx bx-trash"></i></span>
                            </a>
                        </td>
                        @endhasanyrole
                    </tr>
                    @endforeach
                    @else
                    <td colspan="4">Tiada rekod</td>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <span class="mr-2 mx-1">Jumlah rekod per halaman</span>
                <form action="{{ route('computer-lab') }}" method="GET" id="perPageForm">
                    <select name="perPage" id="perPage" class="form-select" onchange="document.getElementById('perPageForm').submit()">
                        <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                        <option value="20" {{ Request::get('perPage') == '20' ? 'selected' : '' }}>20</option>
                        <option value="30" {{ Request::get('perPage') == '30' ? 'selected' : '' }}>30</option>
                    </select>
                </form>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                <div class="mx-1 mt-2">{{ $computerLabList->firstItem() }} – {{ $computerLabList->lastItem() }} dari
                    {{ $computerLabList->total() }} rekod
                </div>
                <div>{{ $computerLabList->links() }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@foreach ($computerLabList as $computerLab)
<div class="modal fade" id="deleteModal{{ $computerLab->id }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Pengesahan Padam Rekod</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @isset($computerLab)
                Adakah anda pasti ingin memadam rekod <span style="font-weight: 600;">Makmal Komputer
                    {{ $computerLab->name }}</span>?
                @else
                Error: Campus data not available.
                @endisset
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                @isset($computerLab)
                <form class="d-inline" method="POST" action="{{ route('computer-lab.destroy', $computerLab->id) }}">
                    @method('DELETE')
                    @csrf
                    <button type="submit" class="btn btn-danger">Padam</button>
                </form>
                @endisset
            </div>
        </div>
    </div>
</div>
@endforeach
<!--end page wrapper -->

<script>
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function () {
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