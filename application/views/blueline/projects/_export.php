<?php
$attributes = array('class' => '', 'id' => '_project');
echo form_open($form_action, $attributes);
if(isset($project)){ ?>
    <input id="id" type="hidden" name="id" value="<?php echo $project->id; ?>" />
<?php } ?>
<label class="col-sm-2 control-label"><?=$this->lang->line('application_projects');?></label>
<div class="col-sm-10">
    <?php foreach ($projects as $project) : ?>
        <div class="checkbox">
            <label>
                <input name="projects[<?php echo $project->id;?>]" value="<?php echo $project->id;?>" type="checkbox"><?php echo $project->ProjectName; ?></label>
        </div>
    <?php endforeach; ?>
</div>


<div class="modal-footer">
    <input type="submit" name="send" class="btn btn-primary" value="<?=$this->lang->line('application_export');?>"/>
    <a class="btn btn-default" data-dismiss="modal"><?=$this->lang->line('application_close');?></a>
</div>

<?php echo form_close(); ?>
