<div class="dashboard-header text-center" style="padding: 0px;">
    <ul class="header-tabs">
        <li id="tab-item-0" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/tasks">Tasks</a></li>
        <li id="tab-item-1" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/gantt">Gantt</a></li>
        <li id="tab-item-2" data-tag="" class="active"><a href="/projects/view/<?= $project->id; ?>/files">Files</a></li>
        <li id="tab-item-3" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/notes">Notes</a></li>
        <li id="tab-item-4" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/team">Team</a></li>
        <li id="tab-item-5" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/invoices">Invoices</a></li>
    </ul>
</div>
<div class="col-xs-12 col-sm-3">
    <div class="table-head"><?= $this->lang->line( 'application_files' ); ?>
        <span class=" pull-right">
    <a class="btn btn-default toggle-media-view tt"
       data-original-title="<?= $this->lang->line( 'application_media_view' ); ?>"><i class="ion-image"></i></a>
    <a class="btn btn-default toggle-media-view hidden tt"
       data-original-title="<?= $this->lang->line( 'application_list_view' ); ?>"><i class="ion-android-list"></i></a>
    <a href="<?= base_url() ?>projects/media/<?= $project->id; ?>/add" class="btn btn-primary"
       data-toggle="mainmodal"><?= $this->lang->line( 'application_add_media' ); ?></a>
</span></div>

    <div class="media-uploader">
		<?php $attributes = array( 'class' => 'dropzone', 'id' => 'dropzoneForm' );
		echo form_open_multipart( base_url() . "projects/dropzone/" . $project->id, $attributes ); ?>
		<?php echo form_close(); ?>
    </div>

</div>
<div class="col-xs-12 col-sm-9">


    <div class=" min-height-410 media-view-container">
        <div class="mediaPreviews dropzone"></div>
		<?php
		foreach ( $project->project_has_files as $value ):
			$type = explode( "/", $value->type );
			$thumb = "./files/media/thumb_" . $value->savename;

			if ( file_exists( $thumb ) ) {
				$filename = base_url() . "files/media/thumb_" . $value->savename;
			} else {
				$filename = base_url() . "files/media/" . $value->savename;
			}
			?>
            <div class="media-galery">
                <a href="<?= base_url() ?>projects/media/<?= $project->id; ?>/view/<?= $value->id; ?>">
                    <div class="overlay">

						<?= $value->name; ?><br><br>
                        <i class="ion-android-download"></i> <?= $value->download_counter; ?>

                    </div>
                </a>
                <div class="file-container">

					<?php switch ( $type[0] ) {
						case "image": ?>
                            <img class="b-lazy"
                                 src=data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==
                                 data-src="<?= $filename ?>"
                                 alt="<?= $value->name; ?>"
                            />
							<?php break; ?>

						<?php default: ?>
                            <div class="icon-box">
                                <i class="ion-ios-copy-outline"></i><br>
								<?= $type[1] ?>
                            </div>
							<?php break; ?>

						<?php } ?>
                </div>
                <div class="media-galery--footer"><?= $value->name; ?></div>
            </div>

		<?php endforeach; ?>
    </div>

    <div class="media-list-view-container hidden">
        <div class="table-head"><?= $this->lang->line( 'application_media' ); ?> <span class=" pull-right"><a
                        href="<?= base_url() ?>projects/media/<?= $project->id; ?>/add" class="btn btn-primary"
                        data-toggle="mainmodal"><?= $this->lang->line( 'application_add_media' ); ?></a></span>
        </div>
        <div class="table-div min-height-410">
            <table id="media" class="table data-media"
                   rel="<?= base_url() ?>projects/media/<?= $project->id; ?>" cellspacing="0" cellpadding="0">
                <thead>
                <tr>
                    <th class="hidden"></th>
                    <th><?= $this->lang->line( 'application_name' ); ?></th>
                    <th class="hidden-xs"><?= $this->lang->line( 'application_filename' ); ?></th>
                    <th class="hidden-xs"><?= $this->lang->line( 'application_phase' ); ?></th>
                    <th class="hidden-xs"><i class="icon dripicons-download"></i></th>
                    <th><?= $this->lang->line( 'application_action' ); ?></th>
                </tr>
                </thead>

                <tbody>
				<?php foreach ( $project->project_has_files as $value ): ?>

                    <tr id="<?= $value->id; ?>">
                        <td class="hidden"><?= human_to_unix( $value->date ); ?></td>
                        <td onclick=""><?= $value->name; ?></td>
                        <td class="hidden-xs"><?= $value->filename; ?></td>
                        <td class="hidden-xs"><?= $value->phase; ?></td>
                        <td class="hidden-xs"><span class="label label-info tt"
                                                    title="<?= $this->lang->line( 'application_download_counter' ); ?>"><?= $value->download_counter; ?></span>
                        </td>
                        <td class="option " width="10%">
                            <button type="button" class="btn-option btn-xs po" data-toggle="popover"
                                    data-placement="left"
                                    data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?= base_url() ?>projects/media/<?= $project->id; ?>/delete/<?= $value->id; ?>'><?= $this->lang->line( 'application_yes_im_sure' ); ?></a> <button class='btn po-close'><?= $this->lang->line( 'application_no' ); ?></button> <input type='hidden' name='td-id' class='id' value='<?= $value->id; ?>'>"
                                    data-original-title="<b><?= $this->lang->line( 'application_really_delete' ); ?></b>">
                                <i class="icon dripicons-cross"></i></button>
                            <a href="<?= base_url() ?>projects/media/<?= $project->id; ?>/update/<?= $value->id; ?>"
                               class="btn-option" data-toggle="mainmodal"><i
                                        class="icon dripicons-gear"></i></a>
                        </td>

                    </tr>

				<?php endforeach; ?>


                </tbody>
            </table>
			<?php if ( ! $project->project_has_files ) { ?>
                <div class="no-files">
                    <i class="icon dripicons-cloud-upload"></i><br>
                    No files have been uploaded yet!
                </div>
			<?php } ?>
        </div>
    </div>
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