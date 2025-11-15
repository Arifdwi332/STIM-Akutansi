<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Registrasi UMKM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CSS (kalau kamu pakai versi lain, silakan ganti) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #E5ECF3;
        }

        .reg-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reg-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .12);
            width: 380px;
            padding: 24px 28px 26px;
        }

        .reg-title {
            text-align: center;
            font-weight: 700;
            font-size: 22px;
            margin-bottom: 18px;
            background: linear-gradient(90deg, #F59E0B, #F97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .reg-label {
            font-size: 12px;
            font-weight: 600;
            color: #4B5563;
            margin-bottom: 4px;
        }

        .reg-input {
            font-size: 13px;
            padding: 8px 10px;
        }

        .reg-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
        }
    </style>
</head>

<body>
    <div class="reg-page">
        <div class="reg-card">

            <div class="reg-title">Daftar</div>

            {{-- pesan sukses --}}
            @if (session('success'))
                <div class="alert alert-success py-2 px-3">
                    {{ session('success') }}
                </div>
            @endif

            {{-- error validasi --}}
            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li style="font-size: 13px;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('mstuser.register.store') }}">
                @csrf

                {{-- Nama Pemilik --}}
                <div class="form-group mb-3">
                    <label class="reg-label" for="nama_pemilik">Nama Pemilik</label>
                    <input type="text" id="nama_pemilik" name="nama_pemilik" value="{{ old('nama_pemilik') }}"
                        class="form-control reg-input" placeholder="Nama lengkap pemilik">
                </div>

                {{-- Nama UMKM --}}
                <div class="form-group mb-3">
                    <label class="reg-label" for="nama_umkm">Nama UMKM</label>
                    <input type="text" id="nama_umkm" name="nama_umkm" value="{{ old('nama_umkm') }}"
                        class="form-control reg-input" placeholder="Nama usaha / UMKM">
                </div>

                {{-- Email --}}
                <div class="form-group mb-3">
                    <label class="reg-label" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="form-control reg-input" placeholder="example@gmail.com">
                </div>

                {{-- Password --}}
                <div class="form-group mb-1">
                    <label class="reg-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control reg-input"
                        placeholder="********">
                </div>

                <div class="reg-footer">
                    <button type="button" class="btn btn-light border" onclick="window.history.back();">
                        Kembali
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bootstrap JS (opsional) --}}
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
