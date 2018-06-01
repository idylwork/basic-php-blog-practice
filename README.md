# 簡易ブログシステム

PHP 初学時に PHP・PDO 学習のために構築

- インフラ環境構築
  - Vagrant
  - PHP
  - MySQL
  - Apache
  - Docker (後日追加)
- CSS フレームワーク
  - Bootstrap
- JS ライブラリ
  - bootstrap-tags
  - Masonry の自作
- サーバーサイドの基礎
  - 中間テーブルをつかった多対多のリレーション
  - PDO によるデータベースアクセスとプリペアドステートメントの学習
  - セッションの理解
  - CSRF トークンの発行

# 動作手順

- Docker ビルド `docker build`
- Docker 起動 `docker-compose up -d`
- データベース構築 `docker exec -i mysql8 mysql -u test -ptest blog_db < docker/mysql/migrations.sql > /dev/null 2>&1`
- Docker 停止 `docker-compose stop`
