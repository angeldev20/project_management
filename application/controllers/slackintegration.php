<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SlackIntegration extends My_Controller
{

    /**
     * Decrypts the string using the server defined SALT string
     * @param string $stringToDecrypt
     * @return string
     */

    function index(){
        //TODO: must be remove. Only for testing in release version.
        die("aa");
    }

    function oauthverify() {
        if(isset($_REQUEST['state']) && isset($_REQUEST['action'])) {
            if( $_REQUEST['action'] == 'oauth') {
                $stateParts = explode(',', $_REQUEST['state']);
                $accountName = $stateParts[0];
                $domainParts = explode('.', $_SERVER['HTTP_HOST']);
                $domain = $domainParts[count($domainParts) - 2] . '.' . $domainParts[count($domainParts) - 1];
                unset($_REQUEST['state']);
                $req = '';
                foreach ($_REQUEST as $key => $value) {
                    $value = urlencode($value);
                    $req .= "&$key=$value";
                }
                redirect('https://' . $accountName . '.' . $domain . '/settings/links?' . $req, 'location');
            }
        }
        return;
    }

    public function slack_send_message()
    {
        if($_POST) {
            if( isset($_POST['ajax'])) {

                header('Content-Type: application/json');

                $message = isset($_POST['msg']) ? $_POST['msg']: '';
                $project_id = isset($_POST['proj_id']) ? $_POST['proj_id']: '';
                $sent_result = 0;

                if( empty($this->user) ) {
                    echo json_encode([
                        'msg' => 'error: This user is not available.'
                    ]);
                } else if ( empty($message) ){
                    echo json_encode([
                        'msg' => 'error: Message is able to empty.'
                    ]);
                } else if ( empty($project_id) ){
                    echo json_encode([
                        'msg' => 'error: Project id is not correct'
                    ]);

                } else {
                    $this->load->helper('slack');
                    // Setup the Slack handler
                    $link = SlackLink::getSlackLatestLink($this->user->id);
                    $str = $link ? json_encode($link->to_array()) : false;
                    $slack = initialize_slack_interface($str);

                    $channel = SlackLinkedChannel::find('first', array('conditions' => array("project_id=? AND user_id=?", $project_id, $this->user->id)));
                    $channelName = $channel->channel_name;
                    $result = slack_connect_channel($slack, $channelName);
                    if ($result['ok'] == true) {
                        slack_post_message($slack, $channelName, '@' . $this->user->firstname . ' : ' . $message, false);
                        if ($result['ok'] == true) {
                            $sent_result = 1;

                            echo json_encode([
                                'msg' => 'sent'
                            ]);
                        } else {
                            echo json_encode([
                                'msg' => 'error: ' . $result['msg']
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'msg' => 'error: ' . $result['msg']
                        ]);
                    }

                    ProjectChat::create( array(
                        'project_id' => $project_id,
                        'chat_message' => $message,
                        'sender_id' => $this->user->id,
                        'sent_result' => $sent_result,
                        'from_external'=> 0
                    ));
                }
            }
            die();

        } else {
            //this one will access database
            header('Content-Type: application/json');
            echo json_encode([
                'msg' => 'This request is not allowed.'
            ]);

            die();
        }
    }
    public function receive()
    {

        if($_POST) {
            $this->load->helper('slack');
            slack_slash_command();
        }

        header('Content-Type: application/json');
        echo json_encode([
            'text' => 'This request is not allowed.'
        ]);

        die();
    }
}
