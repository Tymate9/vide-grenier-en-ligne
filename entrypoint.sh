#!/bin/bash
cat > /etc/msmtprc <<EOF
account default
host ${MAILTRAP_HOST}
port ${MAILTRAP_PORT}
auth on
user ${MAILTRAP_USERNAME}
password ${MAILTRAP_PASSWORD}
tls on
tls_starttls on
from noreply@vide-grenier.local
logfile /var/log/msmtp.log
EOF
chown www-data:www-data /etc/msmtprc
chmod 600 /etc/msmtprc
touch /var/log/msmtp.log
chown www-data:www-data /var/log/msmtp.log
exec docker-php-entrypoint "$@"
