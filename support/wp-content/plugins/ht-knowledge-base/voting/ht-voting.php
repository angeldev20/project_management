<?php
/*
* Voting Module
*
*/

if( !class_exists('HT_Voting') ){

	if(!defined('HT_VOTING_KEY')){
		define('HT_VOTING_KEY', '_ht_voting');
	}

	class HT_Voting {		

		//constructor
		function __construct(){
			$this->add_script = false;

			//no longer needs text domain loading - uses ht-knowledge-base
			//load_plugin_textdomain('ht-voting', false, basename( dirname( __FILE__ ) ) . '/languages' );

			add_action( 'init', array( $this, 'register_ht_voting_shortcode_scripts_and_styles' ) );
			add_action( 'wp_footer', array( $this, 'print_ht_voting_shortcode_scripts_and_styles' ) );
			add_shortcode( 'ht_voting', array( $this , 'ht_voting_post_shortcode' ) );
			add_action( 'wp_head', array( $this, 'ht_voting_head' ) );

			//display voting
			add_action( 'ht_kb_end_article', array($this, 'ht_voting_display_voting' ) );

			//ajax filters
        	add_action( 'wp_ajax_ht_voting', array( $this, 'ht_ajax_voting_callback' ) );
        	add_action( 'wp_ajax_nopriv_ht_voting', array( $this, 'ht_ajax_voting_callback' ) );
        	add_action( 'wp_ajax_ht_voting_update_feedback', array( $this, 'ht_ajax_voting_update_feedback_callback' ) );
        	add_action( 'wp_ajax_nopriv_ht_voting_update_feedback', array( $this, 'ht_ajax_voting_update_feedback_callback' ) );
			include_once('php/ht-vote-class.php');
			//meta-boxes
			include_once('php/ht-voting-meta-boxes.php');
			//voting backend
			include_once('php/ht-voting-backend.php');

			//database controller
			include_once('php/ht-voting-database.php');
			$this->voting_database = new HT_Voting_Database();

			//dummy data creator, for testing
			//include_once('php/ht-voting-dummy-data-creator.php');

			//add activation action for table
            add_action( 'ht_kb_activate', array( $this, 'on_activate' ), 10, 1);

		}

		/**
		* Activation functions
		*/
		function on_activate( $network_wide = null ) {
            global $wpdb;
            //@todo - query multisite compatibility
            if ( is_multisite() && $network_wide ) {
                //store the current blog id
                $current_blog = $wpdb->blogid;
                //get all blogs in the network and activate plugin on each one
                $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
                foreach ( $blog_ids as $blog_id ) {
                    switch_to_blog( $blog_id );
                    $this->ht_kb_voting_activation_upgrade_actions();
                    restore_current_blog();
                }
            } else {
                $this->ht_kb_voting_activation_upgrade_actions();
            }
        }


		/**
		* Function to loop through all the existing ht_kb articles a perform any upgrade actions
		*/
		function ht_kb_voting_activation_upgrade_actions(){
			//upgrade - set initial meta if required

			//get all ht_kb articles
			$args = array(
					  'post_type' => 'ht_kb',
					  'posts_per_page' => -1,
					 );
			$ht_kb_posts = get_posts( $args );

			//loop and ugrade
			foreach ( $ht_kb_posts as $post ) {
				//upgrade if required
			   ht_kb_voting_upgrade_votes( $post->ID );
			   
			}
		}

		/**
		* Static function to perform upgrade actions on an individual article
		* @param (Int) $post_id ID of article to upgrade
		*/
		function ht_kb_voting_upgrade_votes($post_id){
			//get old votes
			$votes = get_post_meta($post_id, HT_VOTING_KEY);
			//delete old votes
			delete_post_meta($post_id, HT_VOTING_KEY);
			foreach ($votes as $key => $vote) {
				$key = md5( strval($vote->magnitude) . $vote->ip . $vote->time . $vote->user_id );
	            $vote->key = $key;
	            //initiate comments if not set
	            if(!property_exists($vote, 'comments')){
					$vote->comments = '';
	            }	            
	            //add vote
	            add_post_meta($post_id, HT_VOTING_KEY, $vote);
			}

		}

		/**
		* Voting post shortcode
		* @param (Array) $attrs The shortcode passed attribute
		* @param (Array) $content The shortcode passed content (this will always be ignored in this context)
		*/
		function ht_voting_post_shortcode($atts, $content = null){
			global $post;
			//shortcode used so scripts and styles required
			$this->add_script = true;
			
			//extract arttributes
			extract(shortcode_atts(array(  
	                'display' => 'standard',
	                'allow' => 'user',
	            ), $atts));

			ob_start();

			$this->ht_voting_post_display($post->ID, $allow, $display);
			//return whatever has been passed so far
			return ob_get_clean();
		}

		/**
		* Display a vote
		* @param (Int) $post_id
		* @param (String) $allow
		* @param (String) $display
		*/
		function ht_voting_post_display($post_id, $allow='user', $display='standard', $vote=null){
				//get votes so far
				$votes = $this->get_post_votes($post_id);
			?>
				<div class="ht-voting" id ="ht-voting-post-<?php echo $post_id ?>">
					<?php $this->ht_voting_post_render($post_id, $allow, $votes, $display, $vote); ?>
				</div>
			<?php
		}


		/**
		 * Get post votes
		 * @param (Int) $post_id The post id for the votes to fetch
		 * @return (Array) Vote objects array
		 */
		function get_post_votes($post_id){
			$votes = $this->voting_database->ht_voting_get_votes_as_objects($post_id);
			return $votes;
		}


		/**
		* Render the voting for a post
		* @param (Int) $post_id The post id
		* @param (String) $allow Whether to allow anonymous voting ('anon')
		* @param (Array) $votes An array of existing votes
		* @param (String) $display How the voting display should be rendered
		* @param (Object) $new_vote The vote that has just been made (or null if first render)
		*/
		function ht_voting_post_render($post_id, $allow, $votes, $display='standard', $new_vote=null){

			//enqueue script
			wp_enqueue_script( 'ht-voting-frontend-script'); 

			//add localization if required         

			$number_of_votes = is_array($votes) ? count($votes) : 0;
			$number_of_helpful = 0;
			foreach ((array)$votes as $vote) {
				if($vote->magnitude==10)
					$number_of_helpful++;
			}

			//get current user votes
			$user_vote = $this->get_users_post_vote( $post_id );

			$user_vote_direction = 'none';

			if( is_a( $user_vote, 'HT_Vote_Up' ) )
				$user_vote_direction = 'up';

			if( is_a( $user_vote, 'HT_Vote_Down' ) )
				$user_vote_direction = 'down';		


			$nonce = ( $allow!='anon' && !is_user_logged_in() ) ? '' : wp_create_nonce('ht-voting-post-ajax-nonce');
			$feedback_nonce = ( $allow!='anon' && !is_user_logged_in() ) ? '' : wp_create_nonce('ht-voting-feedback-ajax-nonce');
			$vote_enabled_class = ( $allow!='anon' && !is_user_logged_in() ) ? 'disabled' : 'enabled';

			?>
			<?php if($display=='lowprofile'): ?>
				<div class="ht-voting-links ht-voting-<?php echo $user_vote_direction; ?>">
					<a class="ht-voting-upvote <?php echo $vote_enabled_class; ?>" rel="nofollow" data-direction="up" data-type="post" data-nonce="<?php echo $nonce; ?>" data-id="<?php echo $post_id; ?>" data-allow="<?php echo $allow; ?>" data-display="<?php echo $display; ?>" href="<?php echo '#'; // $this->vote_post_link('up', $post_id, $allow); ?>"></a>
					<a class="ht-voting-downvote <?php echo $vote_enabled_class; ?>" rel="nofollow" data-direction="down" data-type="post" data-nonce="<?php echo $nonce; ?>" data-id="<?php echo $post_id; ?>" data-allow="<?php echo $allow; ?>" data-display="<?php echo $display; ?>" href="<?php echo '#'; // $this->vote_post_link('down', $post_id, $allow); ?>"></a>
				</div>
			<?php else: ?>
				<?php if($allow!='anon' && !is_user_logged_in()): ?>	
					<div class="voting-login-required">
					<?php _e('You must log in to vote', 'ht-knowledge-base'); ?>
					</div>
				<?php endif; ?>
				<div class="ht-voting-links ht-voting-<?php echo $user_vote_direction; ?>">
					<a class="ht-voting-upvote <?php echo $vote_enabled_class; ?>" rel="nofollow" data-direction="up" data-type="post" data-nonce="<?php echo $nonce; ?>" data-id="<?php echo $post_id; ?>" data-allow="<?php echo $allow; ?>" data-display="<?php echo $display; ?>" href="<?php echo '#'; // $this->vote_post_link('up', $post_id, $allow); ?>"><i class="hkb-upvote-icon"></i><span><?php _e('Yes', 'ht-knowledge-base'); ?></span></a>
					<a class="ht-voting-downvote <?php echo $vote_enabled_class; ?>" rel="nofollow" data-direction="down" data-type="post" data-nonce="<?php echo $nonce; ?>" data-id="<?php echo $post_id; ?>" data-allow="<?php echo $allow; ?>" data-display="<?php echo $display; ?>" href="<?php echo '#'; // $this->vote_post_link('down', $post_id, $allow); ?>"><i class="hkb-upvote-icon"></i><span><?php _e('No', 'ht-knowledge-base'); ?></span></a>
				</div>
				<?php if(empty($new_vote)): ?>
					<!-- no new vote -->
				<?php elseif( 	( is_a($new_vote, 'HT_Vote_Up') && ht_kb_voting_upvote_feedback() ) || 
                            	( is_a($new_vote, 'HT_Vote_Down') && ht_kb_voting_downvote_feedback() ) 
                            ): ?>
					<div class="ht-voting-comment <?php echo $vote_enabled_class; ?>" data-nonce="<?php echo $feedback_nonce; ?>"  data-vote-key="<?php echo $vote->key; ?>" data-id="<?php echo $post_id; ?>">
						<textarea class="ht-voting-comment__textarea" rows="4" cols="50" placeholder="<?php _e('Thanks for your feedback, add a comment here to help improve the article', 'ht-knowledge-base'); ?>"><?php if(isset($new_vote->comments)) $new_vote->comments; ?></textarea>
						<button class="ht-voting-comment__submit" type="button"><?php _e('Send Feedback', 'ht-knowledge-base'); ?></button>
					</div>
				<?php else: ?>
                    	<div class="ht-voting-thanks"><?php _e('Thanks for your feedback', 'ht-knowledge-base'); ?></div>
				<?php endif;//vote_key ?>	
			<?php endif; ?>

			<?php
		}


		/**
		* Get the voting link
		* @param (String) $direction The direction up/down
		* @param (Int) $post_id The id of the post for the voting link
		* @param (String) $allow Whether to allow anonymous voting ('anon')
		*/
		function vote_post_link($direction, $post_id, $allow='anon'){
			$bookmark = 'ht-voting-post-'.$post_id;
			if($allow!='anon' && !is_user_logged_in())
				return '?' . '#' . $bookmark ;
			$security = wp_create_nonce( 'ht-post-vote' );
			return '?' . 'vote=' . $direction . '&post=' . $post_id . '&_htvotenonce=' . $security . '#' . $bookmark ;
		}


		/**
		* Get a post vote for a user
		* @param (Int) $post_id The post_id to get the user vote for
		* @param (Array) $votes Existing vote array object to search for first (otherwise load post meta)
		* @return (Object) Vote object
		*/
		function get_users_post_vote($post_id, $votes=null){
			//create a dummy vote to compare
			if(class_exists('HT_Vote_Up')){
				$comp_vote = new HT_Vote_Up();
			} else {
				return;
			}
			//get all votes
			$votes = $this->voting_database->ht_voting_get_votes_as_objects($post_id);
			//loop through and compare users vote
			if($votes && !empty($votes)){
				foreach ($votes as $key => $vote) {
					//if user id is same (and not 0), return vote
					if( $vote->user_id > 0 && $vote->user_id == $comp_vote->user_id )
						return $vote;
					//if ip is same, return vote
					if( $vote->ip == $comp_vote->ip )
						return $vote;
					//else try next one
					continue;
				}
			} else {
				return;
			}
		}


		/**
		* Test whether the user has voted
		* @param (Int) $post_id The post_id to get the user vote for
		* @param (Array) $votes Existing vote array object to search for first (otherwise load post meta)
		* @return (Bool) True when user has already voted
		*/
		function has_user_voted($post_id, $votes=null){
			$user_vote = $this->get_users_post_vote( $post_id, $votes );
			$voted = (empty( $user_vote )) ? false : true;
			return $voted;
		}

		/**
	    * Register scripts and styles
	    */
	    public function register_ht_voting_shortcode_scripts_and_styles(){
	          
	           wp_register_script( 'ht-voting-frontend-script', plugins_url( 'js/ht-voting-frontend-script.js', __FILE__ ), array('jquery') , 1.0, true );	            
	           
	           wp_localize_script( 'ht-voting-frontend-script', 'voting', array( 
	            		'log_in_required' => __('You must be logged in to vote on this', 'ht-knowledge-base'), 
                		'ajaxurl' => admin_url( 'admin-ajax.php' ), 
                		'ajaxnonce' => wp_create_nonce('ht-voting-ajax-nonce') 
	                ));               
				
	    }

	    /**
	    * Print scripts and styles
	    */
	    public function print_ht_voting_shortcode_scripts_and_styles(){
	           if( $this->add_script ){
	                wp_print_styles( 'ht-voting-frontend-style' );
	           }           
	    }

	   /**
	    * HT Voting Head
	    */
	    public function ht_voting_head(){
	    	global $_GET;
	    	$direction = array_key_exists('vote', $_GET) ? $_GET['vote'] : '';
	    	$post_id = array_key_exists('post', $_GET) ? $_GET['post'] : '';
	    	$comment_id = array_key_exists('comment', $_GET) ? $_GET['comment'] : '';
	    	$nonce = array_key_exists('_htvotenonce', $_GET) ? $_GET['_htvotenonce'] : '';
	    	if(!empty($direction)){
	    		//verify security
	    		if ( ! wp_verify_nonce( $nonce, 'ht-post-vote' ) ) {
	    			die( 'Security check - head' ); 
	    		} else {
	    			if(!empty($post_id) ){
			    		//vote	post    		
			    		$this->vote_post($post_id, $direction);
			    	}
			    	if(!empty($comment_id) ){
			    		//vote	comment		
			    		$this->vote_comment($comment_id, $direction);
			    	}
	    		}   				
	    	}	    	
	    }

	   /**
	    * Ajax voting callback 
	    */
	    public function ht_ajax_voting_callback(){
	        global $_POST;
	    	$direction = array_key_exists('direction', $_POST) ? sanitize_text_field($_POST['direction']) : '';
	    	//type - either post or comment
	    	$type = array_key_exists('type', $_POST) ? sanitize_text_field($_POST['type']) : '';
	    	$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
	    	$id = array_key_exists('id', $_POST) ? sanitize_text_field($_POST['id']) : '';
	    	$allow = array_key_exists('allow', $_POST) ? sanitize_text_field($_POST['allow']) : '';
	    	$display = array_key_exists('display', $_POST) ? sanitize_text_field($_POST['display']) : '';

	        if(!empty($direction)){
	    			if( $type=='post' ){
	    				 if ( ! wp_verify_nonce( $nonce, 'ht-voting-post-ajax-nonce' ) ){
	    				 	die( 'Security check - voting callback' );
	    				 } else {
	    				 	//vote	post    		
			    			$vote = $this->vote_post($id, $direction);
							$this->ht_voting_post_display($id, $allow, $display, $vote);
	    				 }	
			    	}		
	    	}	  
	        die(); // this is required to return a proper result
	    }


	   /**
	    * Ajax add feedback callback
	    */
	    public function ht_ajax_voting_update_feedback_callback(){
	        global $_POST;
	    	$vote_key = array_key_exists('key', $_POST) ? sanitize_text_field($_POST['key']) : '';
	    	$post_id = array_key_exists('id', $_POST) ? sanitize_text_field($_POST['id']) : '';
	    	$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
	    	$comment = array_key_exists('comment', $_POST) ? sanitize_text_field($_POST['comment']) : '';
	        if(!empty($vote_key)){
				 if ( ! wp_verify_nonce( $nonce, 'ht-voting-feedback-ajax-nonce' ) ){
				 	die( 'Security check - update feedback callback' );
				 } else {
				 	//add feedback to vote
				 	$this->ht_voting_add_vote_comment($vote_key, $post_id, $comment);
				 	_e('Thanks for your feedback', 'ht-knowledge-base');
				 }				    	
	    	}	  
	        die(); // this is required to return a proper result
	    }

	    /**
		* Add vote comments/feedback
		* Filterable ($comment) - 'ht_voting_add_vote_comment_filter', $comment, $vote, $post_id
		* Action hook - ht_voting_add_vote_comment_action
		* @param (String) $vote_key The vote key
		* @param (Int) $post_id The post id
		* @param (String) $comment Comments/Feedback to add to vote
		*/
		function ht_voting_add_vote_comment($vote_key, $post_id, $comment=''){
			$vote = $this->get_users_post_vote_by_key($post_id, null, $vote_key);
			if(isset($vote)){
				$comment = apply_filters('ht_voting_add_vote_comment_filter', $comment, $vote, $post_id);
				$this->voting_database->update_comments_for_vote($post_id, $vote_key, $comment);
				do_action('ht_voting_add_vote_comment_action', $comment, $vote, $post_id);
			} else {
				_e('Cannot retrieve vote', 'ht-knowledge-base');
				echo $vote_key;
			}
		}

	   /**
	    * Perform the voting action for a post
	    * @param (Int) $post_id The post id to add a vote to
	    * @param (String) $direction Direction of vote up/down/neutral
	    */
	    public function vote_post($post_id, $direction){
	    	//get the users vote and delete it
	    	$user_previous_vote = $this->get_users_post_vote($post_id);

	    	switch($direction){
	    		case 'up':
	    			if(class_exists('HT_Vote_Up')){
	    				$new_vote = new HT_Vote_Up();
	    			}
	    			break;
	    		case 'down':
	    			if(class_exists('HT_Vote_Down')){
	    				$new_vote = new HT_Vote_Down();
	    			}
	    			break;
	    		case 'neutral':
	    			if(class_exists('HT_Vote_Neutral')){
	    				$new_vote = new HT_Vote_Neutral();
	    			}
	    			break;
	    		default:
	    			//numeric value
	    			if(is_numeric($direction)&&class_exists('HT_Vote_Value')){
						$new_vote_val = intval($direction);
						$new_vote = new HT_Vote_Value( $new_vote_val );
	    			}
	    			break;
	    	}

	    	//set the vote
	    	if($user_previous_vote){
	    		$users_vote = $user_previous_vote;
	    		$user_previous_vote->magnitude = $new_vote->magnitude;
	    	} else {
	    		$users_vote = $new_vote;
	    	}

	    	//call database save_vote_for_article
	    	$users_vote = $this->voting_database->save_vote_for_article($post_id, $users_vote);

	    	//return the vote just made
	    	if(is_a($users_vote, 'HT_Vote')){
	    		return $users_vote;
	    	}
	    }


	    /**
		 * Get the article usefulness
		 * @param (Int) $post_id The post id
		 * @return (Int) The usefulness rating (dynamic)
		 */
	    function get_article_usefulness($post_id){
	    	return $this->voting_database->get_article_usefulness($post_id);
	    }

	    /**
		 * Upgrade the meta key values
		 * @param (Int) $post_id The post id being upgraded
		 */
		public static function ht_voting_upgrade_post_meta_fields($postID){
			//keys to be upgraded
			HT_Voting::ht_voting_upgrade_voting_meta_fields($postID, 'voting_checkbox');
			HT_Voting::ht_voting_upgrade_voting_meta_fields($postID, 'voting_reset');
			HT_Voting::ht_voting_upgrade_voting_meta_fields($postID, 'voting_reset_confirm');
		}


	    /**
		 * Upgrade a post meta field
		 * @param (String) $name The name of the meta field to be upgraded
		 */
		static function ht_voting_upgrade_voting_meta_fields($postID, $name){
			$old_prefix = '_ht_knowledge_base_';
			$new_prefix = '_ht_voting_';

			//get the old value
			$old_value = get_post_meta($postID, $old_prefix . $name, true);
			if(!empty($old_value)){
				//get the new value
				$new_value = get_post_meta($postID, $new_prefix . $name, true);
				if(empty($new_value)){
					//sync the new value to the old value
					update_post_meta($postID, $new_prefix . $name, $old_value);
				}
				
			}
			//delete old meta key
			delete_post_meta($postID, $old_prefix . $name);
		}

		/**
		* Display voting - use shortcode
		*/
		function ht_voting_display_voting(){
			$voting_disabled =  get_post_meta( get_the_ID(), '_ht_voting_voting_disabled', true );
			$allow_voting_on_this_article = $voting_disabled ? false : true;


		
			// voting
			if( ht_kb_voting_enable_feedback() && $allow_voting_on_this_article ){ ?>
				<div class="hkb-feedback">
					<h3 class="hkb-feedback__title"><?php _e('Was this article helpful?', 'ht-knowledge-base'); ?></h3>
					<?php if( ht_kb_voting_enable_anonymous() )
						echo do_shortcode('[ht_voting allow="anon"]');
					else
						echo do_shortcode('[ht_voting allow="user"]');
					?>
				</div>
				<?php
			}
		}

		/**
		* Get a vote by key
		* @param (Int) $post_id The post_id to get the user vote for
		* @param (Array) $votes Existing vote array object to search for first (otherwise load post meta)
		* @param (Int) $vote_key The key of the vote to fetch
		* @return (Object) Vote object
		*/
		function get_users_post_vote_by_key($post_id, $votes=null, $vote_key=-1){
			return $this->voting_database->ht_voting_get_vote_by_key($post_id, $vote_key);
		}


		/**
		* Delete vote by vote_id
		* @param (String) $vote_id The vote key (changed to vote id in 2.2.1+)
		* @param (Int) $post_id The post id 
		*/
		function ht_voting_delete_vote($vote_id, $post_id){
			$this->voting_database->delete_vote_from_database($post_id, $vote_id);
		}    

		/**
		* Deletes all votes for a post
		* @param (Int) $post_id The post id
		*/
		function ht_voting_delete_all_post_votes($post_id){
			$this->voting_database->delete_all_article_votes_from_database($post_id);
		}

		/**
		* Update article usefulness
		* @param (Int) $post_id The post id
		*/
		function ht_voting_update_article_usefulness($post_id){
			$this->voting_database->update_article_usefulness($post_id);
		}

		/**
		* Has votes
		* @param (Int) $post_id The post id
		*/
		function ht_voting_has_votes($post_id){
			$this->voting_database->has_votes($post_id);
		}

	} //end class
} //end class exists

if(class_exists('HT_Voting')){
	global $ht_voting_init;

	$ht_voting_init = new HT_Voting();

	if(!function_exists('ht_voting_post')){
		function ht_voting_post( $post_id=null, $allow='user', $display='standard', $vote=null ){
			global $post, $ht_voting_init;
			$post_id = ( empty( $post_id ) ) ? $post->ID : $post_id;
			$ht_voting_init->ht_voting_post_display( $post_id, $allow, $display, $vote );
		}
	} //end if ht_voting_post


	if(!function_exists('ht_usefulness')){
		function ht_usefulness( $post_id=null ){
			global $post, $ht_voting_init;
			//set the post id
			$post_id = ( empty( $post_id ) ) ? $post->ID : $post_id;
			//get the post usefulness
			$post_usefulness_int = $ht_voting_init->get_article_usefulness($post_id);
			//return as integer
			return $post_usefulness_int;
		}
	} //end if ht_usefulness

	if(!function_exists('ht_voting_get_post_votes')){
		function ht_voting_get_post_votes( $post_id=null ){
			global $ht_voting_init;
			
			return $ht_voting_init->get_post_votes($post_id);
		}
	} //end if ht_voting_get_post_votes


	if(!function_exists('ht_voting_delete_vote')){
		function ht_voting_delete_vote( $vote_id, $post_id ){
			global $ht_voting_init;
			
			return $ht_voting_init->ht_voting_delete_vote($vote_id, $post_id);
		}
	} //end if ht_voting_delete_vote

	if(!function_exists('ht_voting_delete_all_post_votes')){
		function ht_voting_delete_all_post_votes( $post_id ){
			global $ht_voting_init;
			
			return $ht_voting_init->ht_voting_delete_all_post_votes( $post_id );
		}
	} //end if ht_voting_delete_all_post_votes

	if(!function_exists('ht_voting_update_article_usefulness')){
		function ht_voting_update_article_usefulness( $post_id ){
			global $ht_voting_init;
			
			return $ht_voting_init->ht_voting_update_article_usefulness( $post_id );
		}
	} //end if ht_voting_update_article_usefulness

	if(!function_exists('ht_voting_has_votes')){
		function ht_voting_has_votes( $post_id ){
			global $ht_voting_init;
			
			return $ht_voting_init->ht_voting_has_votes( $post_id );
		}
	} //end if ht_voting_has_votes

	if(!function_exists('ht_kb_voting_upgrade_votes')){
		function ht_kb_voting_upgrade_votes( $post_id ){
			global $ht_voting_init;
			
			return $ht_voting_init->ht_kb_voting_upgrade_votes( $post_id );
		}
	} //end if ht_voting_has_votes

}
