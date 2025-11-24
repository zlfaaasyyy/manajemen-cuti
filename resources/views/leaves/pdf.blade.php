<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Izin Cuti - {{ $leave->user->name }}</title>
    <!-- CSS dioptimalkan untuk PDF agar tidak terpotong -->
    <style>
        body { 
            font-family: 'Arial', sans-serif; 
            line-height: 1.5;
            margin: 0.5cm; 
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 0; /* Container tidak perlu padding lagi, cukup body margin */
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px; 
            color: #473C33;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 11px;
        }
        .content {
            font-size: 11pt;
        }
        .details {
            margin: 15px 0;
            border-collapse: collapse;
            width: 100%;
        }
        .details th, .details td {
            padding: 5px 0;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .details th {
            width: 35%; /* Kolom Th dibuat sedikit lebih kecil */
            font-weight: bold;
            color: #333;
        }
        .date {
            text-align: right;
            margin-bottom: 20px;
            font-size: 11pt;
        }
        
        /* Style untuk Catatan HRD */
        .info-box {
            border: 1px solid #ABC270;
            background-color: #f7fff0;
            padding: 10px;
            margin-top: 20px;
            font-size: 10pt;
            border-radius: 5px;
        }

        /* Layout Tanda Tangan */
        .signature-area {
            margin-top: 50px;
            width: 100%;
            clear: both;
            overflow: hidden; 
        }
        .signature-box {
            width: 45%; 
            text-align: center;
            padding-top: 10px;
        }
        .signature-leader {
            float: left;
        }
        .signature-hrd {
            float: right;
        }
        .ttd-space {
            height: 50px; /* Ruang TTD */
            border-bottom: 1px dashed #aaa;
            margin-bottom: 5px;
        }
        .clear { clear: both; }

    </style>
</head>
<body>
    <div class="container">
        
        <div class="header">
            <h1>SURAT IZIN CUTI KARYAWAN</h1>
            <p>PT. ABADI NAN JAYA</p>
        </div>

        <div class="date">
            Jakarta, {{ now()->format('d F Y') }}
        </div>

        <div class="content">
            <p>Kepada Yth. Bapak/Ibu di Tempat,</p>
            <p>Berdasarkan pengajuan cuti yang telah diverifikasi dan disetujui, bersama ini kami menerangkan bahwa:</p>

            <table class="details">
                <tr>
                    <th>Nama Karyawan</th>
                    <td>: {{ $leave->user->name }}</td>
                </tr>
                <tr>
                    <th>Nomor Induk Pegawai (NIP)</th>
                    <td>: {{ $leave->user->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Divisi</th>
                    <td>: {{ $leave->user->divisi->nama ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Jenis Cuti</th>
                    <td style="color: #FDA769; font-weight: bold;">: {{ strtoupper($leave->jenis_cuti) }}</td>
                </tr>
            </table>

            <p style="margin-top: 15px;">Diberikan izin untuk mengambil cuti terhitung mulai:</p>
            
            <table class="details">
                <tr>
                    <th>Tanggal Mulai</th>
                    <td>: {{ \Carbon\Carbon::parse($leave->tanggal_mulai)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <th>Tanggal Selesai</th>
                    <td>: {{ \Carbon\Carbon::parse($leave->tanggal_selesai)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <th>Total Hari Cuti</th>
                    <td style="color: #473C33; font-weight: bold;">: {{ $leave->total_hari }} (Hari Kerja)</td>
                </tr>
            </table>

            <p style="margin-top: 15px;">
                Cuti ini diajukan dengan alasan: <strong>{{ $leave->alasan }}</strong>. Karyawan yang bersangkutan dapat dihubungi melalui nomor darurat <strong>{{ $leave->nomor_darurat }}</strong> atau di alamat <strong>{{ $leave->alamat_selama_cuti }}</strong> selama periode cuti.
            </p>
            
            <!-- Menggunakan Info Box yang lebih ringkas -->
            <div class="info-box">
                <p style="margin: 0; font-weight: bold; color: #ABC270;">Catatan HRD:</p>
                <p style="margin: 3px 0 0 0;">{{ $leave->catatan_hrd ?? 'Disetujui tanpa catatan khusus.' }}</p>
            </div>

            <p style="margin-top: 20px;">
                Demikian surat izin cuti ini dibuat untuk digunakan sebagaimana mestinya. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
            </p>
        </div>

        <!-- TANDA TANGAN -->
        <div class="signature-area">
            <div class="signature-box signature-leader">
                Disetujui Oleh,<br>
                Ketua Divisi {{ $leave->user->divisi->nama ?? 'N/A' }}
                <div class="ttd-space"></div>
                <span style="font-weight: bold; font-size: 11pt;">({{ $leave->user->divisi->ketuaDivisi->name ?? 'N/A' }})</span>
            </div>

            <div class="signature-box signature-hrd">
                Dikeluarkan Oleh,<br>
                HRD Manager
                <div class="ttd-space"></div>
                <span style="font-weight: bold; font-size: 11pt;">({{ $hrdManager->name ?? 'HRD Manager' }})</span>
            </div>
            <div class="clear"></div>
        </div>
        
    </div>
</body>
</html>