<?php
$default_password = 'root';
$default_username = 'root';

$message =  '';
$login_errors = array();
$session_username = '';

session_start();

//debug($_POST, 'Post');
//debug($_SESSION, 'Session');

if (isset($_POST['login_submit'])) {
  if ($_POST['login_password'] === $default_password) {
    $_SESSION['login_password'] = $_POST['login_password'];
    $message = 'ログインしました。';
  } else {
    $login_errors[] = 'ログインパスワードが違います。';
  }
}

if (isset($_POST['logout_submit'])) {
  unset($_SESSION['login_password']);
  $message = 'ログアウトしました。';
}

if (isset($_SESSION['login_password'])) {
  if ($_SESSION['login_password'] === $default_password) {
    $session_username = $default_username;
  }
}


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title></title>
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="css/tagsinput.css">
<link rel="stylesheet" type="text/css" href="css/common.css">
<link rel="stylesheet" type="text/css" href="css/font-awesome.css">
</head>
<body>
  <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
    <a class="navbar-brand" href="./">Basic Post System</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="./">Home</a>
        </li>
      </ul>
      <?php if (!$session_username) : ?>
      <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="form-inline my-2 my-lg-0">
        <div class="input-group">
          <input type="password" name="login_password" class="form-control" placeholder="Password" aria-label="Password">
          <span class="input-group-btn">
            <button type="submit" method="post" name="login_submit" class="btn btn-success my-2 my-sm-0">Login</button>
          </span>
        </div>
      </form>
      <?php else : ?>
      <span class="mr-3"><i class="fa fa-tree mr-1"></i><?php echo $session_username ?></span>
      <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="form-inline my-2 my-lg-0">
        <button type="submit" method="post" name="logout_submit" class="btn btn-outline-success my-2 my-sm-0">Logout</button>
      </form>
      <?php endif; ?>
    </div>
  </nav>

  <div class="container">
    <?php if($message): ?>
      <div class="alert alert-success" role="alert">
        <?php echo $message ?>
      </div>
    <?php endif ?>
    <?php if($login_errors): ?>
      <div class="alert alert-danger" role="alert">
      <h5>Error!</h5>
        <ul>
          <?php foreach ($login_errors as $error) : ?>
          <li><?php echo $error ?></li>
          <?php endforeach ?>
        </ul>
      </div>
    <?php endif ?>
  </div>
<?php
//  if (isset($_SESSION["counter"])) {
//  $_SESSION["counter"]++;
//  print($_SESSION["counter"]."回目の読み込みです。");
//  } else {
//  $_SESSION["counter"] = 0;
//  print("はじめての読み込みです。");
//  }
