<?php
session_start();
require('../dbconnect.php');

if (!isset($_SESSION['join'])) {
	//キーjoinが登録されていなければ、index.phpに戻る
	header('Location:index.php');
	exit();
}
//$_POSTが空では無かったら＝値が入っていれば
if (!empty($_POST)) {

	$startment = $db->prepare('INSERT INTO members2 SET name=?,email=?,password=? ,picture=?, created=NOW()');
	echo $startment->execute(array(
		$_SESSION['join']['name'],
		$_SESSION['join']['email'],
		sha1($_SESSION['join']['password']),
		$_SESSION['join']['image'],
	));
	// unset関数は、定義した変数の割当を削除する関数です。
	unset($_SESSION['join']);
	header('Location:thanks.php');
	exit();
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>

<body>
	<div id="wrap">
		<div id="head">
			<h1>会員登録</h1>
		</div>

		<div id="content">
			<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
			<form action="" method="post">
				<input type="hidden" name="action" value="submit" />
				<dl>
					<dt>ニックネーム</dt>
					<?php print(htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES)); ?>
					<dd>
					</dd>
					<dt>メールアドレス</dt>
					<?php print(htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES)); ?>
					<dd>
					</dd>
					<dt>パスワード</dt>
					<dd>
						【表示されません】
					</dd>
					<dt>写真など</dt>
					<!-- 画像ファイルが空でなければ、 -->
					<?php if ($_SESSION['join']['image'] !== '') : ?>
						<!-- member_pictureないの画像をフロントに表示する -->
						<img src="../member_picture/<?php print(htmlspecialchars($_SESSION['join']['image'], ENT_QUOTES)); ?>">
					<?php endif; ?>
					<dd>
					</dd>
				</dl>
				<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
			</form>
		</div>

	</div>
</body>

</html>