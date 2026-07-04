<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <!-- Custom CSS -->
    <?php $bp = defined('BASE_PATH') ? BASE_PATH : '/'; ?>
    <link rel="stylesheet" href="<?= $bp ?>assets/css/style.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.css" />
    <title>DashBoard</title>

    <!-- Función redirect global (DEBE SER PRIMERO) -->
    <script>
        function redirect(vista) {
            const baseUrl = '<?php echo $bp; ?>';
            window.location.href = baseUrl + vista;
        }
    </script>

    <!-- Navbar Header CSS -->
    <style>
        :root {
            --color-navbar: #1a1a1a;
            --color-text-primary: #f4f4f5;
            --color-text-secondary: #a1a1aa;
            --color-dorado: #eab308;
            --color-hover: #27272a;
        }

        .navbar-top {
            background-color: var(--color-navbar);
            border-bottom: 1px solid var(--color-hover);
            padding: 12px 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background-color: var(--color-hover);
            border-radius: 8px;
            border: 1px solid var(--color-text-secondary);
        }

        .user-icon {
            font-size: 1.5rem;
            color: var(--color-dorado);
        }

        .user-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--color-text-primary);
            margin: 0;
        }

        .user-role {
            font-size: 0.8rem;
            color: var(--color-text-secondary);
            margin: 0;
        }

        .btn-logout {
            padding: 8px 16px;
            background-color: #dc2626;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-logout:hover {
            background-color: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .btn-logout:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .navbar-top {
                gap: 12px;
                padding: 10px 15px;
            }

            .user-info {
                gap: 8px;
                padding: 6px 10px;
            }

            .user-name {
                font-size: 0.85rem;
            }

            .user-role {
                font-size: 0.75rem;
            }

            .btn-logout {
                padding: 6px 12px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            .user-details {
                display: none;
            }

            .navbar-top {
                gap: 8px;
            }

            .user-info {
                padding: 6px 10px;
            }
        }
    </style>
</head>
    <body>
        <!-- Navbar con datos del usuario -->
        <nav class="navbar-top">
            <div class="user-info">
                <i class="fas fa-user-circle user-icon"></i>
                <div class="user-details">
                    <p class="user-name"><?php echo htmlspecialchars(Auth::user()['nombre_usuario'] ?? 'Usuario'); ?></p>
                    <p class="user-role"><?php echo htmlspecialchars(Auth::userProfile() ?? 'Invitado'); ?></p>
                </div>
            </div>
            <button id="logoutBtn" class="btn-logout" title="Cerrar sesión">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </nav>
        


