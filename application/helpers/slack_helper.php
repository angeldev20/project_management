<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// Define Slack application identifiers
// Even better is to put these in environment variables so you don't risk exposing
// them to the outer world (e.g. by committing to version control)

// Include our Slack interface classes
require_once(APPPATH.'third_party/slack-interface/class-slack.php');
require_once(APPPATH.'third_party/slack-interface/class-slack-access.php');
require_once(APPPATH.'third_party/slack-interface/class-slack-api-exception.php');

use Slack_Interface\Slack;
use Slack_Interface\Slack_API_Exception;

/**
 * Initializes the Slack handler object, loading the authentication
 * information from a text file. If the text file is not present,
 * the Slack handler is initialized in a non-authenticated state.
 *
 * @param bool|string  $str     The Slack interface object
 *
 * @return Slack    The Slack interface object
 */
function initialize_slack_interface($str) {
    if(!defined('SLACK_CHANNEL_PREFIX'))
    {
        define('SLACK_CHANNEL_PREFIX', 'spera_');
    }
    // Read the access data from a text file
    if ( $str ) {
        $access_string = $str;
    } else {
        $access_string = '{}';
    }

    // Decode the access data into a parameter array
    $access_data = json_decode( $access_string, true );
    if( isset($access_data['scope']))
        $access_data['scope'] = json_decode($access_data['scope'], true);
    if( isset($access_data['incoming_webhook']))
        $access_data['incoming_webhook'] = json_decode($access_data['incoming_webhook'], true);

    $slack = new Slack( $access_data );

    // Register slash commands
    $slack->register_slash_command( '/spera', 'slack_command_spera' );
    $slack->register_slash_command( '/speralocal', 'slack_command_spera' );

    return $slack;
}

/**
 * Executes an application action (e.g. 'oauth').
 *
 * @param Slack  $slack     The Slack interface object
 * @param string $code      The code for slack oauth
 * @param string $user_id   User ID
 *
 * @return array  A result message to show to the user
 */
function slack_oauth( $slack, $code, $user_id ) {
    $result = array();
    $user = User::find($user_id);

    // Exchange code to valid access token
    try {
        $access = $slack->do_oauth( $code )->to_json();
        $access = json_decode($access, true);
        if ( $access ) {
            $already = false;
            foreach ($user->slack_links as $slack_link){
                if( $slack_link->team_id == $access['team_id'] ){
                    $already = $slack_link;
                    break;
                }
            }
            if( $already === false ) {
                $attributes = array(
                    'user_id' => $user_id,
                    'access_token' => isset($access['access_token']) ? $access['access_token'] : '',
                    'scope' => isset($access['scope']) ? json_encode($access['scope']) : '',
                    'team_name' => isset($access['team_name']) ? $access['team_name'] : '',
                    'team_id' => isset($access['team_id']) ? $access['team_id'] : '',
                    'incoming_webhook' => isset($access['incoming_webhook']) ? json_encode($access['incoming_webhook']) : '',
                );
                $link = SlackLink::create($attributes);

                slack_update_user_info($slack, $link->id);

                $result['msg'] = 'The application was successfully added to your Slack channel';

            } else {
                slack_update_user_info($slack, $already->id);
                $result['msg'] = 'This slack team id is already linked into your account';
            }
            $result['ok'] = true;
            $result['content'] = $access;
        }
    } catch ( Slack_API_Exception $e ) {
        $result['ok'] = false;
        $result['msg'] = $e->getMessage();
    }

    return $result;
}


/**
 * Executes an application action (e.g. 'oauth').
 *
 * @param Slack  $slack     The Slack interface object
 * @param string $link_id   the id in Slack_links table
 *
 * @return string   A result message to show to the user
 */
function slack_update_user_info( $slack, $link_id ){
    try {
        $json = $slack->update_info_if_needed();
        if ($json != false) {
            SlackLink::updateUserInfo($link_id, $json['url'], $json['user'], $json['user_id']);
            return true;
        }
        return false;
    } catch ( Slack_API_Exception $e ) {
        return false;
    }
}

function slack_unlink_oauth($user_id, $access_token, $team_id){
    // Setup the Slack handler
    $link = SlackLink::getSlackLinkWithToken($user_id, $access_token);
    $str = $link ? json_encode($link->to_array()) : false;
    $slack = initialize_slack_interface($str);
    slack_revoke_connect($slack);

    SlackLink::deleteLinkBy( $team_id, $access_token );
}

/**
 * Executes an application action (e.g. 'oauth').
 *
 * @param Slack  $slack     The Slack interface object
 * @param string $message   The notification message
 *
 * @return string   A result message to show to the user
 */
function slack_send_notification( $slack, $message ) {
    try {
        $slack->send_notification( $message );
        $result_message = 'Notification sent to Slack channel.';
    } catch ( Slack_API_Exception $e ) {
        $result_message = $e->getMessage();
    }

    return $result_message;
}

/**
 * Executes an application action (e.g. 'oauth').
 *
 * @param Slack  $slack     The Slack interface object
 *
 * @return string   A result message to show to the user
 */
function slack_slash_command( )
{
    $slack = initialize_slack_interface(false);

    $slack->do_slash_command();

}


/**
 * A simple slash command that returns a random joke to the Slack channel.
 *
 * @return array        A data array to return to Slack
 */
function slack_command_spera($text)
{
    $arr = explode(' ', $text, 2);
    $type = $arr[0];
    $content = '';

    if( $type !== 'message' && $type !== 'link') {

        $sampleGuide = array(
            "text" => "Sorry, I don't know what to do with: \"" . $text . "\". Try one of these instead?",
            "attachments"=> array(
                array(
                    "text"=> "Link a project from your Spera platform to this Slack channel\n`/spera link [Spera platform URL]`\nSend a message to chat box of your project connected with this channel\n`/spera message [message]`",
                    "mrkdwn_in"=> [ "text" ],
                    "color"=> "good")
            )
        );

        return $sampleGuide;
    }


    $content = $arr[1];

    $db1['default']['database'] = 'platform_' . ENVIRONMENT;

    switch (ENVIRONMENT) {
        case 'release':
        case 'testing':
        case 'development':
            $db1['default']['hostname'] = '127.0.0.1';
            $db1['default']['username'] = 'platform_' . ENVIRONMENT;
            $db1['default']['password'] = DEVELOPMENT_DB_PW;
            break;
        case 'production':
            $db1['default']['hostname'] = PRODUCTION_RDS_HOST;
            $db1['default']['username'] = 'platform';
            $db1['default']['password'] = PRODUCTION_DB_PW;
            break;

    }
    $tempDatabase = mysqli_connect(
        $db1['default']['hostname'],
        $db1['default']['username'],
        $db1['default']['password'],
        $db1['default']['database'],
        3306);


    $team_id            = isset($_POST['team_id']) ? $_POST['team_id']: '';
    $slack_id           = isset($_POST['user_id']) ? $_POST['user_id']: '';
    $channel_id         = isset($_POST['channel_id']) ? $_POST['channel_id']: '';
    $channel_name       = isset($_POST['channel_name']) ? $_POST['channel_name']: '';
    $slack_user_name    = isset($_POST['user_name']) ? $_POST['user_name']: '';
    $team_domain        = isset($_POST['team_domain']) ? $_POST['team_domain']: '';

    $sql = "SELECT * FROM slack_channel_links WHERE team_id='" . $team_id . "' AND slack_user_id='" . $slack_id . "' AND channel_id='" .$channel_id. "';";
    $channel_link = $tempDatabase->query($sql);

    if( $type === 'link'){
        $regex = '/^https{0,1}:\/\/([^ ]+)\.spera\.(local|io).*$/';
        if( preg_match($regex, $content) ) {
            $accountUrlPrefix = preg_replace($regex, "$1", $content);
            $sql = "SELECT * FROM accounts WHERE accountUrlPrefix='" . $accountUrlPrefix . "';";
            $query = $tempDatabase->query($sql);

            if( $query->num_rows > 0){
                $account = (object) $query->fetch_assoc();

                if($channel_link->num_rows > 0) {
                    $sql = sprintf(
                        'UPDATE slack_channel_links SET channel_name=\'%1$s\', slack_user_name=\'%2$s\', platform_id=\'%3$s\' WHERE team_id=\'%4$s\' AND slack_user_id=\'%5$s\' AND channel_id=\'%6$s\';',
                        $channel_name,
                        $slack_user_name,
                        $account->id,
                        $team_id,
                        $slack_id,
                        $channel_id
                    );
                } else {
                    $sql = sprintf(
                        'INSERT INTO slack_channel_links (channel_name, slack_user_name, platform_id, team_id, slack_user_id, channel_id ) VALUES (\'%1$s\', \'%2$s\', \'%3$s\', \'%4$s\', \'%5$s\', \'%6$s\');',
                        $channel_name,
                        $slack_user_name,
                        $account->id,
                        $team_id,
                        $slack_id,
                        $channel_id
                    );
                }

                $tempDatabase->query($sql);
                return array( 'text' => "Successfully link a project from your Spera platform to this Slack channel." );

            } else {
                return array( 'text' => "We can't find your Spera platform - " . $accountUrlPrefix );
            }
        } else {
            return array( 'text' => "We can't find your Spera platform." );
        }

    } else if( $type === 'message' ) {

        if( $channel_link->num_rows == 0){
            return array(
                "text" => "This channel didn't link any Spera platform yet. Try link a Spera platform",
                "attachments"=> array(
                    array(
                        "text"=> "Link a project from your Spera platform to this Slack channel\n`/spera link [Spera platform URL]`\nExample: `/spera link https://example.spera.io/`",
                        "mrkdwn_in"=> [ "text" ],
                        "color"=> "warning")
                )
            );
        }

        $result = '';

        $channel_link = (object) $channel_link->fetch_assoc();

        $sql = "SELECT * FROM accounts WHERE id='" . $channel_link->platform_id . "';";
        $account = $tempDatabase->query($sql);
        if( $account->num_rows == 0) {
            array(
                "text" => "The Spera platform is not available now. Try link an other Spera platform",
                "attachments"=> array(
                    array(
                        "text"=> "Link a project from your Spera platform to this Slack channel\n`/spera link [Spera platform URL]`\nExample: `/spera link https://example.spera.io/`",
                        "mrkdwn_in"=> [ "text" ],
                        "color"=> "warning")
                )
            );
        }

        $accountObject = new account();
        $account = (object)$account->fetch_assoc();
        $accountDbPassword = $accountObject->decryptString($account->db_password)->getDecryptedString();
        $accountUrlPrefix = $account->accountUrlPrefix;

        $db1[$accountUrlPrefix]['hostname'] = $account->db_hostname;
        $db1[$accountUrlPrefix]['username'] = $account->db_username;
        $db1[$accountUrlPrefix]['password'] = $accountDbPassword;
        $db1[$accountUrlPrefix]['database'] = $account->db_database;

        $accountDatabase = mysqli_connect(
            $db1[$accountUrlPrefix]['hostname'],
            $db1[$accountUrlPrefix]['username'],
            $db1[$accountUrlPrefix]['password'],
            $db1[$accountUrlPrefix]['database'],
            3306);

        // Setup the Slack handler
        $sql = "SELECT * FROM slack_links WHERE team_id='" . $team_id . "' AND slack_id='" . $slack_id . "';";
        $link = $accountDatabase->query($sql);
        if( $link->num_rows == 0 ){
            return array( "text" => "The Spera platform isn't connected with this workspace. Please sign in with Slack in your Spera and try again.");
        }

        $link = (object) $link->fetch_assoc();

        $sql = "SELECT * FROM slack_linked_channels WHERE slack_link_id='" . $link->id . "' AND channel_name='" . $channel_name . "';";
        $linked_channel = $accountDatabase->query($sql);
        if( $linked_channel->num_rows == 0 ){
            return array( "text" => "This is channel isn't connected with any project in Spera.");
        }

        $linked_channel = (object) $linked_channel->fetch_assoc();


        $sql = "SELECT * FROM projects WHERE id='" . $linked_channel->project_id . "';";
        $project = $accountDatabase->query($sql);
        if( $project->num_rows == 0 ){
            return array( "text" => "The Spera project connected this channel is not available no longer.");
        }

        $project = (object) $project->fetch_assoc();
        $sender_id = @$linked_channel->user_id;

        $sql = sprintf(
            'INSERT INTO project_chats (project_id, chat_message, sender_id, sent_result, from_external, slack_id, team_id, channel_id ) VALUES (\'%1$s\', \'%2$s\', \'%3$s\', \'%4$s\', \'%5$s\', \'%6$s\', \'%7$s\', \'%8$s\');',
            $project->id,
            $content,
            $sender_id,
            1,
            1,
            $slack_id,
            $team_id,
            $linked_channel->id
        );

        if( $accountDatabase->query($sql) ) {
            $result = 'Sent the message to "' . $project->name . '" project in "' . $accountUrlPrefix . '" Spera Platform';

            return array(
                "response_type" => "in_channel",
                "text" => $content,
                "attachments" =>
                    array(array("text" => $result,
                        "color" => "#764FA5"))
            );
        } else {
            $result = "Something went a wrong. We didn\'t send this message.";

            return array(
                "text" => $content,
                "attachments" =>
                    array(array("text" => $result,
                        "color" => "#764FA5"))
            );
        }
    }
}

/**
 * Executes an application action (e.g. 'oauth').
 *
 * @param Slack  $slack     The Slack interface object
 * @param string $name    The name of slack channel
 *
 * @return array   A result message to show to the user
 */
function slack_connect_channel( $slack, $name ) {
    $result = array();

    // Exchange code to valid access token
    try {
        $result['channel'] = $slack->create_channel( $name );

        $result['ok'] = true;
        $result['msg'] = 'The channel was successfully created/connected to this project';

    } catch ( Slack_API_Exception $e ) {
        $result['ok'] = false;
        $result['msg'] = $e->getMessage();
    }

    return $result;
}

/**
 * Executes an application action (e.g. 'oauth').
 *
 * @param Slack  $slack     The Slack interface object
 *
 */
function slack_revoke_connect( $slack ) {
    try {
        $slack->revoke_connect( );
    } catch ( Slack_API_Exception $e ) {
    }
}

/**
 * Post a message to slack.
 *
 * @param Slack  $slack     The Slack interface object
 * @param string $channel    The name of slack channel
 * @param string $message    The message to be sent
 * @param bool $as_user    sent by user or bot
 *
 * @return string   A result message to show to the user
 */
function slack_post_message( $slack, $channel, $message, $as_user = true ) {
    $result = array();

    // Exchange code to valid access token
    try {
        $slack->post_message( $channel, $message, $as_user );

        $result['ok'] = true;
        $result['msg'] = 'The channel was successfully created/connected to this project';
    } catch ( Slack_API_Exception $e ) {
        $result['ok'] = false;
        $result['msg'] = $e->getMessage();
    }

    return $result;
}