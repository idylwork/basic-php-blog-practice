# 簡易ブログシステム
PHP初学時にPHP・PDO学習のために構築したものです

- インフラ環境構築
  - Vagrant
  - PHP
  - MySQL
  - Apache
  - Docker (後日追加)
- CSSフレームワーク
  - Bootstrap
- JSライブラリ
  - bootstrap-tags
  - Masonryの自作
- サーバーサイドの基礎
  - 中間テーブルをつかった多対多のリレーション
  - PDOによるデータベースアクセスとプリペアドステートメントの学習
  - セッションの理解
  - CSRFトークンの発行

# 動作手順
- Dockerビルド `docker build`
- Docker起動 `docker-compose up -d`
- データベース構築 `docker exec -i mysql8 mysql -u test -ptest blog_db < docker/mysql/migrations.sql > /dev/null 2>&1`
- Docker停止 `docker-compose stop`
