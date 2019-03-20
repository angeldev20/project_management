<?php $attributes = array('class' => 'form-signup form-register', 'role'=> 'form', 'id' => 'register'); ?>
<?=form_open($form_action, $attributes)?>
<div class="logo"><img src="https://spera-<?=ENVIRONMENT ?>.s3-us-west-2.amazonaws.com/<?php echo (isset($_SESSION['accountUrlPrefix'])) ? $_SESSION['accountUrlPrefix'] : 'default';?>/<?php if($core_settings->login_logo == ""){ echo $core_settings->invoice_logo;} else{ echo $core_settings->login_logo; }?>" alt="<?=$core_settings->company;?>"></div>
<?php if($error != 'false') { ?>
    <div id="error" style="display:block">
        <?=$error?>
    </div>
<?php } ?>
<div class="title">
    <h2>Get a free trial.</h2>
</div>
<div class="subtext">
    <p>Try it Spera risk-free. No Credit card required.</p>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="username">Username *</label>
            <input id="username" type="text" name="username" class="required form-control" pattern="[a-z0-9]+" value="<?php if(isset($registerdata)){echo $registerdata['username'];} ?>" maxlength="16" required/>
        </div>
        <div class="form-group">
            <label for="firstname"><?=$this->lang->line('application_firstname');?> *</label>
            <input id="firstname" type="text" name="firstname" class=" form-control" value="<?php if(isset($registerdata)){echo $registerdata['firstname'];}?>" required/>
        </div>
        <div class="form-group">
            <label for="lastname"><?=$this->lang->line('application_lastname');?> *</label>
            <input id="lastname" type="text" name="lastname" class="required form-control" value="<?php if(isset($registerdata)){echo $registerdata['lastname'];}?>" required/>
        </div>

        <div class="form-group <?php if(isset($registerdata)){echo 'has-error';} ?>">
            <label for="email"><?=$this->lang->line('application_email');?> *</label>
            <input id="email" type="email" name="email" class="required email form-control" value="<?php if(isset($registerdata)){echo $registerdata['email'];}?>" required/>
        </div>
        <div class="form-group">
            <label for="name"><?=$this->lang->line('application_company');?> <?=$this->lang->line('application_name');?> *</label>
            <input id="name" type="text" name="name" class="required form-control" value="<?php if(isset($registerdata)){echo $registerdata['name'];} ?>"  required/>
        </div>

        <div class="form-group">
            <label for="password"><?=$this->lang->line('application_password');?> *</label>
            <input id="password" autocomplete="off" type="password" name="password" class="form-control" value="" required />
        </div>
        <div class="form-group">
            <label for="password"><?=$this->lang->line('application_confirm_password');?> *</label>
            <input id="confirm_password" autocomplete="off" type="password" class="form-control" data-match="#password" required />
        </div>
        <?php if ($planTypes) : ?>
            <div class="form-group" style="display: none;">
                <label for="planType">Plan *</label>
                <select name="planType" class="chosen-select form-control" multiple="">
                    <?php foreach ($planTypes as $planTypeItem) : ?>
                        <option value="<?php echo $planTypeItem->type ;?>"<?php if(isset($planType) && $planType == $planTypeItem->type) echo ' selected';?>><?php echo $planTypeItem->name ;?> ($<?php echo ($planTypeItem->amount/100) ;?>/<?php echo $planTypeItem->billingFrequencyDays . ' days';?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <?php   $number1 = rand(1, 10);
        $number2 = rand(1, 10);
        $captcha = $number1+$number2;

        //captcha
        $html_fields = '<input type="hidden" id="captcha" name="captcha" value="'.$captcha.'"><div class="form-group">';
        $html_fields .= '<label class="control-label-e">'.$number1.'+'.$number2.' = ?</label>';
        $html_fields .= '<input type="text" id="confirmcaptch" name="confirmcaptcha" data-match="#captcha" class="form-control" required/></div>';
        echo $html_fields;
        ?>
    </div>

</div>

<div class="row">
    <div class="col-md-12">
        <input id="register_submit" type="submit" class="btn btn-primary" value="SIGNUP NOW" />

        <div class="forgotpassword"><a href="#">Terms &amp; Conditions</a></div>
    </div>
</div>
<?=form_close()?>

<script src="/assets/blueline/js/plugins/jquery-3.2.1.min.js"></script>
<script src="/assets/blueline/js/plugins/jquery-migrate-3.0.0.min.js"></script>
<script type="text/javascript">
    var userAccounts = {};

    function doAccountsAlreadyExistForThisEmail(email) {
        if(userAccounts.data.length > 0) {
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

    $(document).ready(function () {
        getAccountsForEmail('dhogan@spera.io');
        //$("#package").find("option").eq(0).removeAttr('selected');
        /*$('#register_submit').click(function(){
            var package_option=$('#package_chosen a span').text();
            //alert(package_option);
            if(package_option=='-')
            {
                $('#package_div').addClass('error1');
            }
        });*/
        /*$('#package').change(function(){
            var package_option=$('#package_chosen a span').text();
            if(package_option=='-')
            {
                $('#package_div').addClass('error1');
            }
            else
            {
                $('#package_div').removeClass('error1');
            }
        });*/
        var fade = "Left";
        $("form").validator();

        $(".form-signup").addClass("animated fadeIn"+fade);
        $( ".fadeoutOnClick" ).on( "click", function(){
            NProgress.start();
            $(".form-signup").addClass("animated fadeOut"+fade);
            NProgress.done();
        });

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

        $('#login').submit(function(event) {
            $('.ajax-loader').show();
            $('.form-login-error').html('');
            var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
            var email = $("#emailid").val();
            var password = $("#password").val();

            if(email == "") {
                $('.form-login-error').html('<div id="error">Please enter valid email address!</div>');
            }

            if(!$(this).hasClass("email_validate"))
            {
                if(email == "") {
                    $('.ajax-loader').hide();
                    $('.success').fadeOut(200).hide();
                    $('.error').fadeOut(200).show();
                } else if(filter.test(email)){
                    var formData = $(this).serialize();
                    $.ajax({
                        type: "POST",
                        url: "http://reddotdev.co.uk/spera-crm/auth/email_validate/",
                        data: formData,
                        success: function(response){
                            var resultData = $.parseJSON(response);
                            //console.log('email validate '+data);
                            $('.ajax-loader').hide();

                            if(resultData.validate == 'success') {
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
                            $(".chosen-select").chosen({scroll_to_highlighted: false, disable_search_threshold: 4, width: "100%"});
                        }
                    });
                } else {
                    $('.ajax-loader').hide();
                    $('.form-login-error').html('<div id="error">Please enter valid email address!</div>');
                }
            }
            else
            {

                var companytype = $('#companytype');
                if(companytype.length > 0 && companytype.val() == 0) {
                    $('.ajax-loader').hide();
                    $('.form-login-error').html('<div id="error">Please select company!</div>');

                } else if(password != '') {
                    $('.ajax-loader').show();
                    var formData = $(this).serialize();
                    $.ajax({
                        type: "POST",
                        url: "http://reddotdev.co.uk/spera-crm/auth/user_validate/",
                        data: formData,
                        success: function(response){
                            var resultData = $.parseJSON(response);
                            // console.log('user validate '+data);
                            $('.ajax-loader').hide();

                            if(resultData.validate == 'success') {
                                // $("#showusertypes").html(data);
                                $("#submitbutton").hide();
                                $('#emailid').attr('readonly', true);
                                $('#login').addClass("email_validate");

                                //window.location.href = task.return;
                                window.location.href = resultData.html_response;

                            } else {
                                $('.form-login-error').html('<div id="error">'+resultData.html_response+'</div>');
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


        $('#forgotpass').submit(function(event) {
            $('.ajax-loader').show();
            $('.form-forgot-error').html('');
            var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
            var email = $("#emailid").val();
            var password = $("#password").val();

            if(!$(this).hasClass("email_validate"))
            {	event.preventDefault();
                if(email == "") {
                    $('.ajax-loader').hide();
                    $('.success').fadeOut(200).hide();
                    $('.error').fadeOut(200).show();
                } else if(filter.test(email)){
                    var formData = $(this).serialize();
                    $.ajax({
                        type: "POST",
                        url: "http://reddotdev.co.uk/spera-crm/auth/email_validate/",
                        data: formData,
                        success: function(response){
                            var resultData = $.parseJSON(response);
                            //console.log('email validate '+data);
                            $('.ajax-loader').hide();

                            if(resultData.validate == 'success') {
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
                            $(".chosen-select").chosen({scroll_to_highlighted: false, disable_search_threshold: 4, width: "100%"});
                        }
                    });
                } else {
                    $('.ajax-loader').hide();
                    $('.form-forgot-error').html('<div id="error">Please enter valid email address!</div>');
                    return false;
                }
            }
            else
            {

                var companytype = $('#companytype');
                if(companytype.length > 0 && companytype.val() == 0) {
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

