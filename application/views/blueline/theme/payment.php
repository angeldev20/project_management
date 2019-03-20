<?php
/**
 * @file        Payment View
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
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-K6JPGLH');</script>
    <!-- End Google Tag Manager -->
    <script src="<?= base_url() ?>assets/blueline/js/plugins/jquery-3.2.1.min.js?ver=<?= $core_settings->version; ?>"></script>
    <script src="<?= base_url() ?>assets/blueline/js/plugins/jquery-migrate-3.0.0.min.js"></script>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K6JPGLH"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="container-fluid">
    <div class="row" style="margin-bottom:0px">
        <?=$yield?>
    </div>
</div>
<!-- Notify -->
<?php if($this->session->flashdata('message')) { $exp = explode(':', $this->session->flashdata('message'))?>
    <div class="notify <?=$exp[0]?>"><?=$exp[1]?></div>
<?php } ?>
<script src="<?= base_url() ?>assets/blueline/js/plugins/jquery-3.2.1.min.js"></script>
<script src="<?= base_url() ?>assets/blueline/js/plugins/jquery-migrate-3.0.0.min.js"></script>
<script src="<?=base_url()?>assets/blueline/js/bootstrap.min.js"></script>
<?php include('footer.phtml'); ?>
</body>
</html>