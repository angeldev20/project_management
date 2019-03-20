<?php

class mailchimp {

	/** @var string */
	private $_user_key;

	/** @var mixed  */
	private $_list_id;

	/** @var string */
	private $_LNAME;

	/** @var string */
	private $_FNAME;

	/** @var string */
	private $_email;

	/** @var string */
	private $_subscribe = 'Subscribe';




	public function __construct($init_array = NULL)
	{

		if ($init_array == null) {
			$init_array = [
				'user_key' =>  '77f3a241415fd5e544c5b4e73',
				'list_id' => 'dcd868a09b'
			];
		}

		if (isset($init_array['user_key'])) $this->_user_key = $init_array['user_key'];
		if (isset($init_array['list_id'])) $this->_list_id = $init_array['list_id'];
	}

	/**
	 * @param string $firstName
	 *
	 * @return $this
	 */
	public function setFirstName($firstName) {
		$this->_FNAME = $firstName;
		return $this;
	}

	/**
	 * @param string $lastName
	 *
	 * @return $this
	 */
	public function setLastName($lastName) {
		$this->_LNAME = $lastName;
		return $this;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email) {
        $this->_email = $email;
        return $this;
	}

	public function addEmailToSubscriberList() {
		$url = 'https://spera.us11.list-manage.com/subscribe/post?u=' . $this->_user_key . '&id=' . 'dcd868a09b';

		$post = [
			'FNAME' => $this->_FNAME,
			'LNAME' => $this->_LNAME,
			'EMAIL' => $this->_email,
			'subscribe' => $this->_subscribe
		];
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );

		$response = curl_exec( $ch );

		curl_close( $ch );

		return $response;

	}
}