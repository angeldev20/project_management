<?php $attributes = array('class' => 'form-signin', 'role'=> 'form', 'id' => 'login', 'autocomplete' => 'off'); ?>
<?=form_open('login', $attributes)?>
    <div class="logo"><img style="max-height: 50px; max-width: 200px; width: auto;" class="login-logo" src="https://spera-<?=ENVIRONMENT ?>.s3-us-west-2.amazonaws.com/<?PHP echo (isset($_SESSION['accountUrlPrefix'])) ? $_SESSION['accountUrlPrefix'] : 'default';?>/<?php if($core_settings->login_logo == ""){ echo $core_settings->invoice_logo;} else{ echo $core_settings->login_logo; }?>" alt="<?=$core_settings->company;?>"></div>
<?php if($error == "true") { $message = explode(':', $message)?>
    <div id="error">
        <?=$message[1]?>
    </div>
<?php } ?>
<?php if ($accountUrlPrefix) : ?>
    <div class="form-group">
        <label for="username"><?=$this->lang->line('application_username');?> | <?=$this->lang->line('application_email');?></label>
        <input type="username" class="form-control" id="username" name="username" placeholder="<?=$this->lang->line('application_enter_your_username');?>" />
    </div>
    <div class="form-group">
        <label for="password"><?=$this->lang->line('application_password');?></label>
        <input autocomplete="off" type="password" class="form-control" id="password" name="password" placeholder="<?=$this->lang->line('application_enter_your_password');?>" />
    </div>
    <!--a href="<?= 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode('https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/plus.me') . '&redirect_uri=' . urlencode(DEV_SUB_CLIENT_REDIRECT_URL) . '&response_type=code&client_id=' . DEV_SUB_CLIENT_ID . '&access_type=online&state='.urldecode($accountUrlPrefix) ?>"><img src="https://developers.google.com/identity/images/g-logo.png" style="margin-left: 3em;height: 50px;"></a-->
<?php endif; ?>
    <!-- TODO; need to translate 'Select Account' and perhaps other things on this page -->
    
    <input type="submit" class="btn btn-primary fadeoutOnClick" value="<?php echo $this->lang->line('application_login');?>" />
    <div class="forgotpassword"><a href="<?=site_url("forgotpass");?>"><?=$this->lang->line('application_forgot_password');?></a></div>

    <div class="sub">
        <?php if($core_settings->registration == 1){ ?><div class="small"><small><?=$this->lang->line('application_you_dont_have_an_account');?></small></div><hr/><a href="<?=site_url("register");?>" class="btn btn-success"><?=$this->lang->line('application_create_account');?></a> <?php } ?>
    </div>
<?=form_close()?>

