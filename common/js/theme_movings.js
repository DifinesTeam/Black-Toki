(function ($) {
  $(function(){
    //loading();
    links();/*リンク関連の設定*/
    hovers();/*ホバー関連の設定*/
    clicks();/*クリック関連の設定*/
    scrolls();/*スクロール関連の設定*/
    animation();/*アニメーション関連の設定*/
    page_top();
    //mouse_stalker(); //マウスストーカー
  });
  function loading(){
  //ローディング
  $(window).on('load',function(){
     $("#loading-wrapper").addClass("completed")
  });
  }
  function links(){
    /* $("セレクタ").link_box()もしくは
      $("セレクタのwrapper class > *").link_box()で
      リンクボックス化
      内包する<a>タグのリンク先を勝手に拾ってくる*/
    $(".js_link-box").link_box();
    $(".js_link-box_wrp > *").link_box();
  }
  function hovers(){
    /* $("セレクタ").mhover()もしくは
      $("セレクタのwrapperクラス > *").mhover()で
      ホバー時に光らせる
       $("セレクタ").mhover() = $.mhover_num(0.5,1)*/
    /* $("セレクタ").mhover_num(ホバー時の透明度,元の透明度)*/
    /* $("セレクタ").mhover_num_target(内包する光らせたい対象,ホバー時の透明度,元の透明度)*/
      $(".link_btn").mhover();
      $(".js_hvr-btn").mhover();
      $(".js_hvr-btn_wrp > *").mhover();


      $('.imgline_wrp .layout_line li').hover(
        function(){
          var idx = $(this).index();
          $('.imgline_wrp .layout_imgline li.active').removeClass('active');
          $('.imgline_wrp .layout_imgline li:nth-child('+parseInt(idx+1)+')').addClass('active');
        },
        function(){}
      );

      /*例
      $("#mhn_target_exm1").mhover_num_target('dt',0.5,1);
      $("#mhn_target_exm2").mhover_num_target('dd',0.5,1);*/
  }
  function clicks(){
    /* <p>や<span>のオプションにhref="電話番号"がある場合、
      スマホ使用時電話番号のリンクがつく*/
    $('.js_tel-btn').tel_btn();
  }
  function scrolls(){
//フロートボタンを特定の位置から特定の位置まで表示させる
$(window).on('load',function(){//頁内の全ての要素が読み込まれてから実行
  //ドキュメント全体の高さ
  var pageHeight = $(document).height();
  //頁上部から最初のCTAボタンまでの距離 ＋ CTAボタンの高さ(outerHeight:ボーダーの外側の高さ)
  var ctaFirst = $('#content_top').offset().top
  //ドキュメント全体の高さ - 頁上部から最後のCTAボタンまでの距離
  //var ctaLast = pageHeight - $('#entry').offset().top;

  $(window).on('scroll',function(){//スクロールすると実行される
    //現在のトップからの位置 ＝ ウィンドウの高さ ＋ スクロールした高さ
    var scrollPosition = $(window).height() + $(window).scrollTop();
    //スクロールが最初のCTAボタンを超える＆最後のCTAボタンを超えない場合
    if ($(window).scrollTop() > ctaFirst && pageHeight - scrollPosition/*  >= ctaLast*/) {
        $( 'body' ).addClass( 'upper' );
    } else {//最初のCTAボタンを超えない＆最後のCTAボタンを超えた場合
        $( 'body' ).removeClass( 'upper' );
    }
  });
});
  }
  function animation(){
    /*$('animationをつけたいセレクタのwrapperクラス').on('inview', function(event, isInView) {
      if (isInView) {$(this).find('animationをつけたいセレクタ').addClass('fadeInLeftBig');}
      else{$(this).find('animationをつけたいセレクタ').removeClass('fadeInLeftBig');}
    });*/
    /*inview.jsを使ったアニメーション例
    $('#animation-exam').on('inview', function(event, isInView) {
      if (isInView) {$(this).find('#animation-exam_inview').addClass('fadeInLeftBig');}
      else{$(this).find('#animation-exam_inview').removeClass('fadeInLeftBig');}
    });*/
    /*hoverを使ったアニメーション例
    $('#animation-exam').hover(function() {
        $(this).find('#animation-exam_hover').addClass('bounceIn');
      },function() {
        $(this).find('#animation-exam_hover').removeClass('bounceIn');
    });*/
   //view_animeをを起点に発火させる

        $('.view_anime').on('inview', function(event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                $(this).stop().addClass('fadeIn');
            }
        });
        $('.view_anime_left').on('inview', function(event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                $(this).stop().addClass('fadeInLeft');
            }
        });
        $('.view_anime_right').on('inview', function(event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                $(this).stop().addClass('fadeInRight');
            }
        });
    
  // 段階ごとにフェードイン  
function delayScrollAnime() {
	var time = 0.2;//遅延時間を増やす秒数の値
	var value = time;
	$('.view_anime_wrp').each(function () {
		var parent = this;					//親要素を取得
		var elemPos = $(this).offset().top;//要素の位置まで来たら
		var scroll = $(window).scrollTop();//スクロール値を取得
		var windowHeight = $(window).height();//画面の高さを取得
		var childs = $(this).children();	//子要素を取得
		
		if (scroll >= elemPos - windowHeight && !$(parent).hasClass("play")) {//指定領域内にスクロールが入ったらまた親要素にクラスplayがなければ
			$(childs).each(function () {
				
				if (!$(this).hasClass("fadeIn")) {//アニメーションのクラス名が指定されているかどうかをチェック
					
					$(parent).addClass("play");	//親要素にクラス名playを追加
					$(this).css("animation-delay", value + "s");//アニメーション遅延のCSS animation-delayを追加し
					$(this).addClass("fadeIn");//アニメーションのクラス名を追加
					value = value + time;//delay時間を増加させる
					
					//全ての処理を終わったらplayを外す
					var index = $(childs).index(this);
					if((childs.length-1) == index){
						$(parent).removeClass("play");
					}
				}
			})
		}else {
			$(childs).removeClass("fadeIn");//アニメーションのクラス名を削除
			value = time;//delay初期値の数値に戻す
		}
	})
}

// 画面をスクロールをしたら動かしたい場合の記述
	$(window).scroll(function (){
		delayScrollAnime();/* アニメーション用の関数を呼ぶ*/
	});// ここまで画面をスクロールをしたら動かしたい場合の記述

// 画面が読み込まれたらすぐに動かしたい場合の記述
	$(window).on('load', function(){
		delayScrollAnime();/* アニメーション用の関数を呼ぶ*/
	});// ここまで画面が読み込まれたらすぐに動かしたい場合の記述
    
    //画像を横からスライドイン
$(window).on('scroll', function (){
 
    // .photo-img-crossクラスがつけられている要素
    var elem = $('.photo_cross');     
    elem.each(function () {
        var elemTop = $(this).offset().top;
        var scroll = $(window).scrollTop();
        var wh = $(window).height();
        //　要素の上部が画面の1/4より上にきたら
        if(scroll > elemTop - wh + (wh / 4) ){
              // .photo-move-crossをつける
              $(this).addClass("photo_move_cross");
            }
    });
 
}); 
    
   
  }


  function mouse_stalker(){
    //<div id="stalker"></div>を設置
    //マウスストーカー用のdivを取得
    const stalker = document.getElementById('stalker');
    //aタグにホバー中かどうかの判別フラグ
    let hovFlag = false;
    //マウスに追従させる処理 （リンクに吸い付いてる時は除外する）
    document.addEventListener('mousemove', function (e) {
        if (!hovFlag) {
        stalker.style.transform = 'translate(' + e.clientX + 'px, ' + e.clientY + 'px)';
        }
    });
    //リンクへ吸い付く処理
    const linkElem = document.querySelectorAll('a:not(.no_stick_)');
    for (let i = 0; i < linkElem.length; i++) {
        //マウスホバー時
        linkElem[i].addEventListener('mouseover', function (e) {
            hovFlag = true;

            //マウスストーカーにクラスをつける
            stalker.classList.add('hov_');

            //マウスストーカーの位置をリンクの中心に固定
            let rect = e.target.getBoundingClientRect();
            let posX = rect.left + (rect.width / 2);
            let posY = rect.top + (rect.height / 2);

            stalker.style.transform = 'translate(' + posX + 'px, ' + posY + 'px)';

        });
        //マウスホバー解除時
        linkElem[i].addEventListener('mouseout', function (e) {
            hovFlag = false;
            stalker.classList.remove('hov_');
        });
    }
}
    
    var page_top = function () {
    $(document).ready(function(){
      var topBtn = $('.footer_pagetop');
      topBtn.hide();
      $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
          topBtn.fadeIn();
        } else {
          topBtn.fadeOut();
        }
      });
    });
  }

  $.fn.mhover =function(){
    $(this).mhover_num(0.5,1);
  }
  $.fn.mhover_num =function(num1,num2){
    var w;
    $(window).on('load resize', function(){w=$(window).width();});
    $(this).hover(
      function(){if(w>768){
        $(this).stop().fadeTo("fast",num1);}},
      function(){if(w>768){
        $(this).stop().fadeTo("fast",num2);}}
    );
  }
  $.fn.mhover_num_target =function(target,num1,num2){
    var w;
    $(window).on('load resize', function(){w=$(window).width();});
    $(this).hover(
      function(){if(w>768){
         var $target = $(this).find(target);
        $target.stop().fadeTo("fast",num1);}},
      function(){if(w>768){
        var $target = $(this).find(target);
        $target.stop().fadeTo("fast",num2);}}
    );
  }
  $.fn.link_box =function(){
    $(this).click(function(e){
           if($(this).find("a").attr("target")=="_blank"){
               window.open($(this).find("a").attr("href"), '_blank');
           }else{
               window.location=$(this).find("a").attr("href");
           }
       return false;
    });
  }
  $.fn.tel_btn =  function(){
    var device = navigator.userAgent;
    var tel;
    if((device.indexOf('iPhone') > 0 && device.indexOf('iPad') == -1) || device.indexOf('iPod') > 0 || device.indexOf('Android') > 0){
      tel = $(this).attr("href");
      $(this).wrap('<a href="tel:'+tel+'"></a>');
    }
  };
})(jQuery);
