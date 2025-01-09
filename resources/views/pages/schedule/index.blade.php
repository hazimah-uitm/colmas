@extends('layouts.master')
@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb mb-3">
    <div class="row align-items-center">
        <!-- Breadcrumb Title and Navigation -->
        <div class="col-12 col-md-9 d-flex align-items-center">
            <div class="breadcrumb-title pe-3">Pengurusan Jadual Kuliah</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Senarai Jadual Kuliah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!--end breadcrumb-->
<h6 class="mb-0 text-uppercase">Senarai Jadual Kuliah</h6>
<hr />

<div class="card">
    <div class="card-body">

        <div class="d-lg-flex align-items-center mb-4 gap-3">
            <div class="position-relative">
                <form action="{{ route('schedule.search') }}" method="GET" id="searchForm"
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
        </div>

        <!-- Note Section -->
        <p class="text-danger mt-4 fst-italic">**Sebarang perubahan/pertambahan maklumat boleh maklum kepada moderator (Hazimah).</p>

        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover">
                <thead class="table-light text-center text-uppercase">
                    <tr>
                        <th>#</th>
                        <th>Kategori</th>
                        <th>Kod Ruang</th>
                        <th>Nama Ruang</th>
                        <th>Kampus</th>
                        <th>Pemilik</th>
                        <th>Status</th>
                        <th>Jadual Kuliah</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($computerLabList) > 0)
                    @foreach ($computerLabList as $computerLab)
                    <tr>
                        <td class="text-center">{{ ($computerLabList->currentPage() - 1) * $computerLabList->perPage() + $loop->iteration }}</td>
                        <td class="text-center" style="word-wrap: break-word; white-space: normal;">{{ ucwords(str_replace('_', ' ', $computerLab->category)) }}</td>
                        <td>{{ $computerLab->code ?? '-'}}</td>
                        <td class="text-center" style="word-wrap: break-word; white-space: normal;">{{ $computerLab->name }}</td>
                        <td class="text-center">{{ $computerLab->campus->name }}</td>
                        <td class="text-center" style="word-wrap: break-word; white-space: normal;">{{ $computerLab->pemilik->name }}</td>
                        <td class="text-center">
                            @if ($computerLab->publish_status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a type="button" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                data-bs-title="Papar">
                                <span class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#showModal{{ $computerLab->id }}"><i
                                        class="bx bx-show"></i></span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <td colspan="8">Tiada rekod</td>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <span class="mr-2 mx-1">Jumlah rekod per halaman</span>
                <form action="{{ route('schedule.search') }}" method="GET" id="perPageForm"
                    class="d-flex align-items-center">
                    <input type="hidden" name="search" value="{{ request('search') }}">
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

<!-- Show Modal -->
@foreach ($computerLabList as $computerLab)
<div class="modal fade" id="showModal{{ $computerLab->id }}" tabindex="-1"
    aria-labelledby="showModalLabel{{ $computerLab->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="showModalLabel{{ $computerLab->id }}">
                    {{ $computerLab->name }}
                </h5>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="Tutup"></button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <table class="table table-borderless table-striped">
                    <tbody>
                        <tr>
                            <th scope="row">Kategori</th>
                            <td>{{ ucwords(str_replace('_', ' ', $computerLab->category)) }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Kod Ruang</th>
                            <td>{{ $computerLab->code }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Nama Ruang</th>
                            <td>{{ $computerLab->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Kampus</th>
                            <td>{{ $computerLab->campus->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Pemilik</th>
                            <td>{{ $computerLab->pemilik->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Status</th>
                            <td> @if ($computerLab->publish_status == 'Aktif')
                                <span class="badge bg-success">Aktif</span>
                                @else
                                <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Jadual Kuliah</th>
                            <td>
                                @if (!is_null($computerLab->jadual_kuliah))
                                <img src="{{ asset('public/storage/' . $computerLab->jadual_kuliah) }}" alt="Current Schedule" class="img-fluid" style="max-height: 1000px;">
                                @else
                                <span>Tiada jadual terkini</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
            window.location.href = "{{ route('schedule') }}";
        });
    });
</script>
@endsection