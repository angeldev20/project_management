<div id="row" class="grid">
	<div class="grid__col-sm-12 grid__col-md-3 grid__col-lg-3">
		<div class="list-group">
			<?php foreach ($submenu as $name=>$value):
				$badge = "";
				$active = "";
				if($value == "settings/updates"){ $badge = '<span class="badge badge-success">'.$update_count.'</span>';}
				if($name == $breadcrumb){ $active = 'active';}?>
					<a style="<?php if($name=="SMTP Settings") echo "display: none;"; ?>" class="list-group-item <?=$active;?>" id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$badge?> <?=$name?></a>
			<?php endforeach;?>
		</div>
	</div>

	<div class="grid__col-sm-12 grid__col-md-9 grid__col-lg-9">
		<div class="panel">
			<?php   
				$attributes = array('class' => '', 'id' => 'banktransfer');
				echo form_open_multipart($form_action, $attributes); 
			?>
				<div class="table-head"><?=$this->lang->line('application_bank_transfer');?> <?=$this->lang->line('application_settings');?></div>
				<div class="panel-body">
					<div class="form-group">
						<label><?=$this->lang->line('application_bank_transfer_active');?></label>
						<input name="bank_transfer" type="checkbox" class="checkbox" style="width:100%;" data-labelauty="<?=$this->lang->line('application_bank_transfer_active');?>" value="1" <?php if($settings->bank_transfer == "1"){ ?> checked="checked" <?php } ?>>
					</div>
					<div class="form-group">
						<label><?=$this->lang->line('application_bank_transfer_details');?></label>
						<textarea name="bank_transfer_text" class="form-control summernote"><?=$settings->bank_transfer_text;?></textarea>
					</div>
				</div>
				<div class="panel-footer">
					<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
				</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>