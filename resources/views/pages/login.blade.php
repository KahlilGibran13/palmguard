<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PalmGuard · Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --deep:      #000000;
            --surface:   #0a0a0a;
            --glass:     rgba(255, 255, 255, 0.02);
            --glass-border: rgba(255, 255, 255, 0.06);
            --green:     #00d084;
            --green-glow: rgba(0, 208, 132, 0.3);
            --text:      #ffffff;
            --muted:     #6b6b7b;
            --red:       #ff4757;
            --yellow:    #f5a623;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--deep);
            color: var(--text);
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* STARFIELD BACKGROUND */
        .starfield {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: radial-gradient(ellipse at bottom, #0a0a1a 0%, #000000 70%);
        }

        .stars {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
        }

        .star {
            position: absolute;
            background: #ffffff;
            border-radius: 50%;
            animation: twinkle var(--duration) ease-in-out infinite;
            animation-delay: var(--delay);
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.2; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.2); box-shadow: 0 0 10px rgba(255,255,255,0.5); }
        }

        /* SHOOTING STARS */
        .shooting-star {
            position: absolute;
            width: 100px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
            animation: shoot var(--shoot-duration) linear infinite;
            animation-delay: var(--shoot-delay);
            opacity: 0;
        }

        @keyframes shoot {
            0% {
                transform: translateX(0) translateY(0) rotate(-45deg);
                opacity: 1;
            }
            100% {
                transform: translateX(500px) translateY(500px) rotate(-45deg);
                opacity: 0;
            }
        }

        /* NEBULA GLOW */
        .nebula {
            position: fixed;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            background:
                radial-gradient(ellipse at 20% 80%, rgba(0, 208, 132, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(0, 100, 255, 0.05) 0%, transparent 50%);
        }

        /* SCANLINES */
        .scanlines {
            position: fixed;
            inset: 0;
            z-index: 3;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(255, 255, 255, 0.01) 2px,
                rgba(255, 255, 255, 0.01) 4px
            );
            pointer-events: none;
        }

        /* LOGIN CONTAINER - CENTERED */
        .login-wrap {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* GLASS CARD */
        .glass-card {
            width: 100%;
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--green), transparent);
            opacity: 0.6;
            animation: borderGlow 3s ease-in-out infinite;
        }

        @keyframes borderGlow {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.8; }
        }

        /* FORM HEADER */
        .form-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .form-brand {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 11px;
            color: var(--green);
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 16px;
        }

        .form-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ffffff 0%, var(--green) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-subtitle {
            font-size: 14px;
            color: var(--muted);
        }

        /* INPUTS */
        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-wrap input:focus {
            border-color: var(--green);
            background: rgba(0, 208, 132, 0.03);
            box-shadow: 0 0 0 3px rgba(0, 208, 132, 0.08), 0 0 30px rgba(0, 208, 132, 0.1);
        }

        .input-wrap input::placeholder {
            color: rgba(107, 107, 123, 0.5);
        }

        /* REMEMBER ROW */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .remember-check {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .remember-check input {
            appearance: none;
            width: 18px;
            height: 18px;
            border: 1px solid var(--glass-border);
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.03);
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
        }

        .remember-check input:checked {
            background: var(--green);
            border-color: var(--green);
        }

        .remember-check input:checked::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 5px;
            height: 9px;
            border: solid var(--deep);
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .remember-check label {
            font-size: 13px;
            color: var(--muted);
            cursor: pointer;
        }

        .forgot-link {
            font-size: 13px;
            color: var(--green);
            text-decoration: none;
            transition: opacity 0.2s;
            cursor: pointer;
            background: none;
            border: none;
            font-family: 'Inter', sans-serif;
        }

        .forgot-link:hover {
            opacity: 0.7;
        }

        /* SUBMIT BUTTON */
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--green), #00b894);
            color: var(--deep);
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px var(--green-glow);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        /* BACK BUTTON */
        .btn-back {
            display: block;
            width: 100%;
            padding: 14px;
            margin-top: 12px;
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            border-color: var(--green);
            color: var(--green);
            background: rgba(0, 208, 132, 0.05);
        }

        /* ERROR */
        .error-box {
            background: rgba(255, 71, 87, 0.06);
            border: 1px solid rgba(255, 71, 87, 0.15);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: var(--red);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-box::before {
            content: '';
            width: 4px;
            height: 4px;
            background: var(--red);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--red);
            flex-shrink: 0;
        }

        /* FOOTER */
        .login-footer {
            margin-top: 32px;
            text-align: center;
            font-size: 11px;
            color: var(--muted);
            font-family: 'IBM Plex Mono', monospace;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .pulse-dot {
            width: 6px;
            height: 6px;
            background: var(--green);
            border-radius: 50%;
            animation: pulse 2s infinite;
            box-shadow: 0 0 8px var(--green);
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* NOTIFICATION MODAL */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 9998;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-box {
            background: var(--surface);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 40px;
            max-width: 360px;
            width: 90%;
            text-align: center;
            position: relative;
            transform: scale(0.9) translateY(20px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
        }

        .modal-overlay.active .modal-box {
            transform: scale(1) translateY(0);
        }

        .modal-icon {
            width: 56px;
            height: 56px;
            background: rgba(245, 166, 35, 0.1);
            border: 1px solid rgba(245, 166, 35, 0.3);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .modal-icon::before {
            content: '';
            width: 4px;
            height: 16px;
            background: var(--yellow);
            border-radius: 2px;
            position: relative;
        }

        .modal-icon::after {
            content: '';
            width: 4px;
            height: 4px;
            background: var(--yellow);
            border-radius: 50%;
            position: absolute;
            margin-top: 14px;
        }

        .modal-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text);
        }

        .modal-text {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .modal-btn {
            padding: 12px 32px;
            background: var(--green);
            color: var(--deep);
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .modal-btn:hover {
            background: var(--green2);
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(0, 208, 132, 0.4);
        }

        /* ORBIT RING DECORATION */
        .orbit-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }

        .orbit-ring::before {
            content: '';
            position: absolute;
            top: -2px;
            left: 50%;
            width: 4px;
            height: 4px;
            background: var(--green);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--green);
            animation: orbit 20s linear infinite;
        }

        @keyframes orbit {
            from { transform: rotate(0deg) translateX(300px) rotate(0deg); }
            to { transform: rotate(360deg) translateX(300px) rotate(-360deg); }
        }

        .orbit-ring-2 {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            height: 400px;
            border: 1px solid rgba(255, 255, 255, 0.02);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }

        .orbit-ring-2::before {
            content: '';
            position: absolute;
            top: -2px;
            left: 50%;
            width: 3px;
            height: 3px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: orbit2 15s linear infinite reverse;
        }

        @keyframes orbit2 {
            from { transform: rotate(0deg) translateX(200px) rotate(0deg); }
            to { transform: rotate(360deg) translateX(200px) rotate(-360deg); }
        }
    </style>
</head>
<body>

    <!-- Space Background -->
    <div class="starfield"></div>
    <div class="stars" id="stars"></div>
    <div class="nebula"></div>
    <div class="scanlines"></div>

    <!-- Orbit Decorations -->
    <div class="orbit-ring"></div>
    <div class="orbit-ring-2"></div>

    <!-- Notification Modal -->
    <div class="modal-overlay" id="forgotModal">
        <div class="modal-box">
            <div class="modal-icon"></div>
            <h3 class="modal-title">Lupa Password?</h3>
            <p class="modal-text">Hubungi pihak admin untuk mendapatkan sandi</p>
            <button class="modal-btn" onclick="closeModal()">Mengerti</button>
        </div>
    </div>

    <!-- Login Centered -->
    <div class="login-wrap">
        <div class="glass-card">
            <div class="form-header">
                <div class="form-brand">PALMGUARD</div>
                <h1 class="form-title">Masuk</h1>
                <p class="form-subtitle">Akses sistem deteksi penyakit kelapa sawit</p>
            </div>

            @if($errors->any())
            <div class="error-box">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="input-group">
                    <label>Email</label>
                    <div class="input-wrap">
                        <input type="email" name="email" value="{{ old('email') }}" 
                               placeholder="nama@perusahaan.com" required autofocus>
                    </div>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <input type="password" name="password" 
                               placeholder="Masukkan password" required>
                    </div>
                </div>

                <div class="remember-row">
                    <div class="remember-check">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Ingat saya</label>
                    </div>
                    <button type="button" class="forgot-link" onclick="openModal()">Lupa password?</button>
                </div>

                <button type="submit" class="btn-submit">
                    MASUK KE SISTEM
                </button>
            </form>

            <a href="{{ route('landing') }}" class="btn-back">Kembali</a>
        </div>

        <div class="login-footer">
            <span class="pulse-dot"></span>
            PALMGUARD SYSTEM ONLINE
        </div>
    </div>

    <script>
        // Generate twinkling stars
        const starsContainer = document.getElementById('stars');
        const starCount = 150;

        for (let i = 0; i < starCount; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            
            const size = Math.random() * 2 + 1;
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            const duration = Math.random() * 3 + 2;
            const delay = Math.random() * 5;
            
            star.style.width = size + 'px';
            star.style.height = size + 'px';
            star.style.left = x + '%';
            star.style.top = y + '%';
            star.style.setProperty('--duration', duration + 's');
            star.style.setProperty('--delay', delay + 's');
            
            starsContainer.appendChild(star);
        }

        // Generate shooting stars
        for (let i = 0; i < 3; i++) {
            const shooting = document.createElement('div');
            shooting.className = 'shooting-star';
            shooting.style.top = Math.random() * 50 + '%';
            shooting.style.left = Math.random() * 50 + '%';
            shooting.style.setProperty('--shoot-duration', (Math.random() * 3 + 4) + 's');
            shooting.style.setProperty('--shoot-delay', (Math.random() * 10) + 's');
            starsContainer.appendChild(shooting);
        }

        // Input focus micro-interaction
        document.querySelectorAll('.input-wrap input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Modal functions
        function openModal() {
            document.getElementById('forgotModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('forgotModal').classList.remove('active');
        }

        // Close modal on overlay click
        document.getElementById('forgotModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>

</body>
</html>  