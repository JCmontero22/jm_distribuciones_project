-- Migration: Sede asignada al usuario
-- Date: 2026-10-22
-- Un usuario con id_sede vende y consulta solo en esa sede (vendedor).
-- Un usuario sin id_sede es administrador: elige la sede en cada operacion.

ALTER TABLE usuario
    ADD COLUMN id_sede INT NULL AFTER id_perfil,
    ADD CONSTRAINT fk_usuario_sede FOREIGN KEY (id_sede) REFERENCES sedes (id_sede);
