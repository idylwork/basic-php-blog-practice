<?php
include '../lib/modules.php';
$pdo = new Connection();

$errors = [];
$post_id = $_POST['post_id'];
$post = $pdo->fetch_seq('SELECT * FROM posts WHERE id=:post_id', [':post_id' => $post_id], 'FETCH');

//debug($_POST, 'posts', true);
//debug($comments, 'comments');

if (isset($_POST['submit_comment'])) {
  validate();
  if (!$errors) {
    /** 記事にコメントを追加する */
    $comments = $pdo->fetch_seq('INSERT INTO `comments` (`post_id`,`name`,`content`) VALUES(:post_id, :name, :content)', [':post_id' => $_POST['post_id'], ':name' => $_POST['name'], ':content' => $_POST['content']], 'INSERT');
    header("Location: single.php?id={$_POST['post_id']}");
    exit;
  }
}

if (isset($_POST['submit_comment_remove'])) {
  if (!$errors) {
    /** コメントの削除 */
    $pdo->fetch_seq('DELETE FROM `comments` WHERE `id` = :comment_id', [':post_id' => $_POST['comment_id']], 'INSERT');
    header("Location: edit.php?id={$_POST['post_id']}");
    exit;
  }
}

include '_header.php';
?>
<div class="container">
  <h5>Error!</h5>
  <ul>
    <?php foreach ($errors as $error) : ?>
    <li><?php echo $error ?></li>
    <?php endforeach ?>
  </ul>
  <?php include 'footer.php'; ?>
</div>
