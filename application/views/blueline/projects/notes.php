<div class="dashboard-header text-center" style="padding: 0px;">
    <ul class="header-tabs">
        <li id="tab-item-0" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/tasks">Tasks</a></li>
        <li id="tab-item-1" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/gantt">Gantt</a></li>
        <li id="tab-item-2" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/files">Files</a></li>
        <li id="tab-item-3" data-tag="" class="active"><a href="/projects/view/<?= $project->id; ?>/notes">Notes</a></li>
        <li id="tab-item-4" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/team">Team</a></li>
        <li id="tab-item-5" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/invoices">Invoices</a></li>
    </ul>
</div>
<div class="col-xs-12 col-sm-12">
	<?php $attributes = array( 'class' => 'note-form', 'id' => '_notes' );
	echo form_open( base_url() . "projects/notes/" . $project->id, $attributes ); ?>
    <div class="table-head"><?= $this->lang->line( 'application_notes' ); ?> <span class=" pull-right"><a
                    id="send" name="send"
                    class="btn btn-primary button-loader"><?= $this->lang->line( 'application_save' ); ?></a></span><span
                id="changed"
                class="pull-right label label-warning"><?= $this->lang->line( 'application_unsaved' ); ?></span>
    </div>

    <textarea class="input-block-level summernote-note" name="note"
              id="textfield"><?= $project->note; ?></textarea>
	<?php echo form_close(); ?>
</div>

<script>
    jQuery(function ($) {
        var $top = $(".mainnavbar .topbar__center");
        var $button = $('<button type="button" class="dropdown-toggle transparent" data-toggle="dropdown" aria-expanded="false"> <i class="far fa-angle-down"/> </button>');
        var $ul = $('<ul class="dropdown-menu dropdown-menu--small" role="menu"/>');
        var $dd = $('<div class="chooser"/>');
        var $icon_div = $('<div class="icon-div"/>');
        var $fav_icon = $('<i class="<?= ( $project->sticky ? 'fa' : 'far' ) ?> fa-star" />');

        $top.addClass("has-chooser");

        $fav_icon.on("click", function (e) {
            e.preventDefault();

            if ($(this).hasClass('far')) {
                $(this).removeClass('far');
                $(this).addClass('fa');
            } else {
                $(this).removeClass('fa');
                $(this).addClass('far');
            }

            $.ajax({
                url: '/projects/sticky_json/<?= $id ?>',
                dataType: 'JSON',
                success: function (d) {
                    console.log(d);
                },
                error: function (e) {
                    console.log(e);
                }
            });
        });

        $dd.append($button);
        $dd.append($ul);
        $icon_div.append($fav_icon);

        $top.append($dd);
        $top.prepend($icon_div);

        $.ajax({
            url: '/projects/data',
            dataType: 'JSON',
            success: function (data) {
                if (data.status) {
                    data.projects.map(({id, name}) => {
                        $ul.append(`<li><a href="/projects/view/${id}/tasks">${name}</a></li>`);
                    });
                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    });
</script>