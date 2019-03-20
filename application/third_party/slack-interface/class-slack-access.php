<?php
namespace Slack_Interface;

/**
 * A class for holding Slack authentication data.
 *
 * @author Jarkko Laine <jarkko@jarkkolaine.com>
 */
class Slack_Access {

	// Slack OAuth data

	private $access_token;
	private $scope;
	private $team_name;
	private $team_id;
    private $team_url;
    private $slack_user;
    private $slack_id;
	private $incoming_webhook;

	/**
	 * Sets up the Slack_Access object with authentication data.
	 *
	 * @param array $data   The Slack OAuth authentication data. If the user
	 *                      has not been authenticated, pass an empty array.
	 */
	public function __construct( $data ) {
		$this->access_token = isset( $data['access_token'] ) ? $data['access_token'] : '';
		$this->scope = isset( $data['scope'] ) ? $data['scope'] : array();
		$this->team_name = isset( $data['team_name'] ) ? $data['team_name'] : '';
		$this->team_id = isset( $data['team_id'] ) ? $data['team_id'] : '';
        $this->team_url = isset( $data['team_url'] ) ? $data['team_url'] : '';
        $this->slack_user = isset( $data['slack_user'] ) ? $data['slack_user'] : '';
        $this->slack_id = isset( $data['slack_id'] ) ? $data['slack_id'] : '';
		$this->incoming_webhook = isset( $data['incoming_webhook'] ) ? $data['incoming_webhook'] : array();
	}

	/**
	 * Checks if the object has been initialized with access data.
	 *
	 * @return bool True if authentication data has been stored in the object. Otherwise false.
	 */
	public function is_configured() {
		return $this->access_token != '';
	}

	/**
	 * Returns the authorization data as a JSON formatted string.
	 *
	 * @return string   The data in JSON format
	 */
	public function to_json() {
		$data = array(
			'access_token' => $this->access_token,
			'scope' => $this->scope,
			'team_name' => $this->team_name,
			'team_id' => $this->team_id,
            'team_url' => $this->team_url,
            'slack_user' => $this->slack_user,
            'slack_id' => $this->slack_id,
			'incoming_webhook' => $this->incoming_webhook
		);

		return json_encode( $data );
	}

    /**
     * Checks if the object has been initialized with access data.
     *
     * @return bool True if authentication data has been stored in the object. Otherwise false.
     */
    public function is_needed_update_info() {
        return !empty($this->access_token) && empty($this->slack_id);
    }

    /**
     * Checks if the object has been initialized with access data.
     *
     * @return bool True if authentication data has been stored in the object. Otherwise false.
     */
    public function update_info($url, $slack_user, $slack_id) {
        $this->team_url = $url;
        $this->slack_user = $slack_user;
        $this->slack_id = $slack_id;
    }

	/**
	 * Returns the webhook URL for posting notifications.
	 *
	 * @return string   The incoming webhook URL
	 */
	public function get_incoming_webhook() {
		if ( is_array( $this->incoming_webhook ) && isset( $this->incoming_webhook['url'] ) ) {
			return $this->incoming_webhook['url'];
		}

		return '';
	}

	/**
	 * Returns the channel to which the user has authorized the application
	 * to post notifications.
	 *
	 * @return string   The selected Slack channel's ID
	 */
	public function get_incoming_webhook_channel() {
		if ( is_array( $this->incoming_webhook ) && isset( $this->incoming_webhook['channel'] ) ) {
			return $this->incoming_webhook['channel'];
		}

		return '';
	}

    /**
     * Returns the webhook URL for posting notifications.
     *
     * @return string   The incoming webhook URL
     */
    public function get_access_token() {
        if ( isset( $this->access_token ) ) {
            return $this->access_token ;
        }

        return '';
    }

}