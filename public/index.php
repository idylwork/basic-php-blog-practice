<?php
include '../lib/modules.php';
include '_header.php';
$pdo = new Connection();

//投稿一覧取得
$posts = $pdo->fetch_seq('SELECT `id`, `posts`.* FROM `posts`', [], 'UNIQUE|ASSOC');

//プリペアドステートメントの定義
$pdo->prepare_for('comments', "SELECT * FROM `comments` WHERE `post_id` = :post_id LIMIT :limit");
$pdo->prepare_for('posts_tags', "SELECT `tags`.`name` FROM `posts_tags` INNER JOIN `tags` ON `posts_tags`.`tag_id` = `tags`.`id` AND `posts_tags`.`post_id` = :post_id LIMIT :limit");
$pdo->prepare_for('comments_count', "SELECT COUNT(1) FROM `comments` WHERE `post_id` = :post_id");
$pdo->prepare_for('tags_count', "SELECT COUNT(1) FROM `posts_tags` WHERE `post_id` = :post_id");

foreach($posts as $id => $post) {
  /** コメント */
  $posts[$id]['comments'] = $pdo->fetch_prepared('comments', [':post_id' => (int)$post['id'], ':limit' => Config::$display_comments_max], 'ASSOC');
  /** タグ */
  $posts[$id]['tags'] = $pdo->fetch_prepared('posts_tags', [':post_id' => (int)$post['id'], ':limit' => Config::$display_tags_max], 'COLUMN');
  /** コメントの省略件数 */
  $posts[$id]['comments_ellipsis'] = $pdo->fetch_prepared('comments_count', [':post_id' => (int)$post['id']], 'COLUMN')[0] - Config::$display_comments_max;
  /** タグの省略件数 */
  $posts[$id]['tags_ellipsis'] = $pdo->fetch_prepared('tags_count', [':post_id' => (int)$post['id']], 'COLUMN')[0] - Config::$display_tags_max;
}
?>

<div class="container">
  <?php if ($session_username): ?>
  <h1 class="mt-3">Create Article</h1>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <p class="text-muted">記事の新規作成 (タグの指定は記事更新時のみ)</p>
          <form method="post" action="post.php">
            <div class="form-group row">
              <label for="post_title" class="col-sm-3 col-form-label">Title</label>
              <div class="col-sm-9">
                <input type="text" id="post_title" name="title" class="form-control">
              </div>
            </div>
            <div class="form-group row">
              <label for="post_content" class="col-sm-3 col-form-label">Content</label>
              <div class="col-sm-9">
                <textarea id="post_content" name="content" class="form-control" rows="3"></textarea>
              </div>
            </div>
            <button type="submit" name="submit_post" class="btn btn-dark btn-lg btn-block">Create</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endif ?>
  <p class="text-muted">
    カードの高さが違うときに表示順序を上から表示に並べ替える (<a href="./bricks.html">テスト開発ページ</a>)<br>
    ウィンドウリサイズで再計算<br>
    コメントは最大<?= Config::$display_comments_max ?>件まで、タグは最大<?= Config::$display_tags_max ?>個まで表示
  </p>
</div>

<div id="block-article">
  <?php foreach($posts as $post) : ?>
    <div class="card bg-light card-article">
      <img class="card-img-top" src="img/thumb.jpg" alt="article image">
      <div class="card-body">
        <h4 class="card-title"><?php echo_html($post['title'], 50); ?></h4>
        <?php if(!empty($post['tags'])): ?>
        <div class="card-body tags-list">
          <?php foreach($post['tags'] as $tag) : ?>
          <span class="badge badge-info">
            <?php echo_html($tag); ?>
          </span>
          <?php endforeach; ?>
          <?php if ($post['tags_ellipsis'] > 0) { echo '…'; } ?>
        </div>
        <?php endif; ?>

        <p class="card-text">
          <?php echo_html($post['content'], 300); ?>
        </p>
      </div>
      <?php if(!empty($post['comments'])): ?>
      <div class="card-body">
        <?php foreach($post['comments'] as $comments) : ?>

        <p class="card-text comment-trim">
          <i class="fa fa-comment mr-1"></i><strong><?php echo_html($comments['name']) ?></strong>
          <?php echo_html($comments['content']) ?>
        </p>
        <?php endforeach ?>
        <?php if($post['comments_ellipsis'] > 0) { echo '<p class="card-text">…他' . $post['comments_ellipsis'] . '件のコメント</p>'; } ?>
      </div>
      <?php endif; ?>
      <div class="card-body">
        <a href="single.php?id=<?php echo $post['id']; ?>"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i> 詳細</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>


<?php include 'footer.php'; ?>
<?php
debug($posts, 'posts');
?>
