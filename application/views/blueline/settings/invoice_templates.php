<div id="row" class="grid">
	<div class="grid__col-sm-12 grid__col-md-3 grid__col-lg-3">
		<div class="list-group">
			<?php foreach ($submenu as $name=>$value):
				$badge = "";
				$active = "";
				if($value == "settings/updates"){ $badge = '<span class="badge badge-success">'.$update_count.'</span>';}
				if($name == $breadcrumb){ $active = 'active';} ?>
					<a style="<?php if($name=="SMTP Settings") echo "display: none;"; ?>" class="list-group-item <?=$active;?>" id="<?php $val_id = explode("/", $value); if(!is_numeric(end($val_id))){echo end($val_id);}else{$num = count($val_id)-2; echo $val_id[$num];} ?>" href="<?=site_url($value);?>"><?=$badge?> <?=$name?></a>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="grid__col-sm-12 grid__col-md-9 grid__col-lg-9">
		<div class="panel">
			<?php   
				$attributes = array('class' => '', 'id' => 'template_form');
				echo form_open_multipart($form_action, $attributes); 
			?>
				<div class="table-head"><?=$this->lang->line('application_pdf_settings');?></div>
				<div class="panel-body">
					<div class="form-group">
						<label>Image path</label>
						<input name="pdf_path" type="checkbox" class="checkbox" style="width:100%;" data-labelauty="<?=$this->lang->line('application_pdf_path');?>" value="1" <?php if($settings->pdf_path == "1"){ ?> checked="checked" <?php } ?>>
					</div>
					<div class="form-group">
						<label><?=$this->lang->line('application_pdf_font');?></label>
						<?php $options = array();
							if ($handle = opendir('assets/'.$settings->template.'/fonts/')) {

								while (false !== ($entry = readdir($handle))) {
									if ($entry != "." && $entry != ".." && $entry != "index.html") {
										$apart = explode(".", $entry);
										if($apart[1] == "ttf" || $apart[1] == "TTF"){
												$apart2 = explode("-", $apart[0]);
												if(isset($apart2[1])){
												if(@$apart2[1] == "Regular" || @$apart2[1] == "regular"){
													$options[$apart2[0]] = ucwords($apart2[0]);
												}
												}
											}
										}
								}
								closedir($handle);
							}
							echo form_dropdown('pdf_font', $options, $settings->pdf_font, 'style="width:250px" class="chosen-select"'); ?>
					</div>
				</div>
				<div class="panel-footer">
					<input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
				</div>
			<?php echo form_close(); ?>
		</div>
		
		<div class="panel">
			<div class="table-head"><?=$this->lang->line('application_invoice_template');?></div>
			<div class="panel-body">
				<div class="row">
					<?php foreach ($invoice_template_files as $value): ?>
						<div class="col-md-3 no-padding">
							<div class="template_container">
								<?php 
									$image = "assets/blueline/images/invoice_".$value.".png";
									if(!is_file($image)){ 
										$image = "assets/blueline/images/invoice_no_preview.png";
									}
								?>
								<img class="img-responsive" src="<?=base_url()?><?=$image?>"/>
						
								<?php if($active_template == $value) { ?>
									<div class="template_container_bottom active"><?=$this->lang->line('application_active');?></div>
								<?php } else { ?>
									<div class="template_container_bottom"><a href="<?=base_url()?>settings/invoice_templates/invoice/<?=$value?>"><?=$this->lang->line('application_activate');?></a></div>
								<?php } ?>
							</div>
						</div>
					<?php endforeach;?>
				</div>
			</div>
		</div>
		<div class="panel">
			<div class="table-head"><?=$this->lang->line('application_estimate_template');?></div>
			<div class="panel-body">
			<div class="row">
				<?php foreach ($estimate_template_files as $value): ?>
					<div class="col-md-3 no-padding">
						<div class="template_container">
							<?php 
								$image = "assets/blueline/images/invoice_".$value.".png";
								if(!is_file($image)){ 
									$image = "assets/blueline/images/invoice_no_preview.png";
								}
							?>
							<img class="img-responsive" src="<?=base_url()?><?=$image?>"/>
							<?php if($active_estimate_template == $value){ ?>
								<div class="template_container_bottom active"><?=$this->lang->line('application_active');?></div>
							<?php }else{ ?>
								<div class="template_container_bottom"><a href="<?=base_url()?>settings/invoice_templates/estimate/<?=$value?>"><?=$this->lang->line('application_activate');?></a></div>
							<?php } ?>
						</div>
					</div>
				<?php endforeach;?>
			</div>
		</div>
	</div>
</div>