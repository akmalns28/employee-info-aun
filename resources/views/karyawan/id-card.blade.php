<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" type="image/png" href="{{ asset('assets/static/icon/favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <title>Informasi Karyawan</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        .mini-header {
            background: #ffffff;
            border-radius: 16px;
            padding: 12px 16px;
            margin-bottom: 6px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, .08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .mini-header-title {
            color: #0f172a;
            font-size: 15px;
            font-weight: 700;
        }

        .mini-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-mini {
            border: none;
            border-radius: 9px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .btn-download {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-download:disabled {
            opacity: .75;
            cursor: not-allowed;
        }

        .btn-logout {
            background: #fee2e2;
            color: #b91c1c;
        }

        .employee-card {
            background: #ffffff;
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 0 20px 45px rgba(15, 23, 42, .10);
            position: relative;
        }

        .employee-card::before {
            content: "";
            position: absolute;
            top: -120px;
            left: -120px;
            width: 320px;
            height: 320px;
            background: #eaf2ff;
            border-radius: 50%;
            z-index: 0;
        }

        .profile-section,
        .info-section {
            position: relative;
            z-index: 1;
        }

        .profile-section {
            padding: 38px 28px;
            text-align: center;
        }

        .profile-border {
            border-right: 1px solid #e5e7eb;
        }

        .avatar-wrapper {
            width: 140px;
            height: 140px;
            margin: 0 auto 20px;
        }

        .employee-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #ffffff;
            box-shadow: 0 12px 28px rgba(37, 99, 235, .20);
        }

        .employee-nip {
            display: inline-block;
            background: #e8f0ff;
            color: #2563eb;
            padding: 6px 14px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .employee-name {
            color: #0f172a;
            font-size: 23px;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 6px;
        }

        .employee-position {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            font-size: 14px;
            font-weight: 500;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }

        .info-section {
            padding: 38px 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px 24px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-bottom: 18px;
            border-bottom: 1px solid #e5e7eb;
            min-width: 0;
        }

        .icon-box {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
        }

        .info-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .info-value {
            color: #0f172a;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.45;
            word-break: break-word;
        }

        .status-active,
        .status-nonactive {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-active {
            background: #dcfce7;
            color: #15803d;
        }

        .status-nonactive {
            background: #fee2e2;
            color: #b91c1c;
        }

        @media (max-width: 992px) {
            .profile-border {
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }

            .profile-section {
                padding: 34px 22px;
            }

            .info-section {
                padding: 32px 22px;
            }

            .employee-name {
                font-size: 21px;
            }
        }

        @media (max-width: 576px) {
            .employee-card {
                border-radius: 20px;
            }

            .avatar-wrapper {
                width: 120px;
                height: 120px;
            }

            .info-section {
                grid-template-columns: 1fr;
            }

            .info-item {
                align-items: flex-start;
            }

            .icon-box {
                width: 44px;
                height: 44px;
                font-size: 17px;
            }

            .info-value {
                font-size: 14px;
            }

            .mini-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .mini-header-actions {
                width: 100%;
            }

            .btn-mini {
                flex: 1;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="container py-2">

        <div class="mini-header">
            <div class="mini-header-title">
                Informasi Karyawan
            </div>

            @auth
                <div class="mini-header-actions">
                    <button type="button" id="download-pdf-button" class="btn-mini btn-download">
                        <i class="bi bi-download"></i>
                        Download PDF
                    </button>

                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn-mini btn-logout">
                            <i class="bi bi-box-arrow-right"></i>
                            Logout
                        </button>
                    </form>
                </div>
            @endauth
        </div>

        <div id="print-area">
            <div class="employee-card" id="employee-card" style="{{ $dataKaryawan ? '' : 'display: none;' }}">
                <div class="row g-0 align-items-center">

                    <div class="col-lg-4 profile-border">
                        <div class="profile-section">

                            <div class="avatar-wrapper">
                                <img src="{{ $dataKaryawan['foto'] ?? '' }}" id="employee-avatar"
                                    class="employee-avatar" alt="Avatar {{ $dataKaryawan['namaLengkap'] ?? '' }}">
                            </div>

                            <div class="employee-nip" id="employee-nip">
                                {{ $dataKaryawan['nip'] ?? '' }}
                            </div>

                            <div class="employee-name" id="employee-name">
                                {{ $dataKaryawan['namaLengkap'] ?? '' }}
                            </div>

                            <div class="employee-position">
                                <i class="bi bi-briefcase"></i>
                                <span id="employee-position">
                                    {{ $dataKaryawan['posisi'] ?? '-' }}
                                </span>
                            </div>

                            <div class="mt-2 text-secondary">
                                <span id="employee-departemen">
                                    {{ $dataKaryawan['departemen'] ?? '-' }}
                                </span>
                                <span class="mx-1">•</span>
                                <span id="employee-divisi">
                                    {{ $dataKaryawan['divisi'] ?? '-' }}
                                </span>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="info-section">

                            <div class="info-item">
                                <div class="icon-box">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <div>
                                    <div class="info-label">Email</div>
                                    <div class="info-value" id="employee-email">
                                        {{ $dataKaryawan['email'] ?? '' }}
                                    </div>
                                </div>
                            </div>

                            <div class="info-item" id="employee-no-hp-wrapper"
                                style="{{ $dataKaryawan && ($dataKaryawan['boleh_lihat_alamat'] ?? false) ? '' : 'display: none;' }}">
                                <div class="icon-box">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div>
                                    <div class="info-label">No HP</div>
                                    <div class="info-value" id="employee-no-hp">
                                        {{ $dataKaryawan['no_hp'] ?? '' }}
                                    </div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="icon-box">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <div class="info-label">Jenis Kelamin</div>
                                    <div class="info-value" id="employee-jenis-kelamin">
                                        {{ $dataKaryawan['jenis_kelamin'] ?? '' }}
                                    </div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="icon-box">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <div class="info-label">Tempat Lahir</div>
                                    <div class="info-value" id="employee-tempat-lahir">
                                        {{ $dataKaryawan['tempat_lahir'] ?? '' }}
                                    </div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="icon-box">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <div>
                                    <div class="info-label">Tanggal Lahir</div>
                                    <div class="info-value" id="employee-tgl-lahir">
                                        {{ $dataKaryawan['tgl_lahir'] ?? '' }}
                                    </div>
                                </div>
                            </div>

                            <div class="info-item" id="employee-alamat-wrapper"
                                style="{{ $dataKaryawan && ($dataKaryawan['boleh_lihat_alamat'] ?? false) ? '' : 'display: none;' }}">
                                <div class="icon-box">
                                    <i class="bi bi-house-door"></i>
                                </div>
                                <div>
                                    <div class="info-label">Alamat</div>
                                    <div class="info-value" id="employee-alamat">
                                        {{ $dataKaryawan['alamat'] ?? '' }}
                                    </div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="icon-box">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div>
                                    <div class="info-label">Status</div>
                                    <span id="employee-status" class="{{ $dataKaryawan['status_class'] ?? '' }}">
                                        {{ $dataKaryawan['status'] ?? '' }}
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>


    <script>
        const employeeCard = document.getElementById('employee-card');
        const downloadPdfButton = document.getElementById('download-pdf-button');

        if (downloadPdfButton) {
            downloadPdfButton.addEventListener('click', async function() {
                const printArea = document.getElementById('print-area');

                const employeeName =
                    document.getElementById('employee-name')?.textContent?.trim() || 'karyawan';

                const employeeNip =
                    document.getElementById('employee-nip')?.textContent?.trim() || 'id-card';

                if (!printArea || !employeeCard || employeeCard.style.display === 'none') {
                    return;
                }

                downloadPdfButton.disabled = true;
                downloadPdfButton.innerHTML =
                    '<i class="bi bi-hourglass-split"></i> Membuat PDF...';

                try {
                    const canvas = await html2canvas(printArea, {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: '#ffffff'
                    });

                    const imgData = canvas.toDataURL('image/jpeg', 1.0);

                    const {
                        jsPDF
                    } = window.jspdf;

                    const pdf = new jsPDF('l', 'mm', 'a4');

                    const pageWidth = pdf.internal.pageSize.getWidth();
                    const pageHeight = pdf.internal.pageSize.getHeight();

                    const margin = 8;
                    const maxWidth = pageWidth - (margin * 2);
                    const maxHeight = pageHeight - (margin * 2);

                    const imgWidth = canvas.width;
                    const imgHeight = canvas.height;

                    const ratio = Math.min(
                        maxWidth / imgWidth,
                        maxHeight / imgHeight
                    );

                    const finalWidth = imgWidth * ratio;
                    const finalHeight = imgHeight * ratio;

                    const x = (pageWidth - finalWidth) / 2;
                    const y = (pageHeight - finalHeight) / 2;

                    pdf.addImage(
                        imgData,
                        'JPEG',
                        x,
                        y,
                        finalWidth,
                        finalHeight
                    );

                    const fileName = `${employeeNip}-${employeeName}`
                        .replace(/[^a-zA-Z0-9-_ ]/g, '')
                        .replace(/\s+/g, '-');

                    pdf.save(`${fileName}.pdf`);
                } catch (error) {
                    console.error(error);
                    alert('Gagal membuat PDF.');
                } finally {
                    downloadPdfButton.disabled = false;
                    downloadPdfButton.innerHTML =
                        '<i class="bi bi-download"></i> Download PDF';
                }
            });
        }
    </script>

</body>

</html>
