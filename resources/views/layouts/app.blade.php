<!DOCTYPE html>
<html lang="id">
<meta http-equiv="refresh" content="{{ config('session.lifetime') * 60 }}; url={{ route('login') }}">
<head>
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/favicon.ico">
    <link rel="shortcut icon" href="/favicon.ico">
    <title>PalmGuard · @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg:        #0a0a0a;
            --bg2:       #111111;
            --bg3:       #1a1a1a;
            --bg4:       #222222;
            --border:    #2a2a2a;
            --green:     #1DB954;
            --green2:    #1ed760;
            --green3:    #158a3e;
            --green-dim: rgba(29,185,84,0.15);
            --text:      #ffffff;
            --muted:     #a0a0a0;
            --yellow:    #f5a623;
            --red:       #e05252;
            --blue:      #4a9eff;
            --shadow:    rgba(0,0,0,0.5);
        }

        body.light {
            --bg:        #f0f4f0;
            --bg2:       #ffffff;
            --bg3:       #e8f0e8;
            --bg4:       #d8e8d8;
            --border:    #c0d4c0;
            --green:     #1a9e46;
            --green2:    #1DB954;
            --green3:    #158a3e;
            --green-dim: rgba(29,185,84,0.12);
            --text:      #111111;
            --muted:     #555555;
            --yellow:    #d4880a;
            --red:       #c0392b;
            --blue:      #2176ae;
            --shadow:    rgba(0,0,0,0.1);
        }

        /* PAGE LOADER */
        #page-loader {
            position: fixed; inset: 0;
            background: var(--bg);
            z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; gap: 20px;
            transition: opacity 0.6s ease, visibility 0.6s ease;
        }
        #page-loader.hidden { opacity: 0; visibility: hidden; }

        .loader-logo {
            display: flex; flex-direction: column; align-items: center; gap: 12px;
        }

        .loader-icon-wrap {
            width: 64px; height: 64px;
            background: var(--green-dim);
            border: 1px solid var(--green);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            animation: loaderPulse 1.2s ease-in-out infinite;
            box-shadow: 0 0 30px rgba(29,185,84,0.3);
        }

        .loader-title {
            font-size: 18px; font-weight: 700;
            color: var(--green);
            letter-spacing: 3px;
            font-family: 'Inter', sans-serif;
        }

        .loader-bar {
            width: 220px; height: 2px;
            background: var(--border);
            border-radius: 2px; overflow: hidden;
        }

        .loader-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--green3), var(--green), var(--green2));
            border-radius: 2px;
            animation: loaderFill 1.4s ease forwards;
            box-shadow: 0 0 10px var(--green);
        }

        .loader-text {
            font-size: 11px; color: var(--muted);
            font-family: 'IBM Plex Mono', monospace;
            animation: blink 1s ease infinite;
        }

        @keyframes loaderPulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 20px rgba(29,185,84,0.3); }
            50% { transform: scale(1.08); box-shadow: 0 0 40px rgba(29,185,84,0.5); }
        }
        @keyframes loaderFill { 0% { width: 0%; } 100% { width: 100%; } }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

        /* SCANLINE EFFECT */
        body:not(.light)::before {
            content: '';
            position: fixed; inset: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0,0,0,0.03) 2px,
                rgba(0,0,0,0.03) 4px
            );
            pointer-events: none;
            z-index: 1;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            display: flex;
            min-height: 100vh;
            transition: background 0.3s ease, color 0.3s ease;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--bg2);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0;
            z-index: 200;
            transform: translateX(-100%);
            animation: sidebarSlideIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) 0.4s forwards;
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        @keyframes sidebarSlideIn { to { transform: translateX(0); } }

        .sidebar-logo {
            padding: 28px 24px;
            border-bottom: 1px solid var(--border);
        }

        .logo-mark {
            width: 36px; height: 36px;
            background: var(--green-dim);
            border: 1px solid var(--green);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 14px;
            animation: logoPulse 3s ease infinite;
        }

        .logo-mark::before {
            content: '';
            width: 14px; height: 14px;
            background: var(--green);
            border-radius: 50%;
            box-shadow: 0 0 12px var(--green);
        }

        @keyframes logoPulse {
            0%, 100% { box-shadow: 0 0 10px rgba(29,185,84,0.3); }
            50% { box-shadow: 0 0 25px rgba(29,185,84,0.6); }
        }

        .logo-title {
            font-size: 15px; font-weight: 700;
            color: var(--text);
            letter-spacing: 1.5px;
            font-family: 'Inter', sans-serif;
        }

        .logo-sub {
            font-size: 10px; color: var(--muted);
            font-family: 'IBM Plex Mono', monospace;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }

        .sidebar-nav { padding: 20px 16px; flex: 1; overflow-y: auto; }

        .nav-section {
            font-size: 10px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 0 12px;
            margin-bottom: 10px;
            margin-top: 20px;
        }

        .nav-section:first-child { margin-top: 0; }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 14px;
            border-radius: 8px;
            color: var(--muted);
            text-decoration: none;
            font-size: 13px; font-weight: 500;
            margin-bottom: 3px;
            transition: all 0.25s ease;
            position: relative; overflow: hidden;
        }

        .nav-item::after {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 0;
            background: var(--green);
            border-radius: 0 4px 4px 0;
            transition: width 0.2s ease;
        }

        .nav-item:hover {
            background: var(--bg3);
            color: var(--text);
            transform: translateX(3px);
        }

        .nav-item.active {
            background: var(--green-dim);
            color: var(--green);
            font-weight: 600;
        }

        .nav-item.active::after { width: 3px; }

        .nav-item .nav-indicator {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--border);
            transition: all 0.25s ease;
            flex-shrink: 0;
        }

        .nav-item:hover .nav-indicator {
            background: var(--green);
            box-shadow: 0 0 8px var(--green);
        }

        .nav-item.active .nav-indicator {
            background: var(--green);
            box-shadow: 0 0 8px var(--green);
        }

        .sidebar-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border);
        }

        .user-info {
            margin-bottom: 14px;
        }

        .user-name {
            font-size: 13px; font-weight: 600;
            color: var(--text); display: block; margin-bottom: 3px;
        }

        .user-role {
            font-size: 11px; color: var(--green);
            font-family: 'IBM Plex Mono', monospace;
            display: block;
            letter-spacing: 0.5px;
        }

        .btn-logout {
            width: 100%; padding: 9px 14px;
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 12px; font-weight: 500;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all 0.25s ease;
            letter-spacing: 0.3px;
        }

        .btn-logout:hover {
            border-color: var(--red);
            color: var(--red);
            background: rgba(224,82,82,0.08);
        }

        /* THEME TOGGLE */
        .btn-theme {
            width: 100%; padding: 9px 14px;
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 12px; font-weight: 500;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all 0.25s ease;
            letter-spacing: 0.3px;
            margin-bottom: 8px;
            text-align: left;
        }

        .btn-theme:hover {
            border-color: var(--green);
            color: var(--green);
            background: var(--green-dim);
        }

        .status-bar {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 14px;
            font-size: 11px;
            color: var(--muted);
        }

        .status-dot {
            display: inline-block;
            width: 6px; height: 6px;
            background: var(--green);
            border-radius: 50%;
            animation: pulse 2s infinite;
            box-shadow: 0 0 6px var(--green);
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 6px var(--green); }
            50% { opacity: 0.4; box-shadow: 0 0 2px var(--green); }
        }

        /* MAIN */
        .main {
            margin-left: 240px;
            flex: 1;
            padding: 32px 36px;
            min-height: 100vh;
            position: relative;
            z-index: 2;
            animation: mainFadeIn 0.8s ease 0.6s both;
            max-width: 100%;
        }

        @keyframes mainFadeIn {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* CARD */
        .card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px;
            transition: all 0.3s ease;
            animation: cardSlideUp 0.5s ease both;
        }

        .card:hover {
            border-color: rgba(29,185,84,0.3);
            box-shadow: 0 4px 24px rgba(0,0,0,0.4);
        }

        @keyframes cardSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 18px; padding-bottom: 14px;
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap; gap: 10px;
        }

        .card-header h2, .card-header h3 {
            font-size: 14px; font-weight: 600; color: var(--text);
        }

        .card-icon { font-size: 16px; margin-right: 8px; }

        /* STAT CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px; margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px;
            display: flex; align-items: center; gap: 14px;
            transition: all 0.3s ease;
            min-width: 0;
        }

        .stat-card:hover {
            border-color: rgba(29,185,84,0.4);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }

        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }

        .stat-label { display: block; color: var(--muted); font-size: 11px; margin-bottom: 4px; }
        .stat-value { display: block; font-size: 26px; font-weight: 700; color: var(--text); }

        /* BUTTONS */
        .btn-primary {
            padding: 10px 20px; border-radius: 50px;
            background: var(--green); color: #000;
            border: none; cursor: pointer;
            font-size: 13px; font-weight: 700;
            font-family: 'Inter', sans-serif;
            transition: all 0.25s ease;
            position: relative; overflow: hidden;
        }

        .btn-primary:hover {
            background: var(--green2);
            transform: translateY(-1px) scale(1.02);
            box-shadow: 0 4px 20px rgba(29,185,84,0.4);
        }

        .btn-primary:active { transform: scale(0.97); }

        .btn-secondary {
            padding: 10px 20px; border-radius: 50px;
            background: transparent; color: var(--text);
            border: 1px solid var(--border); cursor: pointer;
            font-size: 13px; font-weight: 500;
            font-family: 'Inter', sans-serif;
            transition: all 0.25s ease;
            text-decoration: none; display: inline-block;
        }

        .btn-secondary:hover {
            border-color: var(--green);
            color: var(--green);
        }

        .btn-detect {
            padding: 13px 24px; border-radius: 50px;
            background: var(--green); color: #000;
            border: none; cursor: pointer;
            font-size: 14px; font-weight: 700;
            font-family: 'Inter', sans-serif;
            width: 100%; margin-top: 12px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 20px rgba(29,185,84,0.3);
        }

        .btn-detect:hover {
            background: var(--green2);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(29,185,84,0.5);
        }

        /* DROPZONE */
        .dropzone {
            border: 2px dashed var(--border);
            border-radius: 14px;
            padding: 48px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--bg3);
        }

        .dropzone:hover {
            border-color: var(--green);
            background: rgba(29,185,84,0.04);
            box-shadow: inset 0 0 30px rgba(29,185,84,0.05);
        }

        .dropzone p { font-size: 14px; font-weight: 500; color: var(--text); margin-bottom: 4px; }
        .dropzone span { font-size: 12px; color: var(--muted); }

        /* INPUT */
        input[type="text"], input[type="email"],
        input[type="number"], input[type="password"] {
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            padding: 10px 14px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.25s ease;
            width: 100%;
        }

        input:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(29,185,84,0.1);
        }

        input::placeholder { color: var(--border); }

        /* BADGE */
        .badge {
            padding: 4px 12px; border-radius: 50px;
            font-size: 11px; font-weight: 600;
        }
        .badge-sehat   { background: rgba(29,185,84,0.15); color: var(--green); }
        .badge-sakit   { background: rgba(224,82,82,0.15); color: var(--red); }
        .badge-waspada { background: rgba(245,166,35,0.15); color: var(--yellow); }

        /* TAB */
        .tab-container {
            display: flex; gap: 8px; margin-bottom: 16px;
            border-bottom: 1px solid var(--border); padding-bottom: 12px;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 8px 18px; border-radius: 50px;
            border: 1px solid var(--border);
            background: transparent; color: var(--muted);
            font-size: 13px; font-weight: 500;
            cursor: pointer;
            transition: all 0.25s ease;
            font-family: 'Inter', sans-serif;
        }

        .tab-btn:hover { border-color: var(--green); color: var(--green); }
        .tab-btn.active {
            background: var(--green); border-color: var(--green);
            color: #000; font-weight: 700;
        }

        /* TABLE */
        .table-scroll { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left; padding: 10px 14px;
            font-size: 11px; font-weight: 600;
            color: var(--muted); text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1px solid var(--border);
            font-family: 'IBM Plex Mono', monospace;
            white-space: nowrap;
        }
        td {
            padding: 13px 14px; font-size: 13px;
            border-bottom: 1px solid var(--border); color: var(--text);
            transition: background 0.2s ease;
        }
        tr:hover td { background: var(--bg3); }

        /* SPINNER */
        .spinner {
            width: 36px; height: 36px;
            border: 2px solid var(--border);
            border-top-color: var(--green);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(29,185,84,0.2);
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* PAGINATION */
        .pagination { display: flex; gap: 6px; margin-top: 16px; justify-content: center; flex-wrap: wrap; }
        .pagination a, .pagination span {
            padding: 7px 13px; border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 12px; color: var(--muted); text-decoration: none;
            transition: all 0.2s ease;
        }
        .pagination a:hover { border-color: var(--green); color: var(--green); }
        .pagination .active span { background: var(--green); border-color: var(--green); color: #000; font-weight: 700; }

        /* SEARCH */
        .search-bar { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
        .search-bar input { min-width: 160px; flex: 1; }

        /* KATALOG */
        .katalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px; }
        .katalog-card {
            background: var(--bg3); border: 1px solid var(--border);
            border-radius: 12px; padding: 18px;
            transition: all 0.3s ease;
        }
        .katalog-card:hover {
            border-color: rgba(29,185,84,0.4);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }
        .katalog-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .katalog-nama { font-size: 14px; font-weight: 600; color: var(--text); }
        .katalog-desc { font-size: 12px; color: var(--muted); line-height: 1.6; margin-bottom: 10px; }
        .katalog-ciri { list-style: none; }
        .katalog-ciri li { font-size: 12px; color: var(--muted); padding: 3px 0; }
        .katalog-ciri li::before { content: "-> "; color: var(--green); }

        /* MISC */
        #preview-area { margin-top: 14px; position: relative; display: none; }
        #preview-img { width: 100%; border-radius: 12px; max-height: 280px; object-fit: contain; background: var(--bg3); display: block; }
        #bbox-canvas { position: absolute; top: 0; left: 0; pointer-events: none; border-radius: 12px; }
        #loading { display: none; text-align: center; padding: 24px; }
        .camera-area { background: var(--bg3); border-radius: 12px; overflow: hidden; min-height: 200px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; }
        #video { width: 100%; display: none; border-radius: 12px; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .empty-state { text-align: center; padding: 3rem 1rem; color: var(--muted); }

        /* RIWAYAT */
        .riwayat-item {
            display: flex; align-items: center; gap: 1rem;
            padding: 1rem 0; border-bottom: 1px solid var(--border);
            transition: all 0.2s ease;
        }
        .riwayat-item:last-child { border-bottom: none; }
        .riwayat-foto { width: 56px; height: 56px; object-fit: cover; border-radius: 10px; border: 1px solid var(--border); flex-shrink: 0; }

        /* RIPPLE */
        .ripple {
            position: absolute; border-radius: 50%;
            background: rgba(0,0,0,0.3);
            transform: scale(0);
            animation: rippleAnim 0.6s linear;
            pointer-events: none;
        }
        @keyframes rippleAnim { to { transform: scale(4); opacity: 0; } }

        /* TOAST */
        #toast {
            position: fixed; bottom: 24px; right: 24px;
            background: var(--bg2);
            border: 1px solid var(--green);
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 13px; color: var(--green);
            z-index: 9999;
            transform: translateY(100px); opacity: 0;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 8px 30px rgba(29,185,84,0.2);
            font-family: 'Inter', sans-serif;
            max-width: calc(100% - 32px);
        }
        #toast.show { transform: translateY(0); opacity: 1; }

        /* STAT MINI (RIWAYAT) */
        .stats-mini { display: flex; gap: 1rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
        .stat-mini-item {
            background: var(--bg2); border: 1px solid var(--border);
            border-radius: 10px; padding: 0.75rem 1.25rem;
            display: flex; flex-direction: column; align-items: center; min-width: 80px;
            transition: all 0.25s ease;
        }
        .stat-mini-item:hover { border-color: rgba(29,185,84,0.4); transform: translateY(-2px); }
        .stat-mini-val { font-size: 1.5rem; font-weight: 700; color: var(--text); }
        .stat-mini-lbl { font-size: 0.75rem; color: var(--muted); margin-top: 0.1rem; }

        /* TOOLBAR */
        .toolbar { display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }
        .btn-search {
            background: var(--bg3); border: 1px solid var(--border);
            color: var(--green); padding: 0.55rem 0.9rem;
            border-radius: 8px; cursor: pointer; font-size: 0.9rem;
            transition: all 0.25s ease;
        }
        .btn-search:hover { border-color: var(--green); }
        .btn-export {
            background: var(--bg3); border: 1px solid var(--border);
            color: var(--muted); padding: 0.55rem 0.9rem;
            border-radius: 8px; cursor: pointer; font-size: 0.85rem;
            text-decoration: none; white-space: nowrap;
            transition: all 0.25s ease;
        }
        .btn-export:hover { border-color: var(--green); color: var(--green); }
        .btn-danger {
            background: rgba(224,82,82,0.08); border: 1px solid rgba(224,82,82,0.3);
            color: var(--red); padding: 0.55rem 0.9rem;
            border-radius: 8px; cursor: pointer; font-size: 0.85rem; white-space: nowrap;
            transition: all 0.25s ease;
        }
        .btn-danger:hover { background: rgba(224,82,82,0.15); }

        /* RIWAYAT AKSI */
        .btn-aksi {
            padding: 0.4rem 0.75rem; border-radius: 8px;
            cursor: pointer; font-size: 0.78rem; font-weight: 600;
            text-decoration: none; text-align: center; border: none; white-space: nowrap;
            transition: all 0.25s ease;
        }
        .btn-pdf { background: var(--bg3); color: var(--green); border: 1px solid var(--border) !important; }
        .btn-pdf:hover { border-color: var(--green) !important; }
        .btn-del { background: rgba(224,82,82,0.08); color: var(--red); border: 1px solid rgba(224,82,82,0.2) !important; }
        .btn-del:hover { background: rgba(224,82,82,0.15); }

        /* ── MOBILE TOPBAR (hidden by default, shown only on mobile) ── */
        .mobile-topbar {
            display: none;
            position: fixed; top: 0; left: 0; right: 0;
            height: 56px;
            background: var(--bg2);
            border-bottom: 1px solid var(--border);
            align-items: center;
            gap: 14px;
            padding: 0 16px;
            z-index: 150;
        }

        .mobile-topbar-title {
            font-size: 14px; font-weight: 700;
            color: var(--green);
            letter-spacing: 2px;
        }

        .hamburger-btn {
            width: 34px; height: 34px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 8px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 4px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .hamburger-btn span {
            width: 16px; height: 2px;
            background: var(--text);
            border-radius: 2px;
            transition: all 0.25s ease;
        }

        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 199;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* ── RESPONSIVE: TABLET & MOBILE ── */
        @media (max-width: 900px) {
            .mobile-topbar { display: flex; }

            .sidebar {
                transform: translateX(-100%) !important;
                transition: transform 0.3s ease;
                animation: none !important;
                width: 260px;
                max-width: 80vw;
            }

            .sidebar.open {
                transform: translateX(0) !important;
            }

            body { display: block; }

            .main {
                margin-left: 0 !important;
                padding: 76px 16px 24px !important;
                animation: none !important;
                opacity: 1 !important;
                width: 100%;
            }

            /* Stat cards: 2 kolom rapi */
            .stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 10px;
            }

            .stat-card { padding: 14px; gap: 10px; }
            .stat-icon { width: 36px; height: 36px; font-size: 16px; }
            .stat-value { font-size: 20px; }
            .stat-label { font-size: 10px; }

            /* Card padding lebih kecil */
            .card { padding: 16px; border-radius: 12px; }
            .card-header h2, .card-header h3 { font-size: 13px; }

            /* Form/grid apa pun yang pakai display:flex row 2 kolom -> stack */
            .form-row,
            .location-row,
            .koordinat-row,
            .form-grid-2 {
                flex-direction: column !important;
                gap: 10px !important;
                display: flex !important;
                grid-template-columns: 1fr !important;
            }
            .form-row > *,
            .location-row > *,
            .koordinat-row > *,
            .form-grid-2 > * {
                width: 100% !important;
            }

            /* Toolbar & search bar wrap biar ga overflow */
            .toolbar, .search-bar, .tab-container {
                flex-wrap: wrap;
            }

            .btn-export, .btn-danger, .btn-search {
                font-size: 0.8rem;
                padding: 0.5rem 0.75rem;
            }

            .katalog-grid {
                grid-template-columns: 1fr !important;
            }

            .dropzone { padding: 32px 16px; }
            .dropzone p { font-size: 13px; }

            #toast {
                left: 16px; right: 16px; bottom: 16px;
                text-align: center;
            }

            .riwayat-item { flex-wrap: wrap; }
        }

        @media (max-width: 420px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr) !important; }
            .stat-value { font-size: 18px; }
            .main { padding: 76px 12px 20px !important; }
        }
    </style>
</head>
<body>

<!-- PAGE LOADER -->
<div id="page-loader">
    <div class="loader-logo">
        {{-- <div class="loader-icon-wrap"></div> --}}
        <div class="loader-title">PALMGUARD</div>
    </div>
    <div class="loader-bar"><div class="loader-bar-fill"></div></div>
    <div class="loader-text">Sebentar yach...</div>
</div>

<!-- TOAST -->
<div id="toast"></div>

<!-- MOBILE TOPBAR -->
<div class="mobile-topbar">
    <button class="hamburger-btn" onclick="toggleSidebar()" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>
    <div class="mobile-topbar-title">PALMGUARD</div>
</div>
<div class="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-mark"></div>
        <div class="logo-title">DUNCHILL</div>
        <div class="logo-sub">CAPSTONE est. 2026</div>
    </div>

    <nav class="sidebar-nav">
        @if(Auth::user()->isAdmin() || Auth::user()->isOperator() || Auth::user()->isManager())
            <div class="nav-section">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-indicator"></span> Dashboard
            </a>
            @if (!Auth::user()->isManager())
                <a href="{{ route('deteksi') }}" class="nav-item {{ request()->routeIs('deteksi') ? 'active' : '' }}">
                    <span class="nav-indicator"></span> Deteksi Penyakit
                </a>
            @endif
            <a href="{{ route('riwayat') }}" class="nav-item {{ request()->routeIs('riwayat') ? 'active' : '' }}">
                <span class="nav-indicator"></span> Riwayat Deteksi
            </a>
        @endif

        @if(Auth::user()->isAdmin())
            <div class="nav-section">Manajemen</div>
            <a href="{{ route('user') }}" class="nav-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
                <span class="nav-indicator"></span> Kelola User
            </a>
            <a href="{{ route('penyakit') }}" class="nav-item {{ request()->routeIs('penyakits.index') ? 'active' : '' }}">
                <span class="nav-indicator"></span> Jenis Penyakit
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="status-bar">
            <span class="status-dot"></span>
            <span>Online</span>
        </div>
        <div class="user-info">
            <span class="user-name">{{ Auth::user()->name }}</span>
            <span class="user-role">{{ strtoupper(Auth::user()->role) }}</span>
        </div>
        <button class="btn-theme" id="theme-toggle" onclick="toggleTheme()"> Light Mode</button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</aside>

<!-- MAIN -->
<main class="main">
    @yield('content')
</main>

<script>
// ── Theme toggle ──
function toggleTheme() {
    const isLight = document.body.classList.toggle('light');
    localStorage.setItem('theme', isLight ? 'light' : 'dark');
    document.getElementById('theme-toggle').textContent = isLight ? ' Dark Mode' : ' Light Mode';
}

// Load preferensi saat halaman dibuka
(function() {
    const saved = localStorage.getItem('theme');
    if (saved === 'light') {
        document.body.classList.add('light');
        const btn = document.getElementById('theme-toggle');
        if (btn) btn.textContent = ' Dark Mode';
    }
})();

// ── Page loader ──
window.addEventListener('load', () => {
    setTimeout(() => {
        document.getElementById('page-loader').classList.add('hidden');
    }, 1400);
});

// ── Ripple effect ──
document.querySelectorAll('.btn-primary, .btn-detect').forEach(btn => {
    btn.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        ripple.className = 'ripple';
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = (e.clientX - rect.left - size/2) + 'px';
        ripple.style.top = (e.clientY - rect.top - size/2) + 'px';
        this.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    });
});

document.querySelectorAll('.card').forEach((card, i) => {
    card.style.animationDelay = (i * 0.08) + 's';
});

window.showToast = function(msg, color = '#1DB954') {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.style.color = color;
    toast.style.borderColor = color;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
};

// ── Sidebar toggle (mobile) ──
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.querySelector('.sidebar-overlay').classList.toggle('show');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.querySelector('.sidebar-overlay').classList.remove('show');
}
// Tutup sidebar otomatis saat klik menu item (di mobile)
document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', () => {
        if (window.innerWidth <= 900) closeSidebar();
    });
});
</script>

</body>
</html>