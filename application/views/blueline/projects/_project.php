<?php
$attributes = array( 'class' => '', 'id' => '_project' );
echo form_open( $form_action, $attributes );
if ( isset( $project ) ) { ?>
    <input id="id" type="hidden" name="id" value="<?php echo $project->id; ?>"/>
<?php } ?>

<div class="form-group">
    <?php if ( ! empty( $core_settings->project_prefix ) ){ ?>
    <div class="input-group">
        <div class="input-group-addon"><?= $core_settings->project_prefix; ?></div> <?php } ?>
        <input type="hidden" name="reference" class="form-control" id="reference" value="<?php if ( isset( $project ) ) {
            echo $project->reference;
        } else {
            echo $core_settings->project_reference;
        } ?>" placeholder="<?= $this->lang->line( 'application_reference_id' ) ?>" required/>
        <?php if ( ! empty( $core_settings->project_prefix ) ){ ?></div><?php } ?>
</div>
<div class="form-group">
    <h5>Project Name *</h5>
    <input type="text" name="name" class="form-control" id="name" value="<?php if ( isset( $project ) ) {
        echo $project->name;
    } ?>" placeholder="Project Name" required/>
</div>

<div class="form-group hide" id="more-options-div">
    <div class="form-group">
        <h5>Choose Client</h5>
        <?php $options = array();
        $options['0']  = 'Choose an existing client';
        if(isset($companies)){
            foreach ( $companies as $value ):
                $options[ $value->id ] = $value->name;
            endforeach;
            if ( isset( $project ) && is_object( $project->company ) ) {
                $client = $project->company->id;
            } else {
                $client = "";
            }
            echo form_dropdown( 'company_id', $options, $client, 'style="width:100%" class="form-control"' );
        }
        ?>
    </div>
    <div class="form-group hide">
        <label for="progress"><?= $this->lang->line( 'application_progress' ); ?> <span
                    id="progress-amount"><?php if ( isset( $project ) ) {
                    echo $project->progress;
                } else {
                    echo "0";
                } ?></span> %</label>
        <div class="slider-group">
            <div id="slider-range"></div>
        </div>
        <input type="hidden" class="hidden" id="progress" name="progress" value="<?php if ( isset( $project ) ) {
            echo $project->progress;
        } else {
            echo "0";
        } ?>">
    </div>
    <div class="form-group hide">
        <div class="checkbox checkbox-attached">
            <label>
                <input name="progress_calc" value="1"
                       type="checkbox" <?php if ( isset( $project ) && $project->progress_calc == "1" ) { ?> checked="checked" <?php } ?>/>
                <span class="lbl"> <?= $this->lang->line( 'application_calculate_progress' ); ?> </span>
            </label>
            <script>
                $(document).ready(function () {
                    //slider config
                    $("#slider-range").slider({
                        range: "min",
                        min: 0,
                        max: 100,
                        <?php if(isset( $project ) && $project->progress_calc == "1"){ ?>disabled: true,<?php } ?>
                        value: <?php if ( isset( $project ) ) {
                        echo $project->progress;
                    } else {
                        echo "0";
                    } ?>,
                        slide: function (event, ui) {
                            $("#progress-amount").html(ui.value);
                            $("#progress").val(ui.value);
                        }
                    });
                });
            </script>
        </div>
    </div>

    <div class="form-group">
        <h5>Start Date</h5>
        <input class="form-control datepicker" name="start" id="start" type="text" value="<?php if ( isset( $project ) ) {
            echo $project->start;
        } ?>" placeholder="Pick a start date" />
        <span class="inside"><i class="far fa-calendar-alt"></i></span>
    </div>
    <div class="form-group">
        <h5>Due Date</h5>
        <input class="form-control datepicker-linked" name="end" id="end" type="text" value="<?php if ( isset( $project ) ) {
                   echo $project->end;
               } ?>" placeholder="Pick a due date" />
        <span class="inside"><i class="far fa-calendar-alt"></i></span>
    </div>

    <div class="form-group">
        

        <?php if ( $this->user && $this->user->admin == 1 ) { ?>
            <h5>Team Transparency</h5>
            <select class="form-control" name="hide_tasks" style="width: 100%;">
                <option value="">Choose an option</option>
                <option value="0" <?php if ( isset( $project ) ) {
                    if ( $project->hide_tasks == "0" ) { ?> selected="selected" <?php }} ?>>Yes, reveal team tasks.
                </option>
                <option value="1" <?php if ( isset( $project ) ) {
                    if ( $project->hide_tasks == "1" ) { ?> selected="selected" <?php }} ?>>No, hide team tasks.
                </option>
            </select>
        <?php } ?>
    </div>

    <div class="form-group">
        <h5>Client Transparency</h5>
        <select class="form-control" name="enable_client_tasks" style="width: 100%;">
            <option value="">Choose an option</option>
            <option value="1" <?php if ( isset( $project ) ) {
                    if ( $project->enable_client_tasks == "1" ) { ?> selected="selected" <?php }} ?>>Yes, allow clients to add tasks.
            </option>
            <option value="0" <?php if ( isset( $project ) ) {
                    if ( $project->enable_client_tasks == "0" ) { ?> selected="selected" <?php }} ?>>No, don't allow clients to add tasks.
                </option>
        </select>
    </div>

</div>

<div class="form-group hide">
    <input type="text" list="Projectcategorylist" autocomplete="off" name="category" class="form-control typeahead"
           id="category" value="<?php if ( isset( $project ) ) {
        echo $project->category;
    } ?>" placeholder="<?= $this->lang->line( 'application_category' ); ?>"/>
    <datalist id="Projectcategorylist">
        <?php if ( isset( $category_list ) ){
        foreach ( $category_list as $value ): ?>
        <option value="<?= $value->category ?>">
            <?php endforeach;
            } ?>
    </datalist>
</div>

<div class="form-group hide">
    <textarea class="input-block-level form-control" id="textfield" name="description" placeholder="<?= $this->lang->line( 'application_description' ); ?>"><?php if ( isset( $project ) ) {
            echo $project->description;
        } ?></textarea>
</div>

<div class="form-group hide">
    <label><?= $this->lang->line( 'application_slack_channel' ); ?></label><br>

    <label>
        <input name="connect_slk_channel" value="1"
               type="checkbox" <?php if ( $isLinkedChannel == true ) : ?> checked="checked" <?php endif ?> <?php if ( $isLinkedSlack == false ) : ?> disabled <?php endif ?>/>
        <span class="checkbox-lbl"> <?= $this->lang->line( 'application_create_channel' ); ?> </span>
    </label>
</div>

<div class="modal-footer" style="border: none;">
    <div class="col-md-6 text-left">
        <a href="#" id="more-options">
            <i class="far fa-sliders-h"></i>&nbsp;&nbsp;More Options
        </a>
        <a href="#" id="less-options" style="display: none;">
            <i class="far fa-sliders-h"></i>&nbsp;&nbsp;Less Options
        </a>
    </div>
    <div class="col-md-6">
        <input type="hidden" name="progress_calc" value="1" />
        <input type="submit" name="send" class="btn btn-success" value="<?= $this->lang->line( 'application_save' ); ?> &amp; <?= $this->lang->line( 'application_close' ); ?>" />
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#more-options').click(function() {
            $('#more-options-div').removeClass('hide');
            $('#more-options').css('display', 'none');
            $('#less-options').css('display', 'block');
        });

        $('#less-options').click(function() {
            $('#more-options-div').addClass('hide');
            $('#more-options').css('display', 'block');
            $('#less-options').css('display', 'none');
        });
    });
</script>

<?php echo form_close(); ?>
