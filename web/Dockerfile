# TODO/web/Dockerfile

# Nginxの最新安定版をベースにする
FROM nginx:stable-alpine

# コンテナが起動したときに、このディレクトリを「作業場所」として設定する
WORKDIR /var/www/html/app

# コンテナが起動したときにNginxを動かす(基本的には変更不要)
CMD ["nginx", "-g", "daemon off;"]

