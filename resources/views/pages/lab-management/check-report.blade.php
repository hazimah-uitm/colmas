    @foreach ($labManagementList as $labManagement)
    <form id="checkForm{{ $labManagement->id }}" action="{{ route('lab-management.check', $labManagement->id) }}"
        method="POST" class="d-inline">
        {{ csrf_field() }}
        <div class="modal fade" id="checkModal{{ $labManagement->id }}" tabindex="-1" aria-labelledby="checkModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-uppercase" id="checkModalLabel">Laporan Semakan Makmal Komputer
                        </h5>
                        <a href="{{ route('lab-management.check-detail', $labManagement->id) }}"
                            class="btn btn-primary btn-sm ms-2" data-bs-toggle="tooltip" data-bs-placement="bottom"
                            title="Lihat Keterangan">
                            <i class="bx bx-show"></i>
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th class="mb-3 text-uppercase">Nama Pemilik</th>
                                    <td class="mb-3 text-uppercase">
                                        {{ $labManagement->computerLab->pemilik->name ?? '-'}}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="mb-3 text-uppercase">Makmal Komputer</th>
                                    <td class="mb-3 text-uppercase">{{ $labManagement->computerLab->name ?? '-'}},
                                        {{ $labManagement->computerLab->campus->name ?? '-'}}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Masa Mula</th>
                                    <th>Masa Tamat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $labManagement->month }}</td>
                                    <td>{{ $labManagement->year }}</td>
<td>{{ \Carbon\Carbon::parse($labManagement->start_time)->format('d-m-Y h:i A') }}</td>
<td>{{ $labManagement->end_time ? \Carbon\Carbon::parse($labManagement->end_time)->format('d-m-Y h:i A') : '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                        <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 25%" class="text-center">Bil. Keseluruhan Komputer</th>
                                    <th style="width: 25%" class="text-center">Bil. Komputer Telah Diselenggara</th>
                                    <th style="width: 25%" class="text-center">Bil. Komputer Rosak/Keluar</th>
                                    <th style="width: 25%" class="text-center">Bil. Komputer Belum Diselenggara</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">{{ $labManagement->computer_no ?? '-' }}</td>
                                    <td class="text-center">{{ $labManagement->pc_maintenance_no ?? '-' }}</td>
                                    <td class="text-center">{{ $labManagement->pc_damage_no ?? '-' }}</td>
                                    <td class="text-center">{{ $labManagement->pc_unmaintenance_no ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3 text-uppercase">Senarai Semak Makmal</h6>
                                <table class="table">
                                    <thead class="bg-light">
                                        <tr>
                                            @foreach ($labCheckList as $labCheck)
                                            <th class="text-center">{{ $labCheck->title }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach ($labCheckList as $labCheck)
                                            <td class="text-center">
                                                @php
                                                $isSelected =
                                                !empty($labManagement->lab_checklist_id) &&
                                                in_array(
                                                $labCheck->id,
                                                $labManagement->lab_checklist_id);
                                                @endphp
                                                @if ($isSelected)
                                                <span class="tick-icon">&#10004;</span>
                                                @else
                                                <span class="empty-icon" style="color: red">&#10006;</span>
                                                @endif
                                            </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3 text-uppercase">Senarai Rekod PC Diselenggara/Rosak</h6>
                            <table class="table table-bordered table-sm">
                                    <thead class="bg-light">
                                        <tr>
                                        <th style="width: 5%" rowspan="2" class="text-center">No.</th>
                                            <th style="width: 10%" class="text-center">Nama Komputer</th>
                                            <th style="width: 30%" class="text-center" colspan="{{ count($workChecklists) }}">Kerja Selenggara</th>
                                            <th style="width: 15%" rowspan="2" class="text-center">No. Aduan</th>
                                            <th style="width: 40%" rowspan="2" class="text-center">Catatan</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">IP Address</th>
                                            @foreach ($workChecklists as $workChecklist)
                                            <th class="text-center">{{ $workChecklist->title }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($labManagement->maintenanceRecords as $maintenanceRecord)
                                        @php
                                        $noAduan = '-';

                                        // Check the entryOption for each maintenance record
                                        if ($maintenanceRecord->entry_option == 'manual') {
                                        $noAduan = $maintenanceRecord->aduan_unit_no ?? '-';
                                        } elseif ($maintenanceRecord->entry_option == 'pc_rosak') {
                                        $noAduan = $maintenanceRecord->vms_no ?? '-';
                                        }
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $maintenanceRecord->computer_name }} <br>
                                                {{ $maintenanceRecord->ip_address }}
                                            </td>
                                            @if ($maintenanceRecord->entry_option == "pc_rosak")
                                            <td class="text-center" colspan="{{ count($workChecklists) }}">Komputer Bermasalah</td>
                                            @elseif ($maintenanceRecord->entry_option == "pc_keluar")
                                            <td class="text-center" colspan="{{ count($workChecklists) }}">PC dibawa keluar pada {{ $maintenanceRecord->keluar_date }} <br> ke {{ $maintenanceRecord->keluar_location }}</td>
                                            @else
                                            @foreach ($workChecklists as $workChecklist)
                                            <td class="text-center">
                                                @php
                                                $isSelected = in_array(
                                                $workChecklist->id,
                                                $maintenanceRecord->work_checklist_id);
                                                @endphp
                                                @if ($isSelected)
                                                <span class="tick-icon">&#10004;</span>
                                                @else
                                                <span class="empty-icon"
                                                    style="color: red;">&#10006;</span>
                                                @endif
                                            </td>
                                            @endforeach
                                            @endif
                                            <td class="text-center">{{ $noAduan }}</td>
                                            <td class="text-center" style="word-wrap: break-word; white-space: normal;">
    <span class="badge text-dark mb-1" style="font-size: 12px; background-color: yellow">
        {{ $maintenanceRecord->computer_name }} selesai pada: 
        {{ $maintenanceRecord->created_at->format('d-m-Y') }} | 
        {{ $maintenanceRecord->created_at->format('h:i A') }}
    </span>
    <br>
    {!! nl2br(e($maintenanceRecord->remarks ?? '-')) !!}
</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="{{ count($workChecklists) + 3 }}">Tiada rekod</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3 text-uppercase">Senarai Perisian</h6>
                                <div class="row">
                                    @foreach ($labManagement->computerLab->software as $software)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">&#10004;</span>
                                            {{ $software->title }} {{ $software->version }}
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 50%">Catatan/Ulasan Pemilik</th>
                                <td style="width: 50%">{!! nl2br(e($labManagement->remarks_submitter ?? '-')) !!}</td>
                            </tr>
                            <tr>
                                <th style="width: 50%">Catatan/Ulasan Pegawai Penyemak</th>
                                <td style="width: 50%">
                                    <div class="mb-3">
                                        <textarea class="form-control {{ $errors->has('remarks_checker') ? 'is-invalid' : '' }}" id="remarks_checker"
                                            name="remarks_checker" rows="3">{{ old('remarks_checker') ?? ($labManagement->remarks_checker ?? '') }}</textarea>
                                        @if ($errors->has('remarks_checker'))
                                        <div class="invalid-feedback">
                                            @foreach ($errors->get('remarks_checker') as $error)
                                            {{ $error }}
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th style="width: 50%">Status</th>
                                <td style="width: 50%">{{ str_replace('_', ' ', ucwords(strtolower($labManagement->status))) }}</td>
                            </tr>
                            <tr>
                                <th style="width: 50%">Dihantar oleh</th>
                                <td style="width: 50%">{{ $labManagement->submittedBy->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th style="width: 50%">Dihantar pada</th>
                                <td style="width: 50%">{{ $labManagement->submitted_at ?? '-' }}</td>
                            <tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <input type="hidden" name="checked_by" value="{{ auth()->id() }}">
                        <input type="hidden" name="checked_at" value="{{ now() }}">
                        <button type="button" class="btn btn-success"
                            onclick="checkForm('{{ $labManagement->id }}')">Semak</button>

                    </div>
                </div>
            </div>
        </div>
    </form>
    @endforeach