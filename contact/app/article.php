<?php
# ------------------------
# 項目の設定情報
# ------------------------
$articles = array(

    # name ---
    'name1' => array(
        'type'              => 'textbox',
        'validation'        => ['require'],
    ),
    # /name ---

    # name ---
    'name2' => array(
        'type'              => 'textbox',
        'validation'        => ['require'],
    ),
    # /name ---
    # email ---
    'email' => array(
        'type'              => 'email',
        'validation'        => [
            array('key' => 'require',   'text' => 'Please enter your email address'),
            array('key' => 'email',     'text' => 'Please enter the correct email address')
        ],
        'input_action'      => ['z2h'],
    ),

    # subject ---
    'subject' => array(
        'type'              => 'textbox',
        'validation'        => ['require'],
    ),
    # /subject ---

    # comment ---
    'comment' => array(
        'type'              => 'textarea',
        'validation'        => ['require'],
    ),
    # /comment ---
    
    /*
    
    # kana_hira ---
    'kana_hira' => array(
        'type'              => 'textbox',
        'validation'        => ['HiraganaOnly'],
    ),
    # /kana_hira ---

    # /email ---

    # passwd ---
    'passwd' => array(
        'type'              => 'textbox',
        'validation'        => [
            'require', 
            'HankakuOnly', 
            array(
                'key'       =>  'length',
                'min'       =>  6,
                'max'       =>  12,
                'text'      => '半角英数6～12文字で入力してください。',
            ),
        ],
        'input_action'  => ['z2h'],
    ),
    # /passwd ---

    # tel ---
    'tel' => array(
        'type'              => 'textbox',
        'validation'        => [
            array('key' => 'require',   'text' => '電話番号入れてください'),
            array('key' => 'tel',       'text' => '電話番号正しく入れてください')
        ],
        'input_action'      => ['z2h'],
    ),
    # /tel ---

    # zip ---
    'zip' => array(
        'type'              => 'textbox',
        'validation'        => ['require'],
        'error_group'       => 'address',
        'option'            => ['zipsearch'],
        'input_action'      => ['z2h'],
    ),
    # /zip ---

    # zip_1 ---
    'zip_1' => array(
        'type'              => 'textbox',
        'validation'        => [
            'require',
            array(
                'key'       =>  'length',
                'max'       =>  3
            ),
        ],
        'error_group'       => 'zip1_2',
        'option'            => ['zipsearch'],
        'input_action'      => ['z2h'],
    ),
    # /zip_1 ---
    # zip_2 ---
    'zip_2' => array(
        'type'              => 'textbox',
        'validation'        => [
            'require',
            array(
                'key'       =>  'length',
                'max'       =>  4,
            ),
        ],
        'error_group'       => 'zip1_2',
        'option'            => ['zipsearch'],
        'input_action'      => ['z2h'],
    ),
    # /zip_2 ---


    # pref ---
    'pref' => array(
        'type'              => 'select',
        'choise'            => $pref_arr,     # 都道府県配列デフォルトセット
        'default'           => '',
        'validation'        => [
            array('key' => 'require', 'text' => '都道府県を入れてください')
        ],
        'error_group'       => 'address',
    ),
    # /pref ---

    # address_1 ---
    'address_1' => array(
        'type'              => 'textbox',
        'validation'        => ['require'],
        'error_group'       => 'address',
    ),
    # /address_1 ---

    # address_2 ---
    'address_2' => array(
        'type'              => 'textbox',
        'validation'        => [],
        'error_group'       => 'address',
    ),
    # /address_2 ---

    # gender ---
    'gender' => array(
        'type'              => 'radio',
        'choise'            => ['1' => '男性', '2' => '女性', '3' => 'その他'],
        'default'           => '1',
        'validation'        => ['require'],
    ),
    # /gender ---

    # birth_year ---
    'birth_year' => array(
        'type'              => 'select',
        'choise'            => setKeyValue(range(date('Y') - 10,  date('Y') - 50)),
        'default'           => '',
        'validation'        => [],
        'error_group'       => 'birthday',
    ),
    # /birth_year ---

    # birth_month ---
    'birth_month' => array(
        'type'              => 'select',
        'choise'            => setKeyValue($month_arr),     # 月配列デフォルトセット
        'default'           => '',
        'validation'        => [],
        'error_group'       => 'birthday',
    ),
    # /birth_month ---

    # birth_day ---
    'birth_day' => array(
        'type'              => 'select',
        'choise'            => setKeyValue($day_arr),       # 日配列デフォルトセット
        'default'           => '',
        'validation'        => [],
        'error_group'       => 'birthday',
    ),
    # /birth_day ---

    # time_hour ---
    'time_hour' => array(
        'type'              => 'select',
        'choise'            => setKeyValue($hour_arr),      # 時配列デフォルトセット
        'validation'        => [],
        'error_group'       => 'time',
    ),
    # /time_hour ---

    # time_min ---
    'time_min' => array(
        'type'              => 'select',
        'choise'            => setKeyValue($min_arr),       # 分配列デフォルトセット
        'validation'        => [],
        'error_group'       => 'time',
    ),
    # /time_min ---

    # time_sec ---
    'time_sec' => array(
        'type'              => 'select',
        'choise'            => setKeyValue($sec_arr),       # 秒配列デフォルトセット
        'validation'        => [],
        'error_group'       => 'time',
    ),
    # /time_sec ---

    # interest ---
    'interest' => array(
        'type'              => 'radio',
        'choise'            => [
            '1' => 'シンギュラリティー',
            '2' => 'フィンテック',
            '3' => 'キュレーション',
            '4' => 'データサイエンティスト',
            '5' => 'ペアレンタルコントロール',
            '6' => 'ライフハック',
            '7' => 'ディープラーニング',
            '8' => 'ホワイトハッカー',
            '99' => 'その他'
        ],
        'default'           => '',
        'validation'        => [],
    ),
    # /interest ---

    # interest_other ---
    'interest_other' => array(
        'type'              => 'textbox',
        'validation'        => [
            array(
                'key'               => 'require', 
                'condition_key'     => 'interest', 
                'condition_value'   => '99',
                'text'              => '舐めないでください。', 
            )
        ],
        'error_group'       => 'interest',
    ),
    # /interest_other ---

    # plant ---
    'plant' => array(
        'type'              => 'checkbox',
        'choise'            => ['1' => 'りんご', '2' => 'バナナ', '3' => 'キウイフルーツ', '4' => 'いちご', '5' => 'ライチ'],
        'validation'        => [],
        'join'              => ', ',
    ),
    # /plant ---

    # animal ---
    'animal' => array(
        'type'              => 'checkbox',
        'choise'            => ['1' => 'ゴリラ', '2' => 'ネズミ', '3' => 'サイ', '4' => 'チーター'],
        'default'           => [],
        'validation'        => [],
        'join'              => '==',
    ),
    # /animal ---

    # words ---
    'words' => array(
        'type'              => 'checkbox',
        'choise'            => ['1' => '君が！', '2' => '泣くまで！', '3' => '殴るのを！', '4' => '止めない！'],
        'default'           => [1, 4],
    ),
    # /words ---

    # job ---
    'job' => array(
        'type'              => 'select',
        'choise'            => ['1' => 'ディレクタ', '2' => 'プログラマ', '3' => 'コーダー', '4' => 'プロジェクトマネージャー', '5' => 'システムエンジニア'],
        'default'           => '',
        'validation'        => [],
    ),
    # /job ---


    # image_file ---
    'image_file' => array(
        'type'              => 'file',
        'validation'        => [
            array(                                        # ファイル容量チェック（byte_size）
                'key'       =>  'byte_size',
                'min'       =>  10 * 1024,                # 10KB
                'max'       =>  1 * 1024 * 1024,          # 1MB
                'text'      => '容量は10KB以上、1MB以下でお願いします。',
            ),
            array(                              # ファイル拡張子チェック（extension）
                'key'       => 'extension',
                'value'     => ['jpg', 'JPG', 'jpeg', 'JPEG', 'gif', 'png'],
                # 'text'      => '拡張子！'
            ),

        ],
    ),
    # /image_file ---

    # pdf_file ---
    'pdf_file' => array(
        'type'              => 'file',
        'validation'        => [
            array(                                       # ファイル拡張子チェック（extension）
                'key'      => 'extension',
                'value'    => ['pdf', 'PDF'],
                'text'     => 'PDF拡張子！'
            ),
        ],
    ),
    # /pdf_file ---

    # agree ---
    'agree' => array(
        'type'              => 'checkbox',
        'choise'            => ['1' => '同意する'],
        'validation'        => [
            array('key' => 'require', 'text' => '同意してくりゃれ')
        ],
    ),
    # /agree ---
*/
);
