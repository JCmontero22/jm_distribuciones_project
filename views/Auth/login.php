<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - JM Distribuciones</title>

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
            background-color: var(--color-fondo-principal);
            color: var(--texto-principal);
        }

        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: linear-gradient(135deg, var(--color-fondo-tarjeta) 0%, #1f1f23 100%);
            border: 1px solid var(--color-borde);
            border-radius: 12px;
            padding: 50px 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-container {
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--color-dorado);
            font-weight: 700;
        }

        .login-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--texto-principal);
        }

        .login-header p {
            font-size: 0.9rem;
            color: var(--texto-secundario);
            margin: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--texto-principal);
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background-color: #1a1a1d;
            border: 1px solid var(--color-borde);
            border-radius: 8px;
            color: var(--texto-principal);
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--color-dorado);
            box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.1);
            background-color: #1a1a1d;
            color: var(--texto-principal);
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle .toggle-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--texto-secundario);
            cursor: pointer;
            padding: 5px;
            transition: color 0.3s;
        }

        .password-toggle .toggle-btn:hover {
            color: var(--color-dorado);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--color-dorado) 0%, var(--color-dorado-brillo) 100%);
            border: none;
            border-radius: 8px;
            color: #000000;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(234, 179, 8, 0.3);
        }

        .btn-login:disabled {
            background: linear-gradient(135deg, #a1a1aa 0%, #71717a 100%);
            cursor: not-allowed;
            transform: none;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0;
            font-size: 0.85rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .remember-me input[type="checkbox"] {
            cursor: pointer;
            width: 18px;
            height: 18px;
            accent-color: var(--color-dorado);
        }

        .remember-me label {
            cursor: pointer;
            color: var(--texto-secundario);
            margin-bottom: 0;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid;
            padding: 12px 16px;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .alert-success {
            background-color: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.3);
            color: #86efac;
        }

        .loading-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            border-top-color: #000000;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-login.loading {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-login.loading .loading-spinner {
            display: inline-block;
            margin-right: 8px;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 40px 25px;
            }

            .login-header h1 {
                font-size: 1.6rem;
            }

            .remember-forgot {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <i class="fas fa-box-open"></i>
                </div>
                <h1>JM Distribuciones</h1>
                <p>Sistema de Gestión de Inventario</p>
            </div>

            <div id="alertContainer"></div>

            <form id="loginForm" method="POST">
                <div class="form-group">
                    <label for="usuario" class="form-label">
                        <i class="fas fa-user" style="margin-right: 6px;"></i>Usuario
                    </label>
                    <input
                        type="text"
                        id="usuario"
                        name="usuario"
                        class="form-control"
                        placeholder="Ingresa tu usuario"
                        required
                        autocomplete="username"
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock" style="margin-right: 6px;"></i>Contraseña
                    </label>
                    <div class="password-toggle">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Ingresa tu contraseña"
                            required
                            autocomplete="current-password"
                        >
                        <button
                            type="button"
                            class="toggle-btn"
                            id="togglePassword"
                            aria-label="Mostrar/Ocultar contraseña"
                        >
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="recordarme" name="recordarme">
                        <label for="recordarme">Recuérdame</label>
                    </div>
                </div>

                <button type="submit" id="loginBtn" class="btn-login">
                    <span class="btn-text">Iniciar Sesión</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Librerías externas -->
    <script src="https://code.jquery.com/jquery-4.0.0.min.js" integrity="sha256-OaVG6prZf4v69dPg6PhVattBXkcOWQB62pdZ3ORyrao=" crossorigin="anonymous"></script>

    <!-- Cargar módulo de login -->
    <script src="<?php echo defined('BASE_PATH') ? BASE_PATH : '/'; ?>assets/js/config/config.js"></script>
    <script src="<?php echo defined('BASE_PATH') ? BASE_PATH : '/'; ?>assets/js/core/SimpleAPI.js"></script>
    <script src="<?php echo defined('BASE_PATH') ? BASE_PATH : '/'; ?>assets/js/modules/login.js"></script>
</body>
</html>
