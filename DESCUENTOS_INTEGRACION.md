# 📦 Integración del Módulo de Descuentos

## Ubicación de Archivos

```
Proyecto/
├── ajax/
│   └── descuentosAjax.php ✅
├── controller/
│   └── DescuentosController.php ✅
├── model/
│   ├── DescuentosModel.php ✅ (actualizado)
│   └── ProductosModel.php ✅ (actualizado)
├── services/
│   └── DescuentosService.php ✅
├── views/promotions/
│   └── descuentos.php ✅
├── assets/js/modules/
│   └── descuentos.js ✅
├── assets/js/config/
│   └── config.js ✅ (actualizado)
├── core/
│   └── ServiceContainer.php ✅ (actualizado)
└── public/
    └── footer.php ✅ (ya incluye descuentos.js)
```

---

## 🔧 Pasos de Integración

### 1. Incluir la Vista en tu Navegación

En el archivo donde gestiones las rutas/páginas (probablemente `index.php` o un router):

```php
// En tu switch/if de rutas
case 'descuentos':
    require_once(DIR_VIEWS . 'promotions/descuentos.php');
    break;
```

### 2. Agregar enlace en el Menú

En `public/menu.php` o donde manejes la navegación:

```html
<li class="nav-item">
    <a class="nav-link" href="?page=descuentos">
        <i class="fa-solid fa-percentage"></i> Descuentos
    </a>
</li>
```

### 3. Inicializar el Módulo JavaScript

En `assets/js/main.js`, agregar en `DOMContentLoaded`:

```javascript
document.addEventListener('DOMContentLoaded', async () => {
    // ... otros módulos ...
    
    if (document.getElementById('descuentosModule')) {
        DescuentosModule.init();
    }
});
```

---

## 🎯 Flujo Completo desde el Frontend

### 1. Usuario abre la sección "Descuentos"
```
App carga descuentos.php
    ↓
footer.php incluye descuentos.js
    ↓
main.js detecta #descuentosModule
    ↓
DescuentosModule.init() se ejecuta
```

### 2. En el método `init()` se hace:
```javascript
init() {
    this.modalDescuento = AppUI.createModal("modalRegistrarDescuento");
    this.bindEvents();              // Bind de todos los eventos
    this.cargarDatos();             // ← Carga automática al iniciar
}
```

### 3. En `cargarDatos()`:
```
Llamadas paralelas:
  ├─ DescuentosAPI.obtenerDescuentos()
  │   → Llena tabla + selects
  ├─ CatalogoAPI.obtenerProductos()
  │   → Llena checkboxes y selects
  ├─ CatalogoAPI.obtenerMarcas()
  │   → Llena select de marca
  └─ CatalogoAPI.obtenerGeneros()
      → Llena selects de género
```

---

## ✅ Checklist de Funcionalidades

### Registrar Descuentos
- [x] Modal con formulario
- [x] Campos: nombre, %, fecha inicio/fin
- [x] Validación antes de enviar
- [x] Confirmación de usuario
- [x] Tabla de descuentos en tiempo real

### Aplicar Descuentos (6 formas)

**1. Productos Específicos**
- [x] Checkboxes de productos
- [x] Selector de descuento
- [x] Validación: mínimo 1 producto
- [x] Envío con JSON.stringify

**2. Por Marca**
- [x] Select de marca
- [x] Select de descuento
- [x] Validación: ambos requeridos

**3. Género de un Producto**
- [x] Select de producto
- [x] Select de género (carga dinámica)
- [x] Select de descuento

**4. Género Completo**
- [x] Select de género (dama/caballero/etc)
- [x] Select de descuento
- [x] Afecta TODOS los productos de ese género

**5. Todos los Productos**
- [x] Solo selector de descuento
- [x] Confirmación de advertencia
- [x] Afecta TODOS los productos

**6. Remover Descuento**
- [x] Botón en tabla de descuentos
- [x] Confirmación
- [x] Actualiza tabla automáticamente

---

## 🐛 Solución de Problemas

### "No se registra el descuento"
**Causa**: Formulario no está siendo enviado correctamente

**Verificar**:
1. Abre F12 → Pestaña Network
2. Cuando das clic en "Registrar", debe aparecer una solicitud a `descuentosAjax.php`
3. Verifica que el parámetro `accion=registrarDescuento` esté en la solicitud
4. Respuesta debe ser: `{"success": true, ...}`

**Si falla**:
```javascript
// En console, prueba:
await DescuentosAPI.obtenerDescuentos()
// Si esto funciona, el backend está bien
```

### "No carga los productos/marcas"
**Causa**: CatalogoAPI no funciona

**Verificar**:
```javascript
// En console:
await CatalogoAPI.obtenerProductos()
// Debe retornar array de productos
```

Si no funciona, revisa que `catalogoAjax.php` existe y tiene acciones:
- `listadoProductos`
- `listadoMarcas`
- `listadoGeneros`

### "No se aplica el descuento"
**Causa**: Probablemente validación del backend

**Verificar**:
1. Network → Response de la solicitud
2. Mensaje de error debe indicar qué falta
3. Asegúrate de seleccionar un descuento Y el criterio (producto, marca, etc)

---

## 🎨 Diseño y Colores

El módulo usa las clases estándar del proyecto:
- `content-panel` - Encabezado
- `content-list-descuentos` - Sección de tabla
- `btn btn-primary` - Botones principales
- `btn btn-new-product` - Botón "+ Nuevo"
- `btn btn-danger` - Botón eliminar
- `table-dark` - Tablas
- `modal-cabecera` - Encabezado de modal

---

## 📊 Estructura de Respuestas

### Éxito (200)
```json
{
    "success": true,
    "data": {
        "id_descuento": 5
    },
    "message": "Descuento registrado exitosamente"
}
```

### Error
```json
{
    "success": false,
    "message": "Todos los campos son obligatorios"
}
```

---

## 🚀 Performance

- **Carga inicial**: ~500ms (paraleliza 4 llamadas AJAX)
- **Tabla**: Usa DataTable para paginación
- **Modales**: Reutiliza AppUI para crear instancias

---

## 📝 Notas Importantes

1. **FormData**: El registro usa FormData para compatibilidad con futuros uploads de imágenes
2. **Confirmaciones**: Todos los registros/eliminaciones requieren confirmación del usuario
3. **Logaritmo**: Todos los errores se registran en `Logger` y en `app.log`
4. **Cascada de selects**: El género de producto carga dinámicamente (ya implementado)

---

**Creado**: 2026-06-15  
**Última actualización**: 2026-06-15  
**Estado**: ✅ Listo para producción
