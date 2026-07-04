<?php
$bp = defined('BASE_PATH') ? BASE_PATH : '/';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Error del Servidor | JM Distribuciones</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --color-fondo-principal: #09090b;
            --color-fondo-tarjeta: #18181b;
            --color-borde: #27272a;
            --texto-principal: #f4f4f5;
            --texto-secundario: #a1a1aa;
            --color-dorado: #eab308;
            --color-dorado-brillo: #facc15;
            --color-error: #ef4444;
        }

        html, body {
            height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, var(--color-fondo-principal) 0%, #12121a 100%);
            color: var(--texto-principal);
        }

        .error-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .error-card {
            width: 100%;
            max-width: 600px;
            background: linear-gradient(135deg, var(--color-fondo-tarjeta) 0%, #1f1f23 100%);
            border: 1px solid var(--color-borde);
            border-radius: 16px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-icon {
            font-size: 5rem;
            margin-bottom: 30px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .error-500 {
            color: var(--color-error);
        }

        .error-code {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 15px;
            background: linear-gradient(135deg, var(--color-dorado), var(--color-dorado-brillo));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .error-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--texto-principal);
        }

        .error-description {
            font-size: 1rem;
            color: var(--texto-secundario);
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .error-actions {
            display: flex;
            gap: 15px;
            flex-direction: column;
            align-items: center;
        }

        .btn-action {
            padding: 14px 30px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            min-width: 200px;
            justify-content: center;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, var(--color-dorado), var(--color-dorado-brillo));
            color: #000;
        }

        .btn-primary-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(234, 179, 8, 0.3);
        }

        .btn-secondary-action {
            background-color: var(--color-borde);
            color: var(--texto-principal);
            border: 1px solid var(--color-dorado);
        }

        .btn-secondary-action:hover {
            background-color: var(--color-dorado);
            color: #000;
            transform: translateY(-3px);
        }

        .error-footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid var(--color-borde);
            font-size: 0.85rem;
            color: var(--texto-secundario);
        }

        .error-alert {
            margin-top: 30px;
            padding: 20px;
            background-color: rgba(239, 68, 68, 0.1);
            border-left: 4px solid var(--color-error);
            border-radius: 6px;
            text-align: left;
            font-size: 0.85rem;
            color: #fca5a5;
        }

        .error-alert strong {
            color: #f87171;
        }

        @media (max-width: 768px) {
            .error-card {
                padding: 40px 25px;
            }

            .error-icon {
                font-size: 3.5rem;
                margin-bottom: 20px;
            }

            .error-code {
                font-size: 3rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .error-actions {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
            }

            .error-alert {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon error-500">
                <i class="fas fa-triangle-exclamation"></i>
            </div>

            <div class="error-code">500</div>

            <h1 class="error-title">Error del Servidor</h1>

            <p class="error-description">
                Oops, algo salió mal en nuestro servidor.
                <br>
                Nuestro equipo técnico ya ha sido notificado y estamos trabajando para resolverlo.
            </p>

            <div class="error-alert">
                <strong>⚠ Error Interno:</strong> Verifica el archivo de logs para más detalles sobre el problema.
            </div>

            <div class="error-actions">
                <a href="<?= $bp ?>home" class="btn-action btn-primary-action">
                    <i class="fas fa-home"></i>
                    Ir al Inicio
                </a>
                <a href="javascript:history.back()" class="btn-action btn-secondary-action">
                    <i class="fas fa-arrow-left"></i>
                    Volver Atrás
                </a>
            </div>

            <div class="error-footer">
                <p>JM Distribuciones © 2026 - Todos los derechos reservados</p>
            </div>
        </div>
    </div>
</body>
</html>
