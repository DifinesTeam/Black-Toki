<?php
# -----------------------------------------------------
# ,------.  ,---. ,--.   ,--. 
# |  .--. ''.-.  \|   `.'   | 
# |  '--' | .-' .'|  |'.'|  | 
# |  | --' /   '-.|  |   |  | 
# `--'     '-----'`--'   `--'  ver.1.0.1
# -----------------------------------------------------
# PHP To Mail
# ・メールフォームをコードを書かず設定のみで構築できることを目的としたセットです。
# ・テンプレエンジン等は利用せず、バックエンドはPHPのみ利用しています。
# ・基本的にPHPバージョンが合えば動作しますが、環境に依存するためメール送信まで動作するか最後まで要検証。
# -----------------------------------------------------
# 最終更新日 2023/09/10
# -----------------------------------------------------

# -------------------------
# 初期設定
# -------------------------
if (version_compare(PHP_VERSION, '5.1.0', '>=')) { //PHP5.1.0以上の場合のみタイムゾーンを定義
    date_default_timezone_set('Asia/Tokyo'); //タイムゾーンの設定（日本以外の場合には適宜設定ください）
}

# ------------------------
# 初期設置チェック
# ※動作に必要なファイル/ディレクトリの存在チェック
# ※不備があればエラー画面を出して終了
# -------------------------
$setting_alert;
if (!is_dir('./app') ) $setting_alert .= '<div style="width: 100%;background-color: rgb(0 0 0 / 40%);color: #FFFFFF;padding: 10px;position: absolute;z-index: 9999;">※appディレクトリが存在しません。</div>';
if (!empty($setting_alert)) {
    echo $setting_alert;
    exit;
}




# 設定ファイル読込
require_once __DIR__ . "/app/function.php";     # 関数群ファイル
require_once __DIR__ . "/app/init.php";         # フォーム設定フィアル
require_once __DIR__ . "/app/article.php";      # 項目設定ファイル

# ファイル添付時の画像一次保存ディレクトリ
define("TEMP_DIR", __DIR__ . '/temp');

# -------------------------
# 関数実行、変数初期化
# -------------------------
$encode = "UTF-8";                                              //このファイルの文字コード定義（変更不可）
if (isset($_GET)) $_GET = sanitize($_GET);                      //NULLバイト除去//
if (isset($_POST)) $_POST = sanitize($_POST);                   //NULLバイト除去//
if (isset($_COOKIE)) $_COOKIE = sanitize($_COOKIE);             //NULLバイト除去//
if ($encode == 'SJIS') $_POST = sjisReplace($_POST, $encode);   //Shift-JISの場合に誤変換文字の置換実行

# ------------------------
# リファラチェック実行
# ------------------------
$funcRefererCheck = refererCheck();

# ------------------------
# 変数初期化
# ------------------------
$in              = array();  // 渡って来た値
$common          = array();  // 共通データ
$session_arr     = array();  // SESSIONデータ
$validate_result = array();  // バリデーションチェック結果
$replace         = array();  // 出力する値
$cmd             = '';       // 遷移コマンド 
$template        = '';       // 画面テンプレート

# ------------------------
# リクエストデータ取得 GET/POST
# ------------------------
$in = array_merge_recursive($_GET, $_POST);
$in = unMagicQuotes($in); # エスケープ処理

# ------------------------
# 遷移コマンド 
# ------------------------
if (!empty($in['cmd'])) $cmd = $in['cmd'];

# -------------------------
# セッションスタート
# getCsrfToken内でsessionにtokenを格納しているため必要。
# csrfIsValid内でsessionからtokenを取得しているため必要。
# [session.cookie_httponly] Javascriptからクッキーへアクセス出来ないように設定します。
# -------------------------
ini_set('session.cookie_httponly', 1);
session_start();

# -------------------------
# CSRF tokenチェック
# ※CSRF対策機能
# -------------------------

if (USE_TOKEN == 1 && ($cmd == 'confirm' || $cmd == 'comp')) {

    // csrfIsValid実行で、SESSIONに_tokunが生成される
    // 発行されて渡って来た_tokenの値とSESSIONに保存された値を比較し、合えばNull、違えば「新規token発行|IPアドレス」が返る
    $isvalid = csrfIsValid(false);
}

# -------------------------
# 項目アクション
# ※項目ごとに設定されているアクションを実行
# ※ex) action => 'z2h' 全角を半角に変換
# ※アクションはバリデーション前に実行される。
#   つまり、$inのデータに対して実行され、その結果が$_SESSIONに入る
#   - $inデータに対してAction実行
#   - $inデータに対してバリデーション処理
#   - $inデータを$_SESSIONに保存
# -------------------------
if ($cmd == 'confirm') {
    $in = ArticleAction($in);
}

# -------------------------
# バリデーションチェック
# -------------------------
if ($cmd == 'confirm') {

    $validate_result = array();

    # 基本バリデーション処理
    $default_validate = validationCheck($in);

    # =============================================
    # 追加バリデーション ここから
    # =============================================

    # 追加バリデーション処理
    $add_validate    = array();

    # 追加したいバリデーション処理が在ればここにコードを追加
    # if ( $in['name'] == 'あああ' ) $add_validate['ERROR']['name']['error_message'] = 'あああはNG';
    # if ( $in['name'] == 'aaa' ) $add_validate['ERROR']['name']['error_message'] = 'aaaはNG';

    # =============================================
    # /追加バリデーション ここまで
    # =============================================

    if (!empty($add_validate)) {
        $add_validate['error_flg'] = 1;
    }

    # 基本バリデーションと追加バリデーションの結果をマージ
    $mergedArrays = [];
    if (!empty($default_validate)) $mergedArrays[] = $default_validate;
    if (!empty($add_validate))     $mergedArrays[] = $add_validate;
    $validate_result = call_user_func_array('array_merge', $mergedArrays);

    # エラーバック
    if ($validate_result['error_flg'] == 1) {

        # 入力画面に戻る
        $cmd = '';
    }
}

# -------------------------
# 「戻る」処理
# -------------------------
$back_flg = '';

if ($cmd == 'comp' && !empty($in['_back'])) {

    # 入力画面に戻る
    # ※入力画面処理にするためにcmdを空にする
    $cmd = '';

    # 入力画面処理の際、sessionデータを$inにマージ
    $session_arr = $_SESSION[SES_NAME]['post'];
    $in = array_merge($in, $session_arr);

    # 戻る=入力画面処理
    # 入力画面に対して「戻る」で来たフラグを渡す
    $back_flg = 1;
}

# -------------------------
# 添付ファイル一時保存処理
# ※確認画面時、選択ファイルが渡ればtempディレクトリに一時保存
# -------------------------
FileTempUpload($articles, $validate_result);

# -------------------------
# 一時ファイルクリーンアップ（/tempディレクトリ配下）
# -------------------------
CleanTemp();

# -------------------------
# データ保持/セット
# ※主に$_SESSIONにデータをセットする
# ※画面テンプレートに渡す$replace配列にデータをセットする
# ※入力画面時には不要だがコードがバラけないように全画面フローにて実施
# -------------------------

# 入力値をSESSIONの[post]へ保存
$mergedArrays = [];
if (!empty($_SESSION[SES_NAME]['post'])) $mergedArrays[] = $_SESSION[SES_NAME]['post'];
if (!empty($in))                         $mergedArrays[] = $in;
// $mergedArrays 配列が空でない場合にのみ array_merge() を呼び出す
if (!empty($mergedArrays)) {
    $_SESSION[SES_NAME]['post'] = call_user_func_array('array_merge', $mergedArrays);
}


# 画面表示用データをSESSIONの[view]へ保存
# ex) 1=男性 33=岡山
$_SESSION[SES_NAME]['view'] = view_replace($_SESSION[SES_NAME]['post']);

# 配列を結合する
$mergedArrays = [];
if (!empty($replace))                     $mergedArrays[] = $replace;
if (!empty($_SESSION[SES_NAME]['view']))  $mergedArrays[] = $_SESSION[SES_NAME]['view'];
// $mergedArrays 配列が空でない場合にのみ array_merge() を呼び出す
if (!empty($mergedArrays)) {
    $replace = call_user_func_array('array_merge', $mergedArrays);
}

# -------------------------
# 画面フロー制御
# ※確認画面OFF時、確認画面へのコマンドの場合に完了画面処理へ
# -------------------------
if (CONF_DIS == 0 && $cmd == 'confirm') {
    $cmd = 'comp';
}

# 以下、画面表示処理  ===============================================================================

# -------------------------
# ヘッダー出力
# -------------------------
header("Content-Type:text/html;charset=utf-8");

# -------------------------
# 入力画面（cmd=''）
# -------------------------

if ($cmd == '') {

    # 初回時：先ずセッションクリア
    # ※「戻る」時はクリアしない
    if (empty($back_flg)) {
        $_SESSION[SES_NAME]['post'] = array();  // $_SESSION変数を空の配列で上書き
    }

    # トークン取得
    $token = getCsrfToken();

    // 発行された$blade->csrf_tokenの値をSESSIONに保存
    # $_SESSION[SES_NAME]['_token'] = $token;

    # 項目データ
    $articles = set_article($in);

    # テンプレートで以下の配列が利用可能
    # print_r($in);
    # print_r($session_arr);
    # print_r($validate_result);
    # print_r($articles);

    # 画面テンプレート
    $template = 'temp_01_index.php';
}
# ==================================================================

# -------------------------
# 確認画面（cmd='confirm'）
# -------------------------

elseif ($cmd == 'confirm') {

    # トークン取得
    $token = getCsrfToken();

    # セッション内の[view]一式をセット テンプレート側にて使用
    # ex) $data['name']
    $data = $_SESSION[SES_NAME]['view'];

    # 画面テンプレート
    $template = 'temp_02_confirm.php';
}
# ==================================================================

# -------------------------
# 完了画面（cmd='comp'）
# -------------------------

elseif ($cmd == 'comp') {

    # SESSIONのpost内のデータに対して最終バリデーションチェック
    $validate_result = validationCheck($_SESSION[SES_NAME]['post']);

    # 最終チェックエラー画面
    # ※上記最終バリデーションチェックの結果、問題があればESSIONを消してエラー画面を出す処理
    if ($validate_result['error_flg'] == 1 && DEBUG_MODE == 0) {

        # セッション削除
        if (isset($_SESSION[SES_NAME])) unset($_SESSION[SES_NAME]); //トークン破棄
        if (isset($_POST['_token']))    unset($_POST);              //トークン破棄

        # エラーメッセージ
        $message = 'ページ遷移が不正です(01)';

        if (ERROR_DIS == 1) {
            # エラー画面テンプレート表示

            # 出力HTML
            include 'temp_error.php';
            exit();
        } else {
            exit($replace['message']);
        }
    }

    # 自動返信メール送信処理
    $send = send_email($encode);

    # セッション削除

    if (DEBUG_MODE == 0) {
        echo '';

        # トークン破棄
        if (isset($_SESSION[SES_NAME])) unset($_SESSION[SES_NAME]); //トークン破棄
        if (isset($_POST['_token']))    unset($_POST);              //トークン破棄
    }

    # 完了画面表示
    if (JUM_PPAGE == 1) {

        # 別URLへ遷移
        header("Location: " . THANKS_PPAGE);
    } else {

        # 画面テンプレート
        # 通常完了画面
        $template = 'temp_03_comp.php';
    }
}

# -------------------------
# 画面出力
# -------------------------

ob_start(); // バッファリングを開始

include $template;

$output = ob_get_clean(); // バッファの内容を取得して変数に代入し、バッファリングを終了

# -------------------------
# デバッグモード アラート表示
# ※出力HTML内のbodyタグ直下にデバッグモード用タグを置換して表示させ誤ってデバッグモードのままの運用を避ける。
# ※同階層にdebug_session.txt.phpファイルが生成され、$_SESSIONの中身を書きだす
# -------------------------
if (DEBUG_MODE == 1) {

    # デバッグモード表示
    $debug_mode_text = '<div style="width: 100%;background-color: rgb(0 0 0 / 40%);color: #FFFFFF;padding: 10px;position: absolute;z-index: 9999;"><b>デバッグモード ON</b><br>※送信完了時点でSESSIONを削除しません。完了画面F5で何度でもメールが飛びます。<br>※SESSIONを削除しません。</div>';
    $output = preg_replace('/(<body[^>]*>)/', '$1' . PHP_EOL . $debug_mode_text, $output);

    # ファイルに内容を出力
    $mergedData = array_merge($_SESSION, $_POST);

    // 配列をファイルに書き込む
    file_put_contents('debug_session.txt', (var_export($mergedData, true)));
}

echo $output; // 表示
1;
