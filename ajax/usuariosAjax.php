<?php
require_once('../config/config.php');
require_once(__DIR__ . '/../core/ServiceContainer.php');
require_once(__DIR__ . '/../controller/UsuariosController.php');

$accion = isset($_POST['accion']) ? $_POST['accion'] : (isset($_GET['accion']) ? $_GET['accion'] : null);

$usuariosController = null;
$getUsuariosController = function () use (&$usuariosController) {
    if ($usuariosController === null) {
        $servicio = ServiceContainer::getUsuariosService();
        $usuariosController = new UsuariosController($servicio);
    }
    return $usuariosController;
};

switch ($accion) {

    // USUARIOS
    case 'listadoUsuarios':
        $resultado = $getUsuariosController()->obtenerUsuarios();
        echo json_encode($resultado);
        break;

    case 'registrarUsuario':
        $resultado = $getUsuariosController()->registrarUsuario($_POST);
        echo json_encode($resultado);
        break;

    case 'actualizarUsuario':
        $resultado = $getUsuariosController()->actualizarUsuario($_POST);
        echo json_encode($resultado);
        break;

    case 'eliminarUsuario':
        $resultado = $getUsuariosController()->eliminarUsuario((int)($_POST['idUsuario'] ?? 0));
        echo json_encode($resultado);
        break;

    // PERFILES
    case 'listadoPerfiles':
        $resultado = $getUsuariosController()->obtenerPerfiles();
        echo json_encode($resultado);
        break;

    case 'registrarPerfil':
        $resultado = $getUsuariosController()->registrarPerfil($_POST);
        echo json_encode($resultado);
        break;

    case 'actualizarPerfil':
        $resultado = $getUsuariosController()->actualizarPerfil($_POST);
        echo json_encode($resultado);
        break;

    case 'eliminarPerfil':
        $resultado = $getUsuariosController()->eliminarPerfil((int)($_POST['idPerfil'] ?? 0));
        echo json_encode($resultado);
        break;

    // PERMISOS
    case 'listadoPermisos':
        $resultado = $getUsuariosController()->obtenerPermisos();
        echo json_encode($resultado);
        break;

    case 'registrarPermiso':
        $resultado = $getUsuariosController()->registrarPermiso($_POST);
        echo json_encode($resultado);
        break;

    case 'actualizarPermiso':
        $resultado = $getUsuariosController()->actualizarPermiso($_POST);
        echo json_encode($resultado);
        break;

    case 'eliminarPermiso':
        $resultado = $getUsuariosController()->eliminarPermiso((int)($_POST['idPermiso'] ?? 0));
        echo json_encode($resultado);
        break;

    // PERFIL - PERMISOS
    case 'listadoPerfilPermisos':
        $resultado = $getUsuariosController()->obtenerPerfilPermisos();
        echo json_encode($resultado);
        break;

    case 'obtenerPermisosDelPerfil':
        $idPerfil = (int)($_GET['idPerfil'] ?? 0);
        $resultado = $getUsuariosController()->obtenerPermisosDelPerfil($idPerfil);
        echo json_encode($resultado);
        break;

    case 'asignarPermisosAlPerfil':
        $resultado = $getUsuariosController()->asignarPermisosAlPerfil($_POST);
        echo json_encode($resultado);
        break;

    case 'eliminarPermisosDelPerfil':
        $resultado = $getUsuariosController()->eliminarPermisosDelPerfil($_POST);
        echo json_encode($resultado);
        break;

    default:
        echo json_encode(response::error('Acción no válida', ['accion' => $accion]));
        break;
}
