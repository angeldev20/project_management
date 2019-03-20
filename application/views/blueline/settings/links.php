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
            <div class="table-head"><?=$this->lang->line('application_slack_link');?>
                <span class="pull-right">
                    <p>
                        <a href="https://slack.com/oauth/authorize?scope=commands,channels:write,chat:write:user,search:read,chat:write:bot&client_id=<?php echo $slack->get_client_id(); ?>&state=<?php echo $state;?>"><img alt="Add to Slack" height="40" width="139" src="https://platform.slack-edge.com/img/add_to_slack.png" srcset="https://platform.slack-edge.com/img/add_to_slack.png 1x, https://platform.slack-edge.com/img/add_to_slack@2x.png 2x" /></a>
                    </p>
                </span>
            </div>
            <div class="table-div">
                <table id="users" class="data-no-search table" cellspacing="0" cellpadding="0">
                    <thead>
                        <th style="width:20px"></th>
                        <th><?=$this->lang->line('application_slack_team');?></th>
                        <th class="hidden-xs"><?=$this->lang->line('application_slack_domain');?></th>
                        <th class="hidden-xs"><?=$this->lang->line('application_slack_id');?></th>
                        <th><?=$this->lang->line('application_action');?></th>
                    </thead>
                    <tbody>
                        <?php foreach ($slack_links as $slack_link) : ?>
                            <tr id="<?=$slack_link->team_id;?>">

                                <td  style="width:20px">
                                    <img class="mini-slack-team-ic" src="<?php echo "https://a.slack-edge.com/0180/img/avatars-teams/ava_0014-44.png" ?>"/>
                                </td>

                                <td><?php echo $slack_link->team_name ?></td>
                                <td class="hidden-xs"><?php echo $slack_link->team_url ?></td>
                                <td class="hidden-xs"><?php echo $slack_link->team_id ?></td>
                                <td class="option" width="11%">
                                    <a class='btn btn-danger po-delete' href='<?=base_url()?>settings/unlink_slack/<?=$slack_link->team_id;?>/<?=$slack_link->access_token;?>'><?=$this->lang->line('application_remove_button');?></a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <?php if ( $result_message ) : ?>
                    <p class="notification">
                        <?php echo $result_message; ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>