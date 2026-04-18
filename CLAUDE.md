# JM Distribuciones - Documentación de Arquitectura

## 📋 Descripción del Proyecto

Sistema web de gestión de inventario y distribución de productos. Permite registrar productos de múltiples categorías (Relojes, Lociones, Insumos, etc.) con sus presentaciones, gestionar proveedores, y controlar compras.

---

## 🏗️ Estructura del Proyecto

```
jm_distribuciones_project/
├── ajax/                          # Endpoints HTTP (comunicación con frontend)
│   ├── productosAjax.php         # Gestión genérica de productos
│   ├── catalogoAjax.php          # Catálogos (marcas, géneros, categorías)
│   └── proveedorAjax.php         # Gestión de proveedores
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── main.js               # Inicialización global
│   │   └── modules/
│   │       ├── inventarioRelojes.js    # Módulo de relojes (capas API/View/Module)
│   │       ├── compras.js              # Módulo de compras
│   │       └── proveedores.js          # Módulo de proveedores
│   └── img/
├── config/
│   ├── config.php
│   └── configDB.php
├── controller/
│   ├── ProductoController.php    # Controlador genérico de productos
│   └── CatalogoController.php    # Controlador genérico de catálogos
├── core/
│   ├── conexion.php              # Base PDO
│   ├── Logger.php                # Sistema de logging
│   └── response.php              # Respuestas JSON estandarizadas
├── contracts/                     # Interfaces PHP (SOLID - Dependency Inversion)
│   ├── ICatalogModel.php         # Interfaz para modelos de catálogo
│   ├── IProductoRepositorio.php  # Interfaz para repositorio de productos
│   └── IStorageService.php       # Interfaz para servicios de almacenamiento
├── helper/
│   └── utils.php                 # Utilidades (validación, sanitización)
├── Infrastructure/
│   └── FileStorageService.php    # Servicio de almacenamiento de archivos
├── model/
│   ├── ProductosModel.php        # Modelo genérico de productos
│   ├── MarcaModel.php            # Modelo de marcas (catálogo)
│   ├── GeneroModel.php           # Modelo de géneros (catálogo)
│   ├── CategoriaModel.php        # Modelo de categorías (catálogo)
│   ├── PresentacionProductoModel.php  # Modelo de presentaciones
│   └── ProveedorModel.php        # Modelo de proveedores
├── services/
│   ├── ProductoService.php       # Lógica compleja: registrar, editar productos
│   ├── CatalogoService.php       # Lógica de datos de referencia
│   └── ProveedorService.php      # Lógica de negocio de proveedores
├── views/
│   ├── inventory/
│   │   └── relojes.php           # Vista de inventario de relojes
│   └── shopping/
├── logs/
│   └── app.log                   # Log de eventos del sistema
├── public/
│   ├── header.php
│   ├── footer.php
│   └── menu.php
├── CLAUDE.md                      # Este archivo
└── index.php                      # Punto de entrada
```

---

## 🔄 Flujo de Datos

```
Frontend (HTML)
    ↓
JavaScript Module (RelojesModule)
    ↓
AJAX Request (productosAjax.php)
    ↓
Controller (ProductoController)
    ↓
Service (ProductoService) — Lógica de negocio
    ↓
Model (ProductosModel) — Acceso a datos
    ↓
Database
```

---

## 🎯 Patrones Arquitectónicos

### 1. **Modelo MVC + Service Layer**

- **Model**: Acceso a datos (heredan de `conexion`)
- **View**: HTML/Templates PHP
- **Controller**: Lógica HTTP (validación de requests)
- **Service**: Lógica de negocio (entre controller y modelo)

### 2. **Inyección de Dependencias**

Los servicios reciben sus dependencias por constructor, usando **interfaces** en lugar de clases concretas:

```php
// ✅ BIEN - Usando interfaces
class ProductoService {
    public function __construct(
        IStorageService $storage,       // Interfaz
        IProductoRepositorio $modelo    // Interfaz
    ) { }
}

// ❌ MAL - Acoplado a implementación específica
class ProductoService {
    public function __construct(
        LocalFileStorage $storage,      // Clase concreta
        ProductosModel $modelo          // Clase concreta
    ) { }
}
```

### 3. **JavaScript en Capas** (sin framework)

Dentro de cada módulo hay tres capas:

```javascript
// RelojesAPI — Capa de red (solo AJAX/fetch)
const RelojesAPI = {
    listarProductos() { return $.ajax(...) }
};

// RelojesView — Capa de presentación (solo DOM)
const RelojesView = {
    mostrarInventario(productos) { /* renderizar HTML */ }
};

// RelojesModule — Capa de lógica (coordina API y View)
const RelojesModule = {
    async cargarProductos() {
        const productos = await RelojesAPI.listarProductos();
        RelojesView.mostrarInventario(productos);
    }
};
```

**Beneficio**: Cambiar jQuery por fetch/axios solo requiere actualizar RelojesAPI, no toca View ni lógica.

---

## 📐 Principios SOLID Aplicados

| Principio | Implementación |
|-----------|----------------|
| **S** - Single Responsibility | Cada clase/módulo hace una cosa: CatalogoService solo gestiona datos de referencia |
| **O** - Open/Closed | Nuevas categorías de productos se agregan sin modificar código existente |
| **L** - Liskov Substitution | Todos los modelos catálogo implementan `ICatalogModel` |
| **I** - Interface Segregation | Interfaces específicas: `IProductoRepositorio`, `IStorageService`, `ICatalogModel` |
| **D** - Dependency Inversion | `ProductoService` depende de `IStorageService`, no de `LocalFileStorage` |

---

## 🚀 Cómo Agregar una Nueva Categoría de Producto

### Escenario: Agregar categoría "Perfumes"

**1. Base de datos**: Asegurate de que "Perfumes" exista en `categoria_producto`

**2. Frontend - Crear módulo JS** (`assets/js/modules/inventarioPerfumes.js`):
```javascript
const PerfumesAPI = {
    listarProductos() {
        return RelojesAPI.listarProductos("Perfumes");
    }
    // reutiliza los mismos métodos
};

const PerfumesView = {
    // copia los métodos de RelojesView
};

const PerfumesModule = {
    // Similar a RelojesModule pero para Perfumes
    async cargarDatos() {
        const productos = await PerfumesAPI.listarProductos();
        PerfumesView.mostrarInventario(productos);
    }
};
```

**3. Backend**: ✅ **¡Ya está hecho!**
- `productosAjax.php` ya es genérico
- `ProductoController` ya maneja cualquier categoría
- `ProductoService` ya funciona con cualquier categoría
- `ProductosModel` ya filtra por `categoria`

**4. Vista**: Crear `views/inventory/perfumes.php` igual a `relojes.php` pero referenciando `PerfumesModule`

**5. Menú**: Agregar enlace en `public/menu.php`

**¡Listo!** No necesitas crear nuevos controladores, servicios o modelos.

---

## 🔐 Convenciones de Nombres

### Métodos de Modelo
- **Lectura**: `obtenerTodos()`, `obtenerPorCategoria($cat)`
- **Escritura**: `registrar...($data)`, `actualizar...($id)`
- **Verificación**: `existe...($id)`

### Métodos de Servicio
- **Públicos**: `registrarProducto()`, `obtenerProductos()`
- Delegan al modelo y agregan lógica de negocio

### Métodos de Controlador
- **Públicos**: `registrar()`, `listar()`, `editar()`, `eliminar()`
- Siempre devuelven `response::success()` o `response::error()`

### JavaScript
- **API**: `RelojesAPI` - solo promesas con AJAX
- **View**: `RelojesView` - solo funciones que retornan HTML o manipulan DOM
- **Module**: `RelojesModule` - coordina API + View + eventos

---

## 📦 Dependencias Externas

- **PHP**: 7.4+
- **Framework**: Ninguno (vanilla PHP + jQuery + Bootstrap)
- **JS**: jQuery 3.x, Bootstrap 5.x, SweetAlert2, DataTables (para tablas)
- **DB**: MySQL 5.7+

---

## 🧪 Testing Manual

### Crear un nuevo producto
```bash
curl -X POST http://localhost/ajax/productosAjax.php \
  -F 'accion=registrarProducto' \
  -F 'nombreProducto=Reloj XYZ' \
  -F 'codigoProducto=001' \
  -F 'categoriaProducto=1' \
  -F 'marcaProducto=1' \
  -F 'generoProducto=1'
```

### Obtener productos de una categoría
```bash
curl "http://localhost/ajax/productosAjax.php?accion=listadoRelojes&categoria=Relojes"
```

### Obtener catálogos
```bash
curl "http://localhost/ajax/catalogoAjax.php?accion=listadoMarcas"
curl "http://localhost/ajax/catalogoAjax.php?accion=listadoGeneros"
curl "http://localhost/ajax/catalogoAjax.php?accion=listadoCategorias"
```

---

## 🐛 Debugging

### Logs
Los errores se registran en `logs/app.log`:
```php
Logger::error("Mensaje", $exception, $contexto);
```

Formato:
```
[2026-04-18 15:30:45] ERROR: Mensaje del error
Mensaje: Exception message
File: /path/to/file.php
Line: 42
Stack trace:
...
```

### Console Errors
Abre F12 en el navegador. Los módulos JS logean errores en consola:
```javascript
console.error("Error al cargar productos:", error);
```

---

## 📝 Notas para Desarrollo

1. **Agregar un nuevo campo**: Modificar migration, luego modelo y vista
2. **Cambiar storage**: Implementar nueva clase que extienda `IStorageService`
3. **Agregar validación**: Va en `Service` (no en Controller ni JS)
4. **Agregar cálculos**: Va en `Service` (descuentos, impuestos, etc.)

---

## 👥 Convenciones del Equipo

- Commits: Usar prefijos `Fix:`, `Feature:`, `Refactor:`, `Docs:`
- Ramas: `feature/nombre`, `fix/nombre`, `refactor/nombre`
- PR: Describir el "por qué", no el "qué"
- Indentación: 4 espacios (PHP), 2 espacios (JS)

---

**Última actualización**: 2026-04-18
**Mantenedor**: Johan Montero
