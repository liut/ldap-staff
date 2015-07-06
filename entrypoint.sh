#!/bin/bash -e

function start() {

    FIRST_START_DONE="/etc/docker-first-start-done"
    # container first start
    if [ ! -e "$FIRST_START_DONE" ]; then

        mkdir -p /var/log/php
        chown nobody /var/log/php
        sed -i "s|;error_log = php_errors.log|error_log = /var/log/php/errors.log|g" /etc/php/php.ini

        CONFIG="/var/www/inc/config.php"

        if [ ! -z "$LDAP_HOST" ]; then
          sed -i "s/, 'slapd'/, '$LDAP_HOST'/g" $CONFIG
        fi

        if [ ! -z "$LDAP_PORT" ]; then
          sed -i "s/, 389/, $LDAP_PORT/g" $CONFIG
        fi

        if [ ! -z "$LDAP_BASE_DN" ]; then
          sed -i "s/, 'dc=example,dc=org'/, '$LDAP_BASE_DN'/g" $CONFIG
        fi

        if [ ! -z "$APP_DOMAIN" ]; then
          sed -i "s/, 'example\.*'/, '$APP_DOMAIN'/g" $CONFIG
        fi

        if [ ! -z "$RES_URL" ]; then
          sed -i "s/RES_URL', '.*'/RES_URL', '$RES_URL'/g" $CONFIG
        fi

        if [ ! -z "$APP_SESSION" ]; then
          sed -i "s/'APP_SESSION', '.*'/'APP_SESSION', '$APP_SESSION'/g" $CONFIG
        fi


        # Fix file permission
        find /var/www/ -type d -exec chmod 755 {} \;
        find /var/www/ -type f -exec chmod 644 {} \;
        mkdir -p /var/www/data
        chown nobody:nobody -R /var/www/data
        mkdir -p /var/www/web/cache/view/cache
        chown nobody:nobody -R /var/www/web/cache

        touch $FIRST_START_DONE
    fi

    php-fpm && nginx
}


if [[ "$1" = "start" ]]; then
    start
else
    exec "$@"
fi

