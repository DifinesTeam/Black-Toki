$(window).load(function() {
  // headerを高さを取得
  var header = $("#header")
  //時間調整
  var time = 500;
  //スクロール時にヘッダーが可変する場合の数値を入力　body.upper で連動
  var scroll_header = 61; 
  
  //URLのハッシュ値を取得
  var urlHash = location.hash;
  //ハッシュ値があればページ内スクロール
  if(urlHash) {
    //スクロールを0に戻しておく
    $('body,html').stop().scrollTop(0);
    setTimeout(function () {
      //ロード時の処理を待ち、時間差でスクロール実行
      scrollToAnker(urlHash) ;
    }, 100);
  }
  
  //通常のクリック時
  $('a[href^="#"]').on('click', function(event) {
    event.preventDefault();
    // ハッシュ値を取得して URI デコードする
    var decodedHash = decodeURI(this.hash);
    //リンク先が#か空だったらhtmlに
    var hash = decodedHash == "#" || decodedHash == "" ? 'html' : decodedHash;
    //スクロール実行
    scrollToAnker(hash);
    return false;
  });
  
  // 関数：スムーススクロール
  // 指定したアンカー(#ID)へアニメーションでスクロール
  function scrollToAnker(hash) {
  var target = $(hash);
  var adjust = header.outerHeight(true); //ヘッダーの高さを取得
    if($('body').hasClass('upper')){
        var upper_h = 0;
      } else {
        var upper_h = scroll_header;
    }
    var position = target.offset().top - adjust - upper_h;
    $('body,html').stop().animate({scrollTop:position}, time, 'swing');
  }
});
