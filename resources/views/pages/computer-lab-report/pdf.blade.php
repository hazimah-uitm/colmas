<!DOCTYPE html>
<html>

<head>
    <title>Laporan Makmal Komputer {{ $currentMonthName }}-{{ $currentYear }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm 10mm 20mm 10mm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-sizing: border-box;
            overflow: hidden;
        }

        .campus-section h2 {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            background-color: #f4f4f4;
            padding: 10px 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 0;
            text-transform: uppercase;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .header img {
            width: 240px;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            color: #000 !important;
        }

        th,
        td {
            text-align: justify;
            font-size: 9pt;
            border: 1px solid #ddd;
            word-wrap: break-word;
            /* Allow wrapping of long text */
        }

        th {
            padding: 6px;
            background-color: #d6dbdf;
            font-weight: bold;
            text-align: center;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        @media print {
            body {
                transform: scale(0.85);
                transform-origin: top left;
            }
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header">
        <!-- UiTM Logo -->
        <img src="{{ $logoBase64 }}" alt="UiTM Logo">
        <!-- Report Title -->
        <h1>Laporan Makmal Komputer {{ $currentMonthName }} {{ $currentYear }}</h1>
    </div>

    <!-- Report Content -->
    @foreach ($ownersWithLabs as $campusId => $labs)
        <div class="campus-section">
            <h2>{{ $labs->first()->campus->name ?? 'N/A' }}</h2>
            @php
                $labsGroupedByOwner = $labs->groupBy('pemilik_id');
            @endphp
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">No.</th>
                        <th>Nama Ruang</th>
                        <th style="width: 25%;">Pemilik</th>
                        <th style="width: 10%; text-align:right">Total PC</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $counter = 1;
                        $totalPCs = 0;
                    @endphp
                    @foreach ($labsGroupedByOwner as $ownerId => $ownerLabs)
                        @foreach ($ownerLabs as $labIndex => $lab)
                            <tr>
                                <td style="text-align: center">{{ $counter++ }}</td>
                                <td>{{ $lab->name }}</td>
                                <td style="text-align: center">{{ $lab->pemilik->name ?? 'N/A' }}</td>
                                <td style="text-align: right;">{{ $lab->pc_count }}</td>
                            </tr>
                            @php
                            $totalPCs += $lab->pc_count; // Accumulate total PCs
                            @endphp
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot class="table-light text-center text-uppercase">
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Jumlah PC</strong></td>
                        <td style="text-align: right;">
                            <strong>{{ $totalPCs }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endforeach
</body>

</html>
