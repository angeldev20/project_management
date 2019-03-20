<?php
/**
 * @file        Fullpage View
 * @author      Luxsys <support@luxsys-apps.com>
 * @copyright   By Luxsys (http://www.luxsys-apps.com)
 * @version     2.2.0
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <META Http-Equiv="Cache-Control" Content="no-cache">
    <META Http-Equiv="Pragma" Content="no-cache">
    <META Http-Equiv="Expires" Content="0">
    <meta name="robots" content="none"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
    <link rel="SHORTCUT ICON"
          href="<?php echo ( ! empty( $core_settings->favicon ) ) ? 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $core_settings->favicon : 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/default/files/media/avatar.png'; ?>"/>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i"
          rel="stylesheet">
    <title><?= $core_settings->company; ?></title>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-K6JPGLH');</script>
    <!-- End Google Tag Manager -->
    <script src="<?= base_url() ?>assets/blueline/js/plugins/jquery-3.2.1.min.js?ver=<?= $core_settings->version; ?>"></script>
    <script src="<?= base_url() ?>assets/blueline/js/plugins/jquery-migrate-3.0.0.min.js"></script>

    <!-- Google Font Loader -->
    <link href="<?= base_url() ?>assets/blueline/css/font-awesome.min.css" rel="stylesheet">
    <script type="text/javascript">
        WebFontConfig = {
            google: {families: ['Open+Sans:400italic,400,300,600,700:latin,latin-ext']}
        };
        (function () {
            var wf = document.createElement('script');
            wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
                '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
            wf.type = 'text/javascript';
            wf.async = 'true';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(wf, s);
        })();
    </script>

    <link rel="stylesheet" href="<?= base_url() ?>assets/blueline/css/app.css?ver=<?= $core_settings->version; ?>"/>
    <link rel="stylesheet" href="<?= base_url() ?>assets/blueline/css/user.css?ver=<?= $core_settings->version; ?>"/>
	<?= get_theme_colors( $core_settings ); ?>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
        html {
            height: 100%;
        }

        body {
            padding-bottom: 40px;
            height: 100%;
        }
    </style>

</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K6JPGLH"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="mainnavbar">
    <div class="text-center">
        <a class="navbar-brand" href="#"><img
                    src="https://spera-<?= ENVIRONMENT ?>.s3-us-west-2.amazonaws.com/<?= $_SESSION["accountUrlPrefix"] ?>/<?= $core_settings->invoice_logo; ?>"
                    alt="<?= $core_settings->company; ?>"/></a>
    </div>
    <div class="topbar__clear"></div>
</div>
<div class="container small-container" style="margin-top: 40px;">

    <img class="hide fullpage-logo" style="max-height: 50px; max-width: 200px; width: auto;"
         src="https://spera-<?= ENVIRONMENT ?>.s3-us-west-2.amazonaws.com/<?= $_SESSION["accountUrlPrefix"] ?>/<?= $core_settings->invoice_logo; ?>"
         alt="<?= $core_settings->company; ?>"/>


    <div>
		<?php if ( $this->session->flashdata( 'message' ) ) {
			$exp = explode( ':', $this->session->flashdata( 'message' ) ) ?>
            <div id="quotemessage" class="alert alert-success"><span><?= $exp[1] ?></span></div>
		<?php } ?>
		<?= $yield ?>
        <br clear="all"/>
    </div>

</div>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/app.js?ver=<?= $core_settings->version; ?>"></script>
<script type="text/javascript"
        src="<?= base_url() ?>assets/blueline/js/locales/flatpickr_<?= $current_language ?>.js?ver=<?= $core_settings->version; ?>"></script>


<script type="text/javascript" charset="utf-8">

    //Validation
    $("form").validator();

    $(document).ready(function () {

        $(".removehttp").change(function (e) {
            $(this).val($(this).val().replace("http://", ""));
        });

    });
</script>
<?php include( 'footer.phtml' ); ?>
</body>
</html>
