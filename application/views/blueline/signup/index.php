<?php $attributes = array( 'class' => 'form-signup form-register', 'role' => 'form', 'id' => 'register' ); ?>
<?= form_open( $form_action, $attributes ) ?>
<?php if ($emailSent) : ?>
    <div class="logo">
        <h1>Confirm your email address</h1>
        <span>Please confirm your email address to finish setting up your account</span>
    </div>
<?php else: ?>
    <div class="logo">
        <h1>Get a free trial.</h1>
        <span>Try it Spera risk-free. No Credit card required.</span>
    </div>
    <?php if ( $error != 'false' ) { ?>
        <div id="error" style="color: red; display:block;">
            <?= $error ?>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="firstname"><?= $this->lang->line( 'application_firstname' ); ?></label>
                <input id="firstname" type="text" name="firstname" class=" form-control"
                       value="<?php if ( isset( $registerdata ) ) {
                           echo $registerdata['firstname'];
                       } ?>" placeholder="e.x. Jane" required/>
            </div>
            <div class="form-group">
                <label for="lastname"><?= $this->lang->line( 'application_lastname' ); ?></label>
                <input id="lastname" type="text" name="lastname" class="required form-control"
                       value="<?php if ( isset( $registerdata ) ) {
                           echo $registerdata['lastname'];
                       } ?>" placeholder="e.x. Smith" required/>
            </div>

            <div class="form-group <?php if ( isset( $registerdata ) ) {
                echo 'has-error';
            } ?>">
                <label for="email"><?= $this->lang->line( 'application_email' ); ?></label>
                <input id="email" type="email" name="email" class="required email form-control"
                       value="<?php if ( isset( $registerdata ) ) {
                           echo $registerdata['email'];
                       } ?>" placeholder="e.x. youremail@company.com" required/>
            </div>
            <div class="form-group password-field">
                <label for="password"><?= $this->lang->line( 'application_password' ); ?></label>
                <div class="input-group" style="width: 100%;">
                    <input id="password" autocomplete="off" type="password" name="password" class="form-control" value=""
                           placeholder="At least 6 characters with at least 2 symbols"
                           pattern="^\S{6,}$"
                           required/>
                    <div id="validation_rules" class="col-md-12">
                        <div class="rule clearfix">
                            <div class="col-md-8">No spaces</div>
                            <div class="col-md-4 text-right spaces"><i class="far fa-check-circle"></i></div>
                        </div>
                        <div class="rule clearfix">
                            <div class="col-md-8">At least two symbols</div>
                            <div class="col-md-4 text-right special"><i class="far fa-check-circle"></i></div>
                        </div>
                        <div class="rule clearfix">
                            <div class="col-md-8">Greater than 6 characters</div>
                            <div class="col-md-4 text-right character-limit"><i class="far fa-check-circle"></i></div>
                        </div>
                    </div>
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
            <?php if (isset($promoCode)): ?>
                <input id="promoCode" type="hidden" name="promoCode" value="<?php echo $promoCode; ?>" />
            <?php else: ?>
                <div class="form-group">
                    <label for="promoCode">Promo Code (optional)</label>
                    <input id="promoCode" type="text" name="promoCode" class=" form-control"
                           value="<?php if ( isset( $registerdata['promoCode'] ) ) {
                               echo $registerdata['promoCode'];
                           } ?>" placeholder="e.x. A1BDEF" />
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <input id="planType" type="hidden" value="<?php echo (isset($planType)) ? $planType : 'hustle_monthly'; ?>"/>
            <input id="register_submit" type="submit" class="btn btn-primary" value="SIGNUP NOW"/>

            <div class="forgotpassword pull-right"><a target="_blank" href="https://spera.io/terms-conditions/">Terms &amp; Conditions</a></div>
        </div>
    </div>
<?php endif; ?>
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
            $elem.find('i.far').removeClass('fa-times');
            $elem.find('i.far').addClass('fa-check-circle');
            $elem.find('i.fa-check-circle').css({'color': '#3ca84a'});
        } else {
            $elem.find('i.far').addClass('fa-times');
            $elem.find('i.far').removeClass('fa-check-circle');
            $elem.find('i.fa-times').css({'color': '#dd4848'});
        }
    }

    function goGray($elem) {
            $elem.find('i.fa-check-circle').css({'color': '#d3d3d3'});
    }


    $(document).ready(function () {

        var valid_password = false;

        $("#validation_rules").find('i.fa-check-circle').css({'color' : '#d3d3d3'});

        $("#password").on("keyup", function () {
            $('#validation_rules').css('display','inline-block');
            var password = $(this).val();

            var character_limit = $("#validation_rules .character-limit");
            var spaces = $("#validation_rules .spaces");
            var special = $("#validation_rules .special");
            valid_password = false;

            if (password.length > 0) {
                if (password.length > 5) {
                    doneValidation(character_limit, true);
                    valid_password = true;
                }
                else {
                    doneValidation(character_limit, false);
                    valid_password = false;
                }

                if (password.indexOf(' ') > -1) {
                    doneValidation(spaces, false);
                    spaces.find('i.fa-check-circle').css({'color': '#d3d3d3'});
                    valid_password = false;
                } else if (!(password.indexOf(' ') > -1)){
                    doneValidation(spaces, true);
                    valid_password = true;
                }

                var re = /[-\/\\{}\[\]~`_$&+,:;=?@#|'<>.^*()%!]/;

                var symbolCount = 0;
                var passwordArray = password.split("");
                $.each(passwordArray, function( index, passwordCharacter ) {
                    if(re.test(passwordCharacter)) {
                        symbolCount = symbolCount+1;
                    }
                });

                if (symbolCount > 1) {
                    doneValidation(special, true);
                    if (!(password.indexOf(' ') > -1) && password.length > 5) {
                        valid_password = true;
                    }
                } else {
                    goGray(special);
                    valid_password = false;
                }

            } else {
                doneValidation(character_limit, true);
                $("#validation_rules").find('i.fa-check-circle').css({'color' : '#d3d3d3'});
                character_limit.find('i.fa-check-circle').css({'color': '#d3d3d3'});
                doneValidation(spaces, true);
                spaces.find('i.fa-check-circle').css({'color': '#d3d3d3'});
                valid_password = false;
            }

            if (valid_password) {
                $("#register_submit").removeAttr('disabled');
            } else {
                $("#register_submit").attr('disabled', 'disabled');
            }
        });

        $("#register").on("submit", function () {
            return valid_password;
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

        /* 2.5.0 Form styling */

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

        $('#login').submit(function (event) {
            $('.ajax-loader').show();
            $('.form-login-error').html('');
            var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
            var email = $("#emailid").val();
            var password = $("#password").val();

            if (email == "") {
                $('.form-login-error').html('<div id="error">Please enter valid email address!</div>');
            }

            if (!$(this).hasClass("email_validate")) {
                if (email == "") {
                    $('.ajax-loader').hide();
                    $('.success').fadeOut(200).hide();
                    $('.error').fadeOut(200).show();
                } else if (filter.test(email)) {
                    var formData = $(this).serialize();
                    $.ajax({
                        type: "POST",
                        url: "http://reddotdev.co.uk/spera-crm/auth/email_validate/",
                        data: formData,
                        success: function (response) {
                            var resultData = $.parseJSON(response);
                            //console.log('email validate '+data);
                            $('.ajax-loader').hide();

                            if (resultData.validate == 'success') {
                                // console.log(data);
                                $("#showusertypes").html(resultData.html_response);
                                $("#submitbutton").hide();
                                $("#submitbuttonfp").hide();
                                $('#emailid').attr('readonly', true);
                                $('#login').addClass("email_validate");

                            } else {
                                $('.form-login-error').html(resultData.html_response);
                                return false;
                            }
                        },
                        complete: function (data) {
                            $('.ajax-loader').hide();
                            $(".chosen-select").chosen({
                                scroll_to_highlighted: false,
                                disable_search_threshold: 4,
                                width: "100%"
                            });
                        }
                    });
                } else {
                    $('.ajax-loader').hide();
                    $('.form-login-error').html('<div id="error">Please enter valid email address!</div>');
                }
            }
            else {

                var companytype = $('#companytype');
                if (companytype.length > 0 && companytype.val() == 0) {
                    $('.ajax-loader').hide();
                    $('.form-login-error').html('<div id="error">Please select company!</div>');

                } else if (password != '') {
                    $('.ajax-loader').show();
                    var formData = $(this).serialize();
                    $.ajax({
                        type: "POST",
                        url: "http://reddotdev.co.uk/spera-crm/auth/user_validate/",
                        data: formData,
                        success: function (response) {
                            var resultData = $.parseJSON(response);
                            // console.log('user validate '+data);
                            $('.ajax-loader').hide();

                            if (resultData.validate == 'success') {
                                // $("#showusertypes").html(data);
                                $("#submitbutton").hide();
                                $('#emailid').attr('readonly', true);
                                $('#login').addClass("email_validate");

                                //window.location.href = task.return;
                                window.location.href = resultData.html_response;

                            } else {
                                $('.form-login-error').html('<div id="error">' + resultData.html_response + '</div>');
                                // $("#nameError").html("Please Enter Valid Password").addClass("error-msg"); // chained methods
                                return false;
                            }
                        }
                    });
                } else {
                    $('.ajax-loader').hide();
                    $('.form-login-error').html('<div id="error">Password should not be blank!</div>');
                }

            }
            // stop the form from submitting the normal way and refreshing the page
            event.preventDefault();

        });


        $('#forgotpass').submit(function (event) {
            $('.ajax-loader').show();
            $('.form-forgot-error').html('');
            var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
            var email = $("#emailid").val();
            var password = $("#password").val();

            if (!$(this).hasClass("email_validate")) {
                event.preventDefault();
                if (email == "") {
                    $('.ajax-loader').hide();
                    $('.success').fadeOut(200).hide();
                    $('.error').fadeOut(200).show();
                } else if (filter.test(email)) {
                    var formData = $(this).serialize();
                    $.ajax({
                        type: "POST",
                        url: "http://reddotdev.co.uk/spera-crm/auth/email_validate/",
                        data: formData,
                        success: function (response) {
                            var resultData = $.parseJSON(response);
                            //console.log('email validate '+data);
                            $('.ajax-loader').hide();

                            if (resultData.validate == 'success') {
                                // console.log(data);
                                $("#showusertypes").html(resultData.html_response);
                                $("#submitbutton").hide();
                                $('#emailid').attr('readonly', true);
                                $('#forgotpass').addClass("email_validate");

                            } else {
                                $('.form-forgot-error').html(resultData.html_response);
                                return false;
                            }
                        },
                        complete: function (data) {
                            $('.ajax-loader').hide();
                            $(".chosen-select").chosen({
                                scroll_to_highlighted: false,
                                disable_search_threshold: 4,
                                width: "100%"
                            });
                        }
                    });
                } else {
                    $('.ajax-loader').hide();
                    $('.form-forgot-error').html('<div id="error">Please enter valid email address!</div>');
                    return false;
                }
            }
            else {

                var companytype = $('#companytype');
                if (companytype.length > 0 && companytype.val() == 0) {
                    $('.ajax-loader').hide();
                    $('.form-forgot-error').html('<div id="error">Please select company!</div>');
                    return false;
                }

            }
            // stop the form from submitting the normal way and refreshing the page
            // event.preventDefault();

        });
    });
</script>

