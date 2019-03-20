<?php
$attributes = array( 'class' => '', 'id' => 'user_form', 'autocomplete' => 'off' );
echo form_open_multipart( $form_action, $attributes );
?>

    <div id="new-user-emails">
        <div class="form-group">
            <label for="email"><?= $this->lang->line( 'application_email' ); ?> *</label>
            <input type="email" name="emails[]" class="email form-control"/>
        </div>
    </div>
    <h3>OR</h3>
    <div class="form-group">
        <label for="userfile"><?= $this->lang->line( 'application_upload_csv' ); ?></label>
        <div>
            <input id="uploadFile" type="text" name="dummy" class="form-control uploadFile"
                   placeholder="<?= $this->lang->line( 'application_choose_file' ); ?>" disabled="disabled"/>
            <div class="fileUpload btn btn-primary">
                <span><i class="icon dripicons-upload"></i><span
                            class="hidden-xs"> <?= $this->lang->line( 'application_select' ); ?></span></span>
                <input id="uploadBtn" type="file" name="userfile" class="upload"/>
            </div>
        </div>
    </div>

    <div style="padding: 5px; cursor: pointer;">
        <a id="btn-advanced-settings" href="">Advanced Settings&nbsp;<i class="fa fa-angle-down"></i></a>
    </div>
    <div id="advanced-settings" style="display: none;">
		<?php if ( ! isset( $agent ) ) { ?>
            <div class="form-group">
                <label for="status"><?= $this->lang->line( 'application_ticket_queue' ); ?></label>
				<?php $options = array();
				foreach ( $queues as $value ):
					$options[ $value->id ] = $value->name;
				endforeach;

				if ( isset( $user->queue ) ) {
					$queue = $user->queue;
				} else {
					$queue = "";
				}
				echo form_dropdown( 'queue', $options, $queue, 'style="width:100%" class="chosen-select"' ); ?>
            </div>
            <div class="form-group">
                <label for="status"><?= $this->lang->line( 'application_status' ); ?></label>
				<?php $options = array(
					'active'   => $this->lang->line( 'application_active' ),
					'inactive' => $this->lang->line( 'application_inactive' )
				); ?>

				<?php
				if ( isset( $user ) ) {
					$status = $user->status;
				} else {
					$status = 'active';
				}
				echo form_dropdown( 'status', $options, $status, 'style="width:100%" class="chosen-select"' ); ?>
            </div>
            <div class="form-group">
                <label for="admin"><?= $this->lang->line( 'application_super_admin' ); ?></label>
				<?php $options = array(
					'1' => $this->lang->line( 'application_yes' ),
					'0' => $this->lang->line( 'application_no' )
				); ?>

				<?php
				if ( isset( $user ) ) {
					$admin = $user->admin;
				} else {
					$admin = '0';
				}
				echo form_dropdown( 'admin', $options, $admin, 'style="width:100%" class="chosen-select"' ); ?>
            </div>
		<?php } ?>
		<?php if ( ! isset( $agent ) && $this->user->admin == "1" ) {
			$access = array( 1, 2, 3 );
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
    </div>

    <div class="modal-footer">
        <input type="submit" name="send" class="btn btn-primary"
               value="<?= $this->lang->line( 'application_invite' ); ?>"/>
        <a class="btn" data-dismiss="modal"><?= $this->lang->line( 'application_close' ); ?></a>
    </div>

<?php echo form_close(); ?>