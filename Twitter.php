<?php
/**
 * @desc CodeIgniter Twitter Class
 * This class enables you to use Twitter API functions.
 *
 * @version		0.1
 * @author		Arthur Grishin <arthur.grishin@me.com>
 * @link		http://www.arthurgrishin.com/
 * @copyright	© 2010 Arthur Grishin
 */
 
 class Twitter {
 
 	private $_username 		= '';		
 	private $_password 		= '';		
 	private $_authed		= false;	
 	private $_type 			= 'json'; 	
 	private $_log 			= true;		
 	private $_user 			= false;	
 	private $_api_hosts		= array(
								'main' 		=> 'http://twitter.com/',
								'search'	=> 'http://search.twitter.com/',
								'v1' 		=> 'http://api.twitter.com/1/',
							); 
 	private	$_return_as_is	= false;
 	private $_curl 			= false;
 	private $_user_agent	= 'CI-Twitter-Library/0.9 (PHP)';
 	
 	/**
 	 * @desc Returns tweets that match a specified query.
 	 *
 	 * @param string $query
 	 * @param string $callback
 	 * @param string $lang
 	 * @param string $locale
 	 * @param int $rpp
 	 * @param int $page
 	 * @param int $since_id
 	 * @param string $geocode
 	 * @param bool $show_user
 	 * @return array
 	 */
 	public function search($query = FALSE, $callback = FALSE, $lang = FALSE, $locale = FALSE, $rpp = FALSE, $page = FALSE, $since_id = FALSE, $geocode = FALSE, $show_user = FALSE) { 
		if(in_array(strtolower($this->_type), array('json','atom'))) {
			$returned = $this->_get($this->_api_hosts['search'].'search.'.$this->_type,array(
				'q'			=> $query,
				'callback'	=> (strtolower($this->_type) == 'json' ? $callback : ''),
          		'lang'		=> $lang,
          		'locale'	=> $locale,
          		'rpp'		=> $rpp,
          		'page'		=> $page,
          		'since_id'	=> $since_id,
          		'geocode'	=> $geocode,
          		'show_user'	=> $show_user,
			), FALSE);
			return $this->_parse_returned_value($returned);
		}
 	}
 	
 	/**
 	 * @desc Returns the top ten topics that are currently trending on Twitter.
 	 *
 	 * @return array
 	 */
 	public function trends() {
 		if(in_array(strtolower($this->_type), array('json'))) {
 			$returned = $this->_get($this->_api_hosts['search'].'trends.'.$this->_type, FALSE, FALSE);
			return $this->_parse_returned_value($returned);
 		}	
 		return FALSE;
 	}
 	
 	/**
 	 * @desc Returns the current top 10 trending topics on Twitter.
 	 *
 	 * @param string $exclude
 	 * @return array
 	 */
 	public function trends_current($exclude = FALSE) { 
 		if(in_array(strtolower($this->_type), array('json'))) {
 			$returned = $this->_get($this->_api_hosts['search'].'trends/current.'.$this->_type, array(
 				'exclude' => $exclude,
 			), FALSE);
			return $this->_parse_returned_value($returned);
 		}	
 		return FALSE;
 	}
 	
 	/**
 	 * @desc Returns the top 20 trending topics for each hour in a given day.
 	 *
 	 * @param date(YYYY-MM-DD) $date
 	 * @param string $exclude
 	 * @return array
 	 */
 	public function trends_daily($date = FALSE, $exclude = FALSE)	{ 
 		if(in_array(strtolower($this->_type), array('json'))) {
 			$returned = $this->_get($this->_api_hosts['search'].'trends/daily.'.$this->_type, array(
 				'date' 		=> $date,
 				'exclude' 	=> $exclude,
 			), FALSE);
			return $this->_parse_returned_value($returned);
 		}	
 		return FALSE;	
 	}
 	
 	/**
 	 * @desc Returns the top 30 trending topics for each day in a given week.
 	 *
 	 * @param date(YYYY-MM-DD) $date
 	 * @param string $exclude
 	 * @return array
 	 */
 	public function trends_weekly($date = FALSE, $exclude = FALSE) { 
 		if(in_array(strtolower($this->_type), array('json'))) {
 			$returned = $this->_get($this->_api_hosts['search'].'trends/weekly.'.$this->_type, array(
 				'date' 		=> $date,
 				'exclude' 	=> $exclude,
 			), FALSE);
			return $this->_parse_returned_value($returned);
 		}	
 		return FALSE;	
 	}
	
	/**
	 * @desc Returns the 20 most recent statuses from non-protected users who have set a custom user icon. 
	 * The public timeline is cached for 60 seconds so requesting it more often than that is a waste of 
	 * resources.
	 *
	 * @return array
	 */
	public function statuses_public_timeline() { 
		if(in_array(strtolower($this->_type), array('xml','json', 'atom', 'rss'))) {
			$returned = $this->_get($this->_api_hosts['main'].'statuses/public_timeline.'.$this->_type, FALSE, FALSE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns the 20 most recent statuses, including retweets, posted by the authenticating user and 
	 * that user's friends.
	 *
	 * @param int $since_id
	 * @param int $max_id
	 * @param int $count
	 * @param int $page
	 * @return array
	 */
	public function statuses_home_timeline($since_id = FALSE, $max_id = FALSE, $count = FALSE, $page = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json', 'atom'))) {
			$returned = $this->_get($this->_api_hosts['v1'].'statuses/home_timeline.'.$this->_type, array(
				'since_id' 	=> $since_id,
				'max_id' 	=> $max_id,
				'count' 	=> $count,
				'page' 		=> $page,
			), TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns the 20 most recent statuses posted by the authenticating user and that user's friends.
	 *
	 * @param int $since_id
	 * @param int $max_id
	 * @param int $count
	 * @param int $page
	 * @return array
	 */
	public function statuses_friends_timeline($since_id = FALSE, $max_id = FALSE, $count = FALSE, $page = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json', 'atom', 'rss'))) {
			$returned = $this->_get($this->_api_hosts['main'].'statuses/friends_timeline.'.$this->_type, array(
				'since_id' 	=> $since_id,
				'max_id' 	=> $max_id,
				'count' 	=> $count,
				'page' 		=> $page,
			), TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}	
	
	/**
	 * @desc Returns the 20 most recent statuses posted from the authenticating user. It's also possible to 
	 * request another user's timeline via the id parameter.
	 *
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @param int $since_id
	 * @param int $max_id
	 * @param int $count
	 * @param int $page
	 * @param bool $auth
	 * @return array
	 */
	public function statuses_user_timeline($id = FALSE, $user_id = FALSE, $screen_name = FALSE, $since_id = FALSE, $max_id = FALSE, $count = FALSE, $page = FALSE, $auth = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json', 'atom', 'rss'))) {
			$returned = $this->_get($this->_api_hosts['main'].'statuses/user_timeline'.(!empty($id) ? '/'.$id.'.' : '.').$this->_type, array(
				'user_id' 		=> $user_id,
				'screen_name'	=> $screen_name,
				'since_id' 		=> $since_id,
				'max_id' 		=> $max_id,
				'count' 		=> $count,
				'page' 			=> $page,
			), $auth);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns the 20 most recent mentions (status containing @username) for the authenticating user.
	 *
	 * @param int $since_id
	 * @param int $max_id
	 * @param int $count
	 * @param int $page
	 * @return array
	 */
	public function statuses_mentions($since_id = FALSE, $max_id = FALSE, $count = FALSE, $page = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json', 'atom', 'rss'))) {
			$returned = $this->_get($this->_api_hosts['main'].'statuses/mentions.'.$this->_type, array(
				'since_id' 	=> $since_id,
				'max_id' 	=> $max_id,
				'count' 	=> $count,
				'page' 		=> $page,
			));
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns the 20 most recent retweets posted by the authenticating user.
	 *
	 * @param int $since_id
	 * @param int $max_id
	 * @param int $count
	 * @param int $page
	 * @return array
	 */
	public function statuses_retweeted_by_me($since_id = FALSE, $max_id = FALSE, $count = FALSE, $page = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json', 'atom'))) {
			$returned = $this->_get($this->_api_hosts['v1'].'statuses/retweeted_by_me.'.$this->_type, array(
				'since_id' 	=> $since_id,
				'max_id' 	=> $max_id,
				'count' 	=> $count,
				'page' 		=> $page,
			), TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns the 20 most recent retweets posted by the authenticating user's friends.
	 *
	 * @param int $since_id
	 * @param int $max_id
	 * @param int $count
	 * @param int $page
	 * @return array
	 */
	public function statuses_retweeted_to_me($since_id = FALSE, $max_id = FALSE, $count = FALSE, $page = FALSE)	{ 
		if(in_array(strtolower($this->_type), array('xml','json', 'atom'))) {
			$returned = $this->_get($this->_api_hosts['v1'].'statuses/retweeted_to_me.'.$this->_type, array(
				'since_id' 	=> $since_id,
				'max_id' 	=> $max_id,
				'count' 	=> $count,
				'page' 		=> $page,
			), TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns the 20 most recent tweets of the authenticated user that have been retweeted by others.
	 *
	 * @param int $since_id
	 * @param int $max_id
	 * @param int $count
	 * @param int $page
	 * @return array
	 */
	public function statuses_retweets_of_me($since_id = FALSE, $max_id = FALSE, $count = FALSE, $page = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json', 'atom'))) {
			$returned = $this->_get($this->_api_hosts['v1'].'statuses/retweets_of_me.'.$this->_type, array(
				'since_id' 	=> $since_id,
				'max_id' 	=> $max_id,
				'count' 	=> $count,
				'page' 		=> $page,
			), TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns a single status, specified by the id parameter below. The status's author will be 
	 * returned inline.
	 *
	 * @param int $id
	 * @param bool $auth
	 * @return array
	 */
	public function statuses_show($id = FALSE, $auth = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id)) {
				$returned = $this->_get($this->_api_hosts['main'].'statuses/show/'.$id.'.'.$this->_type, FALSE, $auth);
				return $this->_parse_returned_value($returned);
			}
		}
		return false;
	}
	
	/**
	 * @desc Updates the authenticating user's status. Requires the status parameter specified below. 
	 * A status update with text identical to the authenticating user's current status will be ignored 
	 * to prevent duplicates.
	 *
	 * @param string $status
	 * @param int $in_reply_to_status_id
	 * @param string $lat
	 * @param string $long
	 * @return array
	 */
	public function statuses_update($status = FALSE, $in_reply_to_status_id = FALSE, $lat = FALSE, $long = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			$returned = $this->_post($this->_api_hosts['main'].'statuses/update.'.$this->_type, array(
				'status' 					=> $status,
				'in_reply_to_status_id' 	=> $in_reply_to_status_id,
				'lat' 						=> $lat,
				'long' 						=> $long,
			), TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;	
	}
	
	/**
	 * @desc Destroys the status specified by the required ID parameter.  The authenticating user must be 
	 * the author of the specified status.
	 *
	 * @param int $id
	 * @return array
	 */
	public function statuses_destroy($id = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id)) {
				$returned = $this->_post($this->_api_hosts['main'].'statuses/destroy/'.$id.'.'.$this->_type, FALSE, TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Retweets a tweet. Requires the id parameter of the tweet you are retweeting. Returns the original 
	 * tweet with retweet details embedded.
	 *
	 * @param int $id
	 * @return array
	 */
	public function statuses_retweet($id = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id)) {
				$returned = $this->_post($this->_api_hosts['v1'].'statuses/retweet/'.$id.'.'.$this->_type, FALSE, TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns up to 100 of the first retweets of a given tweet.
	 *
	 * @param int $id
	 * @param int $count
	 * @return array
	 */
	public function statuses_retweets($id = FALSE, $count = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id)) {
				$returned = $this->_get($this->_api_hosts['v1'].'statuses/retweets/'.$id.'.'.$this->_type, array(
					'count' => $count,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return false;
	}
	
	/**
	 * @desc Creates a new list for the authenticated user.
	 *
	 * @param string $name
	 * @param string $mode
	 * @param string $description
	 * @return array
	 */
	public function list_create($name = FALSE, $mode = FALSE, $description = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {	
			if(!empty($name)) {									 
				$returned = $this->_post($this->_api_hosts['v1'].$this->_username.'/lists.'.$this->_type, array(
					'name' 			=> $name,
					'mode'			=> $mode,
					'description' 	=> $description,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Updates the specified list. 
	 *
	 * @param int $id
	 * @param string $name
	 * @param string $mode
	 * @param string $description
	 * @return array
	 */
	public function list_update($id = FALSE, $name = FALSE, $mode = FALSE, $description = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {	
			if(!empty($id)) {									 
				$returned = $this->_post($this->_api_hosts['v1'].$this->_username.'/lists/'.$id.'.'.$this->_type, array(
					'name' 			=> $name,
					'mode'			=> $mode,
					'description' 	=> $description,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc List the lists of the specified user.
	 *
	 * @param string $user
	 * @param int $cursor
	 * @return array
	 */
	public function lists_index($user = FALSE, $cursor = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {	
			$returned = $this->_get($this->_api_hosts['v1'].(!empty($user) ? $user : $this->_username).'/lists.'.$this->_type, array(
				'cursor' => $cursor,
			), TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Show the specified list.
	 *
	 * @param string $user
	 * @param int $id
	 * @return array
	 */
	public function list_show($user = FALSE, $id = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {	
			if(!empty($id)) {									 
				$returned = $this->_get($this->_api_hosts['v1'].(!empty($user) ? $user : $this->_username).'/lists/'.$id.'.'.$this->_type, FALSE, TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Deletes the specified list.
	 *
	 * @param int $id
	 * @return array
	 */
	public function list_destroy($id = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {	
			if(!empty($id)) {									 
				$returned = $this->_delete($this->_api_hosts['v1'].$this->_username.'/lists/'.$id.'.'.$this->_type, FALSE, TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Show tweet timeline for members of the specified list.
	 *
	 * @param string $user
	 * @param int $id
	 * @param int $since_id
	 * @param int $max_id
	 * @param int $per_page
	 * @param int $page
	 * @return array
	 */
	public function list_statuses($user = FALSE, $id = FALSE, $since_id = FALSE, $max_id = FALSE, $per_page = FALSE, $page = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json', 'atom'))) {	
			if(!empty($id)) {									 
				$returned = $this->_get($this->_api_hosts['v1'].(!empty($user) ? $user : $this->_username).'/lists/'.$id.'/statuses.'.$this->_type, array(
					'since_id' 	=> $since_id,
					'max_id' 	=> $max_id,
					'per_page' 	=> $per_page,
					'page' 		=> $page,
				), FALSE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;	
 	}
 	
 	/**
 	 * @desc List the lists the specified user has been added to.
 	 *
 	 * @param string $user
 	 * @param int $cursor
 	 * @return array
 	 */
 	public function list_memberships($user = FALSE, $cursor = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {							 
			$returned = $this->_get($this->_api_hosts['v1'].(!empty($user) ? $user : $this->_username).'/lists/memberships.'.$this->_type, array(
				'cursor' 	=> $cursor,
			), TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;	
 	}
 	
 	/**
 	 * @desc List the lists the specified user follows.
 	 *
 	 * @param string $user
 	 * @param int $cursor
 	 * @return array
 	 */
 	public function list_subscriptions($user = FALSE, $cursor = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {							 
			$returned = $this->_get($this->_api_hosts['v1'].(!empty($user) ? $user : $this->_username).'/lists/subscriptions.'.$this->_type, array(
				'cursor' 	=> $cursor,
			), TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;	
 	}
	
 	/**
 	 * @desc Returns the members of the specified list.
 	 *
 	 * @param int $list_id
 	 * @param int $cursor
 	 * @return array
 	 */
 	public function list_members_show($list_id = FALSE, $cursor = false) {
 		if(in_array(strtolower($this->_type), array('xml','json'))) {	
 			if(!empty($list_id)) {						 
				$returned = $this->_get($this->_api_hosts['v1'].$this->_username.'/'.$list_id.'/members.'.$this->_type, array(
					'cursor' 	=> $cursor,
				), TRUE);
				return $this->_parse_returned_value($returned);
 			}
		}
		return FALSE;	
 	}
 	
 	/**
 	 * @desc Add a member to a list.
 	 *
 	 * @param int $list_id
 	 * @param int $id
 	 * @return array
 	 */
 	public function list_members_add($list_id = FALSE, $id = FALSE) {
 		if(in_array(strtolower($this->_type), array('xml','json'))) {	
 			if(!empty($list_id) && !empty($id)) {						 
				$returned = $this->_post($this->_api_hosts['v1'].$this->_username.'/'.$list_id.'/members.'.$this->_type, array(
					'id' 	=> $id,
				), TRUE);
				return $this->_parse_returned_value($returned);
 			}
		}
		return FALSE;		
 	}
 	
 	/**
 	 * @desc Removes the specified member from the list.
 	 *
 	 * @param int $list_id
 	 * @param int $id
 	 * @return array
 	 */
 	public function list_members_destroy($list_id = FALSE, $id = FALSE) {
 		if(in_array(strtolower($this->_type), array('xml','json'))) {	
 			if(!empty($list_id) && !empty($id)) {						 
				$returned = $this->_delete($this->_api_hosts['v1'].$this->_username.'/'.$list_id.'/members.'.$this->_type, array(
					'id' 	=> $id,
				), TRUE);
				return $this->_parse_returned_value($returned);
 			}
		}
		return FALSE;		
 	}
 	
 	/**
 	 * @desc Check if a user is a member of the specified list.
 	 *
 	 * @param int $list_id
 	 * @param int $id
 	 * @return array
 	 */
 	public function list_is_member_of($list_id = FALSE, $id = FALSE) {
 		if(in_array(strtolower($this->_type), array('xml','json'))) {	
 			if(!empty($list_id) && !empty($id)) {						 
				$returned = $this->_get($this->_api_hosts['v1'].$this->_username.'/'.$list_id.'/members/'.$id.'.'.$this->_type, FALSE, TRUE);
				return $this->_parse_returned_value($returned);
 			}
		}
		return FALSE;		
 	}
 	
 	/**
 	 * @desc Returns the subscribers of the specified list.
 	 *
 	 * @param int $list_id
 	 * @param int $cursor
 	 * @return array
 	 */
 	public function list_subscribers_show($list_id = FALSE, $cursor = false) {
 		if(in_array(strtolower($this->_type), array('xml','json'))) {	
 			if(!empty($list_id)) {						 
				$returned = $this->_get($this->_api_hosts['v1'].$this->_username.'/'.$list_id.'/subscribers.'.$this->_type, array(
					'cursor' 	=> $cursor,
				), TRUE);
				return $this->_parse_returned_value($returned);
 			}
		}
		return FALSE;	
 	}
 	
 	/**
 	 * @desc Add a subscriber to a list.
 	 *
 	 * @param int $list_id
 	 * @param int $id
 	 * @return array
 	 */
 	public function list_subscribers_add($list_id = FALSE, $id = FALSE) {
 		if(in_array(strtolower($this->_type), array('xml','json'))) {	
 			if(!empty($list_id) && !empty($id)) {						 
				$returned = $this->_post($this->_api_hosts['v1'].$this->_username.'/'.$list_id.'/subscribers.'.$this->_type, array(
					'id' 	=> $id,
				), TRUE);
				return $this->_parse_returned_value($returned);
 			}
		}
		return FALSE;		
 	}
 	
 	/**
 	 * @desc Removes the specified subscriber from the list.
 	 *
 	 * @param int $list_id
 	 * @param int $id
 	 * @return array
 	 */
 	public function list_subscribers_destroy($list_id = FALSE, $id = FALSE) {
 		if(in_array(strtolower($this->_type), array('xml','json'))) {	
 			if(!empty($list_id) && !empty($id)) {						 
				$returned = $this->_delete($this->_api_hosts['v1'].$this->_username.'/'.$list_id.'/subscribers.'.$this->_type, array(
					'id' 	=> $id,
				), TRUE);
				return $this->_parse_returned_value($returned);
 			}
		}
		return FALSE;		
 	}
 	
 	/**
 	 * @desc Check if a user is a subscriber of the specified list.
 	 *
 	 * @param int $list_id
 	 * @param int $id
 	 * @return array
 	 */
 	public function list_is_subscriber_of($list_id = FALSE, $id = FALSE) {
 		if(in_array(strtolower($this->_type), array('xml','json'))) {	
 			if(!empty($list_id) && !empty($id)) {						 
				$returned = $this->_get($this->_api_hosts['v1'].$this->_username.'/'.$list_id.'/subscribers/'.$id.'.'.$this->_type, FALSE, TRUE);
				return $this->_parse_returned_value($returned);
 			}
		}
		return FALSE;		
 	}
	
	/**
	 * @desc Returns extended information of a given user, specified by ID or screen name as per the required 
	 * id parameter. The author's most recent status will be returned inline.
	 *
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @return array
	 */
	public function users_show($id = FALSE, $user_id = FALSE, $screen_name = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id) || (!empty($user_id) && is_numeric($user_id)) || !empty($screen_name)) {
				$returned = $this->_get($this->_api_hosts['main'].'users/show'.(!empty($id) ? '/'.$id.'.' : '.').$this->_type, array(
					'user_id' 		=> $user_id,
					'screen_name' 	=> $screen_name,
				), FALSE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Run a search for users similar to "Find People" button on Twitter.com.
	 *
	 * @param string $q
	 * @param int $per_page
	 * @param int $page
	 * @return unknown
	 */
	public function users_search($q = FALSE, $per_page = false, $page = false) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($q)) {
				$returned = $this->_get($this->_api_hosts['v1'].'users/search.'.$this->_type, array(
					'q' 		=> $q,
					'per_page' 	=> $per_page,
					'page' 		=> $page,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns a user's friends, each with current status inline. They are ordered by the order in which the 
	 * user followed them, most recently followed first, 100 at a time.
	 *
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @param int $cursor
	 * @return array
	 */
	public function statuses_friends($id = FALSE,$user_id = FALSE, $screen_name = FALSE, $cursor = FALSE, $auth = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			$returned = $this->_get($this->_api_hosts['main'].'statuses/friends.'.$this->_type, array(
				'id' 			=> $id,
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
				'cursor' 		=> $cursor,
			), $auth);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns the authenticating user's followers, each with current status inline. They are ordered by the 
	 * order in which they followed the user, 100 at a time.
	 *
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @param int $cursor
	 * @return array
	 */
	public function statuses_followers($id = FALSE,$user_id = FALSE, $screen_name = FALSE, $cursor = FALSE, $auth = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			$returned = $this->_get($this->_api_hosts['main'].'statuses/followers.'.$this->_type, array(
				'id' 			=> $id,
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
				'cursor' 		=> $cursor,
			), $auth);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns a list of the 20 most recent direct messages sent to the authenticating user. The XML and 
	 * JSON versions include detailed information about the sending and recipient users.
	 *
	 * @param int $since_id
	 * @param int $max_id
	 * @param int $count
	 * @param int $page
	 * @return array
	 */
	public function direct_messages($since_id = FALSE, $max_id = FALSE, $count = FALSE, $page = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json', 'rss', 'atom'))) {
			$returned = $this->_get($this->_api_hosts['main'].'direct_messages.'.$this->_type, array(
				'since_id' 	=> $since_id,
				'max_id' 	=> $max_id,
				'count' 	=> $count,
				'page' 		=> $page,
			), TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns a list of the 20 most recent direct messages sent by the authenticating user. The XML and 
	 * JSON versions include detailed information about the sending and recipient users.
	 *
	 * @param int $since_id
	 * @param int $max_id
	 * @param int $count
	 * @param int $page
	 * @return array
	 */
	public function direct_messages_sent($since_id = FALSE, $max_id = FALSE, $count = FALSE, $page = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json', 'rss', 'atom'))) {
			$returned = $this->_get($this->_api_hosts['main'].'direct_messages/sent.'.$this->_type, array(
				'since_id' 	=> $since_id,
				'max_id' 	=> $max_id,
				'count' 	=> $count,
				'page' 		=> $page,
			), TRUE);
			return $this->_parse_returned_value($returned);	
		}
		return FALSE;
	}
	
	/**
	 * @desc Sends a new direct message to the specified user from the authenticating user. Returns the sent 
	 * message in the requested format when successful.
	 *
	 * @param mixed $user
	 * @param int $user_id
	 * @param string $screen_name
	 * @param string $text
	 * @return array
	 */
	public function direct_messages_new($user = FALSE, $user_id = FALSE, $screen_name = FALSE, $text = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if((!empty($id) || (!empty($user_id) && is_numeric($user_id)) || !empty($screen_name)) && !empty($text)) {
				$returned = $this->_post($this->_api_hosts['main'].'direct_messages/new.'.$this->_type, array(
					'user' 			=> $user,
					'user_id' 		=> $user_id,
					'screen_name' 	=> $screen_name,
					'text' 			=> $text,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Destroys the direct message specified in the required ID parameter.  The authenticating user 
	 * must be the recipient of the specified direct message.
	 *
	 * @param int $id
	 * @return array
	 */
	public function direct_messages_destroy($id = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id)) {
				$returned = $this->_post($this->_api_hosts['main'].'direct_messages/destroy/'.$id.'.'.$this->_type, FALSE, TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/* Friendships methods */
	/**
	 * @desc Allows the authenticating users to follow the user specified in the ID parameter. Returns 
	 * the befriended user in the requested format when successful.
	 *
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @param bool $follow
	 * @return array
	 */
	public function friendships_create($id = FALSE, $user_id = FALSE, $screen_name = FALSE, $follow = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id) || (!empty($user_id) && is_numeric($user_id)) || !empty($screen_name)) {
				$returned = $this->_post($this->_api_hosts['main'].'friendships/create'.(!empty($id) ? '/'.$id.'.' : '.').$this->_type, array(
					'user_id' 		=> $user_id,
					'screen_name' 	=> $screen_name,
					'follow' 		=> $follow,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return false;	
	}	
	
	/**
	 * @desc Allows the authenticating users to unfollow the user specified in the ID parameter.
	 *
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @return unknown
	 */
	public function friendships_destroy($id = FALSE, $user_id = FALSE, $screen_name = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id) || (!empty($user_id) && is_numeric($user_id)) || !empty($screen_name)) {
				$returned = $this->_post($this->_api_hosts['main'].'friendships/destroy'.(!empty($id) ? '/'.$id.'.' : '.').$this->_type, array(
					'user_id' 		=> $user_id,
					'screen_name' 	=> $screen_name,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return false;
	}
	
	/**
	 * @desc Tests for the existence of friendship between two users. Will return true if user_a follows user_b, 
	 * otherwise will return FALSE.
	 * @deprecated Why not try the friendships_show() method, which gives you even more information 
	 * with a single call?
	 *
	 * @param mixed $user_a
	 * @param mixed $user_b
	 * @return unknown
	 */
	public function friendships_exists($user_a = FALSE, $user_b = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($user_a) && !empty($user_b)) {
				$returned = $this->_get($this->_api_hosts['main'].'friendships/exists.'.$this->_type, array(
					'user_a' 	=> $user_a,
					'user_b' 	=> $user_b,
				), FALSE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns detailed information about the relationship between two users.
	 *
	 * @param int $source_id
	 * @param string $source_screen_name
	 * @param int $target_id
	 * @param string $target_screen_name
	 * @return array
	 */
	public function friendships_show($source_id = FALSE, $source_screen_name = FALSE, $target_id = FALSE, $target_screen_name = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if((!empty($source_id) || !empty($source_screen_name)) && (!empty($target_id) || !empty($target_screen_name))) {
				$returned = $this->_get($this->_api_hosts['main'].'friendships/show.'.$this->_type, array(
					'source_id' 			=> $source_id,
					'source_screen_name' 	=> $source_screen_name,
					'target_id' 			=> $target_id,
					'target_screen_name' 	=> $target_screen_name,
				), FALSE, FALSE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns an array of numeric IDs for every user the specified user is following.
	 *
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @param int $cursor
	 * @param bool $auth
	 * @return array
	 */
	public function friends_ids($id = FALSE, $user_id = FALSE, $screen_name = FALSE, $cursor = FALSE, $auth = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id) || (!empty($user_id) && is_numeric($user_id)) || !empty($screen_name)) {
				$returned = $this->_get($this->_api_hosts['main'].'friends/ids'.(!empty($id) ? '/'.$id.'.' : '.').$this->_type, array(
					'user_id' 		=> $user_id,
					'screen_name' 	=> $screen_name,
					'cursor' 		=> $cursor,
				), FALSE, $auth);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns an array of numeric IDs for every user following the specified user.
	 *
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @param int $cursor
	 * @param bool $auth
	 * @return array
	 */
	public function followers_ids($id = FALSE, $user_id = FALSE, $screen_name = FALSE, $cursor = FALSE, $auth = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id) || (!empty($user_id) && is_numeric($user_id)) || !empty($screen_name)) {
				$returned = $this->_get($this->_api_hosts['main'].'followers/ids'.(!empty($id) ? '/'.$id.'.' : '.').$this->_type, array(
					'user_id' 		=> $user_id,
					'screen_name' 	=> $screen_name,
					'cursor' 		=> $cursor,
				), FALSE, $auth);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns a representation of the requesting user if authentication was successful; returns 
	 * FALSE if not. Use this method to test if supplied user credentials are valid.
	 *
	 * @return unknown
	 */
	public function account_verify_credentials() { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			$returned = $this->_get($this->_api_hosts['main'].'account/verify_credentials.'.$this->_type, FALSE, TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns the remaining number of API requests available to the requesting user before 
	 * the API limit is reached for the current hour.
	 *
	 * @param bool $check_ip
	 * @return array
	 */
	public function account_rate_limit_status($check_ip = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			$sReturned = $this->_get($this->_api_hosts['main'].'account/rate_limit_status.'.$this->_type, FALSE, $check_ip);
			return $this->_parse_returned_value($sReturned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Ends the session of the authenticating user, returning a null cookie. 
	 *
	 * @return array
	 */
	public function account_end_session() { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			$returned = $this->_post($this->_api_hosts['main'].'account/end_session.'.$this->_type, FALSE, TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Sets which device Twitter delivers updates to for the authenticating user. Sending none as 
	 * the device parameter will disable IM or SMS updates.
	 *
	 * @param string $device
	 * @return array
	 */
	public function account_update_delivery_device($device = FALSE)	{ 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($device)) {
				$returned = $this->_post($this->_api_hosts['main'].'account/update_delivery_device.'.$this->_type.'?device='.$device, FALSE, TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Sets one or more hex values that control the color scheme of the authenticating user's profile 
	 * page on twitter.com.
	 *
	 * @param string $profile_background_color
	 * @param string $profile_text_color
	 * @param string $profile_link_color
	 * @param string $profile_sidebar_fill_color
	 * @param string $profile_sidebar_border_color
	 * @return array
	 */
	public function account_update_profile_colors($profile_background_color = FALSE,$profile_text_color = FALSE, $profile_link_color = FALSE, $profile_sidebar_fill_color = FALSE, $profile_sidebar_border_color = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($profile_background_color) || !empty($profile_text_color) || !empty($profile_link_color) || !empty($profile_sidebar_fill_color) || !empty($profile_sidebar_border_color)) {
				$returned = $this->_post($this->_api_hosts['main'].'account/update_profile_colors.'.$this->_type, array(
				    'profile_background_color' 		=> $profile_background_color,
			    	'profile_text_color'			=> $profile_text_color,
			    	'profile_link_color'			=> $profile_link_color,
			    	'profile_sidebar_fill_color'	=> $profile_sidebar_fill_color,
			    	'profile_sidebar_border_color'	=> $profile_sidebar_border_color,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Updates the authenticating user's profile image. Note that this method expects image path on 
	 * local server, not a URL to an image or raw multipart data.
	 *
	 * @param string $image
	 * @return array
	 */
	public function account_update_profile_image($image = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($image) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$image)) {
				$info = getimagesize($_SERVER['DOCUMENT_ROOT'].'/'.$image);
				$image = '@'.$image.';type='.$info['mime'];
			
				$returned = $this->_post($this->_api_hosts['main'].'account/update_profile_image.'.$this->_type, array(
				    'image' => $image,
				), TRUE, TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Updates the authenticating user's profile background image. Note that this method expects image 
	 * path on local server, not a URL to an image or raw multipart data.
	 *
	 * @param string $image
	 * @param bool $tile
	 * @return array
	 */
	public function account_update_profile_background_image($image = FALSE,$tile = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($image) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$image)) {
				$info = getimagesize($_SERVER['DOCUMENT_ROOT'].'/'.$image);
				$image = '@'.$image.';type='.$info['mime'];
			
				$returned = $this->_post($this->_api_hosts['main'].'account/update_profile_background_image.'.$this->_type, array(
				    'image' => $image,
			    	'tile'	=> $tile,
				), TRUE, TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Sets values that users are able to set under the "Account" tab of their settings page. Only the 
	 * parameters specified will be updated.
	 *
	 * @param string $name
	 * @param string $url
	 * @param string $location
	 * @param string $description
	 * @return array
	 */
	public function account_update_profile($name = FALSE, $url = FALSE, $location = FALSE, $description = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($name) || !empty($url) || !empty($location) || !empty($description)) {
				$returned = $this->_post($this->_api_hosts['main'].'account/update_profile.'.$this->_type, array(
			    	'name' 			=> $name,
		    		'url'			=> $url,
		    		'location'		=> $location,
		    		'description'	=> $description,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns the 20 most recent favorite statuses for the authenticating user or user specified by the ID 
	 * parameter in the requested format.
	 *
	 * @param int $id
	 * @param int $page
	 * @return array
	 */
	public function favorites($id = FALSE, $page = FALSE)						{ 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			$returned = $this->_get($this->_api_hosts['main'].'favorites'.(!empty($id) ? '/'.$id.'.' : '.').$this->_type, array(
				'page' => $page,
			), TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Favorites the status specified in the ID parameter as the authenticating user. Returns the favorite 
	 * status when successful.
	 *
	 * @param int $id
	 * @return array
	 */
	public function favorites_create($id = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id)) {
				$returned = $this->_post($this->_api_hosts['main'].'favorites/create/'.$id.'.'.$this->_type, FALSE, TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;	
	}
	
	/**
	 * @desc Un-favorites the status specified in the ID parameter as the authenticating user. Returns the 
	 * un-favorited status in the requested format when successful.
	 *
	 * @param int $id
	 * @return array
	 */
	public function favorites_destroy($id = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id)) {
				$returned = $this->_post($this->_api_hosts['main'].'favorites/destroy/'.$id.'.'.$this->_type, FALSE, TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Enables device notifications for updates from the specified user. Returns the specified user 
	 * when successful.
	 *
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @return array
	 */
	public function notifications_follow($id = FALSE, $user_id = FALSE, $screen_name = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id) || (!empty($user_id) && is_numeric($user_id)) || !empty($screen_name)) {
				$returned = $this->_post($this->_api_hosts['main'].'notifications/follow'.(!empty($id) ? '/'.$id.'.' : '.').$this->_type, array(
					'user_id' 		=> $user_id,
					'screen_name' 	=> $screen_name,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Disables notifications for updates from the specified user to the authenticating user. Returns the 
	 * specified user when successful.
	 *
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @return array
	 */
	public function notifications_leave($id = FALSE, $user_id = FALSE, $screen_name = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id) || (!empty($user_id) && is_numeric($user_id)) || !empty($screen_name)) {
				$returned = $this->_post($this->_api_hosts['main'].'notifications/leave'.(!empty($id) ? '/'.$id.'.' : '.').$this->_type, array(
					'user_id' 		=> $user_id,
					'screen_name' 	=> $screen_name,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Blocks the user specified in the ID parameter as the authenticating user. Destroys a friendship to the
	 * blocked user if it exists. Returns the blocked user in the requested format when successful.
	 *
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @return array
	 */
	public function blocks_create($id = FALSE, $user_id = FALSE, $screen_name = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id) || (!empty($user_id) && is_numeric($user_id)) || !empty($screen_name)) {
				$returned = $this->_post($this->_api_hosts['main'].'blocks/create'.(!empty($id) ? '/'.$id.'.' : '.').$this->_type, array(
					'user_id' 		=> $user_id,
					'screen_name' 	=> $screen_name,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Un-blocks the user specified in the ID parameter for the authenticating user.  Returns the un-blocked 
	 * user in the requested format when successful.
	 *
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @return array
	 */
	public function blocks_destroy($id = FALSE, $user_id = FALSE, $screen_name = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id) || (!empty($user_id) && is_numeric($user_id)) || !empty($screen_name)) {
				$returned = $this->_post($this->_api_hosts['main'].'blocks/destroy'.(!empty($id) ? '/'.$id.'.' : '.').$this->_type, array(
					'user_id' 		=> $user_id,
					'screen_name' 	=> $screen_name,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns if the authenticating user is blocking a target user. Will return the blocked user's object 
	 * if a block exists, and FALSE otherwise.
	 *
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @return array
	 */
	public function blocks_exists($id = FALSE, $user_id = FALSE, $screen_name = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id) || (!empty($user_id) && is_numeric($user_id)) || !empty($screen_name)) {
				$returned = $this->_get($this->_api_hosts['main'].'blocks/exists'.(!empty($id) ? '/'.$id.'.' : '.').$this->_type, array(
					'user_id' 		=> $user_id,
					'screen_name' 	=> $screen_name,
				), TRUE);
				return $this->_parse_returned_value($sreturned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns an array of user objects that the authenticating user is blocking.
	 *
	 * @param int $page
	 * @return array
	 */
	public function blocks_blocking($page = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			$returned = $this->_get($this->_api_hosts['main'].'blocks/blocking.'.$this->_type, array(
				'page' 		=> $page,
			), TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns an array of numeric user ids the authenticating user is blocking.
	 *
	 * @return array
	 */
	public function blocks_blocking_ids() {
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			$returned = $this->_get($this->_api_hosts['main'].'blocks/blocking/ids.'.$this->_type, FALSE, TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc The user specified in the id is blocked by the authenticated user and reported as a spammer.
	 * 
	 * @param mixed $id
	 * @param int $user_id
	 * @param string $screen_name
	 * @return array
	 */
	public function report_spam($id = FALSE, $user_id = FALSE, $screen_name = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id) || (!empty($user_id) && is_numeric($user_id)) || !empty($screen_name)) {
				$returned = $this->_post($this->_api_hosts['main'].'report_spam'.$this->_type, array(
					'id' 			=> $id,
					'user_id' 		=> $user_id,
					'screen_name' 	=> $screen_name,
				), TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;	
	}
	
	/**
	 * @desc Returns the authenticated user's saved search queries.
	 *
	 * @return array
	 */
	public function saved_searches() {
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			$returned = $this->_get($this->_api_hosts['main'].'saved_searches.'.$this->_type, FALSE, TRUE);
			return $this->_parse_returned_value($returned);	
		}
		return FALSE;
	}
	
	/**
	 * @desc Retrieve the data for a saved search owned by the authenticating user specified by the given id.
	 *
	 * @param int $id
	 * @return array
	 */
	public function saved_searches_show($id = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id) && is_numeric($id)) {
				$returned = $this->_get($this->_api_hosts['main'].'saved_searches/show/'.$id.'.'.$this->_type, FALSE, TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Creates a saved search for the authenticated user.
	 *
	 * @param string $query
	 * @return array
	 */
	public function saved_searches_create($query = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($query)) {
				$returned = $this->_post($this->_api_hosts['main'].'saved_searches/create.'.$this->_type, array(
					'query' => $query,
				),TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Destroys a saved search for the authenticated user. The search specified by id must be owned 
	 * by the authenticating user.
	 *
	 * @param int $id
	 * @return array
	 */
	public function saved_searches_destroy($id = FALSE) { 
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			if(!empty($id) && is_numeric($id)) {
				$returned = $this->_post($this->_api_hosts['main'].'saved_searches/destroy/'.$id.'.'.$this->_type, FALSE, TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns the locations that Twitter has trending topic information for. The response is 
	 * an array of "locations" that encode the location's WOEID (a Yahoo! Where On Earth ID) and some 
	 * other human-readable information such as a canonical name and country the location belongs in.
	 *
	 * @param string $lat
	 * @param string $long
	 * @return array
	 */
	public function trends_available($lat = FALSE, $long = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {
			$returned = $this->_get($this->_api_hosts['v1'].'trends/available.'.$this->_type, array(
				'lat' 	=> $lat,
				'long'	=> $long,
			), TRUE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns the top 10 trending topics for a specific location Twitter has trending topic 
	 * information for. Global trends information is also available from this API by using a WOEID of 1
	 * 
	 * @param int $woeid
	 * @return array
	 */
	public function trends_location($woeid = FALSE) {
		if(in_array(strtolower($this->_type), array('xml','json'))) {	
			if(!empty($woeid)) {
				$returned = $this->_get($this->_api_hosts['v1'].'trends/'.$woeid.'.'.$this->_type, FALSE, TRUE);
				return $this->_parse_returned_value($returned);
			}
		}
		return FALSE;
	}
	
	/**
	 * @desc Returns the string "ok" for json and string "true" for xml format.
	 *
	 * @return string
	 */
	public function help_test() {
		if(in_array(strtolower($this->_type), array('xml','json'))) {										 
			$returned = $this->_get($this->_api_hosts['main'].'help/test.'.$this->_type, FALSE, FALSE);
			return $this->_parse_returned_value($returned);
		}
		return FALSE;
	}
	
	/**
 	 * @desc Using account_verify_credentials() to verify if username and password are valid.
 	 *
 	 * @param string $username
 	 * @param string $password
 	 * @return bool
 	 */
	public function auth($username = FALSE, $password = FALSE) { 
		if(!empty($username) && !empty($password)) {
			$this->_username = $username;
			$this->_password = $password;
		}
		
		if(!empty($this->_username) && !empty($this->_password)) {
			if($this->_user = $this->account_verify_credentials()) {
				return TRUE;
			};
		}
		
		return FALSE;
	}
	
	/**
 	 * @desc Returns current user info array.
 	 *
 	 * @param string $username
 	 * @param string $password
 	 * @return array
 	 */
	public function get_current_user() {
		if($this->_user) {
			return $this->_user;	
		}									
		return FALSE;
	}
	
	/**
 	 * @desc Sets API return format.
 	 *
 	 * @param string $format
 	 * @return string
 	 */
	public function set_format($format = FALSE) {
		if(!empty($format) && in_array(strtolower($format), array('xml','json', 'rss', 'atom'))) {
			return $this->_type = $format;
		} 	
		return false;
 	}
 	
 	public function set_parse_return($state = TRUE) {
 		$this->_return_as_is = $state;
 		return TRUE;
 	}
	
 	public function __construct($parameters = FALSE) {
		if(!function_exists('log_message')) {
			$this->_log= FALSE;
		}
		
 		if(!function_exists('curl_init')) {
 			if($this->_log) {
 				log_message('error', 'Twitter Class: cURL module not found. Use --with-curl when compiling PHP.');
 			} else {
 				echo ('Twitter Class: cURL module not found. Use --with-curl when compiling PHP.');
 			}
 		}
 		
 		if(strtolower($this->_type) == 'json' && !function_exists('json_decode')) {
 			if($this->_log) {
 				log_message('error', 'Twitter Class: JSON Functions were not found.');
 			} else {
 				echo ('Twitter Class: JSON Functions were not found.');
 			}
 		}
 		
 		if(in_array(strtolower($this->_type), array('xml','rss','atom')) && !class_exists('SimpleXMLElement')) {
 			if($this->_log) {
 				log_message('error', 'Twitter Class: SimpleXMLElement class was not found.');
 			} else {
 				echo ('Twitter Class: SimpleXMLElement class was not found.');
 			}
 		}
 		
 		$this->_curl = curl_init();
 		
 		if(is_array($parameters) && count($parameters) > 0 && !empty($parameters['username']) && !empty($parameters['password'])) {
 			return $this->auth($parameters['username'],$parameters['password']);
 		}
 		
 		return true;
 	}
 	
 	private function _request($url, $parameters = FALSE, $method = 'GET',$authorize = TRUE, $send_array = FALSE) {
 		$method = (is_null($method) ? 'GET' : $method);
 		$parameters = $this->_build_url_parameters($parameters,('GET' != $method ? TRUE : FALSE),$send_array);
 		curl_setopt($this->_curl, CURLOPT_URL, $url.($method == 'GET' ? $parameters : false));
 		curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, TRUE);
 		curl_setopt($this->_curl, $this->_user_agent);
 		if($authorize) {
			curl_setopt($this->_curl, CURLOPT_USERPWD, $this->_username.':'.$this->_password);
 		}
 		switch($method) {
 			case 'DELETE':
 				curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
 			case 'POST':
 				curl_setopt($this->_curl, CURLOPT_POST, TRUE);
				curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $parameters);
				curl_setopt($this->_curl, CURLOPT_HTTPHEADER, array('Expect:'));
 			break;
 		}
 		
 		$returned = curl_exec($this->_curl);
		$code = curl_getinfo($this->_curl, CURLINFO_HTTP_CODE);
		if (200 == $code){
			return $returned;
		} else {
			if($this->_log) {
				log_message('error', 'Twitter Class: '.$method.':'.$code.' HTTP code returned when trying to fetch data.');
			} else {
				echo ('Twitter Class: '.$method.':'.$code.' HTTP code returned when trying to fetch data.');
			}
		}	
		return FALSE;
 	}
	
 	private function _get($url, $parameters = FALSE, $authorize = TRUE) { 
 		return $this->_request($url, $parameters, 'GET', $authorize, FALSE);
 	}
 	
	private function _post($url, $parameters = FALSE, $authorize = TRUE, $send_array = FALSE) { 
		return $this->_request($url, $parameters, 'POST', $authorize, $send_array);
	}
	
	private function _delete($url, $parameters = FALSE, $authorize = TRUE) { 
		return $this->_request($url, $parameters, 'DELETE', $authorize, FALSE);
	}
	
	private function _parse_returned_value($returned) { 
		if(!$this->_return_as_is) {
			switch(strtolower($this->_type)) {
				case 'xml':
				case 'atom':
				case 'rss':
					$return = json_decode(json_encode(new SimpleXMLElement($returned)),true);
					unset($return['@attributes']);
					if(1 == count($return)) {
						foreach($return as $key=>$value) {
							unset($return[$key]);
							$return = $value;	
						}
					}
				break;
				
				case 'json':
					$return = json_decode($returned,true);
				break;
			}
			return $return;
		}
		return $returned;
	}
	
	public function _parse_tweet($tweet) { 
		return $tweet;
	}
	
 	private function _build_url_parameters($parameters, $post = FALSE, $array = FALSE)	{ 
 		if(is_array($parameters)) {
 			$parameters = array_filter($parameters);
 			if(count($parameters) > 0) {
 				if(!$array) {
 					$return = '';
 					foreach($parameters as $key=>$value) {
			 			if(!empty($return)) {
 							$return .= '&';
 						} 
 						$value = (TRUE === $value ? 'true' : $value);
 						$return .= urlencode($key).'='.(!$post ? urlencode($value) : $value);
 					}
	 		
 					$return = (!$post) ? '?'.$return : $return ;
 					return $return;
 				}
 				foreach($parameters as $key=>$value) {
 					$parameters[$key] = (TRUE === $value ? 'true' : $value);
 				}
 				return $parameters;
 			}
 		}
 		return FALSE;
 	} 
 	
 	public function __destruct() { 
 		unset($this);
 	}	
 }
 
 // END Twitter class
/* End of file Twitter.php */