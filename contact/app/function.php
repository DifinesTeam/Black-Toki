<?php
# ============================
# 関数定義
# ============================


# ------------------------
# ■トークン処理
# $token = getCsrfToken();
# ------------------------
function getCsrfToken($fullToken = false, $tokenId = '_token')
{
    # $_SESSION[SES_NAME][$tokenId] 
    if ($_SESSION[SES_NAME][$tokenId] == '') {

        $_SESSION[SES_NAME][$tokenId] = regenerateToken($tokenId);
    }

    # fullToken
    if ($fullToken) {

        return $_SESSION[SES_NAME][$tokenId] . '|' . ipClient();
    }

    return $_SESSION[SES_NAME][$tokenId];
}

# ------------------------
# ■トークンID発行
# regenerateToken('tokenId');
# ------------------------
function regenerateToken($tokenId = '_token')
{
    try {
        $new_token = \bin2hex(\random_bytes(30));
    } catch (Exception $e) {
        $new_token = '123456789012345678901234567890'; // unable to generates a random token.
    }
    # @$_SESSION[SES_NAME][$tokenId] = $set_token . '|' . $this->ipClient();
    $new_token = $new_token . '|' . ipClient();
    return $new_token;
}

# ------------------------
# アクセスIPアドレス取得
# ------------------------
function ipClient()
{
    if (
        isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        && \preg_match('/^(d{1,3}).(d{1,3}).(d{1,3}).(d{1,3})$/', $_SERVER['HTTP_X_FORWARDED_FOR'])
    ) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'] ?? '';
}

# ------------------------
# ■トークンID発行
# ※渡って来た$_POST['_token']の値とSESSIONに保存された_token値を比較し、合えばNull、違えば「新規token発行|IPアドレス」が返る
# ------------------------
function csrfIsValid($alwaysRegenerate = false, $tokenId = '_token')
{
    $csrfIsValid = Null;

    if (@$_SERVER['REQUEST_METHOD'] === 'POST' && $alwaysRegenerate === false) {
        if ($_SESSION[SES_NAME][$tokenId] === $_POST[$tokenId]) {

            $csrfIsValid = $_SESSION[SES_NAME][$tokenId];
        }
    }
    if ($_SESSION[SES_NAME][$tokenId] == '' || $alwaysRegenerate) {
        // if not token then we generate a new one
        return regenerateToken($tokenId);
    }

    $_SESSION[SES_NAME]['_token'] = $csrfIsValid;

    return $csrfIsValid;
}

# ------------------------
# ■項目アクション実行
# ------------------------
function ArticleAction($arr = null)
{
    if (!$arr) return false;

    global $articles;   # 項目設定データ

    $return_arr = $arr;

    # 項目配列にvalue値をセット
    foreach ($articles as $key => $data) {

        # input_action設定が無ければスキップ
        if (!isset($data['input_action'])) {
            continue;
        }

        # 対象データ
        $set_value = $arr[$key];

        # input_actionセット
        $article_actions = $data['input_action'];

        # 項目配列にvalue値をセット
        foreach ($article_actions as $action) {

            # 項目別Action処理
            switch ($action) {

                case 'z2h': # 全角→半角変換処理 ---------------------------------

                    $return_arr[$key] = z2h($set_value);
                    break;

                default:
            }
        }
    }

    return $return_arr;
}

# ------------------------
# ■テンプレート表示用に置換
# ※1の値を「男性」にしたり、33のあたりを「岡山」表記にしたり変換
# ※$_SESSION[SES_NAME]['view']に元入力データとは別に保存
# ※確認画面と、メール文章上に使用
# ------------------------
function view_replace($arr = null)
{
    if (!$arr) return false;

    global $encode;     # エンコード
    global $articles;   # 項目設定データ
    global $replaceStr; # 置換文字データ

    $view_arr = array();

    # 項目配列にvalue値をセット
    foreach ($articles as $key => $data) {

        # 入力値が無ければスキップ
        if (empty($arr[$key])) {
            continue;
        }

        # 対象データ
        $set_value = $arr[$key];

        # 項目タイプ別処理
        # textbox, checkbox, radio, select,textarea
        switch ($data['type']) {

            case 'radio': # ラジオボックス ---------------------------------

                $view_arr[$key] = $data['choise'][$set_value];
                break;

            case 'select': # プルダウン ---------------------------------

                $view_arr[$key] = $data['choise'][$set_value];
                break;

            case 'checkbox': # チェックボックス ---------------------------------

                $check_arr = array();

                foreach ($arr[$key] as $chk_key => $chk_value) {
                    array_push($check_arr, $data['choise'][$chk_value]);
                }

                # 連結する文字
                $join_str = '';
                if (empty($data['join'])) {
                    $join_str = ',';
                } else {
                    $join_str = $data['join'];
                }

                # 連結文字列セット
                $view_arr[$key] = join($join_str, $check_arr);
                break;

            case 'file': # ファイル ---------------------------------

                # file項目は$_FILESの情報をそのままセット
                if (isset($_SESSION[SES_NAME]['post'][$key])) $view_arr[$key] = $_SESSION[SES_NAME]['post'][$key];

                break;

            case 'textarea': # テキストエリア ---------------------------------

                # \r\nを\nに変換
                # 確認画面ではテンプレートにて {!! nl2br(htmlspecialchars( $comment )) !!} で表示でき
                # メール文章ではテンプレートで {{$comment}} だけでOK
                $view_arr[$key] = str_replace(["\r\n", "\r"], "\n", $set_value);

                break;

            default: # デフォルト (textbox, textarea) ---------------------------------

                # HTMLタグとして解釈される可能性のある特殊文字を変換
                $set_value = htmlspecialchars($set_value, ENT_QUOTES, $encode);

                # 機種依存文字の置換処理
                $set_value = str_replace($replaceStr['before'], $replaceStr['after'], $set_value);

                $view_arr[$key] = $set_value;
        }
    }

    return $view_arr;
}

# ------------------------
# メールアドレス形式チェック
# ------------------------
function checkMail($str)
{
    $mailaddress_array = explode('@', $str);
    if (preg_match("/^[\.!#%&\-_0-9a-zA-Z\?\/\+]+\@[!#%&\-_0-9a-zA-Z]+(\.[!#%&\-_0-9a-zA-Z]+)+$/", "$str") && count($mailaddress_array) == 2) {
        return true;
    } else {
        return false;
    }
}

# ------------------------
# 半角のみチェック
# ------------------------
function chkHankakuOnly($str)
{
    if (empty($str)) return false;
    return preg_match('/^[a-zA-Z0-9]+$/', $str) === 1;
}

# ------------------------
# 全角のみチェック
# ------------------------
function chkZenkakuOnly($str)
{
    if (empty($str)) return false;


    // ひらがな、カタカナ、漢字を含む文字列が全て全角かどうかをチェックする
    $pattern = '/^[\p{Hiragana}\p{Katakana}\p{Han}]+$/u';
    return preg_match($pattern, $str) === 1;
}

# ------------------------
# ひらがなのみチェック
# ------------------------
function chkHiraganaOnly($str)
{
    if (empty($str)) return false;
    return preg_match('/^\p{Hiragana}+$/u', $str) === 1;
}

# ------------------------
# カタカナのみチェック
# ------------------------
function chkKatakanaOnly($str)
{
    if (empty($str)) return false;
    return preg_match('/^\p{Katakana}+$/u', $str) === 1;
}

# ------------------------
# 電話番号形式チェック
# ※ハイフンはあり/無し両方に対応
# ------------------------
function checkPhoneNumber($str)
{

    // 数字とハイフン以外の文字を除去
    $phoneNumber = preg_replace('/[^0-9\-]/', '', $str);

    // 電話番号の長さをチェック
    if (strlen($phoneNumber) < 10 || strlen($phoneNumber) > 14) {
        return false;
    }

    // 電話番号の形式をチェック
    if (!preg_match('/^(\+?\d{1,4}[\-\s]?)?(\d{2,4}[\-\s]?)?(\d{3,4}[\-\s]?\d{3,4})$/', $phoneNumber)) {
        return false;
    } else {
        return true;
    }
}

# ------------------------
# アップロードされたファイル名の拡張子が許可されているか確認する関数
# ------------------------
function checkExt($arr, $filename)
{
    # global $cfg;
    $ext = strtolower(getExt($filename));
    return in_array($ext, $arr);
}
# ------------------------
# ファイル名から拡張子を取得する関数
# ------------------------
function getExt($filename)
{
    $info = pathinfo($filename, PATHINFO_EXTENSION);
    return $info;
}

# ------------------------
# NULLバイト除去
# ------------------------
function sanitize($arr)
{
    if (is_array($arr)) {
        return array_map('sanitize', $arr);
    }
    return str_replace("\0", "", $arr);
}
//Shift-JISの場合に誤変換文字の置換関数
function sjisReplace($arr, $encode)
{
    foreach ($arr as $key => $val) {
        $key = str_replace('＼', 'ー', $key);
        $resArray[$key] = $val;
    }
    return $resArray;
}

# --------------------------------------------------
# 全角→半角変換
# --------------------------------------------------
function z2h($str)
{
    global $encode;

    $str = mb_convert_kana($str, 'a', $encode);

    return $str;
}

//配列連結の処理
function connect2val($arr)
{
    $out = '';
    foreach ($arr as $key => $val) {
        if ($key === 0 || $val == '') { //配列が未記入（0）、または内容が空のの場合には連結文字を付加しない（型まで調べる必要あり）
            $key = '';
        } elseif (strpos($key, "円") !== false && $val != '' && preg_match("/^[0-9]+$/", $val)) {
            $val = number_format($val); //金額の場合には3桁ごとにカンマを追加
        }
        $out .= $val . $key;
    }
    return $out;
}


# ------------------------
# リファラチェック
# ------------------------
function refererCheck()
{
    if (REFERER_CHECK == 1 && !empty(REFERER_CHECK_DOMAIN)) {
        if (strpos($_SERVER['HTTP_REFERER'], REFERER_CHECK_DOMAIN) === false) {
            return exit(REFERER_CHECK_MESSAGE);
        }
    }
}

# --------------------------------------------------
# マジッククォート解除
# --------------------------------------------------
function unMagicQuotes($p_arr = null)
{
    if (!$p_arr) return false;

    foreach ($p_arr as $k => $v) {
        if (is_array($v)) {
            $p_arr[$k] = unMagicQuotes($v);
        } else {
            $p_arr[$k] = stripslashes($v);
        }
    }

    return $p_arr;
}

# --------------------------------------------------
# 配列をkey=valueの状態に変換
# ex) [1,2,3] → [ 1=>1, 2=>2, 3=>3 ]
# --------------------------------------------------
function setKeyValue($arr = null)
{
    if (!$arr) return false;

    $values = array_map(function ($key) {
        return $key;
    }, $arr);
    $array = array_combine($arr, $values);

    return $array;
}

# --------------------------------------------------
# 項目情報セット
# ※入力画面で各項目の情報を配列にセット
# ループで回して表示させるために使用
# ※「初期入力画面」、「エラーバックで戻った時」、「確認画面から戻るで戻ったとき」
# の3ケースで利用
# --------------------------------------------------
function set_article($in = null)
{
    global $articles;

    $arr = array();

    # 項目設定配列をセット
    $arr = $articles;

    # 項目配列にvalue値をセット
    foreach ($arr as $key => $data) {

        # 入力値
        $set_val = '';
        $set_val_arr = array();

        # 項目タイプ別処理
        # textbox, checkbox, radio, select,textarea
        switch ($data['type']) {

            case 'textbox': # テキストボックス ---------------------------------

                if (!empty($in[$key])) {
                    $set_val = $in[$key];
                }

                if (empty($set_val)) {

                    # デフォルト値をセット
                    if (!empty($arr[$key]['value'])) $arr[$key]['value'] = $data['default'];
                } else {

                    # 入力値($in)の値をセット
                    $arr[$key]['value'] = $set_val;
                }
                break;

            case 'radio': # ラジオボックス ---------------------------------

                if (!empty($in[$key])) {
                    $set_val = $in[$key];
                }

                if (empty($set_val)) {

                    # デフォルト値をセット
                    $arr[$key]['value'] = $data['default'];
                } else {

                    # 入力値($in)の値をセット
                    $arr[$key]['value'] = $set_val;
                }

                break;

            case 'checkbox': # チェックボックス ---------------------------------

                # 入力値
                if (!empty($in[$key])) $set_val_arr = $in[$key];

                if (empty($set_val_arr)) {
                    # デフォルト値をセット
                    if (!empty($data['default'])) {
                        $arr[$key]['value'] = $data['default'];
                    } else {
                        $arr[$key]['value'] = '';
                    }
                } else {
                    # 入力値の配列を、value（配列）にセット
                    $arr[$key]['value'] = $set_val_arr;
                }

                break;

            case 'select': # セレクトボックス ---------------------------------

                if (!empty($in[$key])) {
                    $set_val = $in[$key];
                }

                if (empty($set_val)) {

                    # デフォルト値をセット
                    if (!empty($data['default'])) {
                        $arr[$key]['value'] = $data['default'];
                    } else {
                        $arr[$key]['value'] = '';
                    }
                } else {

                    # 入力値($in)の値をセット
                    $arr[$key]['value'] = $set_val;
                }
                break;

            case 'textarea': # テキストエリア ---------------------------------

                if (!empty($in[$key])) {
                    $set_val = $in[$key];
                }

                if (empty($set_val)) {

                    # デフォルト値をセット
                    if (!empty($data['default'])) {
                        $arr[$key]['value'] = $data['default'];
                    } else {
                        $arr[$key]['value'] = '';
                    }
                } else {

                    # 入力値($in)の値をセット
                    $arr[$key]['value'] = $set_val;
                }

                break;

            default: # デフォルト ---------------------------------

                if (!empty($in[$key])) {
                    $set_val = $in[$key];
                }

                if (empty($set_val)) {

                    # デフォルト値をセット
                    if (!empty($arr[$key]['value'])) $arr[$key]['value'] = $data['default'];
                } else {

                    # 入力値($in)の値をセット
                    $arr[$key]['value'] = $set_val;
                }
        }
    }

    return $arr;
}

# --------------------------------------------------
# バリデーションチェック処理
# --------------------------------------------------
function validationCheck($in = null)
{
    global $articles;
    global $default_validate;

    $error_arr = array();

    if (!$in) return false;

    # 項目設定配列ループ
    foreach ($articles as $key => $data) {

        $validation_arr = array();

        # 項目のタイプ
        # ex)text, file
        $article_type = $data['type'];

        # 設定バリエーション配列
        $set_validate_arr = array();
        if (isset($data['validation'])) $set_validate_arr = $data['validation'];

        # デフォルトバリデーション設定
        $default_validate_arr = array();
        if (isset($default_validate[$article_type])) $default_validate_arr = $default_validate[$article_type];

        # 設定バリデーションとデフォルトバリデーションをマージ
        $mergedArrays = [];
        if (!empty($set_validate_arr))       $mergedArrays[] = $set_validate_arr;
        if (!empty($default_validate_arr))   $mergedArrays[] = $default_validate_arr;
        $validation_arr = call_user_func_array('array_merge', $mergedArrays);

        # validation設定が無ければスキップ
        if (!isset($data['validation'])) {
            continue;
        }

        # チェック対象入力値
        $obj_val = '';
        if (!empty($in[$key])) {
            $obj_val =  $in[$key];
        }

        # 設定されたバリエーション分、チェック処理
        foreach ($validation_arr as $validation) {

            $arr_flg           = '';
            $error_flg         = '';
            $validation_type   = ''; # バリデーションのタイプ   ex)require
            $message_text      = '';

            # validation_typeセット
            if (is_array($validation) === true) {

                # 配列（オリジナルエラーテキストやオプション設定などを持つバリエーション）
                $validation_type = $validation['key'];
                $arr_flg = 1; # 配列フラグ
            } else {

                # 変数（デフォルト）
                $validation_type = $validation;
            }

            # ---------------------
            # バリデーションのタイプごとに処理分岐
            # ※必須では必須チェック処理をしたり
            # ---------------------
            switch ($validation_type) {

                case 'require':
                    # --------------------------
                    # ■必須チェック
                    # --------------------------

                    if (!empty($validation['condition_key']) && !empty($validation['condition_value'])) {

                        # 必須チェック条件
                        # ex)「●●項目のその他選択時はその他テキスト入力必須」ケースで利用
                        # condition_key
                        # condition_value

                        $validation_key   = $validation['condition_key'];
                        $validation_value = $validation['condition_value'];

                        if (isset($in[$validation_key])) {

                            if ($in[$validation_key] == $validation_value) {

                                if (empty($obj_val)) {

                                    $error_flg = 1;

                                    if (!empty($arr_flg)) {
                                        # 指定テキスト
                                        $message_text = $validation['text'];
                                    } else {
                                        # デフォルトテキスト
                                        $message_text = DEFAULT_ERR_REQUIRE;
                                    }
                                }
                            }
                        }
                    } else {
                        # 通常必須チェック

                        if ($article_type == 'file') {

                            # file要素の場合
                            if (empty($_FILES[$key]['name'])) {

                                $error_flg = 1;

                                if (!empty($arr_flg)) {
                                    # 指定テキスト
                                    $message_text = $validation['text'];
                                } else {
                                    # デフォルトテキスト
                                    $message_text = DEFAULT_ERR_REQUIRE;
                                }
                            }
                        } else {
                            # input要素の場合
                            if (empty($obj_val)) {

                                $error_flg = 1;

                                if (!empty($arr_flg)) {
                                    # 指定テキスト
                                    $message_text = $validation['text'];
                                } else {
                                    # デフォルトテキスト
                                    $message_text = DEFAULT_ERR_REQUIRE;
                                }
                            }
                        }
                    }

                    break;

                case 'HankakuOnly':
                    # --------------------------
                    # ■半角英数のみかチェック
                    # --------------------------
                    if (!empty($obj_val) && chkHankakuOnly($obj_val) == false) {

                        $error_flg = 1;

                        if (!empty($arr_flg)) {
                            # 指定テキスト
                            $message_text = $validation['text'];
                        } else {
                            # デフォルトテキスト
                            $message_text = DEFAULT_ERR_HANKAKU;
                        }
                    }

                    break;

                case 'ZenkakuOnly':
                    # --------------------------
                    # ■全角のみかチェック
                    # --------------------------
                    if (!empty($obj_val) && chkZenkakuOnly($obj_val) == false) {

                        $error_flg = 1;

                        if (!empty($arr_flg)) {
                            # 指定テキスト
                            $message_text = $validation['text'];
                        } else {
                            # デフォルトテキスト
                            $message_text = DEFAULT_ERR_ZENKAKU;
                        }
                    }

                    break;

                case 'HiraganaOnly':
                    # --------------------------
                    # ■ひらがなのみかチェック
                    # --------------------------
                    if (!empty($obj_val) && chkHiraganaOnly($obj_val) == false) {

                        $error_flg = 1;

                        if (!empty($arr_flg)) {
                            # 指定テキスト
                            $message_text = $validation['text'];
                        } else {
                            # デフォルトテキスト
                            $message_text = DEFAULT_ERR_HIRAGANA;
                        }
                    }

                    break;

                case 'KatakanaOnly':
                    # --------------------------
                    # ■カタカナのみかチェック
                    # --------------------------
                    if (!empty($obj_val) && chkKatakanaOnly($obj_val) == false) {

                        $error_flg = 1;

                        if (!empty($arr_flg)) {
                            # 指定テキスト
                            $message_text = $validation['text'];
                        } else {
                            # デフォルトテキスト
                            $message_text = DEFAULT_ERR_KATAKANA;
                        }
                    }

                    break;

                case 'tag':
                    # --------------------------
                    # ■タグ入力チェック
                    # --------------------------
                    if (!empty($obj_val) && preg_match('/<[^>]*>/', $obj_val) == 1) {

                        $error_flg = 1;

                        if (!empty($arr_flg)) {
                            # 指定テキスト
                            $message_text = $validation['text'];
                        } else {
                            # デフォルトテキスト
                            $message_text = DEFAULT_ERR_TAG;
                        }
                    }

                    break;

                case 'length':
                    # --------------------------
                    # ■入力長チェック（Length）
                    # --------------------------

                    $cfg_min = '';
                    $cfg_max = '';

                    if (!empty($validation['min'])) $cfg_min = $validation['min']; # 最小文字数
                    if (!empty($validation['max'])) $cfg_max = $validation['max']; # 最大文字数

                    if (!empty($obj_val)) {

                        if (!empty($cfg_min) && !empty($cfg_max)) {

                            # 最小文字数、最大文字数 両方設定がある場合
                            if (mb_strlen($obj_val) < $cfg_min || mb_strlen($obj_val)  > $cfg_max) { # >

                                $error_flg = 1;

                                if (!empty($arr_flg) && !empty($validation['text'])) {
                                    # 指定テキスト
                                    $message_text = $validation['text'];
                                } else {
                                    # デフォルトテキスト
                                    $message_text = DEFAULT_ERR_LENGTH;
                                }
                            }
                        } else if (!empty($cfg_min) && empty($cfg_max)) {

                            # 最小文字数のみの場合
                            if (mb_strlen($obj_val)  <= $cfg_min) {

                                $error_flg = 1;

                                if (!empty($arr_flg) && !empty($validation['text'])) {
                                    # 指定テキスト
                                    $message_text = $validation['text'];
                                } else {
                                    # デフォルトテキスト
                                    $message_text = DEFAULT_ERR_LENGTH;
                                }
                            }
                        } else if (empty($cfg_min) && !empty($cfg_max)) {

                            # 最大文字数のみの場合
                            if (mb_strlen($obj_val)  > $cfg_max) {

                                $error_flg = 1;

                                if (!empty($arr_flg) && !empty($validation['text'])) {
                                    # 指定テキスト
                                    $message_text = $validation['text'];
                                } else {
                                    # デフォルトテキスト
                                    $message_text = DEFAULT_ERR_LENGTH;
                                }
                            }
                        }
                    }

                    break;

                case 'email':
                    # --------------------------
                    # ■メアド形式チェック
                    # --------------------------
                    if (!empty($obj_val) && checkMail($obj_val) == false) {

                        $error_flg = 1;

                        if (!empty($arr_flg)) {
                            # 指定テキスト
                            $message_text = $validation['text'];
                        } else {
                            # デフォルトテキスト
                            $message_text = DEFAULT_ERR_EMAIL;
                        }
                    }

                    break;

                case 'tel':
                    # --------------------------
                    # ■電話番号形式チェック
                    # --------------------------
                    if (!empty($obj_val) && checkPhoneNumber($obj_val) == false) {

                        $error_flg = 1;

                        if (!empty($arr_flg)) {
                            # 指定テキスト
                            $message_text = $validation['text'];
                        } else {
                            # デフォルトテキスト
                            $message_text = DEFAULT_ERR_PHONE;
                        }
                    }

                    break;

                case 'extension':

                    # --------------------------
                    # ■ファイル拡張子チェック
                    # --------------------------

                    $chkResult = '';

                    if (!empty($_FILES[$key]['name'])) {

                        # ファイル名から拡張子を取得
                        $file_ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);

                        # 許可する拡張子配列に値が在るか 1=存在する NULL=存在しない
                        $chkResult = in_array($file_ext, $validation['value']);
                    }

                    # ファイル選択があり、且つ許可する拡張子でなければエラー
                    if (!empty($_FILES[$key]['name']) && empty($chkResult)) {

                        $error_flg = 1;

                        if (!empty($validation['text'])) {
                            # 指定テキスト
                            $message_text = $validation['text'];
                        } else {
                            # デフォルトテキスト
                            $message_text = DEFAULT_ERR_EXTENSION;
                        }
                    }
                    break;

                case 'byte_size':
                    # --------------------------
                    # ■ファイル容量チェック
                    # --------------------------

                    $cfg_min = $validation['min']; # 最小容量
                    $cfg_max = $validation['max']; # 最大容量

                    # 入力ファイルの容量
                    $fileSize;

                    if (!empty($_FILES[$key]['size']) ) {
                        $fileSize = $_FILES[$key]['size'];
                    }

                    # ファイル入力が在って初めて容量チェック
                    if (!empty($_FILES[$key]['name'])) {

                        if (!empty($cfg_min) && !empty($cfg_max)) {

                            # 最小値、最大値 両方設定がある場合
                            if ($fileSize < $cfg_min || $fileSize > $cfg_max) { # >

                                $error_flg = 1;

                                if (!empty($arr_flg)) {
                                    # 指定テキスト
                                    $message_text = $validation['text'];
                                } else {
                                    # デフォルトテキスト
                                    $message_text = DEFAULT_ERR_BYTE_SIZE;
                                }
                            }
                        } else if (!empty($cfg_min) && empty($cfg_max)) {

                            # 最小値設定のみの場合
                            if ($fileSize <= $cfg_min) {

                                $error_flg = 1;

                                if (!empty($arr_flg)) {
                                    # 指定テキスト
                                    $message_text = $validation['text'];
                                } else {
                                    # デフォルトテキスト
                                    $message_text = DEFAULT_ERR_BYTE_SIZE;
                                }
                            }
                        } else if (empty($cfg_min) && !empty($cfg_max)) {
                            # 最大値設定のみの場合
                            if ($fileSize > $cfg_max) {

                                $error_flg = 1;

                                if (!empty($arr_flg)) {
                                    # 指定テキスト
                                    $message_text = $validation['text'];
                                } else {
                                    # デフォルトテキスト
                                    $message_text = DEFAULT_ERR_BYTE_SIZE;
                                }
                            }
                        }
                    }

                    break;


                default: # デフォルト ---------------------------------
            }

            if (!empty($error_flg)) {
                # 対象項目エラーセット
                $error_arr['ERROR'][$key]['error_message'] = $message_text;

                # エラーグループセット
                if (!empty($data['error_group'])) $error_arr['ERROR'][$data['error_group']]['error_message'] = $message_text;
            }
        }
    }

    if (!empty($error_arr)) {
        $error_arr['error_flg'] = 1; # エラー無し
    } else {
        $error_arr['error_flg'] = 0; # エラー在り
    }

    return $error_arr;
}

# --------------------------------------------------
# 添付ファイル一時保存処理。結果情報をSESSIONに保存
# ※tempディレクトリ配下に_tokenのファイル名でファイル保存
# ※確認画面処理時のみ利用想定
# ※確認画面ではtemp配下の一次ファイルの表示も可
# ※但し、ExcelやPDFファイルの場合はファイル名のみ
# --------------------------------------------------
function FileTempUpload($articles_data = null, $validate_result = null)
{

    # セッション内のファイル情報初期化
    $_SESSION[SES_NAME]['files'] = array();

    # 項目設定配列ループ
    foreach ($articles_data as $key => $data) {

        # type=file以外はスキップ
        if ($data['type'] != 'file') {
            continue;
        }

        # -------------------------------

        # アップロードされたファイルの情報を取得
        $file;

        if (!empty($_FILES[$key])) {
            $file = $_FILES[$key];
        }

        if (!empty($file['tmp_name'])) {

            # アップロードされたファイルの一時保存先パス
            $uploadedFilePath = $file['tmp_name'];

            # ファイルのオリジナル名
            $originalFileName = $file['name'];

            # 拡張子取得（例: jpg）
            $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $file['extension'] = $extension;

            # 出力ファイル名（ 項目名 + '_' + token値 + 拡張子 ）
            $outputFileName = $key . '_' . $_SESSION[SES_NAME]['_token'] . '.' . $extension;
            $file['output_file_name'] = $outputFileName;

            # ファイルサイズ
            $fileSize = $file['size'];

            # アップロード先のディレクトリ
            $uploadDirectory = TEMP_DIR;

            # アップロードされたファイルを指定のディレクトリに移動 /temp/配下へ
            $destinationPath = $uploadDirectory . '/' . $outputFileName;
            move_uploaded_file($uploadedFilePath, $destinationPath);

            $file['output_file_path'] = './temp/' . $outputFileName;

            # アップロードが成功したかチェック
            if (!file_exists($destinationPath)) {
                $file['error'] = 'ファイルのアップロードに失敗しました。';
            }

            # セッション内のファイル情報に保存
            $_SESSION[SES_NAME]['files'][] = $file;

            # セッション内の該当情報に保存
            $_SESSION[SES_NAME]['post'][$key] = $file;
        }
    }
}

# --------------------------------------------------
# 指定時間以上経過したファイルを削除
# ※/tempディレクトリ配下の一時ファイルをクリーンアップ
# --------------------------------------------------
function CleanTemp()
{

    // 削除するファイルのディレクトリ
    $directory = './temp';

    // 指定時間（秒）
    $expirationTime = TMP_FILE_LIMIT; // 指定時間

    // ディレクトリ内のファイルを取得
    $files = scandir($directory);

    // 現在の時刻
    $currentTimestamp = time();

    // ファイルの削除処理
    foreach ($files as $file) {

        // ディレクトリ内の "." および ".." を除外
        if ($file === '.' || $file === '..') {
            continue;
        }

        // ファイルのパス
        $filePath = $directory . '/' . $file;

        // ファイルの作成日時（または更新日時）を取得
        $fileTimestamp = filemtime($filePath);

        // 指定時間以上経過しているかチェック
        if (($currentTimestamp - $fileTimestamp) >= $expirationTime) {
            // ファイルを削除
            unlink($filePath);
        }
    }
}

# --------------------------------------------------
# メール送信処理
# ※管理者とユーザに送信
# ※ユーザへの自動返信は任意 init.phpのREMAIL_FLG
# ※mail関数で送信失敗した場合、同階層にテキストで出力される
# --------------------------------------------------
function send_email($encode = null)
{
    # トークンチェック（CSRF対策）
    # ※CSRF（トークン）処理としてはこの処理がメイン
    if (USE_TOKEN == 1 && DEBUG_MODE == 0) {

        # トークンが空、もしくはSESSION内のトークンとPOSTされたトークンが一致しない場合は「ページ遷移が不正」とみなす
        # ※その場合はSESSIONの破棄とエラー画面処理
        if (empty($_SESSION[SES_NAME]['_token']) || ($_SESSION[SES_NAME]['_token'] !== $_POST['_token'])) {

            # トークン破棄
            if (isset($_SESSION[SES_NAME])) unset($_SESSION[SES_NAME]); //トークン破棄
            if (isset($_POST['_token']))    unset($_POST);              //トークン破棄

            # エラーメッセージ
            $message = 'ページ遷移が不正です(02)';

            if (ERROR_DIS == 1) {
                # エラー画面テンプレート表示

                # 出力HTML
                include 'temp_error.php';
                exit();
            } else {
                exit($replace['message']);
            }
        }
    }

    # 項目設定データ
    global $articles;

    # 管理者送信先アドレス
    $admin_mail = '';
    if (DEBUG_MODE == 1) {
        $admin_mail = DEBUG_TO; # デバッグモード時のTO
    } else {
        $admin_mail = TO;
    }

    # ユーザ送信先アドレス
    $post_mail = $_SESSION[SES_NAME]['post'][EMAIL];

    # --------------------------------
    # ファイル添付があるかどうかチェック
    # --------------------------------

    $objFile     = array();
    $hasFileType = false;

    foreach ($articles as $key => $article) {
        if (isset($article['type']) && $article['type'] === 'file') {

            # file要素の情報 SESSION内にデータがあれば取得してセット
            if (!empty($_SESSION[SES_NAME]['post'][$key])) {

                # 仮に確認画面で放置された場合、一時ファイルの寿命が切れて削除され、ファイルの中身が参照できなくなる。
                # この場合メールに添付できない為、エラーとする。

                $file_path = './temp/' . $file_data['output_file_name'];    // 一時ファイルのパス
                $file_content = file_get_contents($file_path);

                if (empty($file_content)) {
                    # !!! エラー
                }

                # ファイル情報をセット
                $objFile[] = $_SESSION[SES_NAME]['post'][$key];

                # ファイル添付利用フラグ
                $hasFileType = true;
            }
        }
    }


    # --------------------------------
    # 管理者宛に届くメールを準備
    # --------------------------------

    # 管理者メール 本文
    $adminBody = mailToAdmin();

    # 添付ファイルが在る場合、メール本文に追加
    if ($hasFileType) {

        $tmpBody = $adminBody;

        # 本文パート
        $adminBody = '--boundary-string' . "\n";
        $adminBody .= "Content-Type: text/plain; charset=\"UTF-8\"\n";
        $adminBody .= "Content-Transfer-Encoding: 8bit\n\n";
        $adminBody .= $tmpBody . "\n";

        # 添付ファイル在るだけセット
        foreach ($objFile as $key => $file_data) {

            // 添付ファイルを追加
            $file_path = './temp/' . $file_data['output_file_name'];    // 一時ファイルのパス
            $content_type = mime_content_type($file_path);              // Content-Typeに設定するmimetype
            $file_content = file_get_contents($file_path);
            $file_content = chunk_split(base64_encode($file_content));

            $adminBody .= '--boundary-string' . "\n";
            $adminBody .= 'Content-Type: ' . $content_type . '; name="' . $file_data['name'] . '"' . "\n";
            $adminBody .= 'Content-Disposition: attachment; filename="' . $file_data['name'] . '"' . "\n";
            $adminBody .= 'Content-Transfer-Encoding: base64' . "\n";
            $adminBody .= "\n";
            $adminBody .= $file_content . "\n";
        }

        $adminBody .= '--boundary-string' . "\n";
    }


    # 管理者メール ヘッダー
    $header = adminHeader($post_mail, $hasFileType);

    # 管理者メール 件名
    $subject = SUBJECT;

    # --------------------------------
    # ユーザに届くメールを準備
    # --------------------------------
    if (REMAIL_FLG == 1) {

        # ユーザメール 本文
        $userBody = mailToUser();

        # ユーザメール ヘッダー
        $reheader = userHeader();

        # ユーザメール 件名
        $re_subject = RE_SUBJECT;
    }

    # --------------------------------
    # 送信処理
    # --------------------------------

    # -fオプションによるエンベロープFrom（Return-Path）の設定
    # (safe_modeがOFFの場合かつ上記設定がONの場合のみ実施)
    if (USE_ENVELOPE == 0) {

        # ------------
        # 管理者宛
        # ------------

        # 送信処理
        $result_admin = mb_send_mail($admin_mail, $subject, $adminBody, $header);

        # mail関数失敗の場合、メール内容をファイルで出力
        if (empty($result_admin)) {

            # ファイルに内容を出力
            file_put_contents('mail_admin.txt', $adminBody);
        }

        # ------------
        # ユーザ宛
        # ------------
        if (REMAIL_FLG == 1 && !empty($post_mail)) {

            # 送信処理
            $result_user = mb_send_mail($post_mail, $re_subject, $userBody, $reheader);

            # mail関数失敗の場合、メール内容をファイルで出力
            if (empty($result_user)) {

                # ファイルに内容を出力
                file_put_contents('mail_user.txt', $userBody);
            }
        }
    } else {

        # ------------
        # 管理者宛 -fオプション
        # ------------

        # 送信処理
        $result_user = mb_send_mail($admin_mail, $subject, $adminBody, $header, '-f' . FROM);

        # mail関数失敗の場合、メール内容をファイルで出力
        if (empty($result_user)) {

            # ファイルに内容を出力
            file_put_contents('mail_admin.txt', $adminBody);
        }

        # ------------
        # ユーザ宛 -fオプション
        # ------------
        if (REMAIL_FLG == 1 && !empty($post_mail)) {

            # 送信処理
            $result_user = mb_send_mail($post_mail, $re_subject, $userBody, $reheader, '-f' . FROM);

            # mail関数失敗の場合、メール内容をファイルで出力
            if (empty($result_user)) {

                # ファイルに内容を出力
                file_put_contents('mail_user.txt', $userBody);
            }
        }
    }

    # --------------------------------
    # 送信後処理
    # --------------------------------

    # 一時ファイル削除
    foreach ($objFile as $key => $file_data) {

        # 一時ファイルのパス
        $file_path = './temp/' . $file_data['output_file_name'];

        if (file_exists($file_path)) {
            if (unlink($file_path)) {
                # echo "ファイルが正常に削除されました。";

            } else {
                # echo "ファイルの削除に失敗しました。";

            }
        } else {
            # echo "指定されたファイルは存在しません。";

        }
    }


    return;
}


# -----------------------
# 管理者宛メール
# -----------------------

//管理者宛送信メールヘッダ組み立て
function adminHeader($post_mail, $hasFileType = false)
{
    $header = '';

    # BCCアドレス
    $BccMail = '';
    if (DEBUG_MODE == 1) {
        $BccMail = DEBUG_BCC_MAIL; # デバッグモード時のBCC
    } else {
        $BccMail = BCC_MAIL;
    }

    # ---------------------------------

    $header .= 'From: ' . FROM . "\n";

    if ($BccMail != '') {
        $header .= "Bcc: $BccMail\n";
    }
    if (!empty($post_mail)) {
        $header .= "Reply-To: " . $post_mail . "\n";
    }

    if ($hasFileType) {
        $header .= 'MIME-Version: 1.0' . "\n";
        $header .= 'Content-Type: multipart/mixed; boundary="boundary-string"' . "\n";
        $header .= 'Content-Transfer-Encoding: 8bit' . "\n";
    } else {
        $header .= 'Content-Type:text/plain;charset=iso-2022-jp' . "\n";
        $header .= 'X-Mailer: PHP/' . phpversion();
    }

    return $header;
}

//管理者宛送信メールボディ組み立て
function mailToAdmin()
{
    # 項目設定データ
    global $articles;

    $adminBody = '';

    # メール用データ配列
    $mail_data = array();

    # 項目設定データをセット
    # $mail_data_arr['articles'] = $articles;

    # SESSIONの表示用データマージ
    $mail_data = array_merge($mail_data, $_SESSION[SES_NAME]['view']);

    # その他情報をセット
    $mail_data['info_sendtime']  = date("Y/m/d (D) H:i:s", time());       // 送信日時
    $mail_data['info_ipaddress'] = @$_SERVER["REMOTE_ADDR"];              // 送信者のIPアドレス
    $mail_data['info_host']      = getHostByAddr(getenv('REMOTE_ADDR'));  // 送信者のホスト名

    if (CONF_DIS != 1) {
        $mail_data['info_url'] = @$_SERVER['HTTP_REFERER'];  // 問い合わせのページURL
    } else {
        $mail_data['info_url'] = @$arr['httpReferer'];       // 問い合わせのページURL
    }

    # テンプレート内容出力
    # テンプレートで以下の配列が利用可能
    # print_r($in);
    # print_r($session_arr);
    # print_r($mail_data_arr);
    # print_r($articles);

    ob_start(); // バッファリングを開始

    include 'temp_email_admin.php';

    $adminBody = ob_get_clean(); // バッファの内容を取得して変数に代入し、バッファリングを終了

    return $adminBody;
}

# -----------------------
# ユーザ宛メール
# -----------------------

//ユーザ宛送信メールヘッダ組み立て
function userHeader()
{
    global $encode;

    $reheader = "From: ";
    if (!empty(REFORM_NAME)) {
        $default_internal_encode = mb_internal_encoding();
        if ($default_internal_encode != $encode) {
            mb_internal_encoding($encode);
        }
        $reheader .= mb_encode_mimeheader(REFORM_NAME) . " <" . FROM . ">\nReply-To: " . FROM;
    } else {
        $reheader .= FROM . "\nReply-To: " . FROM;
    }

    $reheader .= "\nContent-Type: text/plain;charset=iso-2022-jp\nX-Mailer: PHP/" . phpversion();
    $reheader .= "\n";
    return $reheader;
}

//ユーザ宛送信メールボディ組み立て
function mailToUser()
{
    # 項目設定データ
    global $articles;

    $userBody = '';

    # メール用データ配列
    $mail_data = array();

    # 項目設定データをセット
    # $mail_data_arr['articles'] = $articles;

    # SESSIONの表示用データマージ
    $mail_data = array_merge($mail_data, $_SESSION[SES_NAME]['view']);

    # テンプレート内容出力
    # テンプレートで以下の配列が利用可能
    # print_r($in);
    # print_r($session_arr);
    # print_r($mail_data);
    # print_r($articles);

    ob_start(); // バッファリングを開始

    include 'temp_email_user.php';

    $userBody = ob_get_clean(); // バッファの内容を取得して変数に代入し、バッファリングを終了

    return $userBody;
}
