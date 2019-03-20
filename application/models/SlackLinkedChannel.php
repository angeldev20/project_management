<?php

class SlackLinkedChannel extends ActiveRecord\Model {
    static $table_name = 'slack_linked_channels';

    static $belongs_to  = array(
        array('project'),
        array('user'),
        array('slack_link')
    );

    public static function setConnectionFlag($projectID, $flag = 0){
        $channel = SlackLinkedChannel::find_by_project_id($projectID);
        if( $channel!=null ) {
            $channel->connection_flag = $flag;
            $channel->save();
            return $channel;
        }
        return false;
    }

    public static function getSlackChannelBy($projectID, $userID){
        $channel = SlackLinkedChannel::find( 'first',
            array('conditions' => array('project_id = ? AND user_id = ?', $projectID, $userID)) );

        return $channel;
    }

}
