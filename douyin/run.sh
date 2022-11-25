#!/bin/sh -e

#默认容器IP地址
IP=${IP:-127.0.0.1}

#默认映射端口
PORT=${PORT:-80}

#创建主目录文件夹和caddy配置目录
mkdir -p /var/www/html 
mkdir -p /etc/caddy/

#创建Caddyfile
if [ ! -f /etc/caddy/Caddyfile ]; then
cat >  /etc/caddy/Caddyfile <<EOF
  :80 {
    root * /var/www/html         
    php_fastcgi localhost:9000 
    file_server 
   }
EOF
fi

#创建m3u格式的 源
if [ ! -f /var/www/html/douyin.m3u ]; then
cat >  /var/www/html/douyin.m3u <<EOF
#EXTM3U
#EXTINF:-1 tvg-id="1" tvg-name="抖音直播" tvg-logo="https://img.sj33.cn/uploads/202106/7-2106211G121315.jpg" group-title="直播", 抖音直播
http://$IP:$PORT/zb.php?id=douyin
EOF
fi

#启动php和caddy
php-fpm &

caddy run --config /etc/caddy/Caddyfile --adapter caddyfile 

wait -n

exit $?
