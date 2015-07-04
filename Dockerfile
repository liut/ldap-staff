FROM alpine:3.2
MAINTAINER Eagle Liut <eagle@dantin.me>

RUN apk add --update bash nginx php-fpm php-ldap php-mcrypt php-json php-ctype php-pdo_sqlite rsync && rm -rf /var/cache/apk/*

RUN sed -i "s|listen = .*|listen = /var/run/php5-fpm.sock|g" /etc/php/php-fpm.conf \
 && sed -i "s|;listen.owner = .*|listen.owner = nobody|g" /etc/php/php-fpm.conf \
 && sed -i "s|;listen.group = .*|listen.group = nobody|g" /etc/php/php-fpm.conf

ADD etc/nginx/nginx.conf /etc/nginx/nginx.conf
ADD etc/nginx/host.default /etc/nginx/sites-available/default

RUN mkdir -p /etc/nginx/sites-enabled \
 && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

ADD . /tmp/dist/

RUN rsync -rlptC \
    --exclude=".*" \
    --exclude="composer.*" \
    --exclude="test*" \
    --exclude="doc" \
    --exclude="ext" \
    --exclude="vendor" \
    --exclude="htdocs" \
    --exclude="etc" \
    --exclude="data/" \
    --exclude="web/cache/" \
    --exclude="web/data/" \
    --exclude="/sass/" \
    /tmp/dist/class /tmp/dist/inc /tmp/dist/library /tmp/dist/skins /tmp/dist/third /tmp/dist/web \
    /var/www/ \
  && rm -rf /tmp/dist

ADD entrypoint.sh /entrypoint.sh
# RUN chmod a+x /entrypoint.sh

# ADD class inc library skins third web /var/www/

RUN mkdir -p /var/log/app /var/tmp/app \
    && chmod a+w /var/log/app /var/tmp/app

RUN mkdir -p /var/www/data \
    && chown nobody:nobody /var/www/data

VOLUME ["/var/www/data"]

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]

CMD ["start"]
