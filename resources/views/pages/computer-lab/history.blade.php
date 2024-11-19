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
                            <li class="breadcrumb-item"><a href="{{ route('computer-lab') }}">Senarai Makmal Komputer</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Sejarah Makmal Komputer</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <h6 class="mb-0 text-uppercase">Sejarah Makmal Komputer</h6>
    <hr />
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                 <table class="table table-sm table-striped table-hover">
                    <thead class="table-light text-center text-uppercase">
                        <tr>
                            <th>#</th>
                            <th>Bulan/Tahun</th>
                            <th>Kod</th>
                            <th>Nama Makmal</th>
                            <th>Bil. Komputer</th>
                            <th>Pemilik</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($historyList) > 0)
                            @foreach ($historyList as $history)
                                <tr>
                                    <td class="text-center">{{ ($historyList->currentPage() - 1) * $historyList->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $history->month_year->format('F Y') }}</td>
                                    <td>{{ $history->code }}</td>
                                    <td>{{ $history->name }}</td>
                                    <td class="text-center">{{ $history->pc_no }}</td>
                                    <td class="text-center">{{ $history->pemilik->name ?? 'Unknown' }}</td>
                                    <td class="text-center">
                                        @if ($history->publish_status)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ ucfirst($history->action) }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8">Tiada rekod sejarah</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="mr-2 mx-1">Jumlah rekod per halaman</span>
                    <form action="{{ route('computer-lab.history', ['id' => $computerLab->id]) }}" method="GET"
                        id="perPageForm">
                        <select name="perPage" id="perPage" class="form-select"
                            onchange="document.getElementById('perPageForm').submit()">
                            <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                            <option value="20" {{ Request::get('perPage') == '20' ? 'selected' : '' }}>20</option>
                            <option value="30" {{ Request::get('perPage') == '30' ? 'selected' : '' }}>30</option>
                        </select>
                    </form>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <div class="mx-1 mt-2">{{ $historyList->firstItem() }} – {{ $historyList->lastItem() }} dari
                        {{ $historyList->total() }} rekod
                    </div>
                    <div>{{ $historyList->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
