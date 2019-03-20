<style>

</style>
<?php $attributes = array( 'class' => 'form-signin form-register', 'role' => 'form', 'id' => 'register' ); ?>
<?= form_open_multipart( $form_action, $attributes ) ?>
    <div class="logo"><img style="max-height: 50px; max-width: 200px; width: auto;"
                           src="https://spera-<?= ENVIRONMENT ?>.s3-us-west-2.amazonaws.com/<?= $_SESSION['accountUrlPrefix'] ?>/<?php if ( $core_settings->login_logo == "" ) {
		                       echo $core_settings->invoice_logo;
	                       } else {
		                       echo $core_settings->login_logo;
	                       } ?>" alt="<?= $core_settings->company; ?>"></div>
<?php if ( $error != false ) { ?>
    <div id="error" style="display:block">
		<?= $error ?>
    </div>
<?php } ?>
    <div class="form-group">
        <label for="username"><?= $this->lang->line( 'application_username' ); ?> *</label>
        <input id="username" autocomplete="off" type="text" name="username" class="required form-control" required/>
    </div>
    <div class="form-group">
        <label for="firstname"><?= $this->lang->line( 'application_firstname' ); ?> *</label>
        <input id="firstname" type="text" name="firstname" class="required form-control" required/>
    </div>
    <div class="form-group">
        <label for="lastname"><?= $this->lang->line( 'application_lastname' ); ?> *</label>
        <input id="lastname" type="text" name="lastname" class="required form-control" required/>
    </div>
    <div class="form-group">
        <label for="email"><?= $this->lang->line( 'application_email' ); ?> *</label>
        <input id="email" type="email" name="email" value="<?= $email; ?>" class="required email form-control" required/>
    </div>
    <div class="form-group">
        <label for="password"><?= $this->lang->line( 'application_password' ); ?> *</label>
        <input autocomplete="off" id="password" type="password" name="password" class="required form-control "
               minlength="6" required/>
    </div>
    <div class="form-group">
        <label for="password"><?= $this->lang->line( 'application_confirm_password' ); ?> *</label>
        <input autocomplete="off" id="confirm_password" type="password" name="confirm_password"
               class="required form-control"
               data-match="#password" required/>
    </div>

    <div class="form-group">
        <label for="userfile"><?= $this->lang->line( 'application_profile_picture' ); ?></label>
        <div>
            <input id="uploadFile" type="text" name="dummy" class="form-control uploadFile"
                   placeholder="<?= $this->lang->line( 'application_choose_file' ); ?>" disabled="disabled"/>
            <div class="fileUpload btn btn-primary" style="width: 24% !important; line-height: 25px; height: 35px; padding: 6px 12px;">
                <span><i class="icon dripicons-upload"></i><span
                            class="hidden-xs"> <?= $this->lang->line( 'application_select' ); ?></span></span>
                <input id="uploadBtn" type="file" name="userfile" class="upload"/>
            </div>
        </div>
    </div>
    <hr>
    <input type="submit" class="btn btn-success" value="<?= $this->lang->line( 'application_submit' ); ?>"/>
<?= form_close() ?>