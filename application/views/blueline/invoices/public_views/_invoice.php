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
        <label for="issue_date"><?=$this->lang->line('application_issue_date');?> *</label>
        <input id="issue_date" type="text" name="issue_date" class="required datepicker form-control" value="<?php if(isset($invoice)){echo $invoice->issue_date;} ?>"  required/>
 </div>
 <div class="form-group">
        <label for="due_date"><?=$this->lang->line('application_due_date');?> *</label>
        <input id="due_date" type="text" name="due_date" class="required datepicker-linked form-control" value="<?php if(isset($invoice)){echo $invoice->due_date;} ?>"  required/>
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
        <label for="terms"><?=$this->lang->line('application_terms');?></label>
        <textarea id="terms" name="terms" class="textarea required summernote-modal form-control" style="height:100px"><?php if(isset($invoice)){echo $invoice->terms;}else{ echo $core_settings->invoice_terms; }?></textarea>
 </div>
  <div class="form-group">
        <label for="terms"><?=$this->lang->line('application_custom_tax');?></label>
        <input class="form-control" name="tax" type="text" value="<?php if(isset($invoice)){ echo $invoice->tax;}else{echo $core_settings->tax;} ?>" />
 </div>
    <div class="form-group">
        <label for="terms"><?=$this->lang->line('application_second_tax');?></label>
        <input class="form-control" name="second_tax" type="text" value="<?php if(isset($invoice)){ echo $invoice->second_tax;} else {echo '0.00';} ?>"/>
 </div>

<?php if(isset($invoice_has_items)){ ?>
    <div class="form-group">
        <label for="value"><?=$this->lang->line('application_value');?></label>
        <input id="value" type="text" name="value" class="required form-control number"  value="<?=$invoice_has_items->value;?>" />
    </div>
<?php } else{ ?>
    <div class="form-group">
        <label for="value"><?=$this->lang->line('application_value');?></label>
        <input id="value" type="text" name="value" class="form-control number"  value="" />
    </div>
<?php } ?>
    <div class="form-group">
        <label for="amount"><?=$this->lang->line('application_quantity_hours');?></label>
        <input id="amount" type="text" name="amount" class="required form-control number comma-to-point"  value="<?php if(isset($invoice_has_items)){ echo $invoice_has_items->amount; }else{echo '1';} ?>"  />
    </div>
    <div class="form-group">
        <label for="description"><?=$this->lang->line('application_description');?></label>
        <textarea id="description" class="form-control" name="description"><?php if(isset($invoice_has_items)){ echo $invoice_has_items->description; } ?></textarea>
    </div>

        <div class="modal-footer">
        <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_save');?>"/>
        <a class="btn" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
        </div>

    <input type="hidden" name="company_id" value="0"/>
    <input type="hidden" name="project_id" value="0"/>
    <input id="reference" type="hidden" name="reference" class="form-control"  value="<?php if(isset($invoice)){echo $invoice->reference;} else{ echo $core_settings->invoice_reference; } ?>" />
    <input type="hidden" name="type" value="Service"/>
    <input type="hidden" name="name" value="Service"/>
<?php echo form_close(); ?>