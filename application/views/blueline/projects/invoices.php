<div class="dashboard-header text-center" style="padding: 0px;">
    <ul class="header-tabs">
        <li id="tab-item-0" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/tasks">Tasks</a></li>
        <li id="tab-item-1" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/gantt">Gantt</a></li>
        <li id="tab-item-2" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/files">Files</a></li>
        <li id="tab-item-3" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/notes">Notes</a></li>
        <li id="tab-item-4" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/team">Team</a></li>
        <li id="tab-item-5" data-tag="" class="active"><a href="/projects/view/<?= $project->id; ?>/invoices">Invoices</a></li>
    </ul>
</div>
<div class="col-xs-12 col-sm-12"><br>
    <a href="<?= base_url() ?>projects/invoice/<?= $project->id; ?>" class="btn btn-primary"
       data-toggle="mainmodal"><?= $this->lang->line( 'application_create_invoice' ); ?></a>
    <div class="table-head"><?= $this->lang->line( 'application_invoices' ); ?> <span
                class=" pull-right"></span></div>
    <div class="table-div">
        <table class="data table" id="invoices" rel="<?= base_url() ?>" cellspacing="0" cellpadding="0">
            <thead>
            <th width="70px" class="hidden-xs"><?= $this->lang->line( 'application_invoice_id' ); ?></th>
            <th><?= $this->lang->line( 'application_client' ); ?></th>
            <th class="hidden-xs"><?= $this->lang->line( 'application_issue_date' ); ?></th>
            <th class="hidden-xs"><?= $this->lang->line( 'application_due_date' ); ?></th>
            <th><?= $this->lang->line( 'application_status' ); ?></th>
            <th class="hidden-xs"><?= $this->lang->line( 'application_value' ); ?></th>
            <th class="hidden-xs"><?= $this->lang->line( 'application_action' ); ?></th>
            </thead>
			<?php foreach ( $project_has_invoices as $value ): ?>

                <tr id="<?= $value->id; ?>">
                    <td class="hidden-xs"
                        onclick=""><?= $core_settings->invoice_prefix; ?><?= $value->reference; ?></td>
                    <td onclick=""><span
                                class="label label-info"><?php if ( is_object( $value->company ) ) {
								echo $value->company->name;
							} ?></span></td>
                    <td class="hidden-xs">
                                    <span><?php $unix = human_to_unix( $value->issue_date . ' 00:00' );
	                                    echo '<span class="hidden">' . $unix . '</span> ';
	                                    echo date( $core_settings->date_format, $unix ); ?></span></td>
                    <td class="hidden-xs"><span class="label <?php if ( $value->status == "Paid" ) {
							echo 'label-success';
						}
						if ( $value->due_date <= date( 'Y-m-d' ) && $value->status != "Paid" ) {
							echo 'label-important tt" title="' . $this->lang->line( 'application_overdue' );
						} ?>"><?php $unix = human_to_unix( $value->due_date . ' 00:00' );
							echo '<span class="hidden">' . $unix . '</span> ';
							echo date( $core_settings->date_format, $unix ); ?></span> <span
                                class="hidden"><?= $unix; ?></span></td>
                    <td onclick=""><span
                                class="label <?php $unix = human_to_unix( $value->sent_date . ' 00:00' );
								if ( $value->status == "Paid" ) {
									echo 'label-success';
								} elseif ( $value->status == "Sent" ) {
									echo 'label-warning tt" title="' . date( $core_settings->date_format, $unix );
								} ?>"><?= $this->lang->line( 'application_' . $value->status ); ?></span>
                    </td>
                    <td class="hidden-xs"><?php if ( isset( $value->sum ) ) {
							echo display_money( $value->sum, $value->currency );
						} ?> </td>

                    <td class="option hidden-xs" width="8%">
                        <button type="button" class="btn-option delete po" data-toggle="popover"
                                data-placement="left"
                                data-content="<a class='btn btn-danger po-delete ajax-silent' href='<?= base_url() ?>invoices/delete/<?= $value->id; ?>'><?= $this->lang->line( 'application_yes_im_sure' ); ?></a> <button class='btn po-close'><?= $this->lang->line( 'application_no' ); ?></button> <input type='hidden' name='td-id' class='id' value='<?= $value->id; ?>'>"
                                data-original-title="<b><?= $this->lang->line( 'application_really_delete' ); ?></b>">
                            <i class="icon dripicons-cross"></i></button>
                        <a href="<?= base_url() ?>invoices/update/<?= $value->id; ?>" class="btn-option"
                           data-toggle="mainmodal"><i class="icon dripicons-gear"></i></a>
                    </td>
                </tr>

			<?php endforeach; ?>
        </table>
		<?php if ( ! $project_has_invoices ) { ?>
            <div class="no-files">
                <i class="icon dripicons-document"></i><br>

				<?= $this->lang->line( 'application_no_invoices_yet' ); ?>
            </div>
		<?php } ?>
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