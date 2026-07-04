<?php

require_once('../core/response.php');
require_once('../core/Logger.php');
require_once('../helper/utils.php');

class UsuariosController {
    private UsuariosService $servicio;

    public function __construct(UsuariosService $servicio) {
        $this->servicio = $servicio;
    }

    // ✅ Usuarios
    public function obtenerUsuarios() {
        try {
            $data = $this->servicio->obtenerUsuarios();
            return response::success($data);
        } catch (\Exception $e) {
            Logger::error("Error al obtener usuarios", $e, $_REQUEST);
            return response::error('Error al obtener usuarios');
        }
    }

    public function registrarUsuario(array $data) {
        try {
            if (!utils::validateRequiredFields(['nombreUsuario', 'userUsuario', 'passwordUsuario', 'perfilUsuario'], $data)) {
                return response::error('Todos los campos son obligatorios');
            }

            $data['passwordUsuario'] = password_hash($data['passwordUsuario'], PASSWORD_DEFAULT);

            $resultado = $this->servicio->registrarUsuario($data);
            return response::success($resultado, 'Usuario registrado exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al registrar usuario", $e, ['data' => $data, 'request' => $_REQUEST]);
            return response::error('Error al registrar usuario');
        }
    }

    public function actualizarUsuario(array $data) {
        try {
            $resultado = $this->servicio->actualizarUsuario($data);
            return response::success($resultado, 'Usuario actualizado exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al actualizar usuario", $e, ['data' => $data, 'request' => $_REQUEST]);
            return response::error('Error al actualizar usuario');
        }
    }

    public function eliminarUsuario(int $idUsuario) {
        try {
            $resultado = $this->servicio->eliminarUsuario($idUsuario);
            return response::success($resultado, 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            Logger::error("Error al eliminar usuario", $e, ['idUsuario' => $idUsuario, 'request' => $_REQUEST]);
            return response::error('Error al eliminar usuario');
        }
    }

    // ✅ Perfiles
    public function obtenerPerfiles() {
        try {
            $data = $this->servicio->obtenerPerfiles();
            return response::success($data);
        } catch (\Exception $e) {
            Logger::error("Error al obtener perfiles", $e, $_REQUEST);
            return response::error('Error al obtener perfiles');
        }
    }

    public function registrarPerfil(array $data) {
        try {
            $resultado = $this->servicio->registrarPerfil($data);
            return response::success($resultado, 'Perfil registrado exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al registrar perfil", $e, ['data' => $data, 'request' => $_REQUEST]);
            return response::error('Error al registrar perfil');
        }
    }

    public function actualizarPerfil(array $data) {
        try {
            $resultado = $this->servicio->actualizarPerfil($data);
            return response::success($resultado, 'Perfil actualizado exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al actualizar perfil", $e, ['data' => $data, 'request' => $_REQUEST]);
            return response::error('Error al actualizar perfil');
        }
    }

    public function eliminarPerfil(int $idPerfil) {
        try {
            $resultado = $this->servicio->eliminarPerfil($idPerfil);
            return response::success($resultado, 'Perfil eliminado exitosamente');
        } catch (\Exception $e) {
            Logger::error("Error al eliminar perfil", $e, ['idPerfil' => $idPerfil, 'request' => $_REQUEST]);
            return response::error('Error al eliminar perfil');
        }
    }

    // ✅ Permisos
    public function obtenerPermisos() {
        try {
            $data = $this->servicio->obtenerPermisos();
            return response::success($data);
        } catch (\Exception $e) {
            Logger::error("Error al obtener permisos", $e, $_REQUEST);
            return response::error('Error al obtener permisos');
        }
    }

    public function registrarPermiso(array $data) {
        try {
            $resultado = $this->servicio->registrarPermiso($data);
            return response::success($resultado, 'Permiso registrado exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al registrar permiso", $e, ['data' => $data, 'request' => $_REQUEST]);
            return response::error('Error al registrar permiso');
        }
    }

    public function actualizarPermiso(array $data) {
        try {
            $resultado = $this->servicio->actualizarPermiso($data);
            return response::success($resultado, 'Permiso actualizado exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al actualizar permiso", $e, ['data' => $data, 'request' => $_REQUEST]);
            return response::error('Error al actualizar permiso');
        }
    }

    public function eliminarPermiso(int $idPermiso) {
        try {
            $resultado = $this->servicio->eliminarPermiso($idPermiso);
            return response::success($resultado, 'Permiso eliminado exitosamente');
        } catch (\Exception $e) {
            Logger::error("Error al eliminar permiso", $e, ['idPermiso' => $idPermiso, 'request' => $_REQUEST]);
            return response::error('Error al eliminar permiso');
        }
    }

    // ✅ Perfil - Permisos
    public function obtenerPerfilPermisos() {
        try {
            $data = $this->servicio->obtenerPerfilPermisos();
            return response::success($data);
        } catch (\Exception $e) {
            Logger::error("Error al obtener perfil-permisos", $e, $_REQUEST);
            return response::error('Error al obtener perfil-permisos');
        }
    }

    public function obtenerPermisosDelPerfil(int $idPerfil) {
        try {
            $data = $this->servicio->obtenerPermisosDelPerfil($idPerfil);
            return response::success($data);
        } catch (\Exception $e) {
            Logger::error("Error al obtener permisos del perfil", $e, ['idPerfil' => $idPerfil]);
            return response::error('Error al obtener permisos del perfil');
        }
    }

    public function asignarPermisosAlPerfil(array $data) {
        try {
            $idPerfil = $data['idPerfil'] ?? null;
            $permisosJson = $data['permisos'] ?? '[]';
            $permisos = json_decode($permisosJson, true);

            if (!$idPerfil || !is_array($permisos)) {
                return response::error('Datos inválidos');
            }

            $resultado = $this->servicio->asignarPermisosAlPerfil($idPerfil, $permisos);
            return response::success($resultado, 'Permisos asignados exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al asignar permisos", $e, ['data' => $data]);
            return response::error('Error al asignar permisos');
        }
    }

    public function eliminarPermisosDelPerfil(array $data) {
        try {
            $idPerfil = $data['idPerfil'] ?? null;

            if (!$idPerfil) {
                return response::error('ID del perfil requerido');
            }

            $resultado = $this->servicio->eliminarPermisosDelPerfil($idPerfil);
            return response::success($resultado, 'Permisos eliminados exitosamente');
        } catch (\Exception $e) {
            Logger::error("Error al eliminar permisos del perfil", $e, ['data' => $data]);
            return response::error('Error al eliminar permisos');
        }
    }
}
