<!-- Optional button section -->
<!-- <div class="d-flex justify-content-end mb-2">
    @if (!in_array($labManagement->status, ['dihantar', 'telah_disemak']))
        <a href="{{ route('lab-management.maintenance-records.create', ['labManagement' => $labManagement->id]) }}"
            class="btn btn-primary radius-30 mt-2 mt-lg-0 ms-2">
            <i class="bx bxs-plus-square"></i> Tambah Rekod PC
        </a>
    @endif
</div> -->

<!-- Lab Management Info -->
<table class="table table-borderless mb-2">
    <tr>
        <th class="text-uppercase">Nama Pemilik</th>
        <td class="text-uppercase">{{ $labManagement->computerLab->pemilik->name ?? '-' }}</td>
    </tr>
    <tr>
        <th class="text-uppercase">Makmal Komputer</th>
        <td class="mb-3 text-uppercase">
            {{ $labManagement->computerLab->name ?? '-' }},
            {{ $labManagement->computerLab->campus->name ?? '-' }}
        </td>
    </tr>
</table>

<!-- Date Info -->
<table class="table table-borderless mb-2">
    <tr>
        <th>Bulan</th>
        <th>Tahun</th>
        <th>Tarikh/Masa Mula</th>
        <th>Tarikh/Masa Tamat</th>
    </tr>
    <tr>
        <td>{{ $month ?? '-' }}</td>
        <td>{{ $year ?? '-' }}</td>
        <td>{{ $labManagement->start_time ?? '-' }}</td>
        <td>{{ $labManagement->end_time ?? '-' }}</td>
    </tr>
</table>

<!-- Computer Stats -->
<table class="table mb-5">
    <thead class="bg-light">
        <tr>
            <th>Bil. Keseluruhan Komputer</th>
            <th>Bil. Komputer Telah Diselenggara</th>
            <th>Bil. Komputer Rosak</th>
            <th>Bil. Komputer Belum Diselenggara</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $labManagement->computer_no ?? '-' }}</td>
            <td>{{ $labManagement->pc_maintenance_no ?? '-' }}</td>
            <td>{{ $labManagement->pc_damage_no ?? '-' }}</td>
            <td>{{ $labManagement->pc_unmaintenance_no ?? '-' }}</td>
        </tr>
    </tbody>
</table>
