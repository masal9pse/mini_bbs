<?php
session_start();
//DBに接続しないので,require('dbconnect.php)は要らない

//セッションの情報を削除するので、空の配列で上書きする。
$_SESSION = array();
//設定にクッキーを使用するかの設定ファイル
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    //クッキーの情報を削除する。->クッキーの有効期限を切る
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

//cookieに保存されているメールアドレスを削除->有効期限を切る
setcookie('email', '', time() - 3600);

header('Location:index.php');
exit();
