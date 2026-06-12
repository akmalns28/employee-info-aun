<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lengkapi Data</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <div class="navbar-brand navbar-brand-autodark">
                </div>
            </div>

            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Lengkapi Data Diri</h2>

                    <form action="{{ route('pendaftaran.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" class="form-control @error('nip') is-invalid @enderror" name="nip"
                                placeholder="Masukkan NIP Anda" value="{{ old('nip') }}" required>
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Departemen</label>
                            <select class="form-select @error('departemen_uuid') is-invalid @enderror"
                                name="departemen_uuid" required>
                                <option value="">-- Pilih Departemen --</option>
                                @foreach ($departemen as $dept)
                                    <option value="{{ $dept->uuid }}"
                                        {{ old('departemen_uuid') == $dept->uuid ? 'selected' : '' }}>
                                        {{ $dept->departemen }}
                                    </option>
                                @endforeach
                            </select>
                            @error('departemen_uuid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No HP</label>
                            <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                name="no_hp" placeholder="Contoh: 08123456789" value="{{ old('no_hp') }}" required>
                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Simpan</button>
                        </div>
                    </form>

                    <hr>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link w-100 text-danger">Keluar / Ganti Akun</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
