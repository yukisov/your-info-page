<?php
// set time zone
date_default_timezone_set("Asia/Tokyo");

// set default encoding
if (extension_loaded('mbstring')) {
  mb_internal_encoding('UTF-8');
}

//----------
// 関数定義
//----------

// HTMLエスケープ
function e($html) {
  return htmlspecialchars($html, ENT_QUOTES);
}

// nonce生成
function createNonce() {
  return md5(uniqid(rand(), true));
}

// cookieの中身を表示用の文字列に変換して返す
function showCookies($hash) {
  $html = '';
  foreach($hash as $key => $val) {
    if ($html != '') $html .= ', ';
    $html .= $key . '=' . $val;
  }
  return $html;
}

/**
 * User-Agent文字列からOSを判定してOS名を返す
 * ref. http://stackoverflow.com/questions/18070154/get-operating-system-info-with-php
 */
function getOS($user_agent) { 
  $os_platform = "Unknown OS Platform";
  $os_array = array(
    '/windows nt 10/i'     =>  'Windows 10',
    '/windows nt 6.3/i'     =>  'Windows 8.1',
    '/windows nt 6.2/i'     =>  'Windows 8',
    '/windows nt 6.1/i'     =>  'Windows 7',
    '/windows nt 6.0/i'     =>  'Windows Vista',
    '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
    '/windows nt 5.1/i'     =>  'Windows XP',
    '/windows xp/i'         =>  'Windows XP',
    '/windows nt 5.0/i'     =>  'Windows 2000',
    '/windows me/i'         =>  'Windows ME',
    '/win98/i'              =>  'Windows 98',
    '/win95/i'              =>  'Windows 95',
    '/win16/i'              =>  'Windows 3.11',
    '/macintosh|mac os x/i' =>  'Mac OS X',
    '/mac_powerpc/i'        =>  'Mac OS 9',
    '/linux/i'              =>  'Linux',
    '/ubuntu/i'             =>  'Ubuntu',
    '/iphone/i'             =>  'iPhone',
    '/ipod/i'               =>  'iPod',
    '/ipad/i'               =>  'iPad',
    '/android/i'            =>  'Android',
    '/blackberry/i'         =>  'BlackBerry',
    '/webos/i'              =>  'Mobile'
  );
  foreach ($os_array as $regex => $value) { 
    if (preg_match($regex, $user_agent)) {
      $os_platform    =   $value;
    }
  }   
  return $os_platform;
}
/**
 * User-Agent文字列からブラウザを判定してブラウザ名を返す
 * ref. http://stackoverflow.com/questions/18070154/get-operating-system-info-with-php
 */
function getBrowser($user_agent) {
  $browser = "Unknown Browser";
  $browser_array  =   array(
    '/msie/i'       =>  'Internet Explorer',
    '/firefox/i'    =>  'Firefox',
    '/safari/i'     =>  'Safari',
    '/chrome/i'     =>  'Chrome',
    '/edge/i'       =>  'Edge',
    '/opera/i'      =>  'Opera',
    '/netscape/i'   =>  'Netscape',
    '/maxthon/i'    =>  'Maxthon',
    '/konqueror/i'  =>  'Konqueror',
    '/mobile/i'     =>  'Handheld Browser'
  );
  foreach ($browser_array as $regex => $value) { 
    if (preg_match($regex, $user_agent)) {
      $browser    =   $value;
    }
  }
  return $browser;
}


//---------------------------
// HTTP Headers for Security
//---------------------------

// CSP
// nonceの値はアクセス毎に違う値を生成するのがよい。
$nonce1 = createNonce();
$nonce2 = createNonce();
header("Content-Security-Policy: default-src 'self';"
 . " script-src 'nonce-${nonce1}' code.jquery.com maxcdn.bootstrapcdn.com use.fontawesome.com;"
 . " style-src 'nonce-${nonce2}' maxcdn.bootstrapcdn.com use.fontawesome.com;"
 . " font-src maxcdn.bootstrapcdn.com use.fontawesome.com;");
// XSS攻撃を検知させる（検知したら実行させない）。
header("X-XSS-Protection: 1; mode=block");
// IEにコンテンツの内容を解析させない（ファイルの内容からファイルの種類を決定させない）。
header("X-Content-Type-Options: nosniff");
// このページを iframe に埋め込ませない
header("X-Frame-Options: DENY");

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>あなたのブラウザが送信した情報</title>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <style type="text/css" nonce='<?php echo e($nonce2); ?>'>
body {
  margin: 30px;
}

#resolution {
  margin-right: 10px;
}

.kakunin tbody tr th {
  min-width: 200px;
  background-color: #d9edf7;
}
.kakunin tbody tr th,
.kakunin tbody tr td
 {
  text-align: left;
  vertical-align: middle;
}
.kakunin-req tbody tr th{
  background-color: #dff0d8;
}
.em1 {
  font-size:200%;
  color: blue;
}    
.em2 {
  font-size:150%;
  color: red;
}    

.kakunin-footer {
    margin-top: 50px;
    padding: 40px 0;
    color: #999;
    text-align: center;
    background-color: #f9f9f9;
    border-top: 1px solid #e5e5e5;
}
  </style>
</head>
<body>

  <h1>あなたのブラウザが送信した情報</h1>

  <p>あなたのブラウザがこのWebサーバーに送ってきた情報を表示しています。</p>
  <p class="text-danger">* このWebページはあくまで実験的なものです。定期的にこのようなウェブページが必要な方は、<a href="http://www.ugtop.com/spill.shtml">確認くん</a> などをご利用下さい。</p>

  <div class="table-responsive">
  <table class="kakunin table table-striped table-bordered table-hover">
    <tboby>
    <tr>
      <th>あなたからのリクエストを取得した時間</th>
      <td><?php echo e(date("Y年m月d日 H時i分s秒", $_SERVER['REQUEST_TIME'])); ?></td>
    </tr>
    <tr>
      <th>このWebサーバーのホスト名</th>
      <td><?php echo e($_SERVER['SERVER_NAME']); ?></td>
    </tr>
    <tr>
      <th>あなた側のコンピュータのIPアドレス</th>
      <td>
        <span class="em1"><?php echo e($_SERVER['REMOTE_ADDR']); ?></span><br>
      <p>(通常、あなたのコンピュータがデフォルトゲートウェイに設定しているデバイスのIPアドレスになります(ルータであることが多いです))。</p>
      </td>
    </tr>
    <tr>
      <th>あなた側のコンピュータのホスト名<br></th>
      <td>
        <span class="em2"><?php echo e(gethostbyaddr($_SERVER['REMOTE_ADDR'])); ?></span><br>
        <p>(上のIPアドレスに対応するホスト名です)</p>
      </td>
    </tr>
    </tboby>
  </table>
  </div>

  <h2>HTTPリクエストメッセージ <small>主な項目</small></h2>

  <div class="table-responsive">
  <table class="kakunin kakunin-req table table-striped table-bordered table-hover">
    <tboby>
    <tr>
      <th class="success"><a href="https://tools.ietf.org/html/rfc7231#section-4">Method</a></th>
      <td><?php echo e($_SERVER['REQUEST_METHOD']); ?><br>
        (メソッド名)
      </td>
    </tr>
    <tr>
      <th><a href="https://tools.ietf.org/html/rfc7230#section-3.1.1">Request-URI</a></th>
      <td>
        <?php echo e($_SERVER['REQUEST_URI']); ?><br>
        (要求されたURI)
      </td>
    </tr>
    <tr>
      <th><a href="https://tools.ietf.org/html/rfc7230#section-3.1.1">HTTP-Version</a></th>
      <td>
        <?php echo e($_SERVER['SERVER_PROTOCOL']); ?><br>
        (ページがリクエストされた際のHTTPバージョン)
      </td>
    </tr>
    <tr>
      <th><a href="https://tools.ietf.org/html/rfc7230#section-5.4">Host</a></th>
      <td>
        <?php echo e($_SERVER['HTTP_HOST']); ?><br>
        (要求されたホスト名)
      </td>
    </tr>
    <tr>
      <th><a href="https://tools.ietf.org/html/rfc7231#section-5.5.3">User-Agent</a></th>
      <td>
        <?php echo e($_SERVER['HTTP_USER_AGENT']); ?><br>
        (あなたのブラウザ情報)
      </td>
    </tr>
    <tr>
      <th>あなたのOS<br>(User-Agentから取得)</th>
      <td>
        <span><?php echo e(getOS($_SERVER['HTTP_USER_AGENT'])); ?></span><br>
        解像度： <span id="resolution"></span>
        <span>(この値はクライアント側で取得している)</span>
      </td>
    </tr>
    <tr>
      <th><a href="https://tools.ietf.org/html/rfc7231#section-5.3.5">Accept-Language</a></th>
      <td>
        <?php echo e($_SERVER['HTTP_ACCEPT_LANGUAGE']); ?><br>
        (ブラウザの希望する言語)
      </td>
    </tr>
    <tr>
    <tr>
      <th><a href="https://tools.ietf.org/html/rfc7231#section-5.5.2">Referer</a></th>
      <td>
        <?php echo e($_SERVER['HTTP_REFERER']); ?><br>
        (どこのURLからこのページに来たのか)
      </td>
    </tr>
    <tr>
      <th><a href="https://tools.ietf.org/html/rfc7231#section-5.3.2">Accept</a></th>
      <td>
        <?php echo e($_SERVER['HTTP_ACCEPT']); ?><br>
        (ブラウザが希望するメディアタイプ)
      </td>
    </tr>
    <tr>
      <th><a href="https://tools.ietf.org/html/rfc7231#section-5.3.4">Accept-Encoding</a></th>
      <td>
        <?php echo e($_SERVER['HTTP_ACCEPT_ENCODING']); ?><br>
        (ブラウザが受け入れるエンコーディング)
      </td>
    </tr>
    <tr>
      <th><a href="https://tools.ietf.org/html/rfc6265#section-4.2">Cookie<a></th>
      <td><?php echo e(showCookies($_COOKIE)); ?></td>
    </tr>
    </tboby>
  </table>
  </div>

  <footer class="kakunin-footer">
    <p><i class="fa fa-copyright"></i> 2016 laboradian.com</p>
  </footer>

  <!-- Latest compiled and minified JavaScript -->
  <script type="text/javascript"
    src="https://code.jquery.com/jquery-3.1.1.slim.js"
    integrity="sha256-5i/mQ300M779N2OVDrl16lbohwXNUdzL/R2aVUXyXWA="
    crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <script src="https://use.fontawesome.com/c215ece6c6.js"></script>
  <script type="text/javascript" nonce='<?php echo e($nonce1); ?>'>
$(function(){
  $('#resolution').text(screen.width + ' x ' + screen.height + ' pixel');
});
  </script>
</body>
</html>
