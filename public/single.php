<?php
include '../lib/modules.php';
if (!isset($_GET['id'])) header("Location: /");

//クエリから記事とコメントの取得
$pdo = new Connection();
$post = $pdo->fetch_seq('SELECT * FROM posts WHERE id=:post_id', [':post_id' => $_GET['id']], 'FETCH');
$post['tags'] = $pdo->fetch_seq('SELECT `tags`.`name` FROM `posts_tags` INNER JOIN `tags` ON `posts_tags`.`tag_id` = `tags`.`id` AND `posts_tags`.`post_id` = :post_id', [':post_id' => $post['id']], 'COLUMN');
$post['comments'] = $pdo->fetch_seq('SELECT * FROM comments WHERE post_id=:post_id', [':post_id' => $_GET['id']], 'ASSOC');
?>
<?php include '_header.php'; ?>

<div class="container">
  <div class="card bg-light">
    <div class="card-body">
      <h4 class="card-title clearfix">
        <?php echo_html($post['title']); ?>
        <?php if ($session_username): ?>
          <a class="ml-2" href="edit.php?id=<?php echo $_GET['id'] ?>"><i class="fa fa-pencil mr-1"></i>編集</a>
        <?php endif; ?>
        <a class="float-right" href="./"><i class="fa fa-close"></i></a>
      </h4>
      <?php if(!empty($post['tags'])): ?>
      <div class="card-body tags-list">
        <?php foreach($post['tags'] as $tag) : ?>
        <span class="badge badge-info">
          <?php echo_html($tag); ?>
        </span>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <p class="card-text">
        <?php echo_html($post['content']); ?>
      </p>
      <?php if (!$session_username): ?>
        <p class="text-muted">ログイン中のみ編集可能。右上にrootと入力してログインしてください</p>
      <?php endif; ?>
    </div>
    <?php if($post['comments']) : ?>
    <div class="card-body">
      <?php foreach($post['comments'] as $comment) : ?>
        <p class="card-text">
          <i class="fa fa-comment mr-1"></i>
          <?php if ($comment['name']) : ?>
            <strong><?php echo_html($comment['name']) ?></strong>
          <?php endif ?>
          <?php echo_html($comment['content']) ?>
        </p>
      <?php endforeach ?>
    </div>
    <?php endif; ?>
    <div class="card-body">
      <p class="text-muted">記事へのコメント追加</p>
      <div id="comment_<?php echo $post['id']; ?>">
        <form method="post" action="comment.php">
          <div class="form-group row">
            <label for="comment_name" class="col-sm-3 col-form-label">Name</label>
            <div class="col-sm-9">
              <input type="text" id="comment_name" name="name" class="form-control">
            </div>
          </div>
          <div class="form-group row">
            <label for="comnent_content" class="col-sm-3 col-form-label">Comment</label>
            <div class="col-sm-9">
              <textarea id="comment_content" name="content" class="form-control" rows="3"></textarea>
            </div>
          </div>
          <input type="hidden" name="post_id" value="<?php echo $_GET['id'] ?>">
          <button type="submit" name="submit_comment" class="btn btn-danger btn-lg btn-block">Comment</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
