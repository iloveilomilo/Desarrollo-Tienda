# Cómo publicar NewPhoneMX en internet (Render)

## 1. Base de datos (gratis en Clever Cloud)

1. Crea cuenta en https://www.clever-cloud.com
2. Crea una app nueva → Add-on → MySQL → plan **Dev (gratis)**
3. Copia estos datos, los vas a necesitar después:
   - Host
   - Puerto
   - Nombre de la base
   - Usuario
   - Contraseña
4. Importa tu base de datos actual (`newphonemx_db.sql`) con phpMyAdmin o el cliente MySQL que te dé Clever Cloud.

## 2. Crear el servicio en Render

1. Crea cuenta en https://render.com y conecta tu GitHub.
2. **New → Web Service** → elige el repo `Desarrollo-Tienda`.
3. Render va a detectar el `Dockerfile` solo. Plan: **Free**.
4. En "Root Directory" pon: `tienda_web` (si tu Dockerfile no está en la raíz del repo).

## 3. Configurar variables de entorno

En Render, ve a **Environment** y agrega estas (los valores reales están en tu `.env` local, cópialos de ahí):

| Variable | Qué es |
|---|---|
| `CI_ENVIRONMENT` | `production` |
| `APP_BASE_URL` | La URL que te da Render, ej: `https://newphonemx.onrender.com/` |
| `DB_HOST` | Host de Clever Cloud |
| `DB_NAME` | Nombre de la base |
| `DB_USER` | Usuario |
| `DB_PASS` | Contraseña |
| `DB_PORT` | Normalmente `3306` |
| `JWT_SECRET` | El mismo valor que tienes en tu `.env` |
| `MP_ACCESS_TOKEN` | Token de Mercado Pago |
| `MP_PUBLIC_KEY` | Public key de Mercado Pago |
| `SMTP_HOST` | `smtp.gmail.com` |
| `SMTP_USER` | Tu correo Gmail |
| `SMTP_PASS` | Tu contraseña de aplicación de Gmail |
| `SMTP_PORT` | `465` |
| `SMTP_CRYPTO` | `ssl` |

No subas tu `.env` real a GitHub. Estas variables se configuran solo en el panel de Render.

## 4. Deploy

Dale clic a **Deploy**. Render construye la imagen Docker y en unos minutos te da tu URL pública.

## 5. Probar los 3 roles

Con la URL que te dio Render, entra a `/login` y prueba con un usuario de cada rol:
- Cliente
- Administrador
- Atención al cliente

## Cosas a tener en cuenta

- El plan gratis de Render "duerme" el sitio si nadie lo visita en un rato. La primera visita después de eso tarda unos 30-60 segundos en cargar — es normal, no es un error.
- Las fotos que ya existen en el proyecto (productos, etc.) sí se suben con el sitio. Pero si alguien sube una foto nueva (ej. cambia su foto de perfil) después de publicado, esa foto se puede perder si Render reinicia el servicio, porque el plan gratis no guarda archivos de forma permanente. Para un proyecto escolar esto normalmente no es problema.
