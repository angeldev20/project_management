<?php 
/**
 * @file        Login View
 * @author      Luxsys <support@freelancecockpit.com>
 * @copyright   By Luxsys (http://www.freelancecockpit.com)
 * @version     2.5.0
 */

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <META Http-Equiv="Cache-Control" Content="no-cache">
    <META Http-Equiv="Pragma" Content="no-cache">
    <META Http-Equiv="Expires" Content="0">
    <meta name="robots" content="none" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="18000">


    <title><?=$core_settings->company;?></title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="<?=base_url()?>assets/blueline/css/bootstrap.min.css?ver=<?=$core_settings->version;?>" rel="stylesheet">
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/animate.css?ver=<?=$core_settings->version;?>" />
    <link rel="stylesheet" href="<?=base_url()?>assets/blueline/css/plugins/nprogress.css" />
    <link href="<?=base_url()?>assets/blueline/css/blueline.css?ver=<?=$core_settings->version;?>" rel="stylesheet">
    <link href="<?=base_url()?>assets/blueline/css/user.css?ver=<?=$core_settings->version;?>" rel="stylesheet" />
    <!--link rel="stylesheet" href="http://reddotdev.co.uk/spera-crm/assets/blueline/css/app.css?ver=3.0.4.3"/-->
    <link rel="stylesheet" href="<?= base_url() ?>assets/blueline/css/app.css?ver=<?= $core_settings->version; ?>"/>
    <?=get_theme_colors($core_settings);?>
    <!-- Google Tag Manager -->
      <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
              j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
              'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
          })(window,document,'script','dataLayer','GTM-K6JPGLH');</script>
    <!-- End Google Tag Manager -->
    <script type="text/javascript">
  WebFontConfig = {
    google: { families: [ 'Open+Sans:400italic,400,300,600,700:latin' ] }
  };
  (function() {
    var wf = document.createElement('script');
    wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
      '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
    wf.type = 'text/javascript';
    wf.async = 'true';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(wf, s);
  })(); </script>
     <link rel="SHORTCUT ICON" href="<?php echo (!empty($core_settings->favicon)) ? 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $core_settings->favicon : 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/default/files/media/avatar.png'; ?>"/>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="signup">
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K6JPGLH"
                    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  <span class="body-bg" style="background-image:url('https://s3-us-west-2.amazonaws.com/spera-production/assets/images/backgrounds/bg.png')"></span>
  <div class="container-fluid">
      <div class="row" style="margin-bottom:0px;display: flex;">
        <?=$yield?>
      </div>
    </div>
     <!-- Notify -->
    <?php if($this->session->flashdata('message')) { $exp = explode(':', $this->session->flashdata('message'))?>
        <div class="notify <?=$exp[0]?>"><?=$exp[1]?></div>
    <?php } ?>
    <script src="/assets/blueline/js/plugins/jquery-3.2.1.min.js"></script>
    <script src="/assets/blueline/js/plugins/jquery-migrate-3.0.0.min.js"></script>
    <script src="<?=base_url()?>assets/blueline/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/velocity.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/velocity.ui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/validator.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/blueline/js/plugins/nprogress.js"></script>
    <script type="text/javascript">
            $(document).ready(function(){
              fade = "Left";
              <?php if($core_settings->login_style == "center"){ ?>
                fade = "Up";
              <?php }?>
              $("form").validator();

           $(".form-signin").addClass("animated fadeIn"+fade);
           $( ".fadeoutOnClick" ).on( "click", function(){
              NProgress.start();
              $(".form-signin").addClass("animated fadeOut"+fade);
              NProgress.done();
            });
                <?php if($error == "true") { ?>
                    $("#error").addClass("animated shake"); 
                <?php } ?>

                //notify 
            $('.notify').velocity({
                  opacity: 1,
                  right: "10px",
                }, 900, function() {
                  $('.notify').delay( 4000 ).fadeOut();
                });

             /* 2.5.0 Form styling */

            $( ".form-control" ).each(function( index ) {          
              if ($( this ).val().length > 0 ) {
                    $( this ).closest('.form-group').addClass('filled');
                  }
            });
            $( "select.chosen-select" ).each(function( index ) {          
              if ($( this ).val().length > 0 ) {
                    $( this ).closest('.form-group').addClass('filled');
                  }
            });

            $( ".form-control" ).on( "focusin", function(){
                  $(this).closest('.form-group').addClass("focus");
              });
            $( ".chosen-select" ).on( "chosen:showing_dropdown", function(){
                  $(this).closest('.form-group').addClass("focus");
              });
            $( ".chosen-select" ).on( "chosen:hiding_dropdown", function(){
                  $(this).closest('.form-group').removeClass("focus");
              });
            
            $( ".form-control" ).on( "focusout", function(){
                  $(this).closest('.form-group').removeClass("focus");
                  if ($(this).val().length > 0 ) {
                      $(this).closest('.form-group').addClass('filled');
                  } else {
                      $(this).closest('.form-group').removeClass('filled');
                  }
              });
             
      });
            

        </script>
    <?php include('footer.phtml'); ?>
  </body>
</html>
