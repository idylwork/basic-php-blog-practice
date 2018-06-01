<?php
include '../lib/modules.php';
$pdo = new PDO('mysql:dbname=blog_db;host=localhost;port=2222', 'root', 'root');
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$tags = $pdo->query('CREATE TABLE IF NOT EXISTS tags (id INT(11) PRIMARY KEY, tags_id INT(11) NOT NULL)');
$posts_tags = $pdo->query('CREATE TABLE IF NOT EXISTS posts_tags (posts_id INT(11) NOT NULL, tags_id INT(11) NOT NULL)');

?>

<?php include '_header.php'; ?>
