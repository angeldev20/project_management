<?php

class SlackLink extends ActiveRecord\Model {
    static $table_name = 'slack_links';

    static $belongs_to = array(
        array('user')
    );

    public static function deleteLinkWithSqlByTeamID($teamID){
        $clientTasks = SlackLink::find_by_sql("SELECT
                *
            FROM
                `slack_links`
            WHERE
                 `team_id` = '$teamID'
            ORDER BY
                `created` ASC
            ");
        if( count($clientTasks) ){
            $slack = SlackLink::find($clientTasks[0]->id);
            $slack->delete();
        }
        return ;
    }

    public static function deleteLinkBy($teamID, $access_token){
        $clientTasks = SlackLink::find_by_team_id_and_access_token($teamID, $access_token);
        if( $clientTasks!=null ) {
            $clientTasks->delete();
        }
        return false;
    }

    public static function updateUserInfo($id, $team_url, $slack_user, $slack_id){
        $slack = SlackLink::find_by_id($id);
        $slack->team_url = $team_url;
        $slack->slack_user = $slack_user;
        $slack->slack_id = $slack_id;
        $slack->save();
        return;
    }

    public static function getSlackLatestLink($userID){
        $result = false;
        $clientTasks = SlackLink::find_by_sql("SELECT
                *
            FROM
                `slack_links`
            WHERE
                 `user_id` = '$userID'
            ORDER BY
                `created` DESC
            ");
        if( count($clientTasks) ){
            $result = SlackLink::find($clientTasks[0]->id);
        }
        return $result;
    }

    public static function getSlackLinkWithToken($userID, $token){
        $result = false;
        $clientTasks = SlackLink::find_by_sql("SELECT
                *
            FROM
                `slack_links`
            WHERE
                 `user_id` = '$userID'
            AND
                 `access_token`= '$token'
            ORDER BY
                `created` DESC
            ");
        if( count($clientTasks) ){
            $result = SlackLink::find($clientTasks[0]->id);
        }
        return $result;
    }
}
