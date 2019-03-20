<?php
$attributes = array('class' => '', 'id' => '_invoices');
echo form_open($form_action, $attributes);
?>

<?php if(isset($invoice)){ ?>
    <input id="id" type="hidden" name="id" value="<?=$invoice->id;?>" />
<?php } ?>
<?php if(isset($view)){ ?>
    <input id="view" type="hidden" name="view" value="true" />
<?php } ?>
    <input id="status" name="status" type="hidden" value="Open">
    <div id="client_selection" class="form-group additive">
        <label for="client"><?=$this->lang->line('application_client');?> *</label>
		<?php $options = array();
		$options[''] = '-';
		foreach ($companies as $value):
			$options[$value->id] = $value->name;
			$projects[$value->id] = $value->projects;
		endforeach;
		if(isset($invoice)){$client = $invoice->company_id; $project = $invoice->project_id;}else{$client = ""; $project = "";}
		echo form_dropdown('company_id', $options, $client, 'style="width:100%" data-destination="getProjects" class="chosen-select getProjects required" required');?>
    </div>
<?php if (isset($create) && $create ==true) : ?>
    <a id="client_section_plus" href="javascript:void(0)" class="btn btn-primary addclient pull-right"><i class="icon dripicons-plus"></i></a>
<?php endif; ?>
<?php if (isset($create) && $create ==true) : ?>
    <div style="display: none;" class="client_add_section">
        <div class="form-group">
            <label for="company_name"> COMPANY NAME</label>
            <input id="company_name" type="text" name="company_name" class="form-control"  value="" placeholder="Company Name" />
        </div>
        <div class="form-group">
            <label for="contact_name"> CONTACT NAME</label>
            <input id="contact_name" type="text" name="contact_name" class="form-control"  value="" placeholder="Contact Name" />
        </div>
        <div class="form-group">
            <label for="contact_email"> CONTACT EMAIL*</label>
            <input id="contact_email" type="text" name="contact_email" class="form-control"  value="" placeholder="Company Email" />
        </div>
    </div>
<?php endif; ?>
    <div id="project_selection" class="form-group additive">
        <label for="project"><?=$this->lang->line('application_projects');?> *</label>
        <select name="project_id" id="getProjects" style="width:100%" class="chosen-select required" required>
            <option value="">-</option>
			<?php foreach ($companies as $comp): ?>
                <optgroup label="<?=$comp->name?>" id="optID_<?=$comp->id?>" <?php if($client != $comp->id){ ?>disabled="disabled"<?php } ?>>
					<?php foreach ($comp->projects as $pro): ?>
                        <option value="<?=$pro->id?>" <?php if($project == $pro->id){ ?>selected="selected"<?php } ?>><?=$pro->name?></option>
					<?php endforeach; ?>
                </optgroup>
			<?php endforeach; ?>
        </select>
    </div>
<?php if (isset($create) && $create ==true) : ?>
    <a id="project_section_plus" href="javascript:void(0)" class="btn btn-primary addproject pull-right"><i class="icon dripicons-plus"></i></a>
<?php endif; ?>
<?php if (isset($create) && $create ==true) : ?>
    <div style="display: none;" class="project_add_section">
        <div class="form-group">
            <label for="project_name">Project Name *</label>
            <input id="project_name" type="text" name="project_name" class="form-control"  value="" placeholder="Project Name" />
        </div>
    </div>
<?php endif; ?>
<?php if(isset($invoice)){ ?>
    <div class="form-group">
        <label for="status"><?=$this->lang->line('application_status');?></label>
		<?php $options = array(
			'Open'  => $this->lang->line('application_Open'),
			'Sent'    => $this->lang->line('application_Sent'),
			'Paid' => $this->lang->line('application_Paid'),
			'PartiallyPaid' => $this->lang->line('application_PartiallyPaid'),
			'Canceled' => $this->lang->line('application_Canceled'),

		);
		echo form_dropdown('status', $options, $invoice->status, 'style="width:100%" class="chosen-select"'); ?>
    </div>
<?php } ?>
<?php if(isset($invoice)){ if($invoice->status == "Paid"){ ?>
    <div class="form-group">
        <label for="paid_date"><?=$this->lang->line('application_payment_date');?></label>
        <input id="paid_date" type="text" name="paid_date" class="datepicker form-control" value="<?php if(isset($invoice)){echo $invoice->paid_date;} ?>"  required/>
    </div>
<?php }} ?>
    <div class="form-group">
        <label for="due_date"><?=$this->lang->line('application_due_date');?> *</label>
        <input id="due_date" type="text" name="due_date" class="required datepicker-linked form-control" value="<?php if(isset($invoice)){echo $invoice->due_date;} ?>"  required/>
    </div>
    <div class="form-group">
        <!--todo:  needs translation-->
        <label for="currency">Receive Payment In This Currency</label>
        <select name="payment_currency" id="payment_currency" style="width:100%" class="chosen-select required" required>
			<?php foreach ($payment_currencies as $currency_key => $currency_value): ?>
                <option value="<?=$currency_key?>" <?php if($selectedPaymentCurrency == $currency_key){ ?>selected="selected"<?php } ?>><?=$currency_value?></option>
			<?php endforeach; ?>
        </select>
    </div>
    <?php if (isset($create) && $create ==true) : ?>
        <div class="form-group">
            <label for="value"><?=$this->lang->line('application_value');?> *</label>
            <input id="value" type="text" name="value" class="form-control"  value="" placeholder="value" required/>
        </div>
        <div class="form-group">
            <label for="type"><?=$this->lang->line('application_type');?> *</label>
            <input id="type" type="text" name="type" class="form-control"  value="" placeholder="Product and/or Service" required/>
        </div>
    <?php endif;?>
    <?php if (isset($create) && $create ==true) : ?>
        <div><span></span><a id="advanced_options" href="javascript:void(0)"><i class="icon dripicons-gear"></i> Advanced Options</a></div>
    <?php endif;?>
    <div class="advanced_options_section" <?php if (isset($create) && $create == true ) : ?>style="display: none;"<?php endif;?>>
        <div class="form-group">
            <label for="reference"><?=$this->lang->line('application_reference_id');?> *</label>
			<?php if(!empty($core_settings->invoice_prefix)){ ?>
            <div class="input-group"> <div class="input-group-addon"><?=$core_settings->invoice_prefix;?></div> <?php } ?>
                <input id="reference" type="text" name="reference" class="form-control"  value="<?php if(isset($invoice)){echo $invoice->reference;} else{ echo $core_settings->invoice_reference; } ?>" />
				<?php if(!empty($core_settings->invoice_prefix)){ ?> </div><?php } ?>
        </div>
        <div class="form-group">
            <label for="issue_date"><?=$this->lang->line('application_issue_date');?> *</label>
            <input id="issue_date" type="text" name="issue_date" class="required datepicker form-control" value="<?php if(isset($invoice)){echo $invoice->issue_date;} ?>"  required/>
        </div>
        <div class="form-group">
            <label for="currency"><?=$this->lang->line('application_currency');?></label>
            <select name="currency" id="currency" style="width:100%" class="chosen-select required" required>
                <!--option value="">-</option-->
				<?php foreach ($currencies as $currency): ?>
                    <option value="<?=$currency->symbol?>" <?php if($selectedCurrency == $currency->symbol){ ?>selected="selected"<?php } ?>><?=$currency->code?></option>
				<?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="discount"><?=$this->lang->line('application_discount');?></label>
            <input class="form-control" name="discount" id="appendedInput" type="text" value="<?php if(isset($invoice)){ echo $invoice->discount;} ?>"/>
        </div>
        <div class="form-group">
            <label for="tax"><?=$this->lang->line('application_custom_tax');?></label>
            <input class="form-control" name="tax" type="text" value="<?php if(isset($invoice)){ echo $invoice->tax;}else{echo $core_settings->tax;} ?>" />
        </div>
        <div class="form-group">
            <label for="second_tax"><?=$this->lang->line('application_second_tax');?></label>
            <input class="form-control" name="second_tax" type="text" value="<?php if(isset($invoice)){ echo $invoice->second_tax;} else {echo '0.00';} ?>"/>
        </div>
        <div class="form-group">
            <label for="terms"><?=$this->lang->line('application_terms');?></label>
            <textarea id="terms" name="terms" class="textarea required summernote-modal form-control" style="height:100px"><?php if(isset($invoice)){echo $invoice->terms;}else{ echo $core_settings->invoice_terms; }?></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <input type="submit" name="send_close" class="btn" value="<?=$this->lang->line('application_save');?> & Close"/>
		<?php if (isset($create) && $create ==true) : ?>
            <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?> & Send"/>
		<?php endif; ?>
    </div>
<?php echo form_close(); ?>
<?php if (isset($create) && $create ==true) : ?>
    <script>
        var client_add_toggle = false;
        var project_add_toggle = false;
        var advanced_options_toggle =  false;

        var d = new Date();

        var month = d.getMonth()+1;
        var day = d.getDate();
        //var hour = d.getHours();
        //var minute = d.getMinutes();
        //var second = d.getSeconds();

        var current_date = d.getFullYear() + '-' +
            ((''+month).length<2 ? '0' : '') + month + '-' +
            ((''+day).length<2 ? '0' : '') + day;
        //+ ' ' +
        //((''+hour).length<2 ? '0' :'') + hour + ':' +
        //((''+minute).length<2 ? '0' :'') + minute + ':' +
        //((''+second).length<2 ? '0' :'') + second;
        $("#issue_date").val(current_date);
        $( document ).ready(function() {
        });

        $( "#client_section_plus" ).click(function() {
            if(client_add_toggle == false) {
                $('.client_add_section').show();
                client_add_toggle = true;
                $('#contact_email').attr('required');
                $("#client_selection").hide();
                $( "#client_section_plus" ).hide();
                $("select[name='company_id']").removeAttr( 'required' );
                $( "#project_section_plus" ).click();
            } else {
                $('.client_add_section').hide();
                client_add_toggle = false;
                $('#contact_email').removeAttr( 'required' );
                $("#client_selection").show();
                $( "#client_section_plus" ).show();
                $("select[name='company_id']").attr( 'required' );
                $( "#project_section_plus" ).click();
            }
        });


        $( "#project_section_plus" ).click(function() {
            if(project_add_toggle == false) {
                $('.project_add_section').show();
                project_add_toggle = true;
                $("#project_selection").hide();
                $( "#project_section_plus" ).hide();
                $("select[name='project_id']").removeAttr( 'required' );
                $('#project_name').attr('required');
            } else {
                $('.project_add_section').hide();
                project_add_toggle = false;
                $("#project_selection").show();
                $( "#project_section_plus" ).show();
                $("select[name='project_id']").attr( 'required' );
                $('#project_name').removeAttr('required');
            }
        });


        $( "#advanced_options" ).click(function() {
            if(advanced_options_toggle == false) {
                $('.advanced_options_section').show();
                advanced_options_toggle = true;
            } else {
                $('.advanced_options_section').hide();
                advanced_options_toggle = false;
            }
        });

        $( "#test_click" ).click(function() {
            alert ($("#issue_date").val());
        });

    </script>
<?php endif; ?>