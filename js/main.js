$(function () {

  $(".inview_re").on("inview", function (event, isInView) {
    if (isInView) {
      $(this).stop().addClass("is-show");
    } else {
      $(this).stop().removeClass("is-show");
    }
  });



  $.fakeLoader({
    timeToHide: 2000,
    //ローディング画面が消えるまでの時間。
    //ミリセカンド表示、1200=1.2秒
    spinner: "spinner6",
    //ローディングアニメーションの種類。
    //spinner1～7が指定可能
    bgColor: 'black',
    //背景色を設定することが出来る
    imagePath: "yourPath/customizedImage.gif"
    //オリジナルのローディングアニメーション画像へのパス。
    //gifも使う事が出来る
  });



  // NAV smartphone

  function resizeNav() {
    // Set the nav height to fill the window
    $("#nav-fullscreen").css({ "height": window.innerHeight });

    // Set the circle radius to the length of the window diagonal,
    // this way we're only making the circle as big as it needs to be.
    let radius = Math.sqrt(Math.pow(window.innerHeight, 2) + Math.pow(window.innerWidth, 2));
    let diameter = radius * 2;
    $("#nav-overlay").width(diameter);
    $("#nav-overlay").height(diameter);
    $("#nav-overlay").css({ "margin-top": -radius, "margin-left": -radius });
  }

  // Set up click and window resize callbacks, then init the nav.

  $("#nav-toggle").click(function () {
    $("#nav-toggle, #nav-overlay, #nav-fullscreen").toggleClass("open");
  });
  $(window).resize(resizeNav);
  resizeNav();



  // ProgressBar Animation

  function ProgressBar() {
    let duration = 15000;
    let num_delay = 20000;
    let easing = 'easeOutCirc';

    $('#js-html').stop(true).delay(num_delay).animate({
      width: '90%'
    }, duration, easing);
    $('#js-css').stop(true).delay(num_delay * 1.1).animate({
      width: '90%'
    }, duration, easing);
    $('#js-bootstrap').stop(true).delay(num_delay * 1.2).animate({
      width: '90%'
    }, duration, easing);
    $('#js-wordpress').stop(true).delay(num_delay * 1.3).animate({
      width: '80%'
    }, duration, easing);
    $('#js-sass').stop(true).delay(num_delay * 1.4).animate({
      width: '80%'
    }, duration, easing);
    $('#js-jquery').stop(true).delay(num_delay * 1.5).animate({
      width: '70%'
    }, duration, easing);
    $('#js-php').stop(true).delay(num_delay * 1.6).animate({
      width: '70%'
    }, duration, easing);
  }

  $(window).on('load', function () {
    ProgressBar();
  });


  
  // テキストfadein,out(.img-desc_1)
  $('.work-image_1').on('mouseout', function () {
    $('.img-desc_1').stop(true).animate({
      opacity: '0'
    }, 1000);
  });
  $('.work-image_1').on('mouseover', function () {
    $('.img-desc_1').stop(true).animate({
      opacity: '1'
    }, 1000);
  });
  // テキストfadein,out(.img-desc_2)
  $('.work-image_2').on('mouseout', function () {
    $('.img-desc_2').stop(true).animate({
      opacity: '0'
    }, 1000);
  });
  $('.work-image_2').on('mouseover', function () {
    $('.img-desc_2').stop(true).animate({
      opacity: '1'
    }, 1000);
  });

  // テキストfadein,out(.img-desc_3)
  $('.work-image_3').on('mouseout', function () {
    $('.img-desc_3').stop(true).animate({
      opacity: '0'
    }, 1000);
  });
  $('.work-image_3').on('mouseover', function () {
    $('.img-desc_3').stop(true).animate({
      opacity: '1'
    }, 1000);
  });


  // スムーススクロール

  // ページ内のリンクをクリックした時に動作する
  $('a[href^="#"]').click(function () {
    // リンクを取得
    let href = $(this).attr("href");
    // ジャンプ先のid名をセット
    let target = $(href == "#" || href == "" ? 'html' : href);
    // トップからジャンプ先の要素までの距離を取得
    let position = target.offset().top;
    // animateでスムーススクロールを行う
    // 600はスクロール速度で単位はミリ秒
    $("html, body").animate({ scrollTop: position }, 10, "swing");
    return false;
  });

})