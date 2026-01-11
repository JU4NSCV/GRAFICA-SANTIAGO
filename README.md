<!--
README — Proyecto GRAFICA SANTIAGO
Autor: Juan Cueva - Alex Serrano - Luis Aguilar
Repo: [(link del repo)](https://github.com/JU4NSCV/GRAFICA-SANTIAGO.git)
-->

<div align="center">

# Gráfica Santiago — WordPress + WooCommerce (Astra Child)

Sitio e-commerce para **Gráfica Santiago** (Loja, Ecuador) construido sobre **WordPress + WooCommerce**, usando **Astra Child Theme** y componentes UI modernos (Tailwind-like utilities / clases utilitarias) con plantillas personalizadas para mejorar **experiencia de compra**, **velocidad** y **mantenibilidad**.

<!-- Badges (opcional) -->
![WordPress](https://img.shields.io/badge/WordPress-6%2B-blue)
![WooCommerce](https://img.shields.io/badge/WooCommerce-8%2B-purple)
![PHP](https://img.shields.io/badge/PHP-8%2B-777bb4)
![License](https://img.shields.io/badge/License-MIT-green)

</div>

---

## 📌 Tabla de contenido

- [Descripción](#-descripción)
- [Objetivos del proyecto](#-objetivos-del-proyecto)
- [Funcionalidades implementadas](#-funcionalidades-implementadas)
- [Stack tecnológico](#-stack-tecnológico)
- [Estructura del repositorio](#-estructura-del-repositorio)
- [Instalación local](#-instalación-local)
- [Configuración](#-configuración)
- [Plantillas personalizadas](#-plantillas-personalizadas)
- [WooCommerce Overrides](#-woocommerce-overrides)
- [Sincronización de productos (API externa)](#-sincronización-de-productos-api-externa)
- [Arquitectura tipo MVC (adaptada a WP)](#-arquitectura-tipo-mvc-adaptada-a-wp)
- [Pruebas (Caja Blanca)](#-pruebas-caja-blanca)
- [Buenas prácticas](#-buenas-prácticas)
- [Roadmap](#-roadmap)
- [Contribución](#-contribución)
- [Licencia](#-licencia)

---

## 🧾 Descripción

Este repositorio contiene el desarrollo del sitio web de **Gráfica Santiago**, enfocado en:

- Personalización avanzada del **Astra Child Theme**
- Rediseño de páginas clave (Home, Blog, Mi Cuenta, Carrito, etc.)
- Mejoras UI/UX con estilos utilitarios (Tailwind-like)
- Overrides de WooCommerce para una experiencia más limpia y moderna
- Base para **sincronización de productos** desde una **API externa** (ej. DobraNet / servicio propio)

---

## 🎯 Objetivos del proyecto

- Mejorar la conversión del e-commerce con una interfaz moderna, clara y rápida.
- Mantener el desarrollo escalable usando separación lógica (tipo MVC dentro de WordPress).
- Facilitar mantenimientos: plantillas claras, hooks ordenados, prefijos y módulos.
- Preparar la tienda para automatizar catálogo (actualización por lotes vía API).

---

## ✅ Funcionalidades implementadas

### UI/UX
- Cabecera personalizada (header) con identidad visual de marca.
- Secciones Home con componentes reutilizables (promos, banners, cards).
- Plantilla de post del blog rediseñada para lectura clara.
- Plantillas adaptadas a paleta corporativa y estilos consistentes.

### WooCommerce
- Rediseño / override de pantallas clave:
  - **Mi Cuenta** (navegación y panel)
  - **Carrito vacío** con call-to-action
  - Ajustes de layout para páginas de WooCommerce
- Preparación para roles/perfiles (ej. mayorista) según endpoints.

### Mantenibilidad
- Prefijos `gs_` para funciones y módulos.
- Lógica separada por archivos (inc/ o modules/).
- Pruebas unitarias para lógica crítica (ej. home/promos) usando PHPUnit + Brain Monkey (según el entorno del repo).

---

## 🧰 Stack tecnológico

- **WordPress** (tema hijo de Astra)
- **WooCommerce**
- **PHP 8+**
- **HTML/CSS** (clases utilitarias tipo Tailwind / estilos propios)
- **JavaScript** (interacciones UI: sliders, toggles, etc. según necesidad)
- **MySQL/MariaDB** (local con XAMPP o similar)
- **PHPUnit + Brain Monkey** (pruebas caja blanca / funciones WP)

---

## 🗂️ Estructura del repositorio

> Ejemplo sugerido (ajusta a tu estructura real):

```txt
astra-child/
├─ style.css
├─ functions.php
├─ header.php
├─ footer.php
├─ assets/
│  ├─ img/
│  ├─ css/
│  └─ js/
├─ inc/
│  ├─ setup/
│  ├─ woocommerce/
│  ├─ home/
│  └─ utils/
├─ templates/
│  ├─ page-account.php
│  ├─ page-instituciones.php
│  └─ ...
└─ woocommerce/
   ├─ myaccount/
   │  ├─ my-account.php
   │  ├─ dashboard.php
   │  └─ navigation.php
   └─ cart/
      └─ cart-empty.php
````

---

## 🚀 Instalación local

### Requisitos

* PHP 8+
* MySQL/MariaDB
* WordPress 6+
* WooCommerce 8+
* Servidor local (recomendado: XAMPP, Laragon o LocalWP)

### Pasos (XAMPP recomendado)

1. Clona el repositorio dentro de tu carpeta de temas:

   * `wp-content/themes/astra-child/`

2. Activa el tema hijo:

   * WordPress → Apariencia → Temas → **Astra Child**

3. Instala plugins necesarios:

   * WooCommerce
   * (Opcional) Classic Editor / Elementor / seguridad / caché (según tu stack)

4. Importa base de datos (si aplica) o configura una nueva instalación.

---

## ⚙️ Configuración

### Identidad visual (colores / estilos)

* Mantén variables CSS o clases utilitarias centralizadas:

  * `assets/css/` o `style.css`
* Recomendación: definir tokens de color (paleta corporativa) y reutilizarlos.

Ejemplo (opcional) en `style.css`:

```css
:root{
  --gs-primary: #0B1F4B;   /* azul marino */
  --gs-accent:  #1E88E5;   /* azul */
  --gs-light:   #FFFFFF;   /* blanco */
}
```

---

## 🧩 Plantillas personalizadas

El proyecto utiliza plantillas WP con `Template Name:` para páginas específicas.
Ejemplos implementados o usados durante el desarrollo:

* `page-account.php` → plantilla personalizada para **Mi Cuenta**
* `page-instituciones.php` → vista de instituciones / cliente (banner full width, CTA, etc.)
* Plantillas personalizadas del blog (single post y/o archive)

> Para asignar una plantilla:
> WordPress → Páginas → Editar página → **Atributos → Plantilla**

---

## 🛒 WooCommerce Overrides

Dentro de `astra-child/woocommerce/` se sobreescriben plantillas de WooCommerce para personalizar UI sin tocar el core.

### Ejemplo: Mi Cuenta

Ruta típica:

```txt
astra-child/woocommerce/myaccount/
```

Archivos comunes:

* `my-account.php`
* `navigation.php`
* `dashboard.php`

> Nota: si usas una página con plantilla propia (ej. `page-account.php`), define claramente qué controla la vista (tu plantilla) vs qué controla WooCommerce (templates override). Evita duplicar responsabilidades.

### Ejemplo: Carrito vacío

Ruta típica:

```txt
astra-child/woocommerce/cart/cart-empty.php
```

Se puede personalizar:

* Mensaje
* Botón “Ir a comprar”
* Sección de recomendaciones
* Diseño con gradientes / cards

---

## 🔄 Sincronización de productos (API externa)

El proyecto contempla (o ya integra) una sincronización por lotes para mantener el catálogo de WooCommerce actualizado desde una **API externa** (ejemplo: DobraNet o API propia).

### Qué hace

* Consulta un endpoint tipo:

  * `GET /api/products?updated_after=...&limit=...&offset=...&include_images=...`
* Recorre productos por lotes y actualiza/crea en WooCommerce.
* Permite activar/desactivar imágenes (optimización de tiempo).

### Parámetros comunes

* `api_base` → URL base del servicio
* `api_token` → Token Bearer
* `updated_after` → fecha/hora para sincronizar cambios
* `limit` / `offset` → paginación
* `include_images` → 0/1

### Recomendación de seguridad

* **Nunca** hardcodear tokens en el repositorio.
* Usar `wp-config.php` o variables de entorno (según hosting).

Ejemplo en `wp-config.php` (opcional):

```php
define('GS_API_BASE', 'https://tu-dominio.com');
define('GS_API_TOKEN', 'REEMPLAZA_ESTE_TOKEN');
```

### Admin (panel de control)

Idealmente, el proyecto incluye/ incluirá:

* Página dentro del dashboard (WP Admin) con:

  * Botón “Sincronizar ahora”
  * Logs de ejecución
  * Estado: en progreso / terminado
  * Config de tamaño de batch

### Cron / ejecución automática

Se recomienda WP-Cron o Cron real del servidor:

* Cada X horas: sincronizar cambios desde `updated_after`
* Manejo de reintentos y timeouts

---

## 🧱 Arquitectura tipo MVC (adaptada a WP)

WordPress no es MVC puro, pero se puede organizar de forma clara:

### Model (Datos)

* WooCommerce (productos, pedidos, usuarios)
* Consumo de API externa (catálogo remoto)
* Helpers para mapear datos: SKU, categorías, imágenes, stock, precios

📁 Ejemplo:

```txt
inc/models/
inc/integrations/
```

### Controller (Lógica)

* Hooks/filters en `functions.php`
* Funciones que construyen contexto para vistas
* Endpoints, shortcodes, handlers para admin

📁 Ejemplo:

```txt
inc/controllers/
inc/home/
inc/woocommerce/
```

### View (Vista)

* Plantillas WP (`page-*.php`, `single.php`, etc.)
* Overrides WooCommerce (`woocommerce/`)
* Componentes UI (partials)

📁 Ejemplo:

```txt
templates/
woocommerce/
partials/
```

#### Convención recomendada

* Funciones con prefijo: `gs_`
* Archivos por módulo: `home`, `account`, `cart`, `sync`, etc.
* Contextos:

  * `gs_home_context()`
  * `gs_account_context()`
  * `gs_cart_context()`

---

## 🧪 Pruebas (Caja Blanca)

Se implementan pruebas unitarias para lógica PHP aislable de WP usando:

* **PHPUnit**
* **Brain Monkey** (mock de funciones de WordPress)

Ejemplo de lo que se prueba:

* Promociones en Home: habilitado/deshabilitado
* Construcción de items
* Validación de índices (evitar out-of-range)

📁 Estructura típica:

```txt
tests/
└─ HomeLogicTest.php
```

### Ejecutar pruebas (ejemplo)

> Ajusta comandos a tu `composer.json` si lo tienes.

```bash
composer install
vendor/bin/phpunit
```

---

## 🧼 Buenas prácticas

* ✅ Prefijar funciones y opciones: `gs_`
* ✅ Sanitizar y escapar:

  * `esc_html()`, `esc_attr()`, `wp_kses_post()`
* ✅ No tocar core de WooCommerce/WordPress
* ✅ Mantener overrides mínimos y bien documentados
* ✅ Cuidar rendimiento:

  * lazy load imágenes
  * tamaños correctos
  * evitar queries repetidas
* ✅ Logs controlados (sin exponer tokens ni data sensible)

---

## 🧭 Roadmap

* [ ] Finalizar normalización de estilos (tokens, clases utilitarias)
* [ ] Consolidar plantillas: Blog (single/archive) + Home
* [ ] Panel admin completo para sincronización (logs + botón + cron)
* [ ] Recomendaciones inteligentes en carrito vacío
* [ ] Optimización de performance (caché, minificación, imágenes)
* [ ] Documentar endpoints/contratos de API (Swagger / Postman)

---

## 🤝 Contribución

1. Crea un branch:

   * `feature/nueva-funcionalidad`
2. Commits claros:

   * `feat: ...` `fix: ...` `refactor: ...`
3. Pull Request con:

   * Qué cambia
   * Evidencia (capturas)
   * Pasos para probar

---

## 🪪 Licencia

Este proyecto puede licenciarse como **MIT** (o la que definas).
Si no aplica, reemplaza esta sección por “Propietario / Uso interno”.

---

## 📎 Notas finales

* Este README está pensado para ser **profesional y entendible**, pero es normal que tu estructura real tenga variaciones.
* Si quieres, puedes añadir:

  * Capturas de pantalla en `docs/` y enlazarlas aquí
  * Un `CHANGELOG.md`
  * Un `CONTRIBUTING.md`

---

<div align="center">
Hecho con ❤️ para Gráfica Santiago — Loja, Ecuador
</div>
```
