<?php
session_start();
require('dbconnect.php');

?>
<!--  ログインしているユーザーのメッセージを消そうとしているのか判断-->
<!-- isset関数は、変数に値がセットされていて、かつNULLでないときに、TRUE(真)を戻り値として返します。NULLとは、変数が値を持っていないことをあらわす特別な値です。 -->
<?php if (isset($_SESSION['id'])) {
  // DBに投げたidを変数に代入する
  $id = $_REQUEST['id'];
  // SQLを組み立てる　－＞
  //prepareは引数に指定したSQL文をデータベースに対して発行してくれます。
  $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
  //idの?にURLパラメーターで渡されたidが格納される
  $messages->execute(array($id));
  //　そのデータを取得する
  $message = $messages->fetch();

  //DBから取得した中のmember_idとサーバーに保存されているデータが同じだったら
  if ($message['member_id'] == $_SESSION['id']) {
    $del = $db->prepare('DELETE FROM posts WHERE id=?');
    //dbをじっさいに削除する
    $del->execute(array($id));
  }
}

//処理が上手くいけば、index.phpに遷移する

header('Location: index.php');
exit();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>

<body>

</body>

</html>