<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title><?= $estabelecimento->nome ?> | Links</title>

    <!-- Tabler CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>
    <link href="https://rsms.me/inter/inter.css" rel="stylesheet">

    <style>
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
            --primary-color: #00bd6f;

            /* Light Mode Variables */
            --bg-color: #f5f7fb;
            --text-color: #1e293b;
            --text-secondary: #6c7a91;
            --btn-bg: #ffffff;
            --btn-border: #e6e7e9;
            --btn-text: #1e293b;
            --footer-text: #1e293b;
        }

        [data-theme="dark"] {
            --bg-color: #151f2c; /* Escuro profundo */
            --text-color: #f8fafc;
            --text-secondary: #94a3b8;
            --btn-bg: #1e293b;
            --btn-border: #334155;
            --btn-text: #f8fafc;
            --footer-text: #f8fafc;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-feature-settings: "cv03", "cv04", "cv11";
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .profile-container {
            max-width: 480px;
            width: 100%;
            padding: 2rem 1rem;
            margin: auto;
            position: relative;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .profile-header h1 {
            color: var(--text-color);
        }
        .profile-header .text-secondary {
            color: var(--text-secondary) !important;
        }
        .avatar-xl {
            width: 100px;
            height: 100px;
            font-size: 2.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn-link-custom {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin-bottom: 1rem;
            padding: 1rem;
            background: var(--btn-bg);
            border: 1px solid var(--btn-border);
            border-radius: 50px;
            font-weight: 600;
            color: var(--btn-text);
            transition: all 0.2s ease;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            font-size: 1.1em;
        }
        .btn-link-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        .btn-link-custom:active {
            transform: translateY(0);
        }
        .btn-link-custom i {
            margin-right: 0.5rem;
            font-size: 1.4em;
        }
        .brand-footer {
            margin-top: 3rem;
            text-align: center;
            opacity: 0.6;
            font-size: 0.8rem;
            color: var(--footer-text);
        }
        .brand-footer a {
            color: var(--footer-text) !important;
        }
        .btn-agendar {
            background: var(--primary-color);
            color: #fff !important;
            border-color: var(--primary-color);
            font-size: 1.2em;
        }
        .btn-agendar:hover {
            background: #00995a;
            color: #fff !important;
            border-color: #00995a;
        }

        /* Toggle Switcher */
        .theme-switcher {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--btn-bg);
            border: 1px solid var(--btn-border);
            color: var(--text-color);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 100;
        }
        .theme-switcher:hover {
            transform: scale(1.1);
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="profile-container">

        <!-- Theme Switcher -->
        <button class="theme-switcher" onclick="toggleTheme()" title="Alternar Tema">
            <i class="ti ti-moon" id="theme-icon"></i>
        </button>
        <div class="profile-header">
            <?php if (!empty($estabelecimento->logo) && file_exists('./assets/uploads/' . $estabelecimento->logo)): ?>
                <img src="<?= base_url('assets/uploads/' . $estabelecimento->logo) ?>" class="avatar avatar-xl rounded-circle mb-3">
            <?php else: ?>
                <div class="avatar avatar-xl rounded-circle mb-3 bg-secondary-lt">
                    <?= substr($estabelecimento->nome, 0, 2) ?>
                </div>
            <?php endif; ?>

            <h1 class="h2 mb-1"><?= $estabelecimento->nome ?></h1>
            <div class="text-secondary"><?= $estabelecimento->cidade ?> - <?= $estabelecimento->estado ?></div>
        </div>

        <div class="links-list">
            <!-- Botão Principal de Agendamento -->
            <!-- Botão Principal de Agendamento (Direto para WhatsApp) -->
            <?php
                $whats_principal = preg_replace('/[^0-9]/', '', $estabelecimento->whatsapp);
                if (strlen($whats_principal) < 13) $whats_principal = '55' . $whats_principal;
                $msg_encoded = urlencode("Oi, quero agendar um horário!");
            ?>
            <a href="https://wa.me/<?= $whats_principal ?>?text=<?= $msg_encoded ?>" target="_blank" class="btn-link-custom btn-agendar">
                <i class="ti ti-brand-whatsapp"></i> Agendar Horário pelo WhatsApp
            </a>

            <!-- Website -->
            <?php if (!empty($estabelecimento->website)): ?>
            <a href="<?= $estabelecimento->website ?>" target="_blank" class="btn-link-custom">
                <i class="ti ti-world"></i> Visite nosso Site
            </a>
            <?php endif; ?>

            <!-- WhatsApp -->
            <?php if (!empty($estabelecimento->whatsapp)): ?>
                <?php
                    $whats = preg_replace('/[^0-9]/', '', $estabelecimento->whatsapp);
                    if (strlen($whats) < 13) $whats = '55' . $whats;
                ?>
            <a href="https://wa.me/<?= $whats ?>" target="_blank" class="btn-link-custom">
                <i class="ti ti-brand-whatsapp"></i> Falar no WhatsApp
            </a>
            <?php endif; ?>

            <!-- Instagram -->
            <?php if (!empty($estabelecimento->instagram)): ?>
                <?php
                    $insta = $estabelecimento->instagram;
                    if (strpos($insta, 'http') === false) {
                        $insta = 'https://instagram.com/' . str_replace('@', '', $insta);
                    }
                ?>
            <a href="<?= $insta ?>" target="_blank" class="btn-link-custom">
                <i class="ti ti-brand-instagram"></i> Instagram
            </a>
            <?php endif; ?>

            <!-- Facebook -->
            <?php if (!empty($estabelecimento->facebook)): ?>
            <a href="<?= $estabelecimento->facebook ?>" target="_blank" class="btn-link-custom">
                <i class="ti ti-brand-facebook"></i> Facebook
            </a>
            <?php endif; ?>

             <!-- Localização -->
             <!-- Localização -->
            <?php if (!empty($estabelecimento->endereco)): ?>
                <?php
                    // Monta array de partes do endereço para evitar vírgulas extras
                    $partes_endereco = [];
                    $partes_endereco[] = $estabelecimento->endereco;
                    if (!empty($estabelecimento->bairro)) $partes_endereco[] = $estabelecimento->bairro;
                    if (!empty($estabelecimento->cidade)) $partes_endereco[] = $estabelecimento->cidade;
                    if (!empty($estabelecimento->estado)) $partes_endereco[] = $estabelecimento->estado;
                    if (!empty($estabelecimento->cep)) $partes_endereco[] = $estabelecimento->cep;

                    // Junta com vírgula e espaço
                    $endereco_completo_string = implode(', ', $partes_endereco);
                ?>
            <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($endereco_completo_string) ?>" target="_blank" class="btn-link-custom">
                <i class="ti ti-map-pin"></i> Como Chegar
            </a>
            <?php endif; ?>
        </div>

        <div class="brand-footer">
            <a href="https://zappagenda.com.br" target="_blank" class="text-reset text-decoration-none">
                <i class="ti ti-bolt text-success"></i> Desenvolvido por <strong>ZappAgenda</strong>
            </a>
        </div>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            const currentTheme = html.getAttribute('data-theme');

            if (currentTheme === 'dark') {
                html.removeAttribute('data-theme');
                icon.className = 'ti ti-moon';
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                icon.className = 'ti ti-sun';
                localStorage.setItem('theme', 'dark');
            }
        }

        // Carregar preferência salva
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');

            if (savedTheme === 'dark') {
                html.setAttribute('data-theme', 'dark');
                if(icon) icon.className = 'ti ti-sun';
            }
        })();
    </script>
</body>
</html>
