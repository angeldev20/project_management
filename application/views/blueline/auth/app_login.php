<?php $attributes = array('class' => 'form-signin form-login', 'role'=> 'form', 'id' => 'login'); ?>
<?=form_open('login', $attributes)?>
    <div class="logo"><img style="max-height: 50px; max-width: 200px; width: auto;" class="login-logo" src="https://spera-<?=ENVIRONMENT ?>.s3-us-west-2.amazonaws.com/<?PHP echo (isset($_SESSION['accountUrlPrefix'])) ? $_SESSION['accountUrlPrefix'] : 'default';?>/<?php if($core_settings->login_logo == ""){ echo $core_settings->invoice_logo;} else{ echo $core_settings->login_logo; }?>" alt="<?=$core_settings->company;?>"></div>
<?php if($error == "true") { $message = explode(':', $message)?>
    <div id="error">
        <?=$message[1]?>
    </div>
<?php } ?>
<?php if ($email) : ?>
    <?php if ($selectAccount) : ?>
        <input type="hidden" name="ssoallow" value="<?php (isset($sso)) ? $sso :'';?>">
        <div class="form-group">
            <label for="name"><?=$this->lang->line('application_company');?> <?=$this->lang->line('application_name');?> *</label>
            <select name="accountUrlPrefix" class="form-control" multiple="">
                <?php foreach ($accountList as $account) : ?>
                    <option value="<?php echo $account->accountUrlPrefix ;?>"><?php echo $account->accountName ;?> (<?php echo $account->accountUrlPrefix ;?>.<?php echo $domain ;?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>
<?php else : ?>
    <div class="form-group">
        <label for="email"><?=$this->lang->line('application_email');?> *</label>
        <input id="email" type="email" name="email" class="required email form-control" value="<?php echo $email; ?>" required/>
    </div>

    
<?php endif; ?>
    <!-- TODO; need to translate 'Select Account' and perhaps other things on this page -->
    <!--a href="<?= 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode('https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/plus.me') . '&redirect_uri=' . urlencode(DEV_CLIENT_REDIRECT_URL) . '&response_type=code&client_id=' . DEV_CLIENT_ID . '&access_type=online' ?>"><img src="https://developers.google.com/identity/images/g-logo.png" style="margin-left: 3em;height: 50px;"></a-->
    <input type="submit" class="btn btn-primary fadeoutOnClick" value="<?php if ($selectAccount) {echo "Select Account";} else { echo $this->lang->line('application_login');}?>" />
    <div class="forgotpassword"><a href="<?=site_url("forgotpass");?>"><?=$this->lang->line('application_forgot_password');?></a></div>

    <div class="sub">
        <?php if($core_settings->registration == 1){ ?><div class="small"><small><?=$this->lang->line('application_you_dont_have_an_account');?></small></div><hr/><a href="<?=site_url("register");?>" class="btn btn-success"><?=$this->lang->line('application_create_account');?></a> <?php } ?>
    </div>
<?=form_close()?>
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

