<?php
$attributes = array( 'class' => '', 'id' => 'user_form', 'autocomplete' => 'off' );
echo form_open_multipart( $form_action, $attributes );
?>

<div class="form-group">
    <input id="username" autocomplete="off" type="text" name="username" class="required form-control"
           value="<?php if ( isset( $user ) ) {
		       echo $user->username;
	       } ?>" placeholder="<?= $this->lang->line( 'application_username' ); ?>" required/>
</div>
<div class="form-group">
    <input id="firstname" type="text" name="firstname" class="required form-control"
           value="<?php if ( isset( $user ) ) {
		       echo $user->firstname;
	       } ?>" placeholder="<?= $this->lang->line( 'application_firstname' ); ?>" required/>
</div>
<div class="form-group">
    <input id="lastname" type="text" name="lastname" class="required form-control" value="<?php if ( isset( $user ) ) {
		echo $user->lastname;
	} ?>" placeholder="<?= $this->lang->line( 'application_lastname' ); ?>" required/>
</div>
<div class="form-group">
    <input id="email" type="email" name="email" class="required email form-control" value="<?php if ( isset( $user ) ) {
		echo $user->email;
	} ?>" placeholder="<?= $this->lang->line( 'application_email' ); ?>" required/>
</div>
<div class="form-group">
    <input autocomplete="off" id="password" type="password" name="password" class="form-control "
           minlength="6"
           placeholder="<?= $this->lang->line( 'application_password' ); ?>" <?php if ( ! isset( $user ) ) {
		echo 'required';
	} ?>/>
</div>
<div class="form-group">
    <input autocomplete="off" id="confirm_password" type="password" name="confirm_password" class="form-control"
           data-match="#password" placeholder="<?= $this->lang->line( 'application_confirm_password' ); ?>"/>
</div>

<div class="fileUpload input-group form-group">
    <input id="uploadFile" class="form-control uploadFile"
           placeholder="<?= $this->lang->line( 'application_choose_file' ); ?>"
           disabled="disabled"/>
    <input id="uploadBtn" type="file" name="userfile" class="upload"/>
    <span class="input-group-addon"><i class="fa fa-upload"/></span>
</div>

<?php if ( ! isset( $agent ) ) { ?>
    <div class="form-group">
        <input id="title" type="text" name="title" class="required form-control" value="<?php if ( isset( $user ) ) {
			echo $user->title;
		} ?>" placeholder="<?= $this->lang->line( 'application_title' ); ?>" required/>
    </div>
    <div class="form-group">
		<?php $options = array( '' => $this->lang->line( 'application_ticket_queue' ) );
		foreach ( $queues as $value ):
			$options[ $value->id ] = $value->name;
		endforeach;

		if ( isset( $user->queue ) ) {
			$queue = $user->queue;
		} else {
			$queue = "";
		}
		echo form_dropdown( 'queue', $options, $queue, 'style="width:100%" class="form-control"' ); ?>
    </div>
    <div class="form-group">
		<?php $options = array(
			''         => $this->lang->line( 'application_status' ),
			'active'   => $this->lang->line( 'application_active' ),
			'inactive' => $this->lang->line( 'application_inactive' )
		); ?>

		<?php
		if ( isset( $user ) ) {
			$status = $user->status;
		} else {
			$status = 'active';
		}
		echo form_dropdown( 'status', $options, $status, 'style="width:100%" class="form-control"' ); ?>
    </div>
    <div class="form-group">
		<?php $options = array(
			''  => $this->lang->line( 'application_super_admin' ),
			'1' => $this->lang->line( 'application_yes' ),
			'0' => $this->lang->line( 'application_no' )
		); ?>

		<?php
		if ( isset( $user ) ) {
			$admin = $user->admin;
		} else {
			$admin = '0';
		}
		echo form_dropdown( 'admin', $options, $admin, 'style="width:100%" class="form-control"' ); ?>
    </div>
<?php } ?>
<?php if ( ! isset( $agent ) && $this->user->admin == "1" ) {
	$access = array();
	if ( isset( $user ) ) {
		$access = explode( ",", $user->access );
	}
	?>


    <div class="form-group">
        <label><?= $this->lang->line( 'application_module_access' ); ?></label>
        <ul class="accesslist">

			<?php foreach ( $modules as $key => $value ) {
				if ( $value->type == "widget" && ! isset( $wi ) ) { ?>
                    <label>Widgets</label>
					<?php $wi = true;
				} ?>

                <li><input type="checkbox" class="checkbox" id="r_<?= $value->link; ?>" name="access[]"
                           data-labelauty="<?= $this->lang->line( 'application_' . $value->link ); ?>"
                           value="<?= $value->id; ?>" <?php if ( in_array( $value->id, $access ) ) {
						echo 'checked="checked"';
					} ?>></li>
			<?php } ?>
        </ul>
    </div>
<?php } ?>


<div class="modal-footer">
    <div class="form-submit">
    <input type="submit" name="send" class="btn btn-success" value="<?= $this->lang->line( 'application_save' ); ?>"/>
    <a class="btn" data-dismiss="modal"><?= $this->lang->line( 'application_close' ); ?></a>
    </div>
</div>

<?php echo form_close(); ?>
<script type="javascript">
	<?php if (isset( $kill_username ) && $kill_username == true) : ?>
    $(document).ready(function () {
        setTimeout(function () {
            $('input[name="username"]').val('');
        }, 500);
    });
	<?php endif;?>
</script>
