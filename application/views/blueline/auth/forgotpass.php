<?php $attributes = array( 'class' => 'form-signin', 'role' => 'form', 'id' => 'forgotpass' ); ?>
<?= form_open( 'forgotpass', $attributes ) ?>
    <div class="logo">
        <h1>Password recovery.</h1>
        <span>Reset your password below.</span>
    </div>
<?php if ( $this->session->flashdata( 'message' ) ) {
	$exp = explode( ':', $this->session->flashdata( 'message' ) ); ?>
    <div class="forgotpass-success">
		<?= $exp[1] ?>
    </div>
<?php } else { ?>
    <div class="forgotpass-info"><?= $this->lang->line( 'application_identify_account' ); ?></div>

    <div class="form-group">
        <label for="email"><?= $this->lang->line( 'application_email' ); ?></label>
        <input type="text" class="form-control" name="email" id="email"
               placeholder="<?= $this->lang->line( 'application_email' ); ?>">
    </div>

    <input type="submit" class="btn btn-primary" value="<?= $this->lang->line( 'application_reset_password' ); ?>"/>
<?php } ?>
    <div class="forgotpassword"><a
                href="<?= site_url( "login" ); ?>"><?= $this->lang->line( 'application_go_to_login' ); ?></a></div>
<?= form_close() ?>

<div class="form-signin-right">
    <span class="small-logo"></span>
    <div class="clearfix">
        <div class="col-md-12 text-right">
			<?php if($core_settings->registration == 1){ ?>
                <p>Don't have Spera account?</p>
                <a class="btn btn-default" href="/signup">Get Started</a>
			<?php } ?>
        </div>
    </div>
</div>
