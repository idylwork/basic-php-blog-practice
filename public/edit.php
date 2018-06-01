<?php
include '../lib/modules.php';
include '_header.php';

$post_id = $_GET['id'];

$pdo = new Connection();

// 投稿の取得
$post = $pdo->fetch_seq('SELECT * FROM posts WHERE id=:post_id', [':post_id' => $post_id], 'FETCH');
$post['comments'] = $pdo->fetch_seq('SELECT `id`, `comments`.* FROM `comments` WHERE `post_id` = :post_id', [':post_id' => $post_id], 'UNIQUE|ASSOC');

$post['tags'] = $pdo->fetch_seq('SELECT `posts_tags`.`tag_id`, `tags`.`name` FROM `posts_tags` INNER JOIN `tags` ON `posts_tags`.`tag_id` = `tags`.`id` AND `posts_tags`.`post_id` = :post_id', [':post_id' =>
$post_id], 'UNIQUE|COLUMN');

/** Tagsinputのvalue値 */
$tags_default = implode(',', $post['tags']);

if ($session_username === '') {
  header("Location: single.php?id={$post_id}");
}

?>

<div class="container">
  <div class="card bg-light">
    <div class="card-body">
      <form method="post" action="post.php">
        <h3 class="clearfix">編集中<a class="float-right" href="single.php?id=<?php echo $post_id ?>"><i class="fa fa-close"></i></a></h3>

        <input type="text" name="tags" class="tagsinput" value="<?php echo_html($tags_default) ?>" data-role="tagsinput" placeholder="Tag">
        <p class="text-muted">コンマ区切りで複数タグを設定可能です</p>

        <div class="row">
          <div class="col-md-12">
            <div class="card-body">
                <div class="form-group row">
                  <label for="post_title" class="col-sm-3 col-form-label">Title</label>
                  <div class="col-sm-9">
                    <input type="text" id="post_title" name="title" class="form-control" value="<?php echo_html($post['title']); ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="post_content" class="col-sm-3 col-form-label">Content</label>
                  <div class="col-sm-9">
                    <textarea id="post_content" name="content" class="form-control" rows="3"><?php echo_textarea($post['content']); ?></textarea>
                  </div>
                </div>
                <input type="hidden" name="post_id" value="<?php echo $post_id ?>">
                <button type="submit" name="submit_update" class="btn btn-dark btn-lg btn-block">Update</button>
                <button type="submit" name="submit_post_remove" class="btn btn-danger btn-lg btn-block" onclick="return confirm_submit('記事を削除');">Delete</button>
            </div>
          </div>
        </div>
      </form>
    </div>
    <?php if($post['comments']) : ?>
    <div class="card-body">
      <?php foreach($post['comments'] as $comment) : ?>
      <form method="post" action="comment.php" onsubmit="return confirm_submit('コメントを削除');">
        <p class="card-text">
          <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
          <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
          <button type="submit" name="submit_comment_remove" class="btn btn-sm btn-danger"><i class="fa fa-close"></i></button>
          <strong><?php echo_html($comment['name']) ?></strong>
          <?php echo_html($comment['content']) ?>
        </p>
      </form>
      <?php endforeach ?>
    </div>
    <?php endif; ?>
    <div class="card-body">
      <div id="comment_<?php echo $post['id']; ?>">
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
