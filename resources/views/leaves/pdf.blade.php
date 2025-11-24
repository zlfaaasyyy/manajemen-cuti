<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Izin Cuti - {{ $leave->user->name }}</title>
    <!-- Gunakan CSS Inline atau internal karena package PDF tidak bisa membaca file eksternal -->
    <style>
        body { 
            font-family: 'Arial', sans-serif; 
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 40px auto;
            border: 1px solid #ccc;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #473C33; /* Warna gelap tema Anda */
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .content {
            font-size: 12pt;
        }
        .details {
            margin: 20px 0;
            border-collapse: collapse;
            width: 100%;
        }
        .details th, .details td {
            padding: 8px 0;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .details th {
            width: 35%;
            font-weight: bold;
            color: #333;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            float: left;
        }
        .signature-hrd {
            float: right;
            text-align: center;
        }
        .ttd-space {
            height: 60px;
            border-bottom: 1px dashed #aaa;
            margin-bottom: 5px;
        }
        .date {
            text-align: right;
            margin-bottom: 20px;
            font-size: 12pt;
        }
        .info-box {
            border: 1px solid #ABC270;
            background-color: #f7fff0;
            padding: 15px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="header">
            <h1>SURAT IZIN CUTI KARYAWAN</h1>
            <p>PT. ABADI NAN JAYA</p>
        </div>

        <div class="date">
            Sulawesi, {{ now()->format('d F Y') }}
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
                    <th>Divisi</th>
                    <td>: {{ $leave->user->divisi->nama ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Jenis Cuti</th>
                    <td style="color: #FDA769; font-weight: bold;">: {{ strtoupper($leave->jenis_cuti) }}</td>
                </tr>
            </table>

            <p style="margin-top: 20px;">Diberikan izin untuk mengambil cuti terhitung mulai:</p>
            
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

            <p style="margin-top: 20px;">
                Cuti ini diajukan dengan alasan: <strong>{{ $leave->alasan }}</strong>. Karyawan yang bersangkutan dapat dihubungi melalui nomor darurat <strong>{{ $leave->nomor_darurat }}</strong> atau di alamat <strong>{{ $leave->alamat_selama_cuti }}</strong> selama periode cuti.
            </p>
            
            <div class="info-box">
                <p style="margin: 0; font-size: 10pt; color: #ABC270; font-weight: bold;">Catatan HRD:</p>
                <p style="margin: 5px 0 0 0; font-size: 11pt;">{{ $leave->catatan_hrd ?? 'Disetujui tanpa catatan khusus.' }}</p>
            </div>

            <p style="margin-top: 30px;">
                Demikian surat izin cuti ini dibuat untuk digunakan sebagaimana mestinya. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
            </p>
        </div>

        <div class="signature">
            <div class="signature-box">
                Disetujui Oleh,<br>
                Ketua Divisi {{ $leave->user->divisi->nama ?? 'N/A' }}
                <div class="ttd-space"></div>
                <span style="font-weight: bold;">({{ $leave->user->divisi->ketuaDivisi->name ?? 'N/A' }})</span>
            </div>

            <div class="signature-box">
                Dikeluarkan Oleh,<br>
                HRD Manager
                <div class="ttd-space"></div>
                <span style="font-weight: bold;">({{ $hrdManager->name ?? 'HRD Manager' }})</span>
            </div>
        </div>
        
    </div>
</body>
</html>