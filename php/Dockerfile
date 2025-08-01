# TODO/php/Dockerfile

# PHP-FPM (PHP FastCGI Process Manager) の8.2バージョンをベースにする
# Alpineは軽量なLinuxディストリビューション
FROM php:8.2-fpm-alpine

# PHPがデータベース(MySQL)に接続するために必要な拡張機能をインストール
# pdo_mysql はMySQL接続用
# opcache はPHPの実行速度を速めるためのもの
# rm -rf /var/cache/apk/* はインストール後の不要なキャッシュを削除
RUN apk add --no-cache \
    mysql-client \
    && docker-php-ext-install pdo_mysql opcache \
    && rm -rf /var/cache/apk/*

# Composerを使う場合は、この行のコメントアウトを外す（今は使わないのでこのままでOK）
# COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# コンテナが起動したときに、このディレクトリを「作業場所」として設定する
WORKDIR /var/www/html/app
