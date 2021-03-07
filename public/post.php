<?php
include '../lib/modules.php';
$pdo = new Connection();

$errors = [];

//記事の更新
if (isset($_POST['submit_update'])) {
  validate();
  if (!$errors) {
    //記事の更新
    $posts = $pdo->fetch_seq('UPDATE `posts` SET `title` = :title, `content` = :content WHERE `id` = :post_id', [':post_id' => $_POST['post_id'], ':title' => $_POST['title'], ':content' => $_POST['content']], 'INSERT');

    //タグの更新
    $tags_table = $pdo->fetch_seq('SELECT `tags`.`name` FROM `posts_tags` INNER JOIN `tags` ON `posts_tags`.`tag_id` = `tags`.`id` AND `posts_tags`.`post_id` = :post_id', [':post_id' => $_POST['post_id']], 'COLUMN');
    $tags_input = [];
    if ($_POST['tags']) {
      $tags_input_origin = explode(',', $_POST['tags']);
      foreach ($tags_input_origin as $tag) {
        //if (strpos($tag, ' ') === false) {
          $tags_input[] = $tag;
        //}
      }
    }
    /** 記事から外すタグ */
    $tags_delete = array_diff($tags_table, $tags_input);
    /** 新しく記事に関連づけるタグ */
    $tags_insert = array_diff($tags_input, $tags_table);
    // 記事からタグを削除
    if (count($tags_delete)) {
      $pdo->fetch_list('DELETE `posts_tags` FROM `posts_tags` INNER JOIN `tags` ON `posts_tags`.`tag_id` = `tags`.`id` AND `post_id` = :post_id AND `tags`.`name` IN (:tags)', [':post_id' => $_POST['post_id'], ':tags' => $tags_delete], 'INSERT');
    }
    /** DBに登録されているタグを取得 */
    $tags_exist = $pdo->fetch_list('SELECT name FROM tags WHERE name IN (:tags)', [':tags' => $tags_insert], 'COLUMN');
    /** タグの新規登録 */
    $tags_create = array_diff($tags_insert, $tags_exist);
    if (count($tags_create)) {
      $data = array_map(function ($tag_name) {
        return [$tag_name];
      }, $tags_create);
      $pdo->fetch_list('INSERT INTO `tags` (`name`) VALUES (:tags)', [':tags' => $data], 'INSERT');
    }
    // 記事へのタグ追加
    if (count($tags_insert)) {
      $tag_id_insert = $pdo->fetch_list('SELECT id FROM tags WHERE name IN (:tags)', [':tags' => $tags_insert], 'COLUMN');

      foreach ($tag_id_insert as $tag_id) {
        $records_add[] = [$_POST['post_id'], $tag_id];
      }
      $pdo->fetch_list('INSERT INTO `posts_tags` (`post_id`, `tag_id`) VALUES (:records)', [':records' => $records_add], 'INSERT');
    }

    header("Location: single.php?id={$_POST['post_id']}");
    exit;
  }
}

//新規投稿
if (isset($_POST['submit_post'])) {
  validate();
  if (!$errors) {
    $posts = $pdo->fetch_seq('INSERT INTO `posts` (title,content) VALUES(:title, :content)', [':title' => $_POST['title'], ':content' => $_POST['content']], 'INSERT');
    header('Location: index.php');
    exit;
  }
}

//記事削除
if (isset($_POST['submit_post_remove'])) {
  if (!$errors) {
    $pdo->fetch_seq('DELETE FROM `posts` WHERE `id` = :post_id', [':post_id' => $_POST['post_id']], 'INSERT');

    //記事を削除する際に関連するコメントとタグ情報も削除する
    $pdo->fetch_seq('DELETE FROM `comments` WHERE `post_id` = :post_id;', [':post_id' => $_POST['post_id']], 'INSERT');
    $pdo->fetch_seq('DELETE FROM `posts_tags` WHERE `post_id` = :post_id;', [':post_id' => $_POST['post_id']], 'INSERT');


    header("Location: index.php");
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
