<?php
# ------------------------
# フォーム全体の設定情報
# ------------------------

//----------------------------------------------------------------------
//  必須設定　必ず設定してください
//----------------------------------------------------------------------

// SESSION名
// 1サイト内に複数設置する場合など、それぞれのフォームで別の値を設定する。
// SES_NAMEが被ると、同じ領域に保存して上書きになるので注意。
define('SES_NAME', 'p2m');

//サイトのトップページのURL　※デフォルトでは送信完了後に「トップページへ戻る」ボタンが表示されますので
define('SITE_TOP', 'https://lv09.net/sample/crypto/blacktoki/');

//管理者のメールアドレス ※メールを受け取るメールアドレス(複数指定する場合は「,」で区切ってください 例 $to = "aa@aa.aa,bb@bb.bb";)
define('TO', 'info@blacktoki.com');

//送信元メールアドレス（管理者宛て、及びユーザー宛メールの送信元メールアドレスです）
//必ず実在するメールアドレスでかつ出来る限り設置先サイトのドメインと同じドメインのメールアドレスとすることを強く推奨します
//管理者宛てメールの返信先（reply）はユーザーのメールアドレスになります。
define('FROM', 'info@blacktoki.com');

// 一時ファイルの寿命（/temp配下のファイル）
// 設定時間以上経過しているファイルはクリーンアップ
// 1時間…3600
define('TMP_FILE_LIMIT', 3600);

// 確認画面の表示(する=1, しない=0 ※確認画面表示せず即送信)
define('CONF_DIS', 1);

# エラー画面の表示(する=1, しない=0)
# する=1   : temp_error.blade.phpのエラー画面テンプレートを使って表示
# しない=0 : 白画面にエラー文言だけ表示
define('ERROR_DIS', 0);

// 送信完了後に自動的に指定のページ(サンクスページなど)に移動する(する=1, しない=0)
// CV率を解析したい場合などはサンクスページを別途用意し、URLをこの下の項目で指定してください。
// 0にすると、デフォルトの送信完了画面が表示されます。
define('JUM_PPAGE', 0);

// 送信完了後に表示するページURL（上記で1を設定した場合のみ）※http(https)から始まるURLで指定ください。（相対パスでも基本的には問題ないです）
define('THANKS_PPAGE', 'http://xxx.xxxxxxxxx/thanks.html');

//----------------------------------------------------------------------
//  デバッグモード
//----------------------------------------------------------------------

# デバッグモード
# 0=OFF 1=ON
define('DEBUG_MODE', 0);

# デバッグモード時の管理者アドレス
define('DEBUG_TO', 'info@blacktoki.com');

# デバッグモード時のBCC
define('DEBUG_BCC_MAIL', '');

//----------------------------------------------------------------------
//  セキュリティ、スパム防止のための設定
//----------------------------------------------------------------------

//スパム防止のためのリファラチェック（フォーム側とこのファイルが同一ドメインであるかどうかのチェック）(する=1, しない=0)
//※有効にするにはこのファイルとフォームのページが同一ドメイン内にある必要があります
define('REFERER_CHECK', 0);

//リファラチェックを「する」場合のドメイン ※設置するサイトのドメインを指定して下さい。
//もしこの設定が間違っている場合は送信テストですぐに気付けます。
define('REFERER_CHECK_DOMAIN', 'lv8.jp');

# リファラチェック時エラー画面メッセージ
define('REFERER_CHECK_MESSAGE', '<p align="center">リファラチェックエラー。フォームページのドメインとこのファイルのドメインが一致しません</p>');

/*セッションによるワンタイムトークン（CSRF対策、及びスパム防止）(する=1, しない=0)
※ただし、この機能を使う場合は↓の送信確認画面の表示が必須です。（デフォルトではON（1）になっています）
※【重要】ガラケーは機種によってはクッキーが使えないためガラケーの利用も想定してる場合は「0」（OFF）にして下さい（PC、スマホは問題ないです）*/
define('USE_TOKEN', 1);

//----------------------------------------------------------------------
//  管理者宛 自動返信メール設定(START)
//----------------------------------------------------------------------

# Bccで送るメールアドレス(複数指定する場合は「,」で区切ってください 例 $BccMail = "aa@aa.aa,bb@bb.bb";)
define('BCC_MAIL', '');

# 管理者宛に送信されるメールのタイトル（件名）
define('SUBJECT', '[Black Toki]We have received an inquiry.');

//----------------------------------------------------------------------
//  ユーザ宛 自動返信メール設定(START)
//----------------------------------------------------------------------

# 差出人に送信内容確認メール（自動返信メール）を送る(送る=1, 送らない=0)
# 送る場合は、フォーム側のメール入力欄のname属性の値が上記「$Email」で指定した値と同じである必要があります
define('REMAIL_FLG', 1);

# フォームのメールアドレス入力箇所のname属性の値（name="○○"　の○○部分）
# 自動返信メールを、name="email"項目に入力されたメアド宛に送りたいのであれば、設定は'email'となります
define('EMAIL', 'email');

# 自動返信メールの送信者欄に表示される名前
# 差出人: WWS<info@ww-system.com>   といった感じにしたいのであれば設定は'WWS'
# 空の場合は只のメアド（FROMの値）になる。
define('REFORM_NAME', '[Black Toki]info@blacktoki.com');

# 差出人に送信確認メールを送る場合のメールのタイトル（上記で1を設定した場合のみ）
define('RE_SUBJECT', '[Black Toki]Thank you for your Contact us.');

//----------------------------------------------------------------------
//  デフォルトバリデーション
//  ※articleのvalidation設定に関わらず、絶対に行うバリデーション設定
//----------------------------------------------------------------------

$default_validate = array(
  'email'     => ['tag'],  # テキストボックス
  'textbox'     => ['tag'],  # テキストボックス
  'textarea'    => ['tag'],  # テキストエリア
);


//----------------------------------------------------------------------
//  デフォルトエラー文言
//  ※各項目のエラー文言を変更する場合は、article.phpにて設定
//----------------------------------------------------------------------

# 必須項目チェック
define('DEFAULT_ERR_REQUIRE',    'Required fields.');

# 半角のみチェック
define('DEFAULT_ERR_HANKAKU',    '半角英数字で入力してください。');

# 全角のみチェック
define('DEFAULT_ERR_ZENKAKU',    '全角で入力してください。');

# ひらがなのみチェック
define('DEFAULT_ERR_HIRAGANA',   'ひらがなで入力してください。');

# カタカナのみチェック
define('DEFAULT_ERR_KATAKANA',   'カタカナで入力してください。');

# タグ入力チェック
define('DEFAULT_ERR_TAG',        'html tag is entered.');

# 文字数チェック
define('DEFAULT_ERR_LENGTH',     '文字数を確認してください。');

// メールアドレス形式チェック
define('DEFAULT_ERR_EMAIL',      'Please enter in email address format.');

// 電話番号形式チェック
define('DEFAULT_ERR_PHONE',      '電話番号形式で入力してください。');

// ファイル拡張子チェック
define('DEFAULT_ERR_EXTENSION',  'ファイルの拡張子をご確認ください。');

// ファイル容量チェック
define('DEFAULT_ERR_BYTE_SIZE',  'ファイルの容量をご確認ください。');

//----------------------------------------------------------------------
//  その他設定
//----------------------------------------------------------------------

# -fオプションによるエンベロープFrom（Return-Path）の設定(する=1, しない=0)　
# ※宛先不明（間違いなどで存在しないアドレス）の場合に 管理者宛に「Mail Delivery System」から「Undelivered Mail Returned to Sender」というメールが届きます。
# サーバーによっては稀にこの設定が必須の場合もあります。
# 設置サーバーでPHPがセーフモードで動作している場合は使用できませんので送信時にエラーが出たりメールが届かない場合は「0」（OFF）として下さい。
define('USE_ENVELOPE', 0);

//----------------------------------------------------------------------
//  基本定数設定
//----------------------------------------------------------------------

//機種依存文字の変換
/*たとえば㈱（かっこ株）や①（丸1）、その他特殊な記号や特殊な漢字などは変換できずに「？」と表示されます。それを回避するための機能です。
確認画面表示時に置換処理されます。「変換前の文字」が「変換後の文字」に変換され、送信メール内でも変換された状態で送信されます。（たとえば「㈱」の場合、「（株）」に変換されます） 
必要に応じて自由に追加して下さい。ただし、変換前の文字と変換後の文字の順番と数は必ず合わせる必要がありますのでご注意下さい。*/

//変換前の文字
$replaceStr['before'] = array('①', '②', '③', '④', '⑤', '⑥', '⑦', '⑧', '⑨', '⑩', '№', '㈲', '㈱', '髙');
//変換後の文字
$replaceStr['after'] = array('(1)', '(2)', '(3)', '(4)', '(5)', '(6)', '(7)', '(8)', '(9)', '(10)', 'No.', '（有）', '（株）', '高');

# 月
$month_arr = array_map(function ($value) {
   # return $value;                                       # 0詰無し
   return str_pad($value, 2, '0', STR_PAD_LEFT);      # 0詰有り
}, range(1, 12));

# 日
$day_arr = array_map(function ($value) {
   # return $value;                                       # 0詰無し
   return str_pad($value, 2, '0', STR_PAD_LEFT);      # 0詰有り
}, range(1, 31));

# 時
$hour_arr = array_map(function ($value) {
   # return $value;                                       # 0詰無し
   return str_pad($value, 2, '0', STR_PAD_LEFT);      # 0詰有り
}, range(0, 23));

# 分
$min_arr = array_map(function ($value) {
   # return $value;                                       # 0詰無し
   return str_pad($value, 2, '0', STR_PAD_LEFT);      # 0詰有り
}, range(0, 59));

# 秒
$sec_arr = array_map(function ($value) {
   # return $value;                                       # 0詰無し
   return str_pad($value, 2, '0', STR_PAD_LEFT);      # 0詰有り
}, range(0, 59));

# 都道府県
$pref_arr = [
    '1'  => '北海道',
    '2'  => '青森県',
    '3'  => '岩手県',
    '4'  => '宮城県',
    '5'  => '秋田県',
    '6'  => '山形県',
    '7'  => '福島県',
    '8'  => '茨城県',
    '9'  => '栃木県',
    '10' => '群馬県',
    '11' => '埼玉県',
    '12' => '千葉県',
    '13' => '東京都',
    '14' => '神奈川県',
    '15' => '新潟県',
    '16' => '富山県',
    '17' => '石川県',
    '18' => '福井県',
    '19' => '山梨県',
    '20' => '長野県',
    '21' => '岐阜県',
    '22' => '静岡県',
    '23' => '愛知県',
    '24' => '三重県',
    '25' => '滋賀県',
    '26' => '京都府',
    '27' => '大阪府',
    '28' => '兵庫県',
    '29' => '奈良県',
    '30' => '和歌山県',
    '31' => '鳥取県',
    '32' => '島根県',
    '33' => '岡山県',
    '34' => '広島県',
    '35' => '山口県',
    '36' => '徳島県',
    '37' => '香川県',
    '38' => '愛媛県',
    '39' => '高知県',
    '40' => '福岡県',
    '41' => '佐賀県',
    '42' => '長崎県',
    '43' => '熊本県',
    '44' => '大分県',
    '45' => '宮崎県',
    '46' => '鹿児島県',
    '47' => '沖縄県',
];