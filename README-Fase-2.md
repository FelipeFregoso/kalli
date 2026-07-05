# Kalli — Fase 2: Base visual y Hero

Esta entrega incluye la base funcional del sitio: navegación fija, menú móvil, hero responsive, animaciones iniciales con GSAP, página base de contacto y los archivos esenciales para SEO y hosting compartido.

## Archivos principales

- `index.html`: portada de Kalli con header y hero.
- `css/styles.css`: variables de diseño, navegación y responsividad.
- `js/main.js`: menú móvil, efecto del header, toast temporal y animaciones GSAP.
- `contact.php`: página base de contacto con validación PHP, honeypot y sanitización.
- `includes/contact-config.php`: configuración privada del correo que recibirá solicitudes.
- `img/hero/`: visuales temporales originales en SVG para no depender de bancos de imágenes.
- `.htaccess`: seguridad básica, HTTPS, caché y compresión.

## Pendiente para siguientes fases

- Concepto de Kalli.
- Beneficios, amenidades, master plan y galería.
- Ubicación y mapa Leaflet.
- Formulario integrado a la portada con envío asíncrono.
- Sustituir los visuales SVG temporales por renders/fotografías oficiales cuando estén disponibles.

## Configuración obligatoria del formulario

Antes de activar el formulario, abre `includes/contact-config.php` y cambia:

```php
define('KALLI_CONTACT_EMAIL', 'REEMPLAZA_CON_TU_CORREO@DOMINIO.COM');
define('KALLI_FROM_EMAIL', 'no-reply@kalli.mayartestudio.com');
define('KALLI_FORM_TOKEN', 'KALLI-CAMBIA-ESTE-TOKEN-POR-UNO-LARGO-Y-UNICO-2026');
```

Para el token usa una frase larga, única y difícil de adivinar. No subas este archivo a repositorios públicos.
