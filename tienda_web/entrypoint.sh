#!/bin/sh
# Genera el .env real de CodeIgniter usando las variables de entorno
# que se configuran en el panel de Render (sin puntos, porque Render
# no permite puntos en el nombre de las variables).

cat > /var/www/html/.env <<EOF
CI_ENVIRONMENT = ${CI_ENVIRONMENT:-production}

app.baseURL = '${APP_BASE_URL}'

database.default.hostname = ${DB_HOST}
database.default.database = ${DB_NAME}
database.default.username = ${DB_USER}
database.default.password = ${DB_PASS}
database.default.DBDriver = MySQLi
database.default.port = ${DB_PORT:-3306}

JWT_SECRET="${JWT_SECRET}"

MP_ACCESS_TOKEN="${MP_ACCESS_TOKEN}"
MP_PUBLIC_KEY="${MP_PUBLIC_KEY}"

email.protocol = 'smtp'
email.SMTPHost = '${SMTP_HOST}'
email.SMTPUser = '${SMTP_USER}'
email.SMTPPass = '${SMTP_PASS}'
email.SMTPPort = ${SMTP_PORT:-465}
email.SMTPCrypto = '${SMTP_CRYPTO:-ssl}'
email.mailType = 'html'
email.charset  = 'utf-8'
EOF

exec "$@"
