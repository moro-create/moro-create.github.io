<?php
//エスケープ処理を行う関数
function h($var){
  return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
}
//GET メソッドで渡された値を初期化（取得）
$name = filter_input(INPUT_GET, 'name');
$email = filter_input(INPUT_GET, 'email');
$tel = filter_input(INPUT_GET, 'tel');
$subject = filter_input(INPUT_GET, 'subject');
$body = filter_input(INPUT_GET, 'body');
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>完了画面</title>

    <!-- Bootstrap5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
  <div class="container">
    <br>
    <br>
    <h4>送信完了いたしました。</h4>
    <br>
    <br>
    <p>この度は、お問い合わせ頂き誠にありがとうございます。</p>
    <br>
    <p>確認の自動返信メールをお送りいたしました。</p>
    <br>
    <p>返信までにお時間をいただくことがございます。</p>
    <p>ご了承ください。</p>

    <a href="https://ingeniousmorocorn.com/portfolio/">TOPに戻る</a>
  </div>
  
</body>

</html>