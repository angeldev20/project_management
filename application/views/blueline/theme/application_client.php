<?php
/**
 * @file        Application View
 * @author      Luxsys <support@freelancecockpit.com>
 * @copyright   By Luxsys (http://www.freelancecockpit.com)
 * @version     3.x.x
 */

$act_uri         = $this->uri->segment( 1, 0 );
$lastsec         = $this->uri->total_segments();
$act_uri_submenu = $this->uri->segment( $lastsec );
if ( ! $act_uri ) {
    $act_uri = 'dashboard';
}
if ( is_numeric( $act_uri_submenu ) ) {
    $lastsec         = $lastsec - 1;
    $act_uri_submenu = $this->uri->segment( $lastsec );
}
$message_icon = false;
$this->load->helper('cookie');
$navopen = get_cookie('navopen');
$navopen = ($navopen !== "nav-close");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
    <meta http-equiv="Pragma" content="no-cache"/>
    <meta http-equiv="Expires" content="0"/>
    <meta name="robots" content="none"/>
    <link rel="SHORTCUT ICON"
          href="<?php echo ( ! empty( $core_settings->favicon ) ) ? 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION["accountUrlPrefix"] . '/' . $core_settings->favicon : 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/default/files/media/avatar.png'; ?>"/>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i" rel="stylesheet">
    <title><?= $core_settings->company; ?></title>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-K6JPGLH');</script>
    <!-- End Google Tag Manager -->
    <script>
        var app =<?=json_encode( [
                                     "base_url"   => base_url(),
                                     "mentions"   => $mention_names,
                                     "token"      => $token,
                                     "token_name" => $token_name
                                 ] )?>;
    </script>

    <!-- Bootstrap core CSS and JS -->
    <script src="<?= base_url() ?>assets/blueline/js/plugins/jquery-3.2.1.min.js?ver=<?= $core_settings->version; ?>"></script>
    <script src="<?= base_url() ?>assets/blueline/js/plugins/jquery-migrate-3.0.0.min.js"></script>

    <!-- Google Font Loader -->
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
    <?php
    if ( ENVIRONMENT == 'development' && 0 ) {
        ?>
        <link rel="stylesheet" href="<?= base_url() ?>assets/blueline/css/blueline.css"/>
        <?php
    }
    ?>
    <link rel="stylesheet" href="<?= base_url() ?>assets/blueline/css/user.css?ver=<?= $core_settings->version; ?>"/>
    <?= get_theme_colors( $core_settings ); ?>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/blueline/css/plugins/formbuilder.css"/>
</head>

<body class="<?php echo $navopen ? "" : "nav-close" ?>">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K6JPGLH"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div id="mainwrapper">
    <div class="side" style="<?php echo $navopen ? "width: 200px;" : "width: 55px;" ?>">
        <div class="branding" style="<?php echo $navopen ? "width: 200px;" : "width: 55px;" ?>">
            <a class="navbar-brand" href="#" style="<?php echo $navopen ? "" : "display: none;" ?>">
                <img
                    onerror="this.style.display='none'"
                    src="https://spera-<?= ENVIRONMENT ?>.s3-us-west-2.amazonaws.com/<?= $_SESSION["accountUrlPrefix"] ?>/<?= $core_settings->logo; ?>"
                    alt="<?= $core_settings->company; ?>">
            </a>
            <a href="#" class="menu-trigger"><i class="<?php echo $navopen ? "far fa-arrow-to-left" : "fa fa-bars" ?>"></i></a>
        </div>
        <i class="icon dripicons-menu topbar__icon fc-dropdown--trigger hidden"></i>
        <div class="sidebar-bg" style="<?php echo $navopen ? "width: 200px;" : "width: 55px;" ?>"></div>
        <div class="clearfix"></div>
        <div class="sidebar">
            <div class="navbar-header" style="display: none;">
                <a class="navbar-brand" href="#" style="display: none;">
                    <img
                        onerror="this.style.display='none'"
                        src="https://spera-<?= ENVIRONMENT ?>.s3-us-west-2.amazonaws.com/<?= $_SESSION["accountUrlPrefix"] ?>/<?= $core_settings->logo; ?>"
                        alt="<?= $core_settings->company; ?>">
                </a>
            </div>

            <ul class="nav nav-sidebar">
                <h4 style="<?php echo $navopen ? "" : "display: none;" ?>"><?= $this->lang->line( 'application_navigation' ); ?></h4>
                <?php foreach ( $menu as $key => $value ) { ?>
                    <?php
                    if ( strtolower( $value->link ) == "cmessages" ) {
                        $message_icon = true;
                    }
                    ?>
                    <li id="<?= strtolower( $value->name ); ?>"
                        class="<?php if ( $act_uri == strtolower( $value->link ) ) {
                            echo "active";
                        } ?>"><a href="<?= site_url( $value->link ); ?>"><span class="menu-icon"><i
                                        class="fa <?= $value->icon; ?>"></i></span><span
                                    class="nav-text" style="<?php echo $navopen ? "" : "display: none;" ?>"><?php echo $this->lang->line( 'application_' . $value->link ); ?></span>
                            <?php if ( strtolower( $value->link ) == "cmessages" && $messages_new[0]->amount != "0" ) { ?>
                                <span class="notification-badge"><?= $messages_new[0]->amount; ?></span><?php } ?>
                            <?php if ( strtolower( $value->link ) == "quotations" && $quotations_new[0]->amount != "0" ) { ?>
                                <span class="notification-badge"><?= $quotations_new[0]->amount; ?></span><?php } ?>
                            <?php if ( strtolower( $value->link ) == "cestimates" && $estimates_new[0]->amount != "0" ) { ?>
                                <span class="notification-badge"><?= $estimates_new[0]->amount; ?></span><?php } ?>

                        </a></li>
                <?php } ?>
            </ul>

          

        </div>
        
    </div>

    <div class="content-area" onclick="" style="<?php echo $navopen ? "margin-left: 200px;" : "margin-left: 55px;" ?>">
        <div class="mainnavbar">
            <div class="topbar__left">
                <div class="top-menu">
                    <div class="fc-dropdown shortcut-menu grid">
                        <div class="grid__col-6 shortcut--item"><i
                                    class="ion-ios-paper-outline shortcut--icon"></i> <?= $this->lang->line( 'application_create_invoice' ); ?>
                        </div>
                        <div class="grid__col-6 shortcut--item"><i
                                    class="ion-ios-lightbulb shortcut--icon"></i> <?= $this->lang->line( 'application_create_project' ); ?>
                        </div>
                        <div class="grid__col-6 shortcut--item"><i
                                    class="ion-ios-pricetags shortcut--icon"></i> <?= $this->lang->line( 'application_create_ticket' ); ?>
                        </div>
                        <div class="grid__col-6 shortcut--item"><i
                                    class="ion-ios-email shortcut--icon"></i> <?= $this->lang->line( 'application_write_messages' ); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="topbar__center">
                <span><?= $title; ?></span>
            </div>
            <div class="topbar__right">
                <div class="add-icon" style="position: relative;">
                    <a href="#" type="button" class="dropdown-toggle transparent" data-toggle="dropdown"
                       aria-expanded="false">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu--small" role="menu">
                        <li><a href="<?= base_url() ?>projects/create" data-toggle="mainmodal">Add Project</a></li>
                    </ul>
                </div>
                <div class="profile-icon">
                    <?php $userimage = get_user_pic( $this->client->userpic, $this->client->email ); ?>
                    <?php if ( $userimage ) { ?>
                        <img class="img-circle topbar-userpic" src="<?= $userimage; ?>" height="35" width="35">
                    <?php } else { ?>
                        <span class="topbar__userimage"><?php echo strtoupper( substr( $this->client->firstname, 0, 1 ) . substr( $this->client->lastname, 0, 1 ) ); ?></span>
                    <?php } ?>
                    <span class="topbar__name fc-dropdown--trigger">
                    <span class="hidden-xs"><?php echo character_limiter( $this->client->firstname . " " . $this->client->lastname, 10 ); ?></span> <i
                                class="far fa-angle-down topbar__drop"></i>
                </span>
                    <div class="fc-dropdown profile-dropdown">
                        <ul>
                            <li>
                                <a id="profile-link" href="<?= base_url() ?>agent" data-toggle="mainmodal">
                            <span class="icon-wrapper"><i
                                        class="icon dripicons-gear"></i></span> <?= $this->lang->line( 'application_profile' ); ?>
                                </a>
                            </li>
                            <li>
                                <a id="my-invoices-link" href="/tinvoices">My Invoices</a>
                            </li>

                            <li class="fc-dropdown__submenu--trigger">
                        <span class="icon-wrapper"><i
                                    class="icon dripicons-chevron-left"></i></span> <?= $current_language; ?>
                                <ul class="fc-dropdown__submenu">
                                    <span class="fc-dropdown__title"><?= $this->lang->line( 'application_languages' ); ?></span>
                                    <?php foreach ( $installed_languages as $entry ) { ?>
                                        <li>
                                            <a href="<?= base_url() ?>agent/language/<?= $entry; ?>">
                                                <img src="<?= base_url() ?>assets/blueline/img/<?= $entry; ?>.png"
                                                     class="language-img"> <?= ucwords( $entry ); ?>
                                            </a>
                                        </li>

                                    <?php } ?>
                                </ul>

                            </li>
                            <li class="profile-dropdown__logout">
                                <a href="<?= site_url( "logout" ); ?>"
                                   title="<?= $this->lang->line( 'application_logout' ); ?>">
                                    <?= $this->lang->line( 'application_logout' ); ?> <i
                                            class="icon dripicons-power pull-right"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="topbar__clear"></div>
        </div>
        <div class="row mainnavbar" style="display: none;">
            <div class="topbar__left noselect">
                <a href="#" class="menu-trigger"><i class="ion-navicon visible-xs"></i></a>
                <i class="icon dripicons-menu topbar__icon fc-dropdown--trigger hidden"></i>
                <div class="fc-dropdown shortcut-menu grid">
                    <div class="grid__col-6 shortcut--item"><i
                                class="ion-ios-paper-outline shortcut--icon"></i> <?= $this->lang->line( 'application_create_invoice' ); ?>
                    </div>
                    <div class="grid__col-6 shortcut--item"><i
                                class="ion-ios-lightbulb shortcut--icon"></i> <?= $this->lang->line( 'application_create_project' ); ?>
                    </div>
                    <div class="grid__col-6 shortcut--item"><i
                                class="ion-ios-pricetags shortcut--icon"></i> <?= $this->lang->line( 'application_create_ticket' ); ?>
                    </div>
                    <div class="grid__col-6 shortcut--item"><i
                                class="ion-ios-email shortcut--icon"></i> <?= $this->lang->line( 'application_write_messages' ); ?>
                    </div>
                </div>
                <i class="icon dripicons-bell topbar__icon fc-dropdown--trigger" data-placement="bottom"
                   title="<?= $this->lang->line( 'application_alerts' ); ?>"><?php if ( $notification_count > 0 ) { ?>
                        <span class="topbar__icon_alert"></span><?php } ?></i>
                <div class="fc-dropdown notification-center">
                    <div class="notification-center__header">
                        <a href="#" class="active"><?= $this->lang->line( 'application_alerts' ); ?>
                            (<?= $notification_count; ?>)</a>
                        <!-- <a href="#"><?= $this->lang->line( 'application_announcements' ); ?></a> -->
                    </div>
                    <ul class="notificationlist">
                        <?php
                        foreach ( $notification_list as $value ): ?>
                            <li class="">
                                <p class="truncate"><?= $value; ?></p>
                            </li>
                        <?php endforeach; ?>
                        <?php if ( $notification_count == 0 ) { ?>
                            <li><p class="truncate"><?= $this->lang->line( 'application_no_events_yet' ); ?></p></li>
                        <?php } ?>
                    </ul>
                </div>

                
                <?php if ( $message_icon ) { ?>
                    <span class="hidden-xs">
                  <a href="<?= site_url( "cmessages" ); ?>" title="<?= $this->lang->line( 'application_messages' ); ?>">
                     <i class="icon dripicons-inbox topbar__icon"></i>
                  </a>
              </span>
                <?php } ?>
                <!-- <i class="ion-ios-search-strong topbar__icon shortcut-menu--trigger"></i> -->
            </div>
            <div class="topbar noselect">
                <?php $userimage = get_user_pic( $this->client->userpic, $this->client->email ); ?>

                <img class="img-circle topbar-userpic" src="<?= $userimage; ?>" height="21px">
                <span class="topbar__name fc-dropdown--trigger">
          <span class="hidden-xs"><?php echo character_limiter( $this->client->firstname . " " . $this->client->lastname, 25 ); ?></span> <i
                            class="icon dripicons-chevron-down topbar__drop"></i>
      </span>
                <div class="fc-dropdown profile-dropdown">
                    <ul>
                        <li>
                            <a href="<?= site_url( "agent" ); ?>" data-toggle="mainmodal">
                                <span class="icon-wrapper"><i
                                            class="icon dripicons-gear"></i></span> <?= $this->lang->line( 'application_profile' ); ?>
                            </a>
                        </li>

                        <li class="fc-dropdown__submenu--trigger">
                            <span class="icon-wrapper"><i
                                        class="icon dripicons-chevron-left"></i></span> <?= $current_language; ?>
                            <ul class="fc-dropdown__submenu">
                                <span class="fc-dropdown__title"><?= $this->lang->line( 'application_languages' ); ?></span>
                                <?php foreach ( $installed_languages as $entry ) { ?>
                                    <li>
                                        <a href="<?= base_url() ?>agent/language/<?= $entry; ?>">
                                            <img src="<?= base_url() ?>assets/blueline/img/<?= $entry; ?>.png"
                                                 class="language-img"> <?= ucwords( $entry ); ?>
                                        </a>
                                    </li>

                                <?php } ?>
                            </ul>

                        </li>
                        <li class="profile-dropdown__logout">
                            <a href="<?= site_url( "logout" ); ?>"
                               title="<?= $this->lang->line( 'application_logout' ); ?>">
                                <?= $this->lang->line( 'application_logout' ); ?> <i
                                        class="icon dripicons-power pull-right"></i>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        <?= $yield ?>

    </div>
    <!-- Notify -->
    <?php if ( $this->session->flashdata( 'message' ) ) {
        $exp = explode( ':', $this->session->flashdata( 'message' ) ) ?>
        <div class="notify <?= $exp[0] ?>"><?= $exp[1] ?></div>
    <?php } ?>
    <div class="ajax-notify"></div>

    <!-- Modal -->
    <div class="modal fade" id="mainModal" data-easein="flipXIn" tabindex="-1" role="dialog" data-backdrop="static"
         aria-labelledby="mainModalLabel" aria-hidden="true"></div>
    <div class="modal fade form-popup in" id="addTeamModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="far fa-times"></i>
                    </button>
                    <h4 class="modal-title">New Team Member</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <form id="inviteForm">
                            <div class="form-group">
                                <input name="firstname" class="form-control" placeholder="Name" value="" type="text">
                            </div>
                            <div class="form-group">
                                <input name="email" class="form-control" placeholder="Email" value="" type="text">
                            </div>
                            <div class="form-group">
                                <input name="company" class="form-control" placeholder="Company Name" value="" type="hidden">
                            </div>
                            <div id="more-options-div" style="display: none;">
                                <div class="form-group">
                                    <select name="queue" class="form-control">
                                        <option value="">Select Queue</option>
                                        <option value="1">Service</option>
                                        <option value="2">Second Level</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="status" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="admin" class="form-control">
                                        <option value="">Super Admin</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-submit">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="#" id="more-options"><i class="fa fa-sliders"></i>&nbsp;More options</a>
                                        <a href="#" id="less-options" style="display: none;"><i class="fa fa-sliders"></i>&nbsp;Less options</a>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <input class="btn btn-success" value="Save &amp; Close" type="submit">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blueline Js -->
    <script type="text/javascript"
            src="<?= base_url() ?>assets/blueline/js/app.js?ver=<?= $core_settings->version; ?>"></script>
    <?php if ( file_exists( "assets/blueline/js/locales/flatpickr_" . $current_language . ".js" ) ) { ?>
        <script type="text/javascript"
                src="<?= base_url() ?>assets/blueline/js/locales/flatpickr_<?= $current_language ?>.js?ver=<?= $core_settings->version; ?>"></script>
    <?php } ?>
    <?php
    if ( ENVIRONMENT == 'development' && 0 ) {
        ?>
        <script type="text/javascript" src="<?= base_url() ?>assets/blueline/js/blueline.js"></script>
        <?php
    }
    ?>
</div> <!-- Mainwrapper end -->
<!--
<script type="text/javascript" src="<?= base_url() ?>assets/blueline/js/plugins/formbuilder-vendor.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/blueline/js/plugins/formbuilder.js"></script>
-->
<script async type="text/javascript" src="<?= base_url() ?>react_bundle/components.js"></script>
<script async type="text/javascript" src="<?= base_url() ?>react_bundle/bundle.js"></script>

<script type="text/javascript" charset="utf-8">

    function flatdatepicker(activeform) {

        Flatpickr.localize(Flatpickr.l10ns.<?=$current_language?>);
        var required = "required";
        if ($(".datepicker").hasClass("not-required")) {
            required = "";
        }
        var datepicker = flatpickr('.datepicker', {
            dateFormat: 'Y-m-d',
            timeFormat: '<?=$timeformat;?>',
            time_24hr: <?=$time24hours;?>,
            altInput: true,
            'static': true,
            altFormat: '<?=$dateformat?>',
            altInputClass: 'form-control ',
            onChange: function (selectedDates, dateStr, instance) {
                if (activeform && !$(".datepicker").hasClass("not-required")) {
                    activeform.validator('validate');
                }

                if ($.inArray('datepicker-linked', instance.element.classList) == "-1" && $(".datepicker-linked").length == 1) {
                    datepickerLinked.set("minDate", dateStr);
                }
            }
        });
        var required = "required";
        if ($(".datepicker-time").hasClass("not-required")) {
            required = "";
        }
        var datepicker = flatpickr('.datepicker-time', {
            //dateFormat: 'U', 
            timeFormat: '<?=$timeformat;?>',
            time_24hr: <?=$time24hours;?>,
            altInput: true,
            altInputClass: 'form-control ',
            altFormat: '<?=$dateformat?> <?=$timeformat;?>',
            onChange: function (selectedDates, dateStr, instance) {
                if (activeform && !$(".datepicker-time").hasClass("not-required")) {
                    activeform.validator('validate');
                }
                if ($.inArray('datepicker-time-linked', instance.element.classList) == "-1" && $(".datepicker-time-linked").length == 1) {
                    datepicker[1].set("minDate", dateStr);
                }
            }
        });

        if ($(".datepicker-time-unix").hasClass("not-required")) {
            required = "";
        }
        var datepicker = flatpickr('.datepicker-time-unix', {
            dateFormat: 'U',
            //timeFormat: '<?=$timeformat;?>',
            time_24hr: <?=$time24hours;?>,
            altInput: true,
            //static:true,
            altInputClass: 'form-control ',
            altFormat: '<?=$dateformat?> <?=$timeformat;?>',
            onChange: function (selectedDates, dateStr, instance) {
                if (activeform && !$(".datepicker-time-unix").hasClass("not-required")) {
                    activeform.validator('validate');
                }


            },
            onValueUpdate: function (selectedDates, dateStr, instance) {
                timediff = $(".datepicker-time-unix.end_time").val() - $(".datepicker-time-unix.start_time").val();
                if (timediff > 0) {
                    timediff = timediff.secondsToHoursAndMinutes();
                    $(".hours").val(timediff[0]);
                    $(".minutes").val(timediff[1]);
                }
            }
        });
        if ($(".datepicker-linked").hasClass("not-required")) {
            var required = "";
        } else {
            var required = "required";
        }
        var datepickerLinked = flatpickr('.datepicker-linked', {
            dateFormat: 'Y-m-d',
            timeFormat: '<?=$timeformat;?>',
            time_24hr: <?=$time24hours;?>,
            altInput: true,
            altFormat: '<?=$dateformat?>',
            static: true,
            altInputClass: 'form-control ',
            onChange: function (d) {
                if (activeform && !$(".datepicker-linked").hasClass("not-required")) {
                    activeform.validator('validate');
                }
            }
        });
        //set dummyfields to be required
        $(".required").attr('required', 'required');

    }
    flatdatepicker();

    $(document).ready(function () {


        sorting_list("<?=base_url();?>");

        if ($("form").length > 0)
            $("form").validator();

        $("#menu li a, .submenu li a").removeClass("active");
        if ("" == "<?php echo $act_uri_submenu; ?>") {
            $("#sidebar li a").first().addClass("active");
        }
        <?php if($act_uri_submenu != "0"){ ?>$(".submenu li a#<?php echo $act_uri_submenu; ?>").parent().addClass("active");<?php } ?>
        $("#menu li#<?php echo $act_uri; ?>").addClass("active");

        //Datatables

        var dontSort = [];
        $('.data-sorting thead th').each(function () {
            if ($(this).hasClass('no_sort')) {
                dontSort.push({"bSortable": false});
            } else {
                dontSort.push(null);
            }
        });

        //initDataTables();

        $(document).on('onComponentLoad', function () {
            initDataTables();
        });

        function initDataTables() {
            $('table.data').each(function () {
                if (!$.fn.dataTable.isDataTable($(this))) {
                    $(this).dataTable({
                        "initComplete": function () {
                            var api = this.api();
                            api.$('td.add-to-search').click(function () {
                                api.search($(this).data("tdvalue")).draw();
                            });
                        },
                        "iDisplayLength": 25,
                        stateSave: true,
                        "bLengthChange": false,
                        "aaSorting": [[0, 'desc']],
                        "oLanguage": {
                            "sSearch": "",
                            "sInfo": "<?=$this->lang->line( 'application_showing_from_to' );?>",
                            "sInfoEmpty": "<?=$this->lang->line( 'application_showing_from_to_empty' );?>",
                            "sEmptyTable": "<?=$this->lang->line( 'application_no_data_yet' );?>",
                            "oPaginate": {
                                "sNext": '<i class="far fa-angle-right"></i>',
                                "sPrevious": '<i class="far fa-angle-left"></i>',
                            }
                        }
                    });
                }
            });

            $('table.data-media').each(function () {
                if (!$.fn.dataTable.isDataTable($(this))) {
                    $(this).dataTable({
                        "iDisplayLength": 15,
                        stateSave: true,
                        "bLengthChange": false,
                        "bFilter": false,
                        "bInfo": false,
                        "aaSorting": [[0, 'desc']],
                        "oLanguage": {
                            "sSearch": "",
                            "sInfo": "<?=$this->lang->line( 'application_showing_from_to' );?>",
                            "sInfoEmpty": "<?=$this->lang->line( 'application_showing_from_to_empty' );?>",
                            "sEmptyTable": " ",
                            "oPaginate": {
                                "sNext": '<i class="far fa-angle-right"></i>',
                                "sPrevious": '<i class="far fa-angle-left"></i>',
                            }
                        }
                    });
                }
            });
            $('table.data-no-search').each(function () {
                if (!$.fn.dataTable.isDataTable($(this))) {
                    $(this).dataTable({
                        "iDisplayLength": 20,
                        stateSave: true,
                        "bLengthChange": false,
                        "bFilter": false,
                        "bInfo": false,
                        "aaSorting": [[1, 'desc']],
                        "oLanguage": {
                            "sSearch": "",
                            "sInfo": "<?=$this->lang->line( 'application_showing_from_to' );?>",
                            "sInfoEmpty": "<?=$this->lang->line( 'application_showing_from_to_empty' );?>",
                            "sEmptyTable": " ",
                            "oPaginate": {
                                "sNext": '<i class="far fa-angle-right"></i>',
                                "sPrevious": '<i class="far fa-angle-left"></i>',
                            }
                        },
                        fnDrawCallback: function (settings) {
                            $(this).parent().toggle(settings.fnRecordsDisplay() > 0);
                            if (settings._iDisplayLength > settings.fnRecordsDisplay()) {
                                $(settings.nTableWrapper).find('.dataTables_paginate').hide();
                            }

                        }

                    });
                }
            });
            $('table.data-sorting').each(function () {
                if (!$.fn.dataTable.isDataTable($(this))) {
                    $(this).dataTable({
                        "iDisplayLength": 25,
                        "bLengthChange": false,
                        "aoColumns": dontSort,
                        "aaSorting": [[1, 'desc']],
                        "oLanguage": {
                            "sSearch": "",
                            "sInfo": "<?=$this->lang->line( 'application_showing_from_to' );?>",
                            "sInfoEmpty": "<?=$this->lang->line( 'application_showing_from_to_empty' );?>",
                            "sEmptyTable": "<?=$this->lang->line( 'application_no_data_yet' );?>",
                            "oPaginate": {
                                "sNext": '<i class="far fa-angle-right"></i>',
                                "sPrevious": '<i class="far fa-angle-left"></i>',
                            }
                        }
                    });
                }
            });
            $('table.data-small').each(function () {
                if (!$.fn.dataTable.isDataTable($(this))) {
                    $(this).dataTable({
                        "iDisplayLength": 5,
                        "bLengthChange": false,
                        "aaSorting": [[2, 'desc']],
                        "oLanguage": {
                            "sSearch": "",
                            "sInfo": "<?=$this->lang->line( 'application_showing_from_to' );?>",
                            "sInfoEmpty": "<?=$this->lang->line( 'application_showing_from_to_empty' );?>",
                            "sEmptyTable": "<?=$this->lang->line( 'application_no_data_yet' );?>",
                            "oPaginate": {
                                "sNext": '<i class="far fa-angle-right"></i>',
                                "sPrevious": '<i class="far fa-angle-left"></i>',
                            }
                        }
                    });
                }
            });

            $('table.data-reports').each(function () {
                if (!$.fn.dataTable.isDataTable($(this))) {
                    $(this).dataTable({
                        "iDisplayLength": 30,
                        colReorder: true,
                        buttons: [
                            'copyHtml5',
                            'excelHtml5',
                            'csvHtml5',
                            'pdfHtml5'
                        ],

                        "bLengthChange": false,
                        "order": [[1, 'desc']],
                        "columnDefs": [
                            {"orderable": false, "targets": 0}
                        ],
                        "oLanguage": {
                            "sSearch": "",
                            "sInfo": "<?=$this->lang->line( 'application_showing_from_to' );?>",
                            "sInfoEmpty": "<?=$this->lang->line( 'application_showing_from_to_empty' );?>",
                            "sEmptyTable": "<?=$this->lang->line( 'application_no_data_yet' );?>",
                            "oPaginate": {
                                "sNext": '<i class="far fa-angle-right"></i>',
                                "sPrevious": '<i class="far fa-angle-left"></i>',
                            }
                        }
                    });
                }
            });
        }

        $('#more-options').click(function() {
            $('#more-options-div').css('display', 'block');
            $('#more-options').css('display', 'none');
            $('#less-options').css('display', 'block');
        });

        $('#less-options').click(function() {
            $('#more-options-div').css('display', 'none');
            $('#more-options').css('display', 'block');
            $('#less-options').css('display', 'none');
        });


        $("#inviteForm").submit(function(e) {

            e.preventDefault();

            var url = "<?= base_url() ?>team/invite";
            var formData = {
                'Content-Type'      : 'multipart/form-data',
                'firstname'         : $('input[name=firstname]').val(),
                'email'             : $('input[name=email]').val(),
                'company'           : '',
                'queue'             : $('select[name=queue] option:selected').val(),
                'status'            : $('select[name=status] option:selected').val(),
                'admin'             : $('select[name=admin] option:selected').val(),
                'fcs_csrf_token'    : '<?= $token;?>'
            };

            $('.btn.btn-success').val("Saving...");
            $('.btn.btn-success').css('opacity', '0.5');
            $('.btn.btn-success').attr('disabled', 'disabled');

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                success: function(response)
                {
                    invitation_send();
                    window.location.reload();
                },
                error: function (response) {
                    invitation_fail();
                    window.location.reload();
                },
            });
        });

        function invitation_send() {
            $.notify({
                message: 'Invitaion sent.'
            },{
                type: "info",
                placement: {
                    from: "bottom",
                    align: "right"
                },
                offset: 20,
                spacing: 10,
                z_index: 1031,
                delay: 2000,
                timer: 1000,
                animate: {
                    enter: 'animated fadeInRight',
                    exit: 'animated fadeOut'
                },
                icon_type: 'class',
                template:   '<div data-notify="container" role="alert" class="notify success" style="width:150px;text-align:center;">\
                    <span data-notify="message">{2}</span>\
                    </div>'
            });
        }

        function invitation_fail() {
            $.notify({
                message: 'Failed to send invitation.'
            },{
                type: "info",
                placement: {
                    from: "bottom",
                    align: "right"
                },
                offset: 20,
                spacing: 10,
                z_index: 1031,
                delay: 2000,
                timer: 1000,
                animate: {
                    enter: 'animated fadeInRight',
                    exit: 'animated fadeOut'
                },
                icon_type: 'class',
                template:   '<div data-notify="container" role="alert" class="notify error" style="width:150px;text-align:center;">\
                    <span data-notify="message">{2}</span>\
                    </div>'
            });
        }

    });


</script>
<?php include( 'footer.phtml' ); ?>
</body>
</html>
