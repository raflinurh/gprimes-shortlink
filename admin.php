<?php

$password = "admin123";
session_start();

if (isset($_GET['logout'])) {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
    header("Location: admin.php");
    exit;
}

if (!isset($_SESSION['login'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['password']) && $_POST['password'] === $password) {
            $_SESSION['login'] = true;
            header("Location: admin.php");
            exit;
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - GPrimes</title>
        <style>
            * { box-sizing: border-box; font-family: Inter, Arial, Helvetica, sans-serif; }

            body {
                margin: 0;
                min-height: 100vh;
                background:
                    radial-gradient(circle at top left, rgba(59,130,246,.18), transparent 30%),
                    radial-gradient(circle at bottom right, rgba(16,185,129,.12), transparent 30%),
                    linear-gradient(135deg, #0b1220, #111827, #1f2937);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
                color: #fff;
            }

            .card {
                width: 100%;
                max-width: 440px;
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.12);
                border-radius: 24px;
                padding: 32px;
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
                backdrop-filter: blur(12px);
            }

            .badge {
                display: inline-block;
                padding: 8px 14px;
                border-radius: 999px;
                background: rgba(59, 130, 246, 0.15);
                color: #93c5fd;
                border: 1px solid rgba(147, 197, 253, 0.2);
                font-size: 13px;
                margin-bottom: 16px;
            }

            h2 {
                margin: 0 0 10px;
                font-size: 28px;
            }

            p {
                color: #cbd5e1;
                margin: 0 0 24px;
                line-height: 1.6;
            }

            input {
                width: 100%;
                padding: 14px 16px;
                border-radius: 14px;
                border: 1px solid rgba(255, 255, 255, 0.12);
                background: rgba(255, 255, 255, 0.08);
                color: #fff;
                margin-bottom: 14px;
                outline: none;
            }

            input::placeholder {
                color: #94a3b8;
            }

            input:focus {
                border-color: #60a5fa;
                box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            }

            button {
                width: 100%;
                padding: 14px;
                border: 0;
                border-radius: 14px;
                background: linear-gradient(135deg, #3b82f6, #2563eb);
                color: #fff;
                font-weight: 700;
                cursor: pointer;
                transition: 0.2s ease;
            }

            button:hover {
                transform: translateY(-1px);
                box-shadow: 0 12px 30px rgba(37, 99, 235, 0.35);
            }

            .back {
                display: block;
                text-align: center;
                margin-top: 16px;
                color: #93c5fd;
                text-decoration: none;
            }

            .back:hover {
                color: #bfdbfe;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="badge">GPrimes Admin</div>
            <h2>Login Dashboard</h2>
            <p>Masuk untuk mengelola shortlink gprimes.net dari satu tempat.</p>

            <form method="post">
                <input type="password" name="password" placeholder="Masukkan password admin" required>
                <button type="submit">Masuk</button>
            </form>

            <a class="back" href="/">← Kembali ke homepage</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$file = __DIR__ . '/data/links.json';

if (!file_exists($file)) {
    file_put_contents($file, "{}");
}

$data = json_decode(file_get_contents($file), true);
if (!is_array($data)) {
    $data = [];
}

/* TAMBAH LINK */
if (isset($_POST['slug'], $_POST['url'])) {
    $slug = trim($_POST['slug']);
    $url = trim($_POST['url']);

    if ($slug !== '' && $url !== '') {
        $hits = isset($data[$slug]['hits']) ? (int)$data[$slug]['hits'] : 0;
        unset($data[$slug]);
        $data[$slug] = [
            "url" => $url,
            "hits" => $hits
        ];

        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        header("Location: admin.php");
        exit;
    }
}

/* DELETE LINK */
if (isset($_POST['delete_slug'])) {
    $deleteSlug = trim($_POST['delete_slug']);

    if ($deleteSlug !== '' && isset($data[$deleteSlug])) {
        unset($data[$deleteSlug]);
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    header("Location: admin.php");
    exit;
}

$totalLinks = count($data);
$totalClicks = 0;

foreach ($data as $item) {
    $totalClicks += isset($item['hits']) ? (int)$item['hits'] : 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - GPrimes</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Inter, Arial, Helvetica, sans-serif;
        }

        body {
            margin: 0;
            background:
                radial-gradient(circle at top left, rgba(59,130,246,.10), transparent 20%),
                linear-gradient(180deg, #0b1220 0%, #0f172a 100%);
            color: #fff;
            padding: 24px;
        }

        .wrap {
            max-width: 1120px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .title h1 {
            margin: 0 0 6px;
            font-size: 32px;
        }

        .muted {
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.6;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .actions a {
            text-decoration: none;
            color: #fff;
            padding: 11px 15px;
            border-radius: 12px;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.08);
            transition: 0.2s ease;
        }

        .btn-home { background: #1e293b; }
        .btn-logout { background: #dc2626; }

        .actions a:hover {
            transform: translateY(-1px);
            opacity: .95;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card,
        .card {
            background: rgba(17, 24, 39, 0.88);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
        }

        .stat-label {
            color: #93c5fd;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
        }

        h2 {
            margin: 0 0 16px;
            font-size: 22px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 220px 1fr 140px;
            gap: 12px;
        }

        input {
            width: 100%;
            padding: 14px 15px;
            border-radius: 14px;
            border: 1px solid #334155;
            background: #0f172a;
            color: #fff;
            outline: none;
        }

        input::placeholder {
            color: #94a3b8;
        }

        input:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        button {
            padding: 14px 18px;
            border: 0;
            border-radius: 14px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.3);
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 16px 12px;
            border-bottom: 1px solid #1f2937;
            vertical-align: top;
        }

        th {
            color: #93c5fd;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .slug {
            font-weight: 700;
            margin-bottom: 6px;
        }

        .slug-url {
            color: #94a3b8;
            font-size: 14px;
            word-break: break-word;
        }

        .target-link {
            color: #93c5fd;
            text-decoration: none;
            word-break: break-all;
        }

        .target-link:hover {
            color: #bfdbfe;
        }

        .hit-badge {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(59,130,246,.12);
            color: #bfdbfe;
            font-weight: 700;
            font-size: 13px;
            border: 1px solid rgba(59,130,246,.2);
        }

        .delete-form {
            margin: 0;
        }

        .delete-btn {
            background: #dc2626;
            border: none;
            color: #fff;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            width: auto;
            box-shadow: none;
        }

        .delete-btn:hover {
            background: #b91c1c;
            transform: translateY(-1px);
            box-shadow: none;
        }

        .empty {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
            border: 1px dashed #334155;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.5);
        }

        .footer-note {
            margin-top: 18px;
            color: #64748b;
            font-size: 13px;
        }

        @media (max-width: 860px) {
            .stats {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .title h1 {
                font-size: 26px;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 16px;
            }

            .card, .stat-card {
                padding: 18px;
                border-radius: 18px;
            }

            th, td {
                padding: 14px 10px;
            }

            .actions {
                width: 100%;
            }

            .actions a {
                flex: 1 1 auto;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="topbar">
            <div class="title">
                <h1>GPrimes Admin Panel</h1>
                <div class="muted">Kelola shortlink dan pantau statistik klik dari satu dashboard.</div>
            </div>

            <div class="actions">
                <a href="/" class="btn-home">Home</a>
                <a href="admin.php?logout=1" class="btn-logout">Logout</a>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-label">Total Shortlink</div>
                <div class="stat-value"><?php echo $totalLinks; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Klik</div>
                <div class="stat-value"><?php echo $totalClicks; ?></div>
            </div>
        </div>

        <div class="card">
            <h2>Buat Shortlink Baru</h2>
            <form method="post" id="linkForm">
                <div class="form-grid">
                    <input type="text" name="slug" placeholder="Slug, contoh: abcd" required>
                    <input type="url" name="url" placeholder="https://example.com" required>
                    <button type="submit">Simpan Link</button>
                </div>
            </form>
            <div class="footer-note">
                Contoh hasil: <strong>gprimes.net/abcd</strong>
            </div>
        </div>

        <div style="height: 20px"></div>

        <div class="card">
            <h2>Daftar Link</h2>

            <?php if (empty($data)): ?>
                <div class="empty">
                    Belum ada shortlink yang dibuat.
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Slug</th>
                                <th>Tujuan</th>
                                <th>Klik</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_reverse($data, true) as $slug => $link): ?>
                                <tr>
                                    <td>
                                        <div class="slug"><?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div class="slug-url">https://gprimes.net/<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?></div>
                                    </td>
                                    <td>
                                        <a class="target-link" href="<?php echo htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" title="<?php echo htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php 
                                                $displayUrl = $link['url'];
                                                echo htmlspecialchars(strlen($displayUrl) > 50 ? substr($displayUrl, 0, 47) . '...' : $displayUrl, ENT_QUOTES, 'UTF-8'); 
                                            ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="hit-badge">
                                            <?php echo isset($link['hits']) ? (int)$link['hits'] : 0; ?> klik
                                        </span>
                                    </td>
                                    <td>
                                        <form method="post" class="delete-form" onsubmit="return confirm('Yakin mau hapus shortlink ini?');">
                                            <input type="hidden" name="delete_slug" value="<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>">
                                            <button type="submit" class="delete-btn">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        const existingSlugs = <?php echo json_encode(array_keys($data)); ?>;
        document.getElementById('linkForm').onsubmit = function(e) {
            const slug = this.querySelector('input[name="slug"]').value.trim();
            if (existingSlugs.indexOf(slug) !== -1) {
                if (!confirm('Slug "' + slug + '" sudah ada. Apakah Anda ingin mengubahnya (update)?')) {
                    return false;
                }
            }
            return true;
        };
    </script>
</body>
</html>