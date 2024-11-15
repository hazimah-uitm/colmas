<!DOCTYPE html>
<html>

<head>
    <title>Laporan Makmal Komputer {{ $currentMonth }}-{{ $currentYear }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm; /* Adds a little margin to the page */
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Align to the top to leave space at the bottom */
            height: 100%;
            box-sizing: border-box;
            overflow: hidden;
        }

        .container {
            width: 100%;
            max-width: 700px; /* Reduced max-width to fit within A4 */
            padding: 15px;
            box-sizing: border-box;
            margin: 0 auto;
        }

        .header,
        .footer {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1,
        .footer p {
            font-size: 18pt;
            margin: 5px 0;
        }

        .header h2 {
            font-size: 16pt;
            margin: 5px 0;
        }

        .content {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px; /* Reduced padding */
            text-align: left;
            font-size: 9pt;
        }

        th {
            background-color: #f4f4f4;
            text-align: center;
        }

        .campus-section h2 {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            background-color: #f7f7f7;
            padding: 8px 20px; /* Reduced padding */
            text-align: center;
            margin: 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 12px; /* Reduced padding */
            background-color: white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .footer p {
            font-size: 10pt;
            margin-top: 30px;
            text-align: center;
        }

        .dijana-oleh {
            font-size: 10pt;
            color: #555;
            margin-top: 15px;
            text-align: center;
        }

        hr {
            margin: 10px 0;
            border: none;
            border-top: 1px solid #ccc;
        }

        .footer hr {
            margin-top: 30px;
        }

        /* Optional: Scale content for better printing */
        @media print {
            body {
                transform: scale(0.85); /* Scales content to 85% of original size */
                transform-origin: top left;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Makmal Komputer</h1>
            <h2>{{ $currentMonth }} - {{ $currentYear }}</h2>
        </div>

        @foreach ($ownersWithLabs as $campusId => $labs)
        <div class="content">
            <div class="campus-section">
                <h2>{{ $labs->first()->campus->name ?? 'N/A' }}</h2>
            </div>
            <div class="card-body">
                @php
                $labsGroupedByOwner = $labs->groupBy('pemilik_id');
                @endphp
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%; background-color: #ddd; text-align: center">No.</th>
                            <th style="background-color: #ddd;">Computer Lab</th>
                            <th style="width: 25%; background-color: #ddd;">Pemilik</th>
                            <th style="width: 10%; background-color: #ddd; text-align: center">Total PC</th>
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
                            <td>{{ $lab->pemilik->name ?? 'N/A' }}</td>
                            <td style="text-align: center">{{ $lab->pc_count }}</td>
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach

        <div class="footer">
            <hr>
            <p class="dijana-oleh">Dijana oleh: {{ $username }}</p>
        </div>
    </div>
</body>

</html>
