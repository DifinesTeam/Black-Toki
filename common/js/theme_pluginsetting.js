(function ($) {
  $(function(){
   // scrollsmoothly();
   // matchHeight();
    lightbox();/*modaalの設定*/
    slider();/*slickの設定*/
  });


// 横にゆっくり流れる
$(function(){
    //アニメーションスピード
    var scrollSpeed = 0.1;
    //画像サイズ
    var imgWidth = 3300;
    //画像の初期位置
    var posX = 0;
    //ループ処理
    setInterval(function(){
        //画像のサイズまで移動したら0に戻る。
        if (posX >= imgWidth) posX= 0;
        //scrollSpeed分移動
        posX += scrollSpeed;
        $('.slide_ticker').css("background-position",-posX+"px 0px");
    }, 1);
});
  
  var scrollsmoothly = function(){
    var scroll = new SmoothScroll('a[href*="#"]', {
    //header: '#header' //header固定
    //offset: 0, // 到達場所からズラすピクセル数
});
  }
  var matchHeight = function(){
    $('').matchHeight();
  }
  var lightbox = function(){
    $(".inline").modaal({});
    $(".modaal.image").modaal({type:'image'});
    $(".modaal.video").modaal({type:'video'});
    $(".modaal.iframe").modaal({type:'iframe', width: 600, height:400});
    $(".modaal.image").modaal({type:'image'});
  }
  var slider = function(){
    $('.main-slider').slick({
      infinite: true,
      slidesToShow: 1,
      slidesToScroll: 1,
      fade: true,
      arrows: false,
      dots: false,
      autoplay:true,
      speed:2500,
      autoplaySpeed:3000,
    });
  $(".main-slider_zoom").on("init", function () {
    // 最初のスライドに"add-animation"のclassを付ける(data-slick-index="0"が最初のスライドを指す)
      $('.slick-slide[data-slick-index="0"]').addClass("add-animation");
    })
    // 通常のオプション
    .slick({
      autoplay: true, // 自動再生ON
      fade: true, // フェードON
      arrows: false, // 矢印OFF
      speed: 4000, // スライド、フェードアニメーションの速度2000ミリ秒
      autoplaySpeed: 6000, // 自動再生速度4000ミリ秒
      pauseOnFocus: false, // フォーカスで一時停止OFF
      pauseOnHover: false, // マウスホバーで一時停止OFF
    })
    .on({
      // スライドが移動する前に発生するイベント
      beforeChange: function (event, slick, currentSlide, nextSlide) {
        // 表示されているスライドに"add-animation"のclassをつける
        $(".slick-slide", this).eq(nextSlide).addClass("add-animation");
        // あとで"add-animation"のclassを消すための"remove-animation"classを付ける
        $(".slick-slide", this).eq(currentSlide).addClass("remove-animation");
      },
      // スライドが移動した後に発生するイベント
      afterChange: function () {
        // 表示していないスライドはアニメーションのclassを外す
        $(".remove-animation", this).removeClass(
          "remove-animation add-animation"
        );
      },
    });
    $('.slider_box3, .slider3').slick({
         infinite: true,
         slidesToShow: 3,
         slidesToScroll: 1,
         arrows: true,
         dots: true,
         autoplay:true,
         autoplaySpeed:5000,
         responsive: [{
              breakpoint: 769,
                settings: {
                   slidesToShow: 2
                 }
             },{
         breakpoint: 480,
               settings: {
                  slidesToShow: 1
               }
            }
         ]
    });
    $('.thumb-slider_main').slick({
         infinite: true,
         slidesToShow: 1,
         slidesToScroll: 1,
         arrows: false,
         fade: true,
         adaptiveHeight: true,
         asNavFor: '.thumb-slider_thumb' //サムネイルのクラス名
    });
    $('.thumb-slider_thumb').slick({
        infinite: true,
        slidesToShow: 9,
        slidesToScroll: 1,
        arrows: false,
        asNavFor: '.thumb-slider_main', //スライダー本体のクラス名
        focusOnSelect: true,
        draggable: true,
        adaptiveHeight: true,
        //vertical: true,
        responsive: [{
        breakpoint: 769,
            settings: {
            slidesToShow: 5
                }
             },{
         breakpoint: 480,
               settings: {
                  slidesToShow: 3
               }
            }
         ]
    });

    
  }
})(jQuery);

