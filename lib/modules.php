<?php
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('extension', 'pdo_mysql.so');
ini_set('extension', 'php-mysql');

/**
 * 設定値
 */
class Config
{
  public static $test =1;
  public static $display_comments_max = 2;
  public static $display_tags_max = 4;
}

/**
 * フォーム入力のバリデーションを行う
 */
function echo_html($str, $max_length = NULL) {
  if (gettype($max_length) === 'integer') {
    $str = mb_strimwidth($str, 0, $max_length, '...', 'UTF-8');
  }

  echo nl2br(htmlspecialchars($str));
}

function echo_textarea($str) {
  echo htmlspecialchars($str);
}
// htmlentities($str, ENT_QUOTES, mb_internal_encoding());


/**
 * デバッグ出力
 *
 * @param mixed $var 表示する変数
 * @param string $title タイトルをつける
 * @param string $option [called]バックトレースの深度を上げる [exit]強制終了
 */
function debug($var, $title = '', $option = '') {
  $title = ucfirst($title);
  if ($title === '') {
    echo '<div class="alert alert-warning clearfix mx-2" role="alert">';
  } else {
    echo '<div class="alert alert-danger clearfix mx-2" role="alert">';
  }
  echo '<small class="float-right">';

  $backtrace = debug_backtrace()[0];
  echo '(' . gettype($var) . ') ' . preg_replace('/.+\/(.+?)$/', '$1', $backtrace['file']);
  echo ' <strong>' . $backtrace['line'] . '</strong>';
  $var_type = gettype($var);

  if ($option === 'called') {
    $backtrace = debug_backtrace()[1];
    echo '<br>└ ' . '(' . gettype($var) . ') ' . preg_replace('/.+\/(.+?)$/', '$1', $backtrace['file']);
    echo ' <strong>' . $backtrace['line'] . '</strong></small>';
    $var_type = gettype($var);
  }

  echo '</small>';

  if ($var_type === 'NULL') {
    //NULL
    echo '<p>';
    if ($title) { echo '<strong>' . $title . '</strong> '; }
    echo 'NULL';
    echo '</p>';
  } if ($var_type === 'boolean') {
    //NULL
    echo '<p>';
    if ($title) { echo '<strong>' . $title . '</strong> '; }
    echo $var === true ? 'TRUE' : 'FALSE';
    echo '</p>';
  } else if ($var_type === 'integer' || $var_type === 'string') {
    //文字列・数値
    echo '<p>';
    if ($title) { echo '<strong>' . $title . '</strong> '; }
    echo $var;
    echo '</p>';
  } else if ($var_type === 'object') {
    if ($title) { echo '<strong>' . $title . '</strong> '; }
    print_r($var);
  } else if ($var_type === 'array') {
    if (array_values($var) === $var && is_multi_array($var) === false) {
      //ただの配列
      echo '<p>';
      if ($title) { echo '<strong>' . $title . '</strong> '; }
      echo '<small>';
      print_r(implode(', ', $var));
      echo '</small>';
      echo '</p>';
    } else {
      //連想配列
      echo '<h3>' . $title . '</h3>';
      echo '<small><pre>';
      print_r($var);
      echo '</pre></small>';
    }
  }
  echo '</div>';
  if ($option === 'exit') exit;
}


/**
 * PDO操作クラス
 */
class Connection
{
  // DockerのHostsに設定されているのでコンテナ名でアクセス可
  const HOST = 'mysql8';
  const DB_NAME = 'blog_db';
  const PORT = '2222';
  const USER = 'test';
  const PASS = 'test';
  const ENCODING = 'utf8';

  function __construct() {
    // MySQLに接続
    try {
      $this->pdo = new PDO("mysql:dbname=" . self::DB_NAME . ";host=".self::HOST . ";", self::USER, self::PASS);
      //charset=self::ENCODING,
      $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      $this->prepared = [];
    } catch(PDOException $e) {
      debug($e, 'PDO Error');
      debug($this->pdo->errorInfo(), 'PDO Error');
      phpinfo();
      die();
    } catch(Exception $e) {
      debug($e->getMesseage, 'Error');
      die();
    }
    //エラーを表示
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    return $this->pdo;
  }

  /**
   * プリペアドステートメントを準備する
   */
  function prepare_for($stmt_id, $sql) {
    $this->prepared[$stmt_id] = $this->pdo->prepare($sql);
  }

  /**
   * プリペアドステートメントを使用する
   */
  function fetch_prepared($stmt_id, $values = [], $mode = '') {
    $stmt = $this->prepared[$stmt_id];
    //debug($stmt, 'FETCH START', 'called');
    foreach ($values as $id => $value) {
      $pdo_param = NULL;
      if (gettype($value) === 'integer') {
        $pdo_param = PDO::PARAM_INT;
      }
      $stmt->bindValue($id, $value, $pdo_param);
      //debug($id . ' = ' . $value, NULL, 'called');
    }
    $this->prepared[$stmt_id]->execute();
    return $this->run_fetch($stmt, $mode);
  }

  /**
   * SELECT文のときに使用する関数
   *
   * @param $sql {string} SQL文
   * @param $mode {string} fetchモード ASSOC|COLUMN のようにして指定
   * @param $values {array} プレースホルダを連想配列で指定 'post_id' => 6 のようにする
   */
  function fetch_seq($sql, $values = [], $mode = ''){
    $stmt = $this->pdo->prepare($sql);

    foreach ($values as $id => $value) {
      $stmt->bindValue($id, $value);
    }
    $stmt->execute();
    if ($mode !== 'INSERT') {
      return $this->run_fetch($stmt, $mode);
    }
  }

  /**
   * IN句やINSERTで、配列をバインドする必要があるときに使用する関数
   * 疑問符プレースホルダに置き換える
   *
   * @param $sql {string} SQL文
   * @param $mode {string} fetchモード ASSOC|COLUMN のようにして指定
   * @param $values {array} プレースホルダを連想配列で指定 'post_id' => 6 のようにする
   */
  function fetch_list($sql, $values = [], $mode = ''){
    $this->execute_values = [];
    $this->values = $values;
    $this->hasEmpty = false;

    $this->separeter = ',?';
    $this->separeter_count = mb_strlen($this->separeter) - 1;

    //SQL文を疑問符プレースホルダに置き換え、execute_valuesに対応する値を追加する
    $sql = preg_replace_callback('/:\S+(?=[ ()])/', function ($_id) use ($values, $mode) {
      $value = $values[$_id[0]];
      if (gettype($value) === 'array') {
        if ($mode === 'INSERT' && is_multi_array($value)) {
          // 連想配列の場合
          $placeholder = '';
          foreach($value as $record_value) {
            $placeholder .= '),(' . substr(str_repeat(',?', count($record_value)), 1);
            $this->execute_values = array_merge($this->execute_values, $record_value);
          }
          $placeholder = substr($placeholder, 3);
        } else {
          // 配列
          $this->execute_values = array_merge($this->execute_values, $value);
          $placeholder = substr(str_repeat(',?', count($value)), 1);
        }
        if (empty($placeholder)) { $this->hasEmpty = true; }
        return $placeholder;
      } else {
        // 整数・文字列
        $this->execute_values[] = $value;
        if (empty($value)) { $this->hasEmpty = true; }
        return '?';
      }
    }, $sql);
    //debug($sql,'SQL');
    //debug($this->execute_values);
    if ($this->hasEmpty) { return []; }

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($this->execute_values);

    //FETCHモードが指定されている場合にはfetchする。
    if ($mode !== 'INSERT') {
      return $this->run_fetch($stmt, $mode);
    }
  }

  /**
   * プリペアドステートメントとFETCHモードを受け取ってレコードを返す
   * @param $stmt {PDOStatement} プリペアドステートメント
   * @param $mode {string} FETCHモード
   */
  function run_fetch ($stmt, $mode) {
    //fetchモードの指定 'FETCH'だった場合はfetchAll()ではなくfetch()を使用する。
    if ($mode === 'COLUMN') {
      $records = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else if ($mode === 'UNIQUE|ASSOC') { // IDをキーとした連想配列
      $records = $stmt->fetchAll(PDO::FETCH_UNIQUE|PDO::FETCH_ASSOC);
    } else if ($mode === 'ASSOC|UNIQUE') { // IDをキーとした連想配列
      $records = $stmt->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_UNIQUE);
    } else if ($mode === 'UNIQUE|COLUMN') {
      $records = $stmt->fetchAll(PDO::FETCH_UNIQUE|PDO::FETCH_COLUMN);
    } else if ($mode === 'COLUMN|GROUP') {
      $records = $stmt->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
    } else if ($mode === 'ASSOC') {
      $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else if ($mode === 'FETCH') {
      $records = $stmt->fetch(PDO::FETCH_ASSOC);
    } else if ($mode === 'KEY_PAIR') { //先頭カラムがキー、次カラムが要素
      $records = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    return $records;
  }

  //SELECT,INSERT,UPDATE,DELETE文の時に使用する関数。
  function plural($sql,$item){
    $hoge=$this->pdo();
    $stmt=$hoge->prepare($sql);
    $stmt->execute(array(':id'=>$item));//sql文のVALUES等の値が?の場合は$itemでもいい。
    return $stmt;
  }
}

//if (isset($_SESSION['password'])) {
//  if ($_SESSION['password'] ===  $loginpass) {
//    $_SESSION['username'] =
//    echo 'ログインしました';
//  }
//  echo($_SESSION["password"]."回目の読み込みです。");
//} else {
//  $_SESSION["counter"] = 0;
//  echo("はじめての読み込みです。");
//}

/**
 * 配列の深さを調べる
 * @param $array {array} 深さを調べる配列
 * @param $depth {integer} 再帰関数として呼ばれた際に深さを引き継ぐ
 */
function array_depth($array, $depth = 0) {
  if (is_array($array) && count($array)) {
    ++$depth;
    $depths = array($depth);
    foreach ($array as $value) {
      if (is_array($value) && count($value)) {
        $depths[] = array_depth($value, $depth);
      }
    }
    return max($depths);
  }
  return $depth;
}

function is_multi_array($array) {
  if (is_array($array) && count($array)) {
    foreach ($array as $value) {
      if (is_array($value)) {
        return true;
      }
    }
  }
  return false;
}

//CSRFトークンの生成
function get_csrf_token() {
  return hash('sha256', session_id());
}

//CSRFトークンの照合
function has_csrf_token() {
  return $token === getCsrfToken();
}

function validate() {
  global $errors;
  if (isset($_POST['submit_post']) || isset($_POST['submit_update'])) {
    //記事の投稿・更新
    if (empty($_POST['title'])) $errors[] = 'タイトルを入力してください。';
    if ($_POST['title'] > 80) $errors[] = 'タイトルが長すぎます。';
    if (empty($_POST['content'])) $errors[] = '内容を入力してください。';
  } else if (isset($_POST['submit_comment'])) {
    //コメントの投稿
    global $errors;
    if ($_POST['name'] > 80) $errors[] = '名前が長すぎます。';
    if (empty($_POST['content'])) $errors[] = '内容を入力してください。';
  }
}
