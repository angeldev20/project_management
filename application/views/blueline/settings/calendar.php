<div id="row" class="grid">
	<div class="grid__col-sm-12 grid__col-md-3 grid__col-lg-3">
		<div class="list-group">
			<?php foreach ($submenu as $name=>$value):
				$badge = "";
				$active = "";
				if ($value == "settings/updates"){ $badge = '<span class="badge badge-success">'.$update_count.'</span>';}
				if ($name == $breadcrumb){ $active = 'active';}?>
					<a style="<?php if($name=="SMTP Settings") echo "display: none;"; ?>" class="list-group-item <?=$active;?>" id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$badge?> <?=$name?></a>
			<?php endforeach;?>
		</div>
	</div>
	<div class="grid__col-sm-12 grid__col-md-9 grid__col-lg-9">
		<?php   
			$attributes = array('class' => '', 'id' => 'calendar');
			echo form_open_multipart($form_action, $attributes); 
		?>
		<div class="panel">
			<div class="table-head"><?=$this->lang->line('application_calendar');?> <?=$this->lang->line('application_settings');?></div>
			<div class="panel-body">
				<span class="highlight-text"><?=$this->lang->line('application_google_calendar_integration_help');?>: <a href="http://luxsys.helpscoutdocs.com/article/20-google-calendar-integration" target="_blank">Google Calendar Integration</a></span>
					<div class="form-group">
						<label><?=$this->lang->line('application_calendar_google_api_key');?></label>
						<input type="text" name="calendar_google_api_key" class="form-control" value="<?=$settings->calendar_google_api_key;?>">
					</div>
					<div class="form-group">
						<label><?=$this->lang->line('application_calendar_google_event_address');?></label>
						<input type="text" name="calendar_google_event_address" class="form-control" value="<?=$settings->calendar_google_event_address;?>">
					</div>
			</div class="table-div">
			<div class="panel-footer">
				<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>