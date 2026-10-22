-- Migration: Formulas genericas y vinculos de lociones
-- Date: 2026-10-21
-- Precondicion: 003 y 004 aplicadas.
--
-- Modelo:
--   formulas (genericas): volumen + concentracion + gramos de esencia + frasco.
--     id_presentacion_envase = presentacion de inventario del frasco (ej. BALAS 30ml).
--   productos_presentaciones (lociones vendibles): id_producto_esencia + id_formula.
--     La receta se deriva: gramos = formulas.cantidad_esencia de la esencia indicada,
--     envase = formulas.id_presentacion_envase, 1 unidad por loción.
--
-- Los vinculos se llenan desde la aplicacion, no con IDs fijos en esta migracion,
-- porque los IDs cambian al limpiar y recargar datos.
-- receta_presentacion queda sin uso; se puede eliminar cuando ya no haga falta.

ALTER TABLE formulas
    ADD COLUMN id_presentacion_envase INT NULL AFTER id_insumo_formula,
    ADD CONSTRAINT fk_formulas_envase FOREIGN KEY (id_presentacion_envase) REFERENCES productos_presentaciones (id_presentacion);

ALTER TABLE productos_presentaciones
    ADD COLUMN id_producto_esencia INT NULL AFTER id_producto,
    ADD COLUMN id_formula INT NULL AFTER id_producto_esencia,
    ADD CONSTRAINT fk_presentacion_esencia FOREIGN KEY (id_producto_esencia) REFERENCES productos (id_producto),
    ADD CONSTRAINT fk_presentacion_formula FOREIGN KEY (id_formula) REFERENCES formulas (id_formula);
