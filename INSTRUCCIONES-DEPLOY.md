# Kalli — instrucciones de actualización

Este paquete contiene el rediseño de la página principal, el logo oficial y sus favicons.

## Antes de publicar

1. Revisa `includes/contact-config.php`.
2. Sustituye `REEMPLAZA_CON_TU_CORREO@DOMINIO.COM` por el correo real que recibirá las solicitudes.
3. Sustituye `KALLI-CAMBIA-ESTE-TOKEN-POR-UNO-LARGO-Y-UNICO-2026` por una frase larga y única.

## Publicar con GitHub

Desde la carpeta local del proyecto ejecuta:

```bash
git add .
git commit -m "Rediseña inicio y actualiza identidad Kalli"
git push origin main
```

Después revisa GitHub > Actions. Al finalizar en verde, prueba:

- https://kalli.mayartestudio.com/
- https://kalli.mayartestudio.com/contact.php
