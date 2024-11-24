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

        .header {
            text-align: center;
            margin-bottom: 30px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        th,
        td {
            padding: 6px;
            text-align: left;
            font-size: 9pt;
            border: 1px solid #ddd;
        }

        th {
            background-color: #abb2b9;
            font-weight: bold;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        h4 {
            font-size: 14pt;
            margin-top: 10px;
            margin-bottom: 5px;
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
        <h4>{{ $labs->first()->campus->name ?? 'N/A' }}</h4>
        @php
        $labsGroupedByOwner = $labs->groupBy('pemilik_id');
        @endphp
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No.</th>
                    <th>Makmal Komputer</th>
                    <th style="width: 25%;">Pemilik</th>
                    <th style="width: 10%;">Total PC</th>
                </tr>
            </thead>
            <tbody>
                @php
                $counter = 1;
                @endphp
                @foreach ($labsGroupedByOwner as $ownerId => $ownerLabs)
                @foreach ($ownerLabs as $labIndex => $lab)
                <tr>
                    <td style="text-align: center">{{ $counter++ }}</td>
                    <td>{{ $lab->name }}</td>
                    <td style="text-align: center">{{ $lab->pemilik->name ?? 'N/A' }}</td>
                    <td style="text-align: center">{{ $lab->pc_count }}</td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>

</html>