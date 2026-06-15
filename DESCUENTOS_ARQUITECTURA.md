# 🏗️ Arquitectura de Descuentos

## Resumen

He implementado un sistema completo de descuentos siguiendo la arquitectura MVC + Service Layer del proyecto. Los descuentos se pueden aplicar de **6 maneras diferentes** a las presentaciones de productos.

---

## 📍 Dónde Va Cada Cosa

### **1. Base de Datos (Lógica de Persistencia)**

**Archivo**: `DescuentosModel.php` (ya existía)

**Métodos agregados**:
- `asignarDescuentoAPresentaciones($idDescuento, $idsProductos)` → UPDATE masivo
- `removerDescuentoDePresentaciones($idsProductos)` → Remover descuento de presentaciones

**Responsabilidad**: Solo ejecutar SQL. No contiene lógica de negocio.

---

### **2. Lógica de Negocio (6 Formas de Aplicar Descuentos)**

**Archivo**: `services/DescuentosService.php` ⭐ **NUEVO**

Este servicio contiene toda la inteligencia. Cada método sabe:
1. **Qué presentaciones traer** basado en el criterio
2. **Validar que existan** presentaciones aplicables
3. **Delegar la actualización** al modelo

**Métodos públicos**:

```php
// 1️⃣ Registrar un descuento (base de datos)
registrarDescuento($data): mixed

// 2️⃣ Obtener todos los descuentos
obtenerDescuentos(): array

// 3️⃣ Obtener descuento por ID
obtenerDescuentoPorID($id): ?array

// 4️⃣ Aplicar a PRODUCTOS ESPECÍFICOS
aplicarDescuentoAProductosEspecificos($idDescuento, $idsProductos): bool
  └─ Ejemplo: "Descuento solo en Reloj X, Reloj Y, Loción Z"

// 5️⃣ Aplicar a MARCA COMPLETA
aplicarDescuentoAMarca($idDescuento, $idMarca): bool
  └─ Ejemplo: "Descuento en todos los productos de la marca Timex"

// 6️⃣ Aplicar a GÉNERO de un PRODUCTO
aplicarDescuentoAProductoPorGenero($idDescuento, $idProducto, $idGenero): bool
  └─ Ejemplo: "Descuento en Relojes para Dama solamente"

// 7️⃣ Aplicar a GÉNERO COMPLETO
aplicarDescuentoAGenero($idDescuento, $idGenero): bool
  └─ Ejemplo: "Descuento en TODOS los productos para Dama"

// 8️⃣ Aplicar a TODOS LOS PRODUCTOS
aplicarDescuentoATodos($idDescuento): bool
  └─ Ejemplo: "Black Friday - Descuento en todo"

// 9️⃣ Remover un descuento
removerDescuento($idDescuento): bool
```

---

### **3. Validación y Respuestas HTTP**

**Archivo**: `controller/DescuentosController.php` ⭐ **NUEVO**

**Responsabilidad**:
- Validar que los parámetros POST/GET sean correctos
- Capturar excepciones del servicio
- Retornar respuestas JSON estandarizadas

**Métodos** (uno para cada acción):
- `registrarDescuento($data)`
- `obtenerDescuentos()`
- `obtenerDescuentoPorID($request)`
- `aplicarDescuentoAProductosEspecificos($data)`
- `aplicarDescuentoAMarca($data)`
- `aplicarDescuentoAProductoPorGenero($data)`
- `aplicarDescuentoAGenero($data)`
- `aplicarDescuentoATodos($data)`
- `removerDescuento($data)`

---

### **4. Punto de Entrada (AJAX)**

**Archivo**: `ajax/descuentosAjax.php` ⭐ **NUEVO**

Router que mapea acciones (`$_POST['accion']`) a métodos del controlador.

---

### **5. Inyección de Dependencias**

**Archivo actualizado**: `core/ServiceContainer.php`

Agregué método:
```php
public static function getDescuentosService(): DescuentosService
```

Instancia automáticamente `DescuentosModel` + `ProductosModel` + crea el servicio.

---

### **6. Métodos Auxiliares en ProductosModel**

**Archivo actualizado**: `model/ProductosModel.php`

Agregué métodos públicos para obtener IDs de presentaciones según criterios:

```php
obtenerIdsPresentacionesPorMarca($idMarca): array
obtenerIdsPresentacionesPorProductoYGenero($idProducto, $idGenero): array
obtenerIdsPresentacionesPorGenero($idGenero): array
obtenerIdsTodosPresentaciones(): array
obtenerIdsPresentacionesPorDescuento($idDescuento): array
```

Estos métodos son llamados por `DescuentosService` para saber qué actualizar.

---

## 🔄 Flujo de Datos

```
Frontend (AJAX Request)
    ↓
descuentosAjax.php (routing por acción)
    ↓
DescuentosController (validación + manejo de errores)
    ↓
DescuentosService (lógica: qué presentaciones actualizar)
    ├─ Consulta ProductosModel (obtener IDs de presentaciones)
    └─ Llama DescuentosModel (ejecutar UPDATE)
    ↓
DescuentosModel (ejecuta SQL)
    ↓
Base de datos
```

---

## 📝 Ejemplos de Uso desde Frontend

### 1️⃣ Registrar un descuento

```javascript
const data = {
    accion: 'registrarDescuento',
    nombreDescuento: 'Black Friday 2026',
    porcentajeDescuento: 20,
    fechaInicio: '2026-11-28',
    fechaFin: '2026-12-01'
};

$.post('ajax/descuentosAjax.php', data, (response) => {
    const idDescuento = response.data.id_descuento;
    console.log('Descuento registrado:', idDescuento);
});
```

### 2️⃣ Aplicar a productos específicos

```javascript
const data = {
    accion: 'aplicarAProductosEspecificos',
    idDescuento: 5,
    idsProductos: JSON.stringify([1, 2, 3])  // IDs de productos
};

$.post('ajax/descuentosAjax.php', data, (response) => {
    console.log('Descuento aplicado a productos:', response);
});
```

### 3️⃣ Aplicar a una marca

```javascript
const data = {
    accion: 'aplicarAMarca',
    idDescuento: 5,
    idMarca: 2  // Marca Timex (ejemplo)
};

$.post('ajax/descuentosAjax.php', data, (response) => {
    console.log('Descuento aplicado a marca:', response);
});
```

### 4️⃣ Aplicar a género de un producto

```javascript
const data = {
    accion: 'aplicarAProductoPorGenero',
    idDescuento: 5,
    idProducto: 1,    // Reloj
    idGenero: 1       // Dama
};

$.post('ajax/descuentosAjax.php', data, (response) => {
    console.log('Descuento aplicado a Relojes para Dama:', response);
});
```

### 5️⃣ Aplicar a género completo

```javascript
const data = {
    accion: 'aplicarAGenero',
    idDescuento: 5,
    idGenero: 1  // Todas las presentaciones para Dama
};

$.post('ajax/descuentosAjax.php', data, (response) => {
    console.log('Descuento aplicado a género:', response);
});
```

### 6️⃣ Aplicar a TODO

```javascript
const data = {
    accion: 'aplicarATodos',
    idDescuento: 5
};

$.post('ajax/descuentosAjax.php', data, (response) => {
    console.log('Descuento aplicado a todos:', response);
});
```

### 7️⃣ Remover descuento

```javascript
const data = {
    accion: 'removerDescuento',
    idDescuento: 5
};

$.post('ajax/descuentosAjax.php', data, (response) => {
    console.log('Descuento removido:', response);
});
```

---

## ✅ Resumen de Decisiones Arquitectónicas

| Decisión | Razón |
|----------|-------|
| **Lógica en Service, no en Model** | El modelo es solo acceso a datos. La lógica "qué actualizar según qué filtro" es negocio y va en el servicio. |
| **Métodos pequeños en Model** | `obtenerIdsPresentacionesPorMarca()`, etc. Cada uno hace UNA cosa. |
| **Validación en Controller** | Campos requeridos, conversión de tipos, solo en la entrada HTTP. |
| **No muchos UPDATEs específicos** | En lugar de `actualizarDescuentoMarcaA()`, `actualizarDescuentoGeneroA()`, etc., tengo **métodos genéricos** que reciben arrays de IDs. |
| **Parámetros nombrados en SQL** | Consistente con el proyecto. Seguro contra SQL injection. |

---

## 🚀 Próximos Pasos (Opcional)

Si necesitas agregar más funcionalidad:

1. **Descuentos por Categoría**: Agregar método en `ProductosModel`:
   ```php
   obtenerIdsPresentacionesPorCategoria($idCategoria): array
   ```
   Y en `DescuentosService`:
   ```php
   aplicarDescuentoACategoria($idDescuento, $idCategoria): bool
   ```

2. **Descuentos con Validación de Fechas**: En `DescuentosService`:
   ```php
   private function validarDescuentoActivo($idDescuento): void {
       $desc = $this->modeloDescuento->obtenerDescuentoPorID($idDescuento);
       if (strtotime($desc['fecha_fin']) < time()) {
           throw new DomainException('El descuento ha expirado');
       }
   }
   ```

3. **Vista CRUD**: Crear `views/descuentos.php` con tabla de descuentos y formulario.

---

**Creado**: 2026-06-15  
**Patrón**: MVC + Service Layer + SOLID  
**Principios Aplicados**: Single Responsibility, Dependency Inversion, Open/Closed
