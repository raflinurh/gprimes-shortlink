<?php

$file = __DIR__ . '/data/links.json';

if (!file_exists($file)) {
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0755, true);
    }
    file_put_contents($file, "{}");
}

$data = json_decode(file_get_contents($file), true);
if (!is_array($data)) {
    $data = [];
}

$slug = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

if ($slug === '' || $slug === 'index.php') {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GPrimes.net</title>
        <style>
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
                font-family: Arial, Helvetica, sans-serif;
            }

            body {
                min-height: 100vh;
                background: linear-gradient(135deg, #0f172a, #1e293b, #334155);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
            }

            .card {
                width: 100%;
                max-width: 760px;
                background: rgba(255, 255, 255, 0.08);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.12);
                border-radius: 24px;
                padding: 40px 32px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
                text-align: center;
            }

            .badge {
                display: inline-block;
                padding: 8px 14px;
                border-radius: 999px;
                background: rgba(59, 130, 246, 0.18);
                color: #93c5fd;
                font-size: 13px;
                margin-bottom: 18px;
                border: 1px solid rgba(147, 197, 253, 0.25);
            }

            h1 {
                font-size: 42px;
                line-height: 1.1;
                margin-bottom: 14px;
            }

            p {
                color: #cbd5e1;
                font-size: 16px;
                line-height: 1.7;
                max-width: 560px;
                margin: 0 auto 28px;
            }

            .example {
                margin: 20px auto 30px;
                background: rgba(15, 23, 42, 0.55);
                border: 1px solid rgba(255,255,255,0.08);
                border-radius: 16px;
                padding: 16px 18px;
                color: #e2e8f0;
                font-size: 15px;
                word-break: break-word;
            }

            .buttons {
                display: flex;
                gap: 14px;
                justify-content: center;
                flex-wrap: wrap;
                margin-top: 10px;
            }

            .btn {
                display: inline-block;
                text-decoration: none;
                padding: 14px 22px;
                border-radius: 14px;
                font-weight: bold;
                transition: 0.2s ease;
                border: 1px solid transparent;
            }

            .btn-primary {
                background: #3b82f6;
                color: #fff;
                box-shadow: 0 10px 25px rgba(59, 130, 246, 0.35);
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                background: #2563eb;
            }

            .btn-secondary {
                background: rgba(255,255,255,0.08);
                color: #fff;
                border-color: rgba(255,255,255,0.14);
            }

            .btn-secondary:hover {
                transform: translateY(-2px);
                background: rgba(255,255,255,0.14);
            }

            .footer {
                margin-top: 28px;
                font-size: 13px;
                color: #94a3b8;
            }

            .not-found {
                background: rgba(239, 68, 68, 0.12);
                border: 1px solid rgba(248, 113, 113, 0.25);
                color: #fecaca;
                padding: 14px 16px;
                border-radius: 14px;
                margin: 20px auto 0;
                max-width: 420px;
            }

            @media (max-width: 640px) {
                .card {
                    padding: 30px 20px;
                }

                h1 {
                    font-size: 32px;
                }

                .btn {
                    width: 100%;
                }

                .buttons {
                    flex-direction: column;
                }
            }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="badge">GPrimes Network</div>
            <h1>Welcome to GPrimes</h1>
            <p>
                Shortlink service untuk redirect link cepat dan akses tool internal.
                Kamu bisa langsung masuk ke halaman admin dari sini.
            </p>

            <div class="example">
                Contoh shortlink: <strong>https://gprimes.net/abcd</strong>
            </div>

            <div class="buttons">
                <a class="btn btn-primary" href="/admin.php">Admin Panel</a>
            </div>

            <div class="footer">
                © <?php echo date('Y'); ?> gprimes.net
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if (isset($data[$slug]) && isset($data[$slug]['url'])) {
    if (!isset($data[$slug]['hits'])) {
        $data[$slug]['hits'] = 0;
    }

    $data[$slug]['hits']++;

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    header("Location: " . $data[$slug]['url']);
    exit;
}

http_response_code(404);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Tidak Ditemukan - GPrimes</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #111827, #1f2937, #374151);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .box {
            width: 100%;
            max-width: 560px;
            text-align: center;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 24px;
            padding: 36px 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
        }

        h1 {
            font-size: 30px;
            margin-bottom: 14px;
        }

        p {
            color: #d1d5db;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        a {
            display: inline-block;
            text-decoration: none;
            background: #3b82f6;
            color: #fff;
            padding: 12px 18px;
            border-radius: 12px;
            font-weight: bold;
        }

        a:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Link Tidak Ditemukan</h1>
        <p>
            Shortlink <strong><?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?></strong> tidak tersedia atau sudah dihapus.
        </p>
        <a href="/">Kembali ke Homepage</a>
    </div>
</body>
</html>