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

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
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
        }
    </style>
</head>

<body>
    <div class="container py-5">

        @auth

            <div class="mb-3">
                <label class="form-label">Cari</label>
                <div class="input-icon">
                    <input type="text" id="search" class="form-control" placeholder="Cari NIP">

                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />
                        </svg>
                    </span>
                </div>
                <small id="search-message" class="text-danger d-block mt-2"></small>
            </div>
        @endauth


        <div class="employee-card" id="employee-card">
            <div class="row g-0 align-items-center">

                <div class="col-lg-4 profile-border">
                    <div class="profile-section">

                        <div class="avatar-wrapper">
                            <img src="{{ $dataKaryawan['foto'] }}" id="employee-avatar" class="employee-avatar"
                                alt="Avatar {{ $dataKaryawan['namaLengkap'] }}">
                        </div>

                        @auth
                            <div class="employee-nip" id="employee-nip">
                                {{ $dataKaryawan['nip'] }}
                            </div>
                        @endauth

                        <div class="employee-name" id="employee-name">
                            {{ $dataKaryawan['namaLengkap'] }}
                        </div>

                        <div class="employee-position">
                            <i class="bi bi-briefcase"></i>
                            <span id="employee-position">{{ $dataKaryawan['jabatan'] }}</span>
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
                                    {{ $dataKaryawan['email'] }}
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="icon-box">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <div class="info-label">Departemen</div>
                                <div class="info-value" id="employee-departemen">
                                    {{ $dataKaryawan['departemen'] }}
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="icon-box">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <div>
                                <div class="info-label">No HP</div>
                                <div class="info-value" id="employee-no-hp">
                                    {{ $dataKaryawan['no_hp'] }}
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
                                    {{ $dataKaryawan['jenis_kelamin'] }}
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
                                    {{ $dataKaryawan['tempat_lahir'] }}
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
                                    {{ $dataKaryawan['tgl_lahir'] }}
                                </div>
                            </div>
                        </div>

                        @if (
                            (auth()->check() && auth()->user()?->departemen?->departemen == 'Administrasi') ||
                                auth()->user()?->karyawan?->nip == $dataKaryawan['nip']
                        )
                            <div class="info-item" id="employee-alamat-wrapper">
                                <div class="icon-box">
                                    <i class="bi bi-house-door"></i>
                                </div>
                                <div>
                                    <div class="info-label">Alamat</div>
                                    <div class="info-value" id="employee-alamat">
                                        {{ $dataKaryawan['alamat'] }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="info-item">
                            <div class="icon-box">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div>
                                <div class="info-label">Status</div>
                                <span id="employee-status" class="{{ $dataKaryawan['status_class'] }}">
                                    {{ $dataKaryawan['status'] }}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('search');
        const employeeCard = document.getElementById('employee-card');
        const searchMessage = document.getElementById('search-message');
        const defaultData = @json($dataKaryawan);
        let typingTimer = null;

        searchInput.addEventListener('keyup', function() {
            clearTimeout(typingTimer);

            const nip = this.value.trim();

            if (nip.length === 0) {
                searchMessage.textContent = '';
                fillEmployeeCard(defaultData);
                return;
            }

            typingTimer = setTimeout(function() {
                searchEmployeeByNip(nip);
            }, 500);
        });

        function searchEmployeeByNip(nip) {
            fetch(`{{ route('karyawan.idCard') }}?nip=${encodeURIComponent(nip)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(async function(response) {
                    const result = await response.json();

                    if (!response.ok) {
                        throw result;
                    }

                    return result;
                })
                .then(function(result) {
                    searchMessage.textContent = '';
                    fillEmployeeCard(result.data);
                })
                .catch(function(error) {
                    searchMessage.textContent = error.message || 'Data karyawan tidak ditemukan.';
                });
        }

        function fillEmployeeCard(data) {
            employeeCard.style.display = 'block';

            document.getElementById('employee-avatar').src = data.foto;
            document.getElementById('employee-avatar').alt = `Avatar ${data.namaLengkap}`;
            document.getElementById('employee-name').textContent = data.namaLengkap || '-';
            document.getElementById('employee-position').textContent = data.jabatan;
            document.getElementById('employee-email').textContent = data.email;
            document.getElementById('employee-departemen').textContent = data.departemen;
            document.getElementById('employee-no-hp').textContent = data.no_hp;
            document.getElementById('employee-jenis-kelamin').textContent = data.jenis_kelamin;
            document.getElementById('employee-tempat-lahir').textContent = data.tempat_lahir;
            document.getElementById('employee-tgl-lahir').textContent = data.tgl_lahir;

            const nipElement = document.getElementById('employee-nip');

            if (nipElement) {
                nipElement.textContent = data.nip;
            }

            const alamatElement = document.getElementById('employee-alamat');

            if (alamatElement) {
                alamatElement.textContent = data.alamat;
            }

            const statusElement = document.getElementById('employee-status');
            statusElement.textContent = data.status;
            statusElement.className = data.status_class;
        }
    </script>
</body>

</html>
