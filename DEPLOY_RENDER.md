# Despliegue en Render

El proyecto incluye un `Dockerfile` y un `render.yaml` para desplegar Laravel como servicio web en Render con PostgreSQL.

## Opcion recomendada: Blueprint

1. Sube el repositorio a GitHub o GitLab.
2. En Render, selecciona **New > Blueprint**.
3. Conecta el repositorio y confirma el `render.yaml`.
4. Cuando Render solicite `APP_KEY`, genera una clave localmente con:

```bash
php artisan key:generate --show
```

5. Define `APP_URL` con la URL publica del servicio, por ejemplo `https://entallamiento.onrender.com`.
6. Espera el primer despliegue. El contenedor ejecuta migraciones antes de iniciar Apache.

## Variables importantes

- `APP_KEY`: clave base64 generada por Laravel. No la compartas ni la cambies despues de crear sesiones o tokens.
- `APP_URL`: URL publica completa del servicio.
- `DB_URL`: la crea Render desde la base de datos PostgreSQL del blueprint.
- `APP_DEBUG=false`: debe mantenerse desactivado en produccion.
- `SESSION_DRIVER=database` y `CACHE_STORE=database`: evitan depender del disco efimero del contenedor.

## Despliegue manual como Web Service

Si no usas Blueprint:

- Runtime: **Docker**
- Dockerfile Path: `./Dockerfile`
- Docker Context: `.`
- Health Check Path: `/up`
- Agrega una base de datos PostgreSQL y configura `DB_CONNECTION=pgsql` y `DB_URL` con su Internal Database URL.
- Configura tambien `APP_KEY`, `APP_URL`, `APP_ENV=production` y `APP_DEBUG=false`.

Las imagenes de `public/camisas` se empaquetan dentro de la imagen Docker. El startup script ejecuta `php artisan migrate --force`, cachea la configuracion, rutas y vistas, y luego inicia Apache en el puerto que Render expone internamente.
