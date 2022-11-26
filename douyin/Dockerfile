FROM caddy:2-alpine AS builder

FROM php:8.1-fpm-alpine

LABEL maintainer="cx@tinyserve.com"

COPY --from=builder /usr/bin/caddy /usr/bin/caddy
COPY  zb.php /var/www/html/
COPY  run.sh /
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/' /etc/apk/repositories
RUN chmod +x /run.sh && \
    apk --no-cache update && apk upgrade &&  \
    apk add --no-cache \
    curl \
    dumb-init

EXPOSE 80 9000

ENTRYPOINT ["dumb-init", "/run.sh"]
