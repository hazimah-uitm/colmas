<!DOCTYPE html>
<html>

<head>
    <title>{{ $emailSubject }}</title>
</head>

<body>
    <p>Salam sejahtera {{ $pegawaiPenyemakName }},</p>

    <p>Disertakan Laporan Selenggara Berkala untuk
        <strong>{{ $labManagement->computerLab->name }}</strong>, <strong>{{ $campus }}</strong> bagi
        <strong>{{ $month }}/{{ $year }}</strong>.
    </p>

    <p>Laporan boleh disemak di <a href="{{ route('lab-management.check-detail', $labManagement->id) }}">SINI</a></p>

    <p>Sekian, terima kasih<br>
        {{ $submitterName }}</p>
</body>

</html>
