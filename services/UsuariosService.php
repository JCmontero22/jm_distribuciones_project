<?php

require_once('../model/UsuariosModel.php');
require_once('../model/PermisosModel.php');
require_once('../model/PerfilUsuarioModel.php');

class UsuariosService {
    private UsuariosModel $usuariosModel;
    private PermisosModel $permisosModel;
    private PerfilUsuarioModel $perfilUsuarioModel;

    public function __construct(
        UsuariosModel $usuariosModel,
        PermisosModel $permisosModel,
        PerfilUsuarioModel $perfilUsuarioModel
    ) {
        $this->usuariosModel = $usuariosModel;
        $this->permisosModel = $permisosModel;
        $this->perfilUsuarioModel = $perfilUsuarioModel;
    }

    // ✅ Lógica de Usuarios
    public function obtenerUsuarios(): array {
        $usuarios = $this->usuariosModel->obtenerUsuarios();
        return empty($usuarios) ? [] : $usuarios;
    }

    public function registrarUsuario(array $data): bool {
        // Validar usando los mismos nombres de campos que el frontend y el modelo
        if (empty($data['nombreUsuario']) || empty($data['userUsuario']) || empty($data['passwordUsuario'])) {
            throw new DomainException('Todos los campos son obligatorios');
        }

        // Validar si el usuario ya existe
        if ($this->usuariosModel->existeUsuario($data['userUsuario'])) {
            throw new DomainException('El nombre de usuario ya existe');
        }

        return $this->usuariosModel->registroUsuario($data);
    }

    public function actualizarUsuario(array $data): bool {
        if (empty($data['idUsuario'])) {
            throw new DomainException('ID de usuario requerido');
        }

        return $this->usuariosModel->actualizarUsuario($data);
    }

    public function eliminarUsuario(int $idUsuario): bool {
        return $this->usuariosModel->eliminarUsuario($idUsuario);
    }

    // ✅ Lógica de Perfiles de Usuario
    public function obtenerPerfiles(): array {
        $perfiles = $this->perfilUsuarioModel->obtenerPerfiles();
        return empty($perfiles) ? [] : $perfiles;
    }

    public function registrarPerfil(array $data): bool {
        return $this->perfilUsuarioModel->registroPerfil($data);
    }

    public function actualizarPerfil(array $data): bool {
        return $this->perfilUsuarioModel->actualizarPerfil($data);
    }

    public function eliminarPerfil(int $idPerfil): bool {
        return $this->perfilUsuarioModel->eliminarPerfil($idPerfil);
    }

    // ✅ Lógica de Permisos
    public function obtenerPermisos(): array {
        $permisos = $this->permisosModel->obtenerPermisos();
        return empty($permisos) ? [] : $permisos;
    }

    public function registrarPermiso(array $data): bool {
        return $this->permisosModel->registroPermisos($data);
    }

    public function actualizarPermiso(array $data): bool {
        return $this->permisosModel->actualizarPermiso($data);
    }

    public function eliminarPermiso(int $idPermiso): bool {
        return $this->permisosModel->eliminarPermiso($idPermiso);
    }

    // ✅ Lógica de Perfil - Permisos
    public function obtenerPerfilPermisos(): array {
        $perfilPermisos = $this->perfilUsuarioModel->obtenerPerfilPermisos();
        return empty($perfilPermisos) ? [] : $perfilPermisos;
    }

    public function obtenerPermisosDelPerfil(int $idPerfil): array {
        $permisos = $this->perfilUsuarioModel->obtenerPermisosDelPerfil($idPerfil);
        return empty($permisos) ? [] : $permisos;
    }

    public function asignarPermisosAlPerfil(int $idPerfil, array $permisos): bool {
        if (empty($idPerfil) || empty($permisos)) {
            throw new DomainException('ID de perfil y permisos son requeridos');
        }

        // Primero elimina los permisos previos
        $this->perfilUsuarioModel->eliminarPermisosDelPerfil($idPerfil);

        // Luego asigna los nuevos
        return $this->perfilUsuarioModel->asignarPermisosAlPerfil($idPerfil, $permisos);
    }

    public function eliminarPermisosDelPerfil(int $idPerfil): bool {
        if (empty($idPerfil)) {
            throw new DomainException('ID de perfil requerido');
        }

        return $this->perfilUsuarioModel->eliminarPermisosDelPerfil($idPerfil);
    }
}
