@extends('layouts.master')
@section('content')
<!--breadcrumb-->
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
                        <li class="breadcrumb-item active" aria-current="page">Senarai Makmal Komputer</li>
                    </ol>
                </nav>
            </div>
        </div>
        @hasanyrole('Superadmin|Admin')
        <div class="col-12 col-md-3 d-flex justify-content-center justify-content-md-end mt-2 mt-md-0">
            <a href="{{ route('computer-lab.trash') }}">
                <button type="button" class="btn btn-primary mt-2 mt-lg-0">Senarai Rekod Dipadam</button>
            </a>
        </div>
        @endhasanyrole
    </div>
</div>
<!--end breadcrumb-->
<h6 class="mb-0 text-uppercase">Senarai Makmal Komputer</h6>
<hr />

<div class="card">
    <div class="card-body">

        <div class="d-lg-flex align-items-center mb-4 gap-3">
            <div class="position-relative">
                <form action="{{ route('computer-lab.search') }}" method="GET" id="searchForm"
                    class="d-lg-flex align-items-center gap-3">
                    <div class="input-group">
                        <input type="text" class="form-control rounded" placeholder="Carian..." name="search"
                            value="{{ request('search') }}" id="searchInput">

                        <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
                        <button type="submit" class="btn btn-primary ms-1 rounded" id="searchButton">
                            <i class="bx bx-search"></i>
                        </button>
                        <button type="button" class="btn btn-secondary ms-1 rounded" id="resetButton">
                            Reset
                        </button>
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
        <p class="text-danger mt-4 fst-italic">**Sebarang perubahan/pertambahan maklumat boleh maklum kepada moderator (Hazimah).</p>

        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover">
                <thead class="table-light text-center text-uppercase">
                    <tr>
                        <th>#</th>
                        <th>Kod</th>
                        <th>Makmal Komputer</th>
                        <th>Kampus</th>
                        <th>Pemilik</th>
                        <th>Bil. Komputer</th>
                        <th>Akaun</th>
                        <th>Status</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($computerLabList) > 0)
                    @foreach ($computerLabList as $computerLab)
                    <tr>
                        <td class="text-center">{{ ($computerLabList->currentPage() - 1) * $computerLabList->perPage() + $loop->iteration }}</td>
                        <td>{{ $computerLab->code }}</td>
                        <td>{{ $computerLab->name }}</td>
                        <td>{{ $computerLab->campus->name }}</td>
                        <td class="text-center">{{ $computerLab->pemilik->name }}</td>
                        <td class="text-center">{{ $computerLab->no_of_computer }}</td>
                        <td>
                            @if(count($computerLab->user_credentials) > 0)
                            @foreach ($computerLab->user_credentials as $index => $credential)
                            @if(count($computerLab->user_credentials) > 1)
                            <p class="badge bg-primary text-uppercase">Akaun {{ $index + 1 }}</p>
                            @endif
                            <p><strong>Nama Pengguna:</strong> {{ $credential['username'] }}</p>
                            <p><strong>Kata Laluan:</strong>
                                <span class="password" data-password="{{ $credential['password'] }}">****</span>
                                <button type="button" class="btn btn-link toggle-password" style="padding: 0; font-size: 1.1rem;">
                                    <i class="bx bx-show"></i>
                                </button>
                            </p>
                            @if($index
                            < count($computerLab->user_credentials) - 1)
                                <hr />
                            @endif
                            @endforeach
                            @else
                            <p><strong>Tiada Akaun</strong></p>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($computerLab->publish_status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @hasanyrole('Superadmin|Admin')
                            <a href="{{ route('computer-lab.edit', $computerLab->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kemaskini">
                                <i class="bx bxs-edit"></i>
                            </a>
                            @endhasanyrole
                            <a href="{{ route('computer-lab.show', $computerLab->id) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Papar">
                                <i class="bx bx-show"></i>
                            </a>
                            <a href="{{ route('computer-lab.history', $computerLab->id) }}"
                                class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="Sejarah">
                                <i class="bx bx-history"></i>
                            </a>
                            @hasanyrole('Superadmin|Admin')
                            <a type="button" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Padam">
                                <span class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $computerLab->id }}"><i class="bx bx-trash"></i></span>
                            </a>
                            @endhasanyrole
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <td colspan="10">Tiada rekod</td>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <span class="mr-2 mx-1">Jumlah rekod per halaman</span>
                <form action="{{ route('computer-lab.search') }}" method="GET" id="perPageForm"
                    class="d-flex align-items-center">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="type" value="{{ request('type') }}">
                    <input type="hidden" name="attendance" value="{{ request('attendance') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <select name="perPage" id="perPage" class="form-select form-select-sm"
                        onchange="document.getElementById('perPageForm').submit()">
                        <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                        <option value="20" {{ Request::get('perPage') == '20' ? 'selected' : '' }}>20</option>
                        <option value="30" {{ Request::get('perPage') == '30' ? 'selected' : '' }}>30</option>
                    </select>
                </form>
            </div>

            <div class="d-flex justify-content-end align-items-center">
                <span class="mx-2 mt-2 small text-muted">
                    Menunjukkan {{ $computerLabList->firstItem() }} hingga {{ $computerLabList->lastItem() }} daripada
                    {{ $computerLabList->total() }} rekod
                </span>
                <div class="pagination-wrapper">
                    {{ $computerLabList->appends([
                                'search' => request('search'),
                                'perPage' => request('perPage'),
                            ])->links('pagination::bootstrap-4') }}
                </div>
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
                Error: Data not available.
                @endisset
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                @isset($computerLab)
                <form class="d-inline" method="POST" action="{{ route('computer-lab.destroy', $computerLab->id) }}">
                    {{ method_field('delete') }}
                    {{ csrf_field() }}
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit the form on input change
        document.getElementById('searchInput').addEventListener('input', function() {
            document.getElementById('searchForm').submit();
        });

        // Reset form
        document.getElementById('resetButton').addEventListener('click', function() {
            // Redirect to the base route to clear query parameters
            window.location.href = "{{ route('computer-lab') }}";
        });
    });
</script>
@endsection