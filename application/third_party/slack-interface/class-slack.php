<?php
namespace Slack_Interface;

use Requests;

/**
 * A basic Slack interface you can use as a starting point
 * for your own Slack projects.
 *
 * @author Jarkko Laine <jarkko@jarkkolaine.com>
 */
class Slack {

	private static $api_root = 'https://slack.com/api/';

	/**
	 * @var Slack_Access    Slack authorization data
	 */
	private $access;

	/**
	 * @var array $slash_commands An array of slash commands attached to this Slack interface
	 */
	private $slash_commands;

	/**
	 * Sets up the Slack interface object.
	 *
	 * @param array $access_data An associative array containing OAuth
	 *                           authentication information. If the user
	 *                           is not yet authenticated, pass an empty array.
	 */
	public function __construct( $access_data ) {
		if ( $access_data ) {
			$this->access = new Slack_Access( $access_data );
		}

		$this->slash_commands = array();
	}

    /**
     * Sets up the Slack interface object.
     *
     * @param array $access_data An associative array containing OAuth
     *                           authentication information. If the user
     *                           is not yet authenticated, pass an empty array.
     */
    public function setAccessWith( $access_data ) {
        if ( $access_data ) {
            $this->access = new Slack_Access( $access_data );
        }
    }

	/**
	 * Checks if the Slack interface was initialized with authorization data.
	 *
	 * @return bool True if authentication data is present. Otherwise false.
	 */
	public function is_authenticated() {
		return isset( $this->access ) && $this->access->is_configured();
	}

    /**
     * Completes the OAuth authentication flow by exchanging the received
     * authentication code to actual authentication data.
     *
     * @param string $code  Authentication code sent to the OAuth callback function
     *
     * @return bool|Slack_Access    An access object with the authentication data in place
     *                              if the authentication flow was completed successfully.
     *                              Otherwise false.
     *
     * @throws Slack_API_Exception
     */
    public function do_oauth( $code ) {
        // Set up the request headers
        $headers = array( 'Accept' => 'application/json' );

        // Add the application id and secret to authenticate the request
        $options = array( 'auth' => array( $this->get_client_id(), $this->get_client_secret() ) );

        // Add the one-time token to request parameters
        $data = array( 'code' => $code );

        $response = Requests::post( self::$api_root . 'oauth.access', $headers, $data, $options );

        // Handle the JSON response
        $json_response = json_decode( $response->body );

        if ( ! $json_response->ok ) {
            // There was an error in the request
            throw new Slack_API_Exception( $json_response->error );
        }

        // The action was completed successfully, store and return access data
        $this->access = new Slack_Access(
            array(
                'access_token' => $json_response->access_token,
                'scope' => explode( ',', $json_response->scope ),
                'team_name' => $json_response->team_name,
                'team_id' => $json_response->team_id
                //'incoming_webhook' => array()
            )
        );

        return $this->access;
    }

    /**
     * Get user info after authenticate
     *
     * @return bool   An access object with the authentication data in place
     *                              if the authentication flow was completed successfully.
     *                              Otherwise false.
     *
     * @throws Slack_API_Exception
     */
    public function get_auth_info(  ) {

        if ( ! $this->is_authenticated() ) {
            throw new Slack_API_Exception( 'Access token not specified' );
        }

        // Post to webhook stored in access object
        $headers = array( 'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->access->get_access_token());

        $url = self::$api_root . 'auth.test';

        $response = Requests::post( $url, $headers );
        $json = json_decode($response->body, true);
        if ( $json['ok'] != true ) {
            throw new Slack_API_Exception( 'There was an error when create a channel in Slack' );
        }
        return $json;
    }

    /**
     * Update user info after authenticate
     *
     * @return bool    true if user info updated
     *                      Otherwise false
     *
     */
    public function update_info_if_needed(){
        if( !empty($this->access) && $this->access->is_needed_update_info() ){
            $json = $this->get_auth_info();
            $this->access->update_info($json['url'], $json['user'], $json['user_id']);
            return $json;
        }
        return false;
    }

	/**
	 * Sends a notification to the Slack channel defined in the
	 * authorization (Add to Slack) flow.
	 *
	 * @param string $text          The message to post to Slack
	 * @param array $attachments    Optional list of attachments to send
	 *                              with the notification
	 *
	 * @throws Slack_API_Exception
	 */
	public function send_notification( $text, $attachments = array() ) {
		if ( ! $this->is_authenticated() ) {
			throw new Slack_API_Exception( 'Access token not specified' );
		}
        $text = $this->convert_for_slack($text);

		// Post to webhook stored in access object
		$headers = array( 'Accept' => 'application/json' );

		$url = $this->access->get_incoming_webhook();
		if( strlen($url) >0 ) {
            $data = json_encode(
                array(
                    'text' => $text,
                    'attachments' => $attachments,
                    'channel' => $this->access->get_incoming_webhook_channel()
                )
            );

            $response = Requests::post($url, $headers, $data);

            if ($response->body != 'ok') {
                throw new Slack_API_Exception('There was an error when posting to Slack');
            }
        }
	}

	/**
	 * Registers a new slash command to be available through this
	 * Slack interface.
	 *
	 * @param string    $command    The slash command
	 * @param callback  $callback   The function to call to execute the command
	 */
	public function register_slash_command( $command, $callback ) {
		$this->slash_commands[$command] = $callback;
	}

	/**
	 * Runs the slash command passed in the $_POST data if the
	 * command is valid and has been registered using register_slash_command.
	 *
	 * The response written by the function will be read by Slack.
	 */
	public function do_slash_command() {

        header('Content-Type: application/json');

		// Collect request parameters
        $token          = isset($_POST['token']) ? $_POST['token']: '';
        $team_id        = isset($_POST['team_id']) ? $_POST['team_id']: '';
        $slack_id       = isset($_POST['user_id']) ? $_POST['user_id']: '';
        $team_domain    = isset($_POST['team_domain']) ? $_POST['team_domain']: '';
        $channel_id     = isset($_POST['channel_id']) ? $_POST['channel_id']: '';
        $channel_name   = isset($_POST['channel_name']) ? $_POST['channel_name']: '';
        $command        = isset($_POST['command']) ? $_POST['command']: '';
        $message        = isset($_POST['text']) ? $_POST['text']: '';


        // Use the command verification token to verify the request
        if( !empty( $token ) && $this->get_command_token() == $token){
            if (isset($this->slash_commands[$command])) {
                // This slash command exists, call the callback function to handle the command
                $response = call_user_func($this->slash_commands[$command], $message);
                echo json_encode($response);
            } else {
                // Unknown slash command
                echo json_encode(array(
                    'text' => "Sorry, I don't know how to respond to the command."
                ));
            }
        } else {
            echo json_encode( array(
                'text' => 'Oops... Something went wrong.'
            ) );
        }

		// Don't print anything after the response
		die();
	}

	/**
	 * Returns the Slack client ID.
	 *
	 * @return string   The client ID or empty string if not configured
	 */
	public function get_client_id() {
        if (defined('ENVIRONMENT'))
        {
            switch (ENVIRONMENT)
            {
                case 'release':
                case 'testing':
                case 'production':
                // First, check if client ID is defined in a constant
                if ( defined( 'SLACK_CLIENT_ID' ) ) {
                    return SLACK_CLIENT_ID;
                }

                // If no constant found, look for environment variable
                if ( getenv( 'SLACK_CLIENT_ID' ) ) {
                    return getenv( 'SLACK_CLIENT_ID' );
                }
                    break;
                case 'development':
                    // First, check if client ID is defined in a constant
                    if ( defined( 'SLACK_CLIENT_ID_LOCAL' ) ) {
                        return SLACK_CLIENT_ID_LOCAL;
                    }

                    // If no constant found, look for environment variable
                    if ( getenv( 'SLACK_CLIENT_ID_LOCAL' ) ) {
                        return getenv( 'SLACK_CLIENT_ID_LOCAL' );
                    }
                    break;

                default:
                    exit('The application environment is not set correctly.');
            }
        }

		// Not configured, return empty string
		return '';
	}

	/**
	 * Returns the Slack client secret.
	 *
	 * @return string   The client secret or empty string if not configured
	 */
	private function get_client_secret() {
        if (defined('ENVIRONMENT'))
        {
            switch (ENVIRONMENT)
            {
                case 'release':
                case 'testing':
                case 'production':
                    // First, check if client secret is defined in a constant
                    if ( defined( 'SLACK_CLIENT_SECRET' ) ) {
                        return SLACK_CLIENT_SECRET;
                    }

                    // If no constant found, look for environment variable
                    if ( getenv( 'SLACK_CLIENT_SECRET' ) ) {
                        return getenv( 'SLACK_CLIENT_SECRET' );
                    }
                    break;
                case 'development':
                    // First, check if client secret is defined in a constant
                    if ( defined( 'SLACK_CLIENT_SECRET_LOCAL' ) ) {
                        return SLACK_CLIENT_SECRET_LOCAL;
                    }

                    // If no constant found, look for environment variable
                    if ( getenv( 'SLACK_CLIENT_SECRET_LOCAL' ) ) {
                        return getenv( 'SLACK_CLIENT_SECRET_LOCAL' );
                    }
                    break;

                default:
                    exit('The application environment is not set correctly.');
            }
        }

		// Not configured, return empty string
		return '';
	}

	/**
	 * Returns the command verification token.
	 *
	 * @return string   The command verification token or empty string if not configured
	 */
	private function get_command_token() {
        if (defined('ENVIRONMENT'))
        {
            switch (ENVIRONMENT)
            {
                case 'release':
                case 'testing':
                case 'production':
                    // First, check if command token is defined in a constant
                    if ( defined( 'SLACK_COMMAND_TOKEN' ) ) {
                        return SLACK_COMMAND_TOKEN;
                    }

                    // If no constant found, look for environment variable
                    if ( getenv( 'SLACK_COMMAND_TOKEN' ) ) {
                        return getenv( 'SLACK_COMMAND_TOKEN' );
                    }
                    break;
                case 'development':
                    // First, check if command token is defined in a constant
                    if ( defined( 'SLACK_COMMAND_TOKEN_LOCAL' ) ) {
                        return SLACK_COMMAND_TOKEN_LOCAL;
                    }

                    // If no constant found, look for environment variable
                    if ( getenv( 'SLACK_COMMAND_TOKEN_LOCAL' ) ) {
                        return getenv( 'SLACK_COMMAND_TOKEN_LOCAL' );
                    }
                    break;

                default:
                    exit('The application environment is not set correctly.');
            }
        }

		// Not configured, return empty string
		return '';
	}

    /**
     * create a Slack channel defined in the
     * authorization (Add to Slack) flow.
     *
     * @param string $name          The name of slack channel
     *
     * @return bool   the result
     *
     * @throws Slack_API_Exception
     */
    public function create_channel( $name ) {
        if ( ! $this->is_authenticated() ) {
            throw new Slack_API_Exception( 'Access token not specified' );
        }

        // Post to webhook stored in access object
        $headers = array( 'Content-Type' => 'application/json; charset=utf-8',
            'Authorization' => 'Bearer '.$this->access->get_access_token());

        $url = self::$api_root . 'channels.create';
        $data = json_encode( array( 'name' => $name ) );

        $response = Requests::post( $url, $headers, $data );
        $json = json_decode($response->body, true);
        if ( $json['ok'] != true && $json['error']!='name_taken' ) {
            throw new Slack_API_Exception( 'There was an error when create a channel in Slack' );
        }
        if( empty($json['channel']) ) $json['channel']['name'] = $name;
        return $json['channel'];
    }

    /**
     * Revoke the token for slack connection
     *
     * @return bool   the result
     *
     * @throws Slack_API_Exception
     */
    public function revoke_connect( ) {
        if ( ! $this->is_authenticated() ) {
            throw new Slack_API_Exception( 'Access token not specified' );
        }

        // Post to webhook stored in access object
        $headers = array( 'Accept' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer '.$this->access->get_access_token());

        $url = self::$api_root . 'auth.revoke';

        Requests::get( $url, $headers );
        return true;
    }

    /**
     * post a message to specific channel to Slack.
     *
     * @param string $channel    The name of slack channel
     * @param string $message    The message to be sent
     * @param bool $as_user    sent by user or bot
     *
     * @return bool   the result
     *
     * @throws Slack_API_Exception
     */
    public function post_message( $channel, $message, $as_user ) {
        if ( ! $this->is_authenticated() ) {
            throw new Slack_API_Exception( 'Access token not specified' );
        }
        $message = $this->convert_for_slack($message);

        // Post to webhook stored in access object
        $headers = array( 'Content-Type' => 'application/json; charset=utf-8',
            'Authorization' => 'Bearer '.$this->access->get_access_token());

        $url = self::$api_root . 'chat.postMessage';
        $data = json_encode(
            array(
                'channel' => $channel,
                'text' => $message,
                'as_user' => $as_user
            )
        );

        $response = Requests::post( $url, $headers, $data );
        $json = json_decode($response->body, true);
        if ( $json['ok'] != true ) {
            throw new Slack_API_Exception( 'There was an error when post a message to Slack channel' );
        }
        return true;
    }

    /**
     * convert string for Slack.
     *
     * @param string $str    The string
     *
     * @return string   the result
     *
     */
    private function convert_for_slack($str){
        $source = array("&", "<", ">");
        $target   = array("&amp;", "&lt;", "&gt;");
        $new = str_replace($source, $target, $str);
        return $new;
    }

}