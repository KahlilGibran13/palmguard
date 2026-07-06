<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PalmGuard · Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
   <!DOCTYPE html>

<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PalmGuard · Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">


<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    /* ================= SPOTIFY THEME ================= */
    :root {
        --bg:     #121212;
        --bg2:    #181818;
        --bg3:    #222222;
        --border: #2a2a2a;

        --green:  #1DB954;
        --green2: #1ed760;
        --green3: #1aa34a;

        --text:   #ffffff;
        --muted:  #b3b3b3;
        --red:    #e05252;
    }

    body {
        background: radial-gradient(circle at top, #1a1a1a, #121212);
        color: var(--text);
        font-family: 'Sora', sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-wrap {
        width: 100%;
        max-width: 420px;
        padding: 24px;
    }

    /* ================= LOGO ================= */
    .login-logo {
        text-align: center;
        margin-bottom: 32px;
    }

    .logo-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #1DB954, #1ed760);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin: 0 auto 12px;
        box-shadow: 0 0 20px rgba(29,185,84,0.3);
    }

    .logo-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--green);
        letter-spacing: 2px;
    }

    .logo-sub {
        font-size: 11px;
        color: var(--muted);
        font-family: 'IBM Plex Mono', monospace;
        margin-top: 4px;
    }

    /* ================= CARD ================= */
    .login-card {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.6);
    }

    .login-card h2 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .login-card p {
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 24px;
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ================= FORM ================= */
    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group input {
        width: 100%;
        padding: 12px 14px;
        background: var(--bg3);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text);
        font-size: 14px;
        outline: none;
        transition: 0.2s;
    }

    .form-group input:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 2px rgba(29,185,84,0.2);
    }

    .form-group input::placeholder {
        color: #555;
    }

    /* ================= ERROR ================= */
    .error-msg {
        background: rgba(224, 82, 82, 0.1);
        border: 1px solid rgba(224, 82, 82, 0.3);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 12px;
        color: var(--red);
        margin-bottom: 16px;
    }

    /* ================= REMEMBER ================= */
    .remember-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }

    .remember-row input[type="checkbox"] {
        accent-color: var(--green);
    }

    .remember-row label {
        font-size: 12px;
        color: var(--muted);
    }

    /* ================= BUTTON ================= */
    .btn-login {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #1DB954, #1ed760);
        color: black;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-login:hover {
        background: linear-gradient(135deg, #1ed760, #1DB954);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29,185,84,0.3);
    }

    /* ================= FOOTER ================= */
    .login-footer {
        text-align: center;
        margin-top: 24px;
        font-size: 11px;
        color: var(--muted);
        font-family: 'IBM Plex Mono', monospace;
    }

    .status-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        background: var(--green);
        border-radius: 50%;
        margin-right: 4px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
</style>


</head>
<body>

<div class="login-wrap">
    <div class="login-logo">
        <div class="logo-icon" style="background: linear-gradient(135deg, #e05252, #ff6b6b);">⚠️</div>
        <div class="logo-title" style="color: var(--red);">500 SERVER ERROR</div>
        <div class="logo-sub">PALMGUARD · SYSTEM MALFUNCTION</div>
    </div>

    <div class="login-card">
        <h2>Gangguan Sistem</h2>
        <p>{{ $exception->getMessage() ?: 'Terjadi kesalahan internal pada server. Tim Dunchill sedang melakukan perbaikan.' }}</p>
        
        <button onclick="window.location.reload()" class="btn-login" style="background: var(--red); color: white;">
            COBA LAGI
        </button>
    </div>

    <div class="login-footer">
        <span class="status-dot" style="background: var(--red);"></span> 
        Emergency Protocol Active · PalmGuard 2026
    </div>
</div>

</body>
</html>
