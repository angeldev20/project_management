<div class="dashboard-header text-center" style="padding: 0px;">
    <ul class="header-tabs">
        <li id="tab-item-0" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/tasks">Tasks</a></li>
        <li id="tab-item-1" data-tag="" class="active"><a href="/projects/view/<?= $project->id; ?>/gantt">Gantt</a></li>
        <li id="tab-item-2" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/files">Files</a></li>
        <li id="tab-item-3" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/notes">Notes</a></li>
        <li id="tab-item-4" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/team">Team</a></li>
        <li id="tab-item-5" data-tag="" class=""><a href="/projects/view/<?= $project->id; ?>/invoices">Invoices</a></li>
    </ul>
</div>
<div class="col-xs-12 col-sm-12">
    <div class="table-head">
		<?= $this->lang->line( 'application_gantt' ); ?>
        <span class="pull-right">
            <div class="btn-group pull-right-responsive margin-right-3">
                <button type="button" class="btn btn-primary dropdown-toggle hide" data-toggle="dropdown"
                        aria-expanded="false">
                  <?= $this->lang->line( 'application_show_gantt_by' ); ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu">
                       <li><a href="#"
                              class="resize-gantt"><?= $this->lang->line( 'application_gantt_by_milestones' ); ?></a></li>
                       <li><a href="#"
                              class="users-gantt"><?= $this->lang->line( 'application_gantt_by_agents' ); ?></a></li>
                 </ul>
            </div>
      </span>
    </div>
    <div class="table-div min-height-410 gantt-width">
		<?php
		//get gantt data for Milestones
		$gantt_data = '
                                {
                                  name: "' . htmlspecialchars( $project->name ) . '", desc: "", values: [{ 
                                label: "", from: "' . $project->start . '", to: "' . $project->end . '", customClass: "gantt-headerline" 
                                }]},  ';
		foreach ( $project->project_has_milestones as $milestone ):
			$counter = 0;
			foreach ( $milestone->project_has_tasks as $value ):
				$milestone_Name = "";
				if ( $counter == 0 ) {
					$milestone_Name = $milestone->name;
					$gantt_data     .= '
                                {
                                  name: "' . htmlspecialchars( $milestone_Name ) . '", desc: "", values: [';

					$gantt_data .= '{ 
                                label: "", from: "' . $milestone->start_date . '", to: "' . $milestone->due_date . '", customClass: "gantt-timeline" 
                                }';
					$gantt_data .= ']
                                },  ';
				}

				$counter ++;
				$start      = ( $value->start_date ) ? $value->start_date : $milestone->start_date;
				$end        = ( $value->due_date ) ? $value->due_date : $milestone->due_date;
				$class      = ( $value->status == "done" ) ? "ganttGrey" : "";
				$gantt_data .= '
                          {
                            name: "", desc: "' . htmlspecialchars( $value->name ) . '", values: [';

				$gantt_data .= '{ 
                          label: "' . htmlspecialchars( $value->name ) . '", from: "' . $start . '", to: "' . $end . '", customClass: "' . $class . '" 
                          }';
				$gantt_data .= ']
                          },  ';
			endforeach;
		endforeach;

		//get gantt data for Users
		$gantt_data2 = '
                                { name: "' . htmlspecialchars( $project->name ) . '", desc: "", values: [{ 
                                label: "", from: "' . $project->start . '", to: "' . $project->end . '", customClass: "gantt-headerline" 
                                }]}, ';
        if(isset($project->project_has_workers)){
            foreach ( $project->project_has_workers as $worker ):
                $counter = 0;
                foreach ( $worker->getAllTasksInProject( $project->id, $worker->user->id ) as $value ):
                    $user_name = "";
                    if ( $counter == 0 ) {
                        $user_name   = $worker->user->firstname . " " . $worker->user->lastname;
                        $gantt_data2 .= '
                                    {
                                      name: "' . htmlspecialchars( $user_name ) . '", desc: "", values: [';

                        $gantt_data2 .= '{ 
                                    label: "", from: "' . $project->start . '", to: "' . $project->end . '", customClass: "gantt-timeline" 
                                    }';
                        $gantt_data2 .= ']
                                    },  ';
                    }
                    $counter ++;
                    $start       = ( $value->start_date ) ? $value->start_date : $project->start;
                    $end         = ( $value->due_date ) ? $value->due_date : $project->end;
                    $class       = ( $value->status == "done" ) ? "ganttGrey" : "";
                    $gantt_data2 .= '
                              {
                                name: "", desc: "' . htmlspecialchars( $value->name ) . '", values: [';

                    $gantt_data2 .= '{ 
                              label: "' . htmlspecialchars( $value->name ) . '", from: "' . $start . '", to: "' . $end . '", customClass: "' . $class . '", dataObj: {"id": ' . $value->id . '} 
                              }';
                    $gantt_data2 .= ']
                              },  ';
                endforeach;
            endforeach;
        }
		

		?>

        <div class="gantt"></div>
        <div id="gantData">
            <script type="text/javascript">

                jQuery(function($) {
                    ganttData2 = [<?=$gantt_data2;?>];
                    ganttChart(ganttData2);
                });

                $(document).on("click", '.resize-gantt', function (e) {
                    ganttData = [<?=$gantt_data;?>];
                    ganttChart(ganttData);
                });
                $(document).on("click", '.users-gantt', function (e) {
                    ganttData2 = [<?=$gantt_data2;?>];
                    ganttChart(ganttData2);
                });
            </script>
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