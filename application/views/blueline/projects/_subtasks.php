<?php
$attributes = array('class' => 'dynamic-form', 'data-reload' => 'task-list', 'data-reload2' => 'milestones-list', 'data-reload3' => 'taskviewer-content', 'data-baseurl' => base_url(), 'id' => '_task');
echo form_open($form_action, $attributes);
$public = "0";
?>

<?php if(isset($subtask)){ ?>
    <input id="id" type="hidden" name="id" value="<?php echo $subtask->id; ?>" />
<?php } ?>
    <div class="form-group">
        <label for="name"><?=$this->lang->line('application_task_name');?> *</label>
        <input id="name" type="text" name="name" class="form-control resetvalue" value="<?php if(isset($subtask)){echo $subtask->name;} ?>"  required/>
    </div>
    <div class="form-group">
        <label for="status"><?=$this->lang->line('application_status');?></label>
		<?php $options = array(
			'open'  => $this->lang->line('application_open'),
			'done'    => $this->lang->line('application_done'),
		);
		$status = FALSE;
		if(isset($subtask)){ $status = $subtask->status;}
		echo form_dropdown('status', $options, $status, 'style="width:100%" class="chosen-select"'); ?>
    </div>

    <div class="form-group">
        <label for="user"><?=$this->lang->line('application_assign');?></label>
		<?php $users = array();
		$users['0'] = '-';
		foreach ($project->project_has_workers as $workers):
			$users[$workers->user_id] = $workers->user->firstname.' '.$workers->user->lastname;
		endforeach;
		if(isset($subtask)){
			$user = $subtask->worker_id;
		}else{$user = $this->user->id;}
		echo form_dropdown('worker_id', $users, $user, 'style="width:100%" class="chosen-select"');
		//echo form_dropdown('user_ids[]', $users, $user, 'style="width:100%" class="chosen-select" data-placeholder="'.$this->lang->line('application_select_agents').'" multiple tabindex="3"');?>
    </div>

    <div class="modal-footer">
		<?php if(isset($subtask)){ ?>
            <a href="<?=base_url()?>projects/subtasks/<?=$subtask->project_has_task->project_id;?>/<?= $subtask->project_has_task->id ?>/delete/<?=$subtask->id;?>" class="btn btn-danger pull-left button-loader" ><?=$this->lang->line('application_delete');?></a>
		<?php }else{  ?>
            <a class="btn btn-default pull-left" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
            <i class="icon dripicons-loading spin-it" id="showloader" style="display:none"></i>
            <button id="send" name="send" data-keepModal="true" class="btn btn-primary send button-loader"><?=$this->lang->line('application_save_and_add');?></button>
		<?php } ?>
        <button name="send" class="btn btn-primary send button-loader"><?=$this->lang->line('application_save');?></button>
    </div>
<?php echo form_close(); ?>