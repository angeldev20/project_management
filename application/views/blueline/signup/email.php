<?php $attributes = array( 'class' => 'form-signup form-register', 'role' => 'form', 'id' => 'register' ); ?>
<?= form_open( $form_action, $attributes ) ?>
<div class="logo">
    <h1>Confirm your email address</h1>
    <span>Please confirm your email address to finish setting up your account</span>
</div>
<?= form_close() ?><?= form_close() ?>
<div class="form-signin-right">
    <span class="small-logo"></span>
    <div style="display:none;" class="video-button text-center">
        <a data-toggle="modal" href="#video-modal"><i class="fa fa-play-circle-o"></i></a>
    </div>
    <div class="clearfix">
        <div class="col-md-12 text-right">
            <p>Already have an account?</p>
            <a class="btn btn-default" href="/login">Sign in now</a>
        </div>
    </div>
    <div class="modal fade" id="video-modal" data-easein="flipXIn" tabindex="-1" role="dialog"
         data-backdrop="static" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                                class="icon dripicons-cross"></i></button>
                </div>
                <div class="modal-body">
                    <iframe src="https://player.vimeo.com/video/102779883" style="display: block;" width="100%"
                            height="300" frameborder="0" webkitallowfullscreen mozallowfullscreen
                            allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/blueline/js/plugins/jquery-3.2.1.min.js"></script>
<script src="/assets/blueline/js/plugins/jquery-migrate-3.0.0.min.js"></script>
<script type="text/javascript">
    var userAccounts = {};

    function doAccountsAlreadyExistForThisEmail(email) {
        if (userAccounts.data.length > 0) {
            $.each(userAccounts.data, function (index, object) {
                console.log('accountUrlPrefix: ' + object.accountUrlPrefix + ' | accountName: ' + object.accountName);
            });
        }
    }

    function getAccountsForEmail(email) {
        var url = '/api/accountlogin?email=' + email;
        $.ajax({
            url: url,
            type: "GET",
            dataType: "JSON"
        }).done(function (json) {
            if (typeof json.success !== "undefined" && json.success == true) {
                userAccounts.data = json.data;
                doAccountsAlreadyExistForThisEmail(email)
            } else {

            }
        }).fail(function (json) {
            console.log('failed to get HID via ajax');
        });
    }

    function doneValidation($elem, yes) {
        if (yes) {
            $elem.find('i.fa').removeClass('fa-remove');
            $elem.find('i.fa').addClass('fa-check-circle-o');
            $elem.find('i.fa-check-circle-o').css({'color': '#3ca84a'});
        } else {
            $elem.find('i.fa').addClass('fa-remove');
            $elem.find('i.fa').removeClass('fa-check-circle-o');
            $elem.find('i.fa-remove').css({'color': '#dd4848'});
        }
    }

    function goGray($elem) {
        $elem.find('i.fa-check-circle-o').css({'color': '#d3d3d3'});
    }


    $(document).ready(function () {

        var fade = "Left";
        $("form").validator();

        $(".form-signup").addClass("animated fadeIn" + fade);
        $(".fadeoutOnClick").on("click", function () {
            NProgress.start();
            $(".form-signup").addClass("animated fadeOut" + fade);
            NProgress.done();
        });

    });
</script>

