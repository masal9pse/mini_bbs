<?php
session_start();
require('dbconnect.php');

// １時間何もしないとログアウトする↓
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  // 1h、ログインが有効になる。
  $_SESSION['time'] = time();

  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));
  //$memberは$membersのdataを保存,ログインしているユーザーがDBから吐き出される。
  $member = $members->fetch();
} else {
  header('Location:login.php');
  exit();
}
//投稿するボタンがクリックされた時
if (!empty($_POST)) {
  // var_dump($_POST['reply_message_id']); // ★
  //↓textareaのname属性,messageに該当
  if ($_POST['message'] !== '') {
    $message = $db->prepare('INSERT INTO posts SET member_id=?,message=?, replay_message_id=?,  created=NOW()');
    $message->execute(array(
      //$members=DBに保存されたデータ、$members=セッションに保存されたデータ、今回はDBの方の値を使う
      $member['id'],
      $_POST['message'],
      $_POST['reply_post_id']
    ));

    //裏側でPOSTの値を持ち続けているため、再描画するとデータが保存され続ける。
    //対策->もう一度素の状態のindex.phpを呼び出す。
    header('Location:index.php');
    exit();
  }
}


//5の倍数になっている必要がある、２ページ目10件、3ページ目15件目 ...etc
$page = $_REQUEST['page'];
$start = ($page - 1) * 5;

//リレーション
$posts = $db->prepare('SELECT m.name,m.picture,p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?,5');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();


//Reがクリックされた時、
if (isset($_REQUEST['res'])) {
  //返信(URLパラメーターの処理)の処理
  $response = $db->prepare('SELECT m.name,m.picture,p.* FROM members m,posts p WHERE m.id=p.member_id AND p.id=?');
  $response->execute(array($_REQUEST['res']));

  $table = $response->fetch();
  $message = '@' . $table['name'] .  ' ' . $table['message'];
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ひとこと掲示板</title>

  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>ひとこと掲示板</h1>
    </div>
    <div id="content">
      <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
      <form action="" method="post">
        <dl>
          <dt><?php print(htmlspecialchars($member['name'], ENT_QUOTES)); ?>さん、メッセージをどうぞ</dt>
          <dd>
            <!-- $messageを下記のmessageに入れる,textareaはタグの中に入れる。 -->
            <textarea name="message" cols="50" rows="5"><?php print(htmlspecialchars($message, ENT_QUOTES)); ?></textarea>
            <input type="hidden" name="reply_post_id" value="<?php print(htmlspecialchars($_REQUEST['res'], ENT_QUOTES)); ?>" />
          </dd>
        </dl>
        <div>
          <p>
            <input type="submit" value="投稿する" />
          </p>
        </div>
      </form>
      <?php foreach ($posts as $post) : ?>
        <div class="msg">
          <img src="member_picture/<?php print(htmlspecialchars($post['picture'], ENT_QUOTES)); ?>" width="48" height="48" alt="<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>" />
          <p><?php print(htmlspecialchars($post['message'], ENT_QUOTES));    ?> <span class="name">（<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>）</span>[<a href="index.php?res=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>">Re</a>]</p>
          <p class="day"><a href="view.php?id=<?php print(htmlspecialchars($post['id'])); ?>"><?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?></a>


            <?php if ($post['replay_message_id'] > 0) : ?>
              <a href="view.php?id=<?php print(htmlspecialchars($post['replay_message_id'], ENT_QUOTES)); ?>">
                返信元のメッセージ</a>
            <?php endif; ?>
            <!-- フロントの削除をクリックすると、メッセージが消える -->
            <!-- 自分が他のユーザーのメッセージを削除できるのはおかしい -->
            <!-- 今ログインしている人のIDが、DBにあるmember_idと一致していたら -->
            <?php if ($_SESSION['id'] == $post['member_id']) : ?>
              [<a href="delete.php?id=<?php print(htmlspecialchars($post['id'])); ?>" style="color: #F33;">削除</a>]
          </p>
        <?php endif; ?>
        </div>
      <?php endforeach; ?>
      <ul class="paging">
        <li><a href="index.php?page=">前のページへ</a></li>
        <li>前のページ</li>
        <li><a href="index.php?page=">次のページへ</a></li>
        <li>次のページ</li>
      </ul>
    </div>
  </div>
</body>

</html>