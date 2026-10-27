-- Migration: Usuario que registra la compra
-- Date: 2026-10-27
-- Las compras anteriores quedan con id_usuario NULL (no se sabe quien las registro).

ALTER TABLE compras
    ADD COLUMN id_usuario INT NULL AFTER id_sede,
    ADD CONSTRAINT fk_compras_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario);
