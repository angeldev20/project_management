<?php
$attributes = array( 'class' => '', 'id' => '_ticket' );
echo form_open_multipart( $form_action, $attributes );
if ( isset( $ticket ) ) { ?>
    <input id="id" type="hidden" name="id" value="<?php echo $ticket->id; ?>"/>
<?php } ?>

    <div class="form-group">
		<?php $options = array('' => $this->lang->line( 'application_type' ));
		foreach ( $types as $value ):
			$options[ $value->id ] = $value->name;
		endforeach;
		if ( isset( $ticket ) && is_object( $ticket->type ) ) {
			$type = $ticket->type->id;
		} else {
			$type = $settings->ticket_default_type;
		}
		echo form_dropdown( 'type_id', $options, $type, 'style="width:100%" class="form-control"' ); ?>
    </div>

    <div class="form-group">
		<?php $options = array('' => $this->lang->line( 'application_queue' ));
		foreach ( $queues as $value ):
			$options[ $value->id ] = $value->name;
		endforeach;
		if ( isset( $ticket ) && is_object( $ticket->queue ) ) {
			$queue = $ticket->queue->id;
		} else {
			$queue = "";
		}
		echo form_dropdown( 'queue_id', $options, $queue, 'style="width:100%" class="form-control"' ); ?>
    </div>

    <div class="form-group">
		<?php $options = array();
		$options['0']  = $this->lang->line( 'application_client' );
		foreach ( $clients as $value ):
			$options[ $value->id ] = $value->firstname . ' ' . $value->lastname . ' [' . (isset($value->company->name) ? $value->company->name : 'None') . ']';
		endforeach;
		if ( isset( $ticket ) && is_object( $ticket->company ) ) {
			$client = $ticket->company->id;
		} else {
			$client = "";
		}
		echo form_dropdown( 'client_id', $options, $client, 'style="width:100%" class="form-control"' ); ?>
    </div>

    <div class="form-group">
		<?php $options = array();
		$options['0']  = $this->lang->line( 'application_assign_to' );
		foreach ( $users as $value ):
			$options[ $value->id ] = $value->firstname . ' ' . $value->lastname;
		endforeach;
		if ( isset( $ticket ) && is_object( $ticket->user ) ) {
			$user = $ticket->user->id;
		} else {
			$user = "";
		}
		echo form_dropdown( 'user_id', $options, $user, 'style="width:100%" class="form-control"' ); ?>
    </div>

    <div class="form-group">
        <input id="subject" type="text" name="subject" class="form-control" value="<?php if ( isset( $ticket ) ) {
			echo $ticket->subject;
		} ?>" placeholder="<?= $this->lang->line( 'application_subject' ) ?>" required/>
    </div>

    <div class="form-group">
        <textarea id="text" name="text" rows="9" class="form-control summernote-modal" placeholder="<?= $this->lang->line( 'application_message' ); ?>"></textarea>
    </div>

    <div class="fileUpload input-group form-group">
        <input id="uploadFile" class="form-control uploadFile" placeholder="<?= $this->lang->line( 'application_choose_file' ); ?>"
               disabled="disabled"/>
        <input id="uploadBtn" type="file" name="userfile" class="upload"/>
        <span class="input-group-addon"><i class="fa fa-upload"/></span>
    </div>

    <div class="form-group">
        <label><?= $this->lang->line( 'application_notifications' ); ?></label>
        <ul class="accesslist">
            <li><input type="checkbox" class="checkbox" id="r_notify" name="notify_agent" value="yes"
                       data-labelauty="<?= $this->lang->line( 'application_notify_agent' ); ?>" checked="checked"></li>
            <li><input type="checkbox" class="checkbox" id="c_notify" name="notify_client" value="yes"
                       data-labelauty="<?= $this->lang->line( 'application_notify_client' ); ?>" checked="checked"></li>
        </ul>
    </div>
    <div class="modal-footer">
        <input type="submit" name="send" class="btn btn-primary"
               value="<?= $this->lang->line( 'application_save' ); ?>"/>
        <a class="btn" data-dismiss="modal"><?= $this->lang->line( 'application_close' ); ?></a>
    </div>


<?php echo form_close(); ?>