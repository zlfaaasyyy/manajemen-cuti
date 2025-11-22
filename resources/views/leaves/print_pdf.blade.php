<!DOCTYPE html>
<html>
<head>
    <title>Surat Izin Cuti - {{ $leaveRequest->user->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12pt; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid black; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; font-weight: bold; }
        .header p { margin: 0; font-size: 10pt; }
        .content { margin-top: 20px; }
        .table-data { width: 100%; margin-top: 10px; border-collapse: collapse; }
        .table-data td { padding: 5px; vertical-align: top; }
        .ttd-area { margin-top: 50px; width: 100%; }
        .ttd-box { width: 30%; float: right; text-align: center; }
        .ttd-box-left { width: 30%; float: left; text-align: center; }
        .clear { clear: both; }
    </style>
</head>
<body>

    <div class="header">
        <h1>PT. ABADI NAN JAYA</h1>
        <p>Jl. Sulawesi No. 123, Sulawesi Selatan | Telp: (021) 555-1234</p>
        <p>Email: admin@kantor.com | Website: www.abadinanjaya.com</p>
    </div>

    <div class="content">
        <h3 style="text-align: center; text-decoration: underline;">SURAT IZIN CUTI</h3>
        <p style="text-align: center;">Nomor: CUTI/{{ date('Y') }}/{{ str_pad($leaveRequest->id, 4, '0', STR_PAD_LEFT) }}</p>

        <p>Yang bertanda tangan di bawah ini, HRD PT. Abadi Nan Jaya menerangkan bahwa:</p>

        <table class="table-data">
            <tr>
                <td width="30%">Nama</td>
                <td width="5%">:</td>
                <td><strong>{{ $leaveRequest->user->name }}</strong></td>
            </tr>
            <tr>
                <td>Jabatan / Divisi</td>
                <td>:</td>
                <td>{{ ucfirst($leaveRequest->user->role) }} / {{ $leaveRequest->user->divisi->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>:</td>
                <td>{{ $leaveRequest->user->email }}</td>
            </tr>
        </table>

        <p>Telah diberikan izin untuk melaksanakan <strong>{{ strtoupper($leaveRequest->jenis_cuti) }}</strong> selama <strong>{{ $leaveRequest->total_hari }} hari kerja</strong>, terhitung mulai:</p>

        <table class="table-data">
            <tr>
                <td width="30%">Tanggal Mulai</td>
                <td width="5%">:</td>
                <td>{{ \Carbon\Carbon::parse($leaveRequest->tanggal_mulai)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Tanggal Selesai</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($leaveRequest->tanggal_selesai)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Alasan</td>
                <td>:</td>
                <td>{{ $leaveRequest->alasan }}</td>
            </tr>
        </table>

        <p>Demikian surat izin ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd-area">
        <div class="ttd-box-left">
            <p>Diajukan Oleh,</p>
            <br><br><br>
            <p><strong>{{ $leaveRequest->user->name }}</strong></p>
            <p>Karyawan</p>
        </div>

        <div class="ttd-box">
            <p>Jakarta, {{ date('d F Y') }}</p>
            <p>Disetujui Oleh,</p>
            <br><br><br>
            <p><strong>HRD</strong></p>
            <p>PT. Abadi Nan Jaya</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>