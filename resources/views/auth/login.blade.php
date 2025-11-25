<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login UMKM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #E5ECF3;
        }

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .12);
            width: 380px;
            /* SAMA dengan register */
            padding: 24px 28px 26px;
            /* SAMA dengan register */
        }

        .auth-title {
            text-align: center;
            font-weight: 700;
            font-size: 22px;
            /* SAMA dengan register */
            margin-bottom: 18px;
            /* SAMA dengan register */
            background: linear-gradient(90deg, #3B82F6, #6366F1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-label {
            font-size: 12px;
            /* SAMA dengan register */
            font-weight: 600;
            color: #4B5563;
            margin-bottom: 4px;
        }

        .auth-input {
            font-size: 13px;
            /* SAMA dengan register */
            padding: 8px 10px;
            /* SAMA dengan register */
        }

        .auth-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 18px;
            /* SAMA feel-nya dengan register */
        }

        .auth-link {
            font-size: 13px;
        }

        .alert ul li {
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="auth-page">
        <div class="auth-card">

            <div class="auth-title">Masuk</div>

            {{-- pesan sukses dari register --}}
            @if (session('success'))
                <div class="alert alert-success py-2 px-3">
                    {{ session('success') }}
                </div>
            @endif

            {{-- error validasi / login --}}
            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('mstuser.login.process') }}">
                @csrf

                {{-- Email --}}
                <div class="form-group mb-3">
                    <label class="auth-label" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="form-control auth-input" placeholder="example@gmail.com" autofocus>
                </div>


                {{-- Password --}}
                <div class="form-group mb-1">
                    <label class="auth-label" for="password">Password</label>

                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control auth-input"
                            placeholder="********">

                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="togglePassword">
                                <i class="bi bi-eye" id="iconPassword"></i>
                            </button>
                        </div>
                    </div>
                </div>



                <div class="auth-footer">
                    <a href="{{ route('mstuser.register') }}" class="auth-link">
                        Belum punya akun? Daftar
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bootstrap JS (opsional) --}}
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('password');
        const btn = document.getElementById('togglePassword');
        const icon = document.getElementById('iconPassword');

        btn.addEventListener('click', function() {
            const isHidden = input.type === 'password';

            // ganti tipe input
            input.type = isHidden ? 'text' : 'password';

            // ganti ikon: mata <-> mata tersilang
            if (isHidden) {
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });
</script>


</html>
