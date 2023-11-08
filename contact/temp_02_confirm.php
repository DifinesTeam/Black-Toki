<!doctype html>
<html lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=0.5, maximum-scale=1, user-scalable=0" />
<meta name="description" content="Web3 Investment Firm Helping projects to secure funding and build out the crypto space." />
<meta name="keywords" content="" />
<title>Black Toki | Web3 Investment Firm</title>
<link rel="icon" href="../favicon.ico" id="favicon">
<link rel="stylesheet" type="text/css" href="../common/css/import.css" media="all" />
<script type="text/javascript" src="../common/js/common.js" charset="utf-8"></script>
</head>
<body>
<div id="wrapper" class="top"><a name="page_top" id="page_top"></a> 
  
  <!--navi start--> 
  
  <!--navi start-->
  <header id="header">
    <h1 id="logo"><a href="../" class="js_hvr-btn"><img src="../images/common/logo.svg" alt="Black Toki"/></a></h1>
    <p class="sp_navi-btn"><span></span><span></span><span></span></p>
    <nav class="navi">
      <ul class="navi_list line_anime_wrp">
        <li><a href="../#box1">About</a></li>
        <li><a href="../#total">Ventures</a></li>
        <li><a href="../#box3">Portfolio</a></li>
        <li><a href="../contact">Contact</a></li>
      </ul>
    </nav>
  </header>
  <!--navi end--> 
  <!--contents start--> 
  <a name="content_top" id="content_top"></a> 
  <!--contents start-->
  <div id="contents"> 
    <!--main start-->
    <main id="main"> 
      <!--box start-->
      <article id="contact" class="box cont_box cont_flex">
        <section id="contact_main">
          <h2 class="top_tit"><span class="top_tit_en">Let's Work Togehter</span></h2>
          <p class="m_b60">Thank you for your input. If you are satisfied with the content below, please press the "Submit" button.</p>
        </section>
        <section id="contact_form">
    <form action="index.php" method="post">
        <input type="hidden" name="cmd" value="comp" />
        <input type='hidden' name='_token' value='<?php echo $token; ?>' />
          <ul class="m_b25">
            <li>
              <h3>Name</h3>
                <?= $data['name1'] ?> <?= $data['name2'] ?>
            </li>
            <li>
              <h3>Email</h3>
                <?= $data['email'] ?>
            </li>
            <li>
              <h3>Subject</h3>
                <?= $data['subject'] ?>
              </li>
            <li>
              <h3>Message</h3>
                <?= $data['comment'] ?>
              </li>
          </ul>
          <p class="contact_submit">
                <input type="submit" name="_send" class="link_btn" value="Submit">
                <input type="submit" name="_back" class="link_btn" value="Back" />
          </p>
    </form>
        </section>
      </article>
      <!--box end--> 
      
    </main>
    <!--main end--> 
  </div>
  <!--contents end--> 
  
  <!-- footer start -->
  <footer id="footer">
    <div class="cont cont_flex">
      <p class="footer_logo"><img src="../images/footer_logo.png" alt=""/></p>
      <p class="footer_copyright">Copyright © 2023 Black Toki | All Rights Reserved</p>
    </div>
  </footer>
  <!-- footer end -->
  <p id="footer_pagetop"><a href="#page_top"><img src="../images/common/footer_pagetop.svg" alt="page top" class="js_hvr-btn" /></a></p>
  <div id="stalker"></div>
</div>
</body>
</html>
