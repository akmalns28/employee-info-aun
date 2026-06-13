<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Export QR Code Karyawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 24px;
            color: #0f172a;
        }

        .toolbar {
            margin-bottom: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-print {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-back {
            background: #e5e7eb;
            color: #111827;
        }

        .page-title {
            text-align: center;
            margin-bottom: 24px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .qr-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            page-break-inside: avoid;
        }

        .qr-image {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin-bottom: 12px;
        }

        .nip {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .nama {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .departemen {
            font-size: 12px;
            color: #64748b;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .toolbar {
                display: none;
            }

            .page-title {
                margin-top: 0;
            }

            .grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }

            .qr-card {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button type="button" class="btn btn-back" onclick="window.close()">Tutup</button>

        <button type="button" class="btn btn-print" onclick="window.print()">Print / Save PDF</button>

        <form action="{{ route('karyawan.exportQrCodeZip') }}" method="POST">
            @csrf
            <input type="hidden" name="selected_nips" value='@json($selectedNips)'>

            <button type="submit" class="btn btn-zip">
                Download JPG ZIP
            </button>
        </form>
    </div>

    <h2 class="page-title">Export QR Code Karyawan</h2>

    <div class="grid">
        @foreach ($karyawans as $karyawan)
            @php
                $namaLengkap = trim(($karyawan->nama_depan ?? '') . ' ' . ($karyawan->nama_belakang ?? ''));

                $qrCode = $karyawan->qr_code
                    ? asset('storage/' . $karyawan->qr_code)
                    : asset('assets/static/avatars/default.jpg');
            @endphp

            <div class="qr-card">
                <img src="{{ $qrCode }}" alt="QR Code {{ $karyawan->nip }}" class="qr-image">

                <div class="nip">
                    {{ $karyawan->nip }}
                </div>

                <div class="nama">
                    {{ $namaLengkap ?: '-' }}
                </div>

                <div class="departemen">
                    {{ $karyawan->departemen->departemen ?? '-' }}
                </div>
            </div>
        @endforeach
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>

</html>
