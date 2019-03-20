<?php $attributes = array( 'class' => 'form-signup form-register', 'role' => 'form', 'id' => 'register' ); ?>
<?= form_open( $form_action, $attributes ) ?>
<div class="logo">
    <h1>Setup your domain.</h1>
    <span>What unique URL do you want?</span>
</div>
<?php if ( $error != 'false' ) { ?>
    <div id="error" style="display:block">
		<?= $error ?>
    </div>
<?php } ?>
<div class="row">
    <div class="col-md-12">
        <div class="form-group username-field">
            <label for="username">Domain</label>
            <div class="input-group">
                <input id="username" type="text" name="username" class="required form-control"
                       value="<?php if ( isset( $registerdata ) ) {
					       echo $registerdata['username'];
				       } ?>" maxlength="16" autocomplete="off" required/>
                <div id="validation_rules">
                    <div class="rule clearfix">
                        <div class="col-md-8">All lowercase</div>
                        <div class="col-md-4 text-right lowercase"><i class="fa fa-check-circle-o"></i></div>
                    </div>
                    <div class="rule clearfix">
                        <div class="col-md-8">No spaces</div>
                        <div class="col-md-4 text-right spaces"><i class="fa fa-check-circle-o"></i></div>
                    </div>
                    <div class="rule clearfix">
                        <div class="col-md-8">No special characters</div>
                        <div class="col-md-4 text-right special"><i class="fa fa-check-circle-o"></i></div>
                    </div>
                    <div class="rule clearfix">
                        <div class="col-md-8">Less than 16 characters</div>
                        <div class="col-md-4 text-right character-limit"><i class="fa fa-check-circle-o"></i></div>
                    </div>
                </div>
                <span class="input-group-addon">.spera.io</span>
            </div>
        </div>
		<?php if ( $planTypes ) : ?>
            <div class="form-group" style="display: none;">
                <label for="planType">Plan *</label>
                <select name="planType" class="chosen-select form-control" multiple="">
					<?php foreach ( $planTypes as $planTypeItem ) : ?>
                        <option value="<?php echo $planTypeItem->type; ?>"<?php if ( isset( $planType ) && $planType == $planTypeItem->type ) {
							echo ' selected';
						} ?>><?php echo $planTypeItem->name; ?> ($<?php echo( $planTypeItem->amount / 100 ); ?>
                            /<?php echo $planTypeItem->billingFrequencyDays . ' days'; ?>)
                        </option>
					<?php endforeach; ?>
                </select>
            </div>
		<?php endif; ?>
    </div>

</div>

<div class="row">
    <div class="col-md-12">
        <input onclick="$('.create-account-icon').css('display','block');" id="register_submit" type="submit" class="btn btn-primary" value="CONTINUE" disabled/>
        <i style="display: none;" class="create-account-icon icon dripicons-loading spin-it" id="showloader"></i>
        <div class="forgotpassword"><a target="_blank" class="pull-right" href="https://spera.io/terms-conditions/">Terms &amp; Conditions</a></div>
    </div>
</div>
<?= form_close() ?>

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

    $(document).ready(function () {
        var valid_username = false;

        $("#validation_rules").find('i.fa-check-circle-o').css({'color' : '#d3d3d3'});

        $("#username").on("keyup", function () {
            var $username = $(this).val();

            var $character_limit = $("#validation_rules .character-limit");
            var $lowercase = $("#validation_rules .lowercase");
            var $spaces = $("#validation_rules .spaces");
            var $special = $("#validation_rules .special");

            if ($username.length > 0 && $username.length < 16) {
                doneValidation($character_limit, true);
                valid_username = true;
            } else if ($username.length > 0 && $username.length > 15) {
                doneValidation($character_limit, false);
                valid_username = false;
            } else if ($username.length == 0) {
                doneValidation($character_limit, true);
                valid_username = false;
                $("#validation_rules").find('i.fa-check-circle-o').css({'color' : '#d3d3d3'});
                $character_limit.find('i.fa-check-circle-o').css({'color': '#d3d3d3'});
            } else {
                doneValidation($character_limit, false);
                valid_username = false;
            }

            if ($username.length > 0) {

                if ($username.indexOf(' ') > -1) {
                    doneValidation($spaces, false);
                    valid_username = false;
                } else {
                    doneValidation($spaces, true);
                    valid_username = true;
                }

                var re = /[^a-zA-Z0-9]/;
                if (re.test($username)) {
                    doneValidation($special, false);
                    valid_username = false;
                } else {
                    doneValidation($special, true);
                    valid_username = true;
                }

                var re_cap = /[A-Z]/;

                if (re_cap.test($username)) {
                    doneValidation($lowercase, false);
                    valid_username = false;
                } else{
                    doneValidation($lowercase, true);
                    valid_username = true;
                }
            } else {

            }
            //TODO: test for enabled, disabled, we don't want to enable
            // it if the rest of the forn isn't filled out yet
            if (valid_username) {
                $("#register_submit").removeAttr('disabled');
            } else {
                $("#register_submit").attr('disabled', 'disabled');
            }
        });

        $("#register").on("submit", function () {
            return valid_username;
        });


        var fade = "Left";
        $("form").validator();

        $(".form-signup").addClass("animated fadeIn" + fade);
        $(".fadeoutOnClick").on("click", function () {
            NProgress.start();
            $(".form-signup").addClass("animated fadeOut" + fade);
            NProgress.done();
        });

        //notify
        $('.notify').velocity({
            opacity: 1,
            right: "10px",
        }, 900, function () {
            $('.notify').delay(4000).fadeOut();
        });

        // 2.5.0 Form styling

        $(".form-control").each(function (index) {
            if ($(this).val().length > 0) {
                $(this).closest('.form-group').addClass('filled');
            }
        });
        $("select.chosen-select").each(function (index) {
            if ($(this).val().length > 0) {
                $(this).closest('.form-group').addClass('filled');
            }
        });

        $(".form-control").on("focusin", function () {
            $(this).closest('.form-group').addClass("focus");
        });
        $(".chosen-select").on("chosen:showing_dropdown", function () {
            $(this).closest('.form-group').addClass("focus");
        });
        $(".chosen-select").on("chosen:hiding_dropdown", function () {
            $(this).closest('.form-group').removeClass("focus");
        });

        $(".form-control").on("focusout", function () {
            $(this).closest('.form-group').removeClass("focus");
            if ($(this).val().length > 0) {
                $(this).closest('.form-group').addClass('filled');
            } else {
                $(this).closest('.form-group').removeClass('filled');
            }
        });

    });
</script>

