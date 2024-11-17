@extends('layouts.master')
@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb mb-3">
        <div class="row align-items-center">
            <!-- Breadcrumb Title and Navigation -->
            <div class="col-12 col-md-9 d-flex align-items-center">
                <div class="breadcrumb-title pe-3">Pengurusan Proses Kerja</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Senarai Proses Kerja</li>
                        </ol>
                    </nav>
                </div>
            </div>
            @hasanyrole('Superadmin|Admin')
                <div class="col-12 col-md-3 d-flex justify-content-center justify-content-md-end mt-2 mt-md-0">
                    <a href="{{ route('work-checklist.trash') }}">
                        <button type="button" class="btn btn-primary mt-2 mt-lg-0">Senarai Rekod Dipadam</button>
                    </a>
                </div>
            @endhasanyrole
        </div>
    </div>
    <!--end breadcrumb-->

    <h6 class="mb-0 text-uppercase">Senarai Proses Kerja</h6>
    <hr />
    <div class="card">
        <div class="card-body">
            <div class="d-lg-flex align-items-center mb-4 gap-3">
                <div class="position-relative">
                    <form action="{{ route('work-checklist.search') }}" method="GET" id="searchForm"
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
                        <a href="{{ route('work-checklist.create') }}" class="btn btn-primary radius-30 mt-2 mt-lg-0">
                            <i class="bx bxs-plus-square"></i> Tambah Proses Kerja
                        </a>
                    </div>
                @endhasanyrole
            </div>

            <!-- Note Section -->
            <p class="text-danger mt-4 fst-italic">**Sebarang perubahan/pertambahan maklumat boleh maklum kepada moderator
                (Hazimah).</p>

            <div class="table-responsive">
                <table class="table table-condensed table-striped table-bordered table-hover">
                    <thead class="table-light text-center text-uppercase">
                        <tr>
                            <th>#</th>
                            <th>Proses Kerja</th>
                            <th>Status</th>
                            @hasanyrole('Superadmin|Admin')
                                <th>Tindakan</th>
                            @endhasanyrole
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($workChecklists) > 0)
                            @foreach ($workChecklists as $workChecklist)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $workChecklist->title }}</td>
                                    <td class="text-center">
                                        @if ($workChecklist->publish_status == 'Aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    @hasanyrole('Superadmin|Admin')
                                        <td class="text-center">
                                            <a href="{{ route('work-checklist.edit', $workChecklist->id) }}"
                                                class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Kemaskini">
                                                <i class="bx bxs-edit"></i>
                                            </a>
                                            <a href="{{ route('work-checklist.show', $workChecklist->id) }}"
                                                class="btn btn-primary btn-sm" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="Papar">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a type="button" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                data-bs-title="Padam">
                                                <span class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $workChecklist->id }}"><i
                                                        class="bx bx-trash"></i></span>
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
                    <form action="{{ route('work-checklist.search') }}" method="GET" id="perPageForm"
                        class="d-flex align-items-center">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="perPage" id="perPage" class="form-select form-select-sm"
                            onchange="document.getElementById('perPageForm').submit()">
                            <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                            <option value="20" {{ Request::get('perPage') == '20' ? 'selected' : '' }}>20</option>
                            <option value="30" {{ Request::get('perPage') == '30' ? 'selected' : '' }}>30</option>
                        </select>
                    </form>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <span class="mx-2 mt-2 small text-muted">
                        Menunjukkan {{ $workChecklists->firstItem() }} hingga {{ $workChecklists->lastItem() }} daripada
                        {{ $workChecklists->total() }} rekod
                    </span>
                    <div class="pagination-wrapper">
                        {{ $workChecklists->appends([
                                'search' => request('search'),
                            ])->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @foreach ($workChecklists as $workChecklist)
        <div class="modal fade" id="deleteModal{{ $workChecklist->id }}" tabindex="-1" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Pengesahan Padam Rekod</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @isset($workChecklist)
                            Adakah anda pasti ingin memadam rekod <span style="font-weight: 600;">Proses Kerja
                                {{ $workChecklist->title }}</span>?
                        @else
                            Error: Campus data not available.
                        @endisset
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        @isset($workChecklist)
                            <form class="d-inline" method="POST"
                                action="{{ route('work-checklist.destroy', $workChecklist->id) }}">
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
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit the form on input change
            document.getElementById('searchInput').addEventListener('input', function() {
                document.getElementById('searchForm').submit();
            });

            // Reset form
            document.getElementById('resetButton').addEventListener('click', function() {
                // Redirect to the base route to clear query parameters
                window.location.href = "{{ route('work-checklist') }}";
            });
        });
    </script>
@endsection
