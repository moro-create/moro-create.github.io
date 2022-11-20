<?php
//エスケープ処理やデータチェックを行う関数のファイルの読み込み
require './libs/functions.php';

//POSTされたデータを変数に格納（値の初期化とデータの整形：前後にあるホワイトスペースを削除）
$name = trim(filter_input(INPUT_POST, 'name'));
$email = trim(filter_input(INPUT_POST, 'email'));
$email_check = trim(filter_input(INPUT_POST, 'email_check'));
$tel = trim(filter_input(INPUT_POST, 'tel'));
$subject = trim(filter_input(INPUT_POST, 'subject'));
$body = trim(filter_input(INPUT_POST, 'body'));

//送信ボタンが押された場合の処理
if (isset($_POST['submitted'])) {

  //POSTされたデータをチェック  
  $_POST = checkInput($_POST);

  //エラーメッセージを保存する配列の初期化
  $error = array();

  //値の検証
  if ($name == '') {
    $error['name'] = '*お名前は必須項目です。';
    //制御文字でないことと文字数をチェック
  } else if (preg_match('/\A[[:^cntrl:]]{1,30}\z/u', $name) == 0) {
    $error['name'] = '*お名前は30文字以内でお願いします。';
  }
  if ($email == '') {
    $error['email'] = '*メールアドレスは必須です。';
  } else { //メールアドレスを正規表現でチェック
    $pattern = '/\A([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}\z/uiD';
    if (!preg_match($pattern, $email)) {
      $error['email'] = '*メールアドレスの形式が正しくありません。';
    }
  }
  if ($email_check == '') {
    $error['email_check'] = '*確認用メールアドレスは必須です。';
  } else { //メールアドレスを正規表現でチェック
    if ($email_check !== $email) {
      $error['email_check'] = '*メールアドレスが一致しません。';
    }
  }
  if ($tel != '' && preg_match('/\A\(?\d{2,5}\)?[-(\.\s]{0,2}\d{1,4}[-)\.\s]{0,2}\d{3,4}\z/u', $tel) == 0) {
    $error['tel'] = '*電話番号の形式が正しくありません。';
  }
  if ($subject == '') {
    $error['subject'] = '*件名は必須項目です。';
    //制御文字でないことと文字数をチェック
  } else if (preg_match('/\A[[:^cntrl:]]{1,50}\z/u', $subject) == 0) {
    $error['subject'] = '*件名は50文字以内でお願いします。';
  }
  if ($body == '') {
    $error['body'] = '*内容は必須項目です。';
    //制御文字（タブ、復帰、改行を除く）でないことと文字数をチェック
  } else if (preg_match('/\A[\r\n\t[:^cntrl:]]{1,300}\z/u', $body) == 0) {
    $error['body'] = '*内容は300文字以内でお願いします。';
  }

  //エラーがなく且つ POST でのリクエストの場合
  if (empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    //メールアドレス等を記述したファイルの読み込み
    require './libs/mailvars.php';

    //メール本文の組み立て
    $mail_body = 'コンタクトページからのお問い合わせ' . "\n\n";
    $mail_body .=  "お名前： " . h($name) . "\n";
    $mail_body .=  "Email： " . h($email) . "\n";
    $mail_body .=  "お電話番号： " . h($tel) . "\n\n";
    $mail_body .=  "＜お問い合わせ内容＞" . "\n" . h($body);

    //--------sendmail------------

    //メールの宛先（名前<メールアドレス> の形式）。値は mailvars.php に記載
    $mailTo = mb_encode_mimeheader(MAIL_TO_NAME) . "<" . MAIL_TO . ">";

    //Return-Pathに指定するメールアドレス
    $returnMail = MAIL_RETURN_PATH; //
    //mbstringの日本語設定
    mb_language('ja');
    mb_internal_encoding('UTF-8');

    // 送信者情報（From ヘッダー）の設定
    $header = "From: " . mb_encode_mimeheader($name) . "<" . $email . ">\n";
    $header .= "Cc: " . mb_encode_mimeheader(MAIL_CC_NAME) . "<" . MAIL_CC . ">\n";
    $header .= "Bcc: <" . MAIL_BCC . ">";

    //メールの送信
    //メールの送信結果を変数に代入
    if (ini_get('safe_mode')) {
      //セーフモードがOnの場合は第5引数が使えない
      $result = mb_send_mail($mailTo, $subject, $mail_body, $header);
    } else {
      $result = mb_send_mail($mailTo, $subject, $mail_body, $header, '-f' . $returnMail);
    }

    //メール送信の結果判定
    if ($result) {


      //自動返信メール
      //ヘッダー情報
      $ar_header = "MIME-Version: 1.0\n";
      // AUTO_REPLY_NAME や MAIL_TO は mailvars.php で定義
      $ar_header .= "From: " . mb_encode_mimeheader(AUTO_REPLY_NAME) . " <" . MAIL_TO . ">\n";
      $ar_header .= "Reply-To: " . mb_encode_mimeheader(AUTO_REPLY_NAME) . " <" . MAIL_TO . ">\n";
      //件名
      $ar_subject = 'お問い合わせ自動返信メール';
      //本文
      $ar_body = $name . " 様\n\n";
      $ar_body .= "この度は、お問い合わせ頂き誠にありがとうございます。" . "\n\n";
      $ar_body .= "下記の内容でお問い合わせを受け付けました。\n\n";
      $ar_body .= "お問い合わせ日時：" . date("Y年m月d日 D H時i分") . "\n";
      $ar_body .= "お名前：" . $name . "\n";
      $ar_body .= "メールアドレス：" . $email . "\n";
      $ar_body .= "お電話番号： " . $tel . "\n\n";
      $ar_body .= "＜お問い合わせ内容＞" . "\n" . $body;

      //自動返信メールを送信（送信結果を変数 $result2 に代入）
      if (ini_get('safe_mode')) {
        $result2 = mb_send_mail($email, $ar_subject, $ar_body, $ar_header);
      } else {
        $result2 = mb_send_mail($email, $ar_subject, $ar_body, $ar_header, '-f' . $returnMail);
      }

      //空の配列を代入し、すべてのPOST変数を消去
      $_POST = array();

      $params = '?';
      $params .= 'name=' . h($name);
      $params .= '&email=' . h($email);
      $params .= '&tel=' . h($tel);
      $params .= '&subject=' . h($subject);
      $params .= '&body=' . h($body);

      //変数の値も初期化
      $name = '';
      $email = '';
      $tel = '';
      $subject = '';
      $body = '';

      //完了ページ（complete.php）へリダイレクト
      $url = 'complete.php';
      header('Location:' . $url);
      exit;

      // //再読み込みによる二重送信の防止
      $params = '?result=' . $result;
      // //サーバー変数 $_SERVER['HTTPS'] が取得出来ない環境用
      if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === "https") {
        $_SERVER['HTTPS'] = 'on';
      }
      $url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
      header('Location:' . $url . $params);
      exit;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="stylesheet" href="../css/reset.css">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/nav.css">
  <link rel="stylesheet" href="../css/fakeLoader.css">
  <link rel="stylesheet" href="./contact.css">

  <title>My_Portfolio</title>

  <!-- fontawesome -->
  <link href="https://use.fontawesome.com/releases/v5.0.8/css/all.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/6bfff0d01c.js" crossorigin="anonymous"></script>

  <title>お問い合わせ</title>

  <!-- Bootstrap5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<style>
  @media screen and (max-width: 767px) {
    .fixed-bg {
      height: 94px;
      /* スマホ時は背景をスクロールするように変更 */
      background-attachment: scroll;
    }

    h2.sp-mt {
      margin-top: 100px;
    }
    
  }
</style>

<body style="background-color: #f6f6f6;">

  <div class="fakeLoader"></div>
  <div id="fakeLoader"></div>


  <nav class="back-color">
    <a href="https://ingeniousmorocorn.com/portfolio/">
      <h1>My_Portfolio</h1>
    </a>
  </nav>

  <br><br><br>

  <div class="contact-img fixed-bg inview_re fadeIn">
    <h2 class="sec-title">Contact</h2>
  </div>

  <div class="inner contact wrapper  inview_re fadeIn">
    <div class="contact-form">
      <div class="container">
        <h2 class="sp-mt">CONTACT</h2>
        <?php if (isset($result) && !$result) : // 送信が失敗した場合  
        ?>
        <?php elseif (isset($result) && !$result) : // 送信が失敗した場合 
        ?>
          <h4>送信失敗</h4>
          <p>申し訳ございませんが、送信に失敗しました。</p>
          <p>しばらくしてもう一度お試しになるか、メールにてご連絡ください。</p>
          <p>メール：<a href="mailto:tokkyu_hitati3.14@outlook.jp">Contact</a></p>
          <hr>
        <?php endif; ?>
        <p>以下のフォームからお問い合わせください。</p>
        <form id="form" class="validationForm" method="post" novalidate>
          <div class="form-group">
            <label for="name">お名前（必須）
              <span class="error-php"><?php if (isset($error['name'])) echo h($error['name']); ?></span>
            </label>
            <input type="text" class="required maxlength form-control form" data-maxlength="30" id="name" name="name" placeholder="氏名" data-error-required="お名前は必須です。" value="<?php echo h($name); ?>">
          </div>
          <br>
          <div class="form-group">
            <label for="email">Email（必須）
              <span class="error-php"><?php if (isset($error['email'])) echo h($error['email']); ?></span>
            </label>
            <input type="email" class="required pattern form-control form" data-pattern="email" id="email" name="email" placeholder="Email アドレス" data-error-required="Email アドレスは必須です。" data-error-pattern="Email の形式が正しくないようですのでご確認ください" value="<?php echo h($email); ?>">
          </div>
          <br>
          <div class="form-group">
            <label for="email_check">Email（確認用 必須）
              <span class="error-php"><?php if (isset($error['email_check'])) echo h($error['email_check']); ?></span>
            </label>
            <input type="email" class="form-control equal-to required form" data-equal-to="email" data-error-equal-to="メールアドレスが異なります" id="email_check" name="email_check" placeholder="Email アドレス（確認用 必須）" value="<?php echo h($email_check); ?>">
          </div>
          <br>
          <div class="form-group">
            <label for="tel">お電話番号（半角英数字）
              <span class="error-php"><?php if (isset($error['tel'])) echo h($error['tel']); ?></span>
            </label>
            <input type="tel" class="pattern form-control form" data-pattern="tel" id="tel" name="tel" placeholder="お電話番号" data-error-pattern="電話番号の形式が正しくないようですのでご確認ください" value="<?php echo h($tel); ?>">
          </div>
          <br>
          <div class="form-group">
            <label for="subject">件名（必須）
              <span class="error-php"><?php if (isset($error['subject'])) echo h($error['subject']); ?></span>
            </label>
            <input type="text" class="required maxlength form-control form" data-maxlength="100" id="subject" name="subject" placeholder="件名" value="<?php echo h($subject); ?>">
          </div>
          <br>
          <div class="form-group">
            <label for="body">お問い合わせ内容（必須）
              <span class="error-php"><?php if (isset($error['body'])) echo h($error['body']); ?></span>
            </label>
            <textarea class="required maxlength showCount form-control form textarea" data-maxlength="1000" id="body" name="body" placeholder="お問い合わせ内容（1000文字まで）をお書きください" rows="3"><?php echo h($body); ?></textarea>
          </div>
          <br>
          <button name="submitted" type="submit" class="form-btn semibold">送信</button>
        </form>
      </div>
    </div>
  </div>

  <footer id="footer" class="inview_re fadeIn">
    <h6>My_Portfolio</h6>
    <a href="https://ingeniousmorocorn.com/portfolio/">Home</a>
    <div class="icon-flex">
      <a href="https://twitter.com/koya_coding22" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i>
      </a>
    </div>
    <p>&copy; Koya Morohoshi</p>
  </footer>


  <!--  検証用の JavaScript の読み込み（または script タグに検証用スクリプトを記述） -->
  <script src="formValidation.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="../js/jquery-ui-1.10.3.custom.min.js"></script>
  <script src="../js/fakeLoader.js"></script>
  <script src="../js/jquery.inview.min.js"></script>
  <script src="../js/main.js"></script>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


</body>

</html>