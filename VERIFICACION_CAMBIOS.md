# Guía de Verificación - Consolidación de Inventario por Producto

## 📋 Resumen de Cambios

Se modificaron 4 archivos para consolidar el inventario de esencias por producto en lugar de por presentación:

1. **CapacidadProduccionModel.php** - Queries para agregar por producto
2. **CapacidadProduccionService.php** - Lógica de cálculo actualizada
3. **FormulaCalculatorService.php** - Usa costo promedio ponderado
4. **ComprasModel.php** - Dispara recálculo con id_producto

---

## 🧪 Plan de Pruebas

### Escenario: Esencia "212 MEN" con presentaciones 125g y 250g

#### Paso 1: Registrar primera presentación
1. Ir a **Compras** → crear compra
2. Seleccionar producto: **212 MEN - 125 gramos**
3. Cantidad: 1 unidad
4. Precio compra: $27,000
5. Guardar

**Esperado:** 
- `inventario_esencias`: 1 registro con id_presentacion=2, cantidad_gramos=125, costo_por_gramo=216

#### Paso 2: Registrar segunda presentación del MISMO producto
1. Crear otra compra
2. Seleccionar producto: **212 MEN - 250 gramos** (o la ID que tengas)
3. Cantidad: 1 unidad
4. Precio compra: $50,000
5. Guardar

**Esperado:**
- `inventario_esencias`: Ahora tienes 2 registros (una por presentación)
  - Presentación 2: 125g @ $216/g
  - Presentación 7: 250g @ $200/g

#### Paso 3: Ver Reporte de Capacidad de Producción
1. Ir a **Compras** → pestaña **Capacidad de Producción**
2. Buscar la esencia "212 MEN"

**Esperado (con los cambios aplicados):**
- **Una sola fila** para "212 MEN" mostrando:
  - Cantidad total: **375 gramos** (125 + 250)
  - Costo promedio: **$205.33/gramo** (cálculo: (125×216 + 250×200) / 375)
  - Bajo esa fila: todas las fórmulas que usan esta esencia con su capacidad

**Antes (sin cambios):**
- Dos filas separadas (una por presentación)
- Cada una mostraría su capacidad por separado
- No se veía el total disponible

---

## ✅ Checklist de Verificación

- [ ] El reporte de capacidad muestra **1 fila** por esencia (no una por presentación)
- [ ] La cantidad total es la **suma** de todas las presentaciones
- [ ] El costo por gramo es un **promedio ponderado**
- [ ] Las fórmulas pueden usarse con el stock total (no separado)
- [ ] No hay errores en `logs/app.log`
- [ ] La interfaz JavaScript sigue funcionando (sin errores en consola)

---

## 🔍 Verificación en Base de Datos

### Consulta SQL para validar
```sql
-- Ver inventarios agrupados (cómo los ve el sistema ahora)
SELECT 
    p.id_producto,
    p.nombre_producto,
    ie.id_sede,
    SUM(ie.cantidad_gramos) AS cantidad_total,
    SUM(ie.cantidad_gramos * ie.costo_por_gramo) / SUM(ie.cantidad_gramos) AS costo_promedio
FROM inventario_esencias ie
INNER JOIN productos_presentaciones pp ON ie.id_presentacion = pp.id_presentacion
INNER JOIN productos p ON pp.id_producto = p.id_producto
WHERE ie.cantidad_gramos > 0
GROUP BY p.id_producto, p.nombre_producto, ie.id_sede;
```

**Resultado esperado:** Una fila por producto, no por presentación

---

## 📊 Ejemplo Numérico

Si tienes:
- 125g @ $216/g = costo total $27,000
- 250g @ $200/g = costo total $50,000

**Cálculo del promedio ponderado:**
```
costo_promedio = (125 × 216 + 250 × 200) / (125 + 250)
               = (27,000 + 50,000) / 375
               = 77,000 / 375
               = $205.33 por gramo
```

Si una loción necesita 30g:
- Costo de producción = 30g × $205.33 = $6,160

---

## 🐛 Si Hay Errores

### Error: "No se pudo obtener costo promedio"
- Verifica en `logs/app.log` el mensaje de error
- Asegúrate de que existen registros en `inventario_esencias` para esa esencia

### Error: "Undefined array key 'cantidad_gramos'"
- Limpia el cache del navegador (F12 → Clear Storage)
- Recarga la página

### Las fórmulas no se actualizan después de comprar
- Verifica que `FormulaCalculatorService` se ejecutó sin errores
- Revisa `logs/app.log` para mensajes de `FormulaCalculatorService`

---

## 📝 Notas

- El esquema de BD **NO cambió** - solo las queries de lectura
- El historial de compras sigue guardando el detalle por presentación
- Los cambios son **trasparentes** para el usuario final
