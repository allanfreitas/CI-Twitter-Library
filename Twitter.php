<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @desc CodeIgniter Twitter Class
 *This class enables you to use Twitter API functions.
 *
 * @version 	0.1
 * @author		Arthur Grishin <arthur.grishin@me.com>
 * @link		http://www.arthurgrishin.com/
 * @copyright 	© 2010 Arthur Grishin
 */
 
 class Twitter {
 	
 	private $_sUsername 		= '';		// Default username
 	private $_sPassword 		= '';		// Default password
 	private $_bAuthed 			= false;	// Is user authed variable
 	private $_sType 			= 'json'; 	// API answer Type (json or xml)
 	private $_bLog				= true;		// For CodeIgniter error handling
 	private $_aUser				= false;	
 	private $_sApiHost			= 'http://twitter.com/';
 	private $_sSearchApiHost	= 'http://search.twitter.com/';
 	private $_sTestApiHost		= 'http://api.twitter.com/1/';
 	public	$bReturnAsIs		= false;
 	
 	/* Search API methods */
 	public function search($query = '', $callback = '',$lang = '', $locale = '', $rpp = '', $page = '', $since_id = 0, $geocode = '', $show_user = false) { 
		if($this->_sType == 'json' || $this->_sType == 'atom') {
			$sReturned = $this->_get($this->_sSearchApiHost.'search.'.$this->_sType,array(
				'q'			=> $query,
				'callback'	=> ($this->_sType == 'json' ? $callback : ''),
          		'lang'		=> $lang,
          		'locale'	=> $locale,
          		'rpp'		=> $rpp,
          		'page'		=> $page,
          		'since_id'	=> $since_id,
          		'geocode'	=> $geocode,
          		'show_user'	=> $show_user,
			));
			return $this->_parse_returned_value($sReturned);
		}
 	}
 	
 	public function trends()											{
 		if($this->_sType == 'json') {
 			$sReturned = $this->_get($this->_sSearchApiHost.'trends.'.$this->_sType);
			return $this->_parse_returned_value($sReturned);
 		}	
 		return false;
 	}
 	
 	public function trends_current($exclude = '')						{ 
 		if($this->_sType == 'json') {
 			$sReturned = $this->_get($this->_sSearchApiHost.'trends/current.'.$this->_sType,array(
 				'exclude' => $exclude,
 			));
			return $this->_parse_returned_value($sReturned);
 		}	
 		return false;
 	}
 	
 	public function trends_daily($date = '', $exclude = '')				{ 
 		if($this->_sType == 'json') {
 			$sReturned = $this->_get($this->_sSearchApiHost.'trends/daily.'.$this->_sType,array(
 				'date' 		=> $date,
 				'exclude' 	=> $exclude,
 			));
			return $this->_parse_returned_value($sReturned);
 		}	
 		return false;	
 	}
 	
 	public function trends_weekly()										{ 
 		if($this->_sType == 'json') {
 			$sReturned = $this->_get($this->_sSearchApiHost.'trends/weekly.'.$this->_sType,array(
 				'date' 		=> $date,
 				'exclude' 	=> $exclude,
 			));
			return $this->_parse_returned_value($sReturned);
 		}	
 		return false;	
 	}
	
	/* Timeline methods */
	public function statuses_public_timeline() 							{ 
		$sReturned = $this->_get($this->_sApiHost.'statuses/public_timeline.'.$this->_sType);
		return $this->_parse_returned_value($sReturned);
	}
	
	public function statuses_home_timeline($since_id = false, $max_id = false, $count = false, $page = false) { 
		$sReturned = $this->_get($this->_sApiHost.'statuses/home_timeline.'.$this->_sType,array(
			'since_id' 	=> $since_id,
			'max_id' 	=> $max_id,
			'count' 	=> $count,
			'page' 		=> $page,
		));
		return $this->_parse_returned_value($sReturned);
	}
	
	public function statuses_friends_timeline($since_id = false, $max_id = false, $count = false, $page = false)							{ 
		$sReturned = $this->_get($this->_sApiHost.'statuses/friends_timeline.'.$this->_sType,array(
			'since_id' 	=> $since_id,
			'max_id' 	=> $max_id,
			'count' 	=> $count,
			'page' 		=> $page,
		));
		return $this->_parse_returned_value($sReturned);
	}	
	
	public function statuses_user_timeline($id = '',$user_id = false,$screen_name = false, $since_id = false, $max_id = false, $count = false, $page = false) 							{ 
		$sReturned = $this->_get($this->_sApiHost.'statuses/user_timeline'.(!empty($id) ? '/'.$id.'.' : '.').$this->_sType,array(
			'user_id' 		=> $user_id,
			'screen_name'	=> $screen_name,
			'since_id' 		=> $since_id,
			'max_id' 		=> $max_id,
			'count' 		=> $count,
			'page' 			=> $page,
		));
		return $this->_parse_returned_value($sReturned);
	}
	
	public function statuses_mentions($since_id = false, $max_id = false, $count = false, $page = false) 								{ 
		$sReturned = $this->_get($this->_sApiHost.'statuses/mentions.'.$this->_sType,array(
			'since_id' 	=> $since_id,
			'max_id' 	=> $max_id,
			'count' 	=> $count,
			'page' 		=> $page,
		));
		return $this->_parse_returned_value($sReturned);
	}
	
	public function statuses_retweeted_by_me($since_id = false, $max_id = false, $count = false, $page = false) 							{ 
		$sReturned = $this->_get($this->_sTestApiHost.'statuses/retweeted_by_me.'.$this->_sType,array(
			'since_id' 	=> $since_id,
			'max_id' 	=> $max_id,
			'count' 	=> $count,
			'page' 		=> $page,
		));
		return $this->_parse_returned_value($sReturned);
	}
	
	public function statuses_retweeted_to_me($since_id = false, $max_id = false, $count = false, $page = false) 							{ 
		$sReturned = $this->_get($this->_sTestApiHost.'statuses/retweeted_to_me.'.$this->_sType,array(
			'since_id' 	=> $since_id,
			'max_id' 	=> $max_id,
			'count' 	=> $count,
			'page' 		=> $page,
		));
		return $this->_parse_returned_value($sReturned);
	}
	
	public function statuses_retweets_of_me($since_id = false, $max_id = false, $count = false, $page = false) 							{ 
		$sReturned = $this->_get($this->_sTestApiHost.'statuses/retweets_of_me.'.$this->_sType,array(
			'since_id' 	=> $since_id,
			'max_id' 	=> $max_id,
			'count' 	=> $count,
			'page' 		=> $page,
		));
		return $this->_parse_returned_value($sReturned);
	}
	
	/* Status methods */
	public function statuses_show($id = '') 							{ 
		if(!empty($id)) {
			$sReturned = $this->_get($this->_sApiHost.'statuses/show/'.$id.'.'.$this->_sType);
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	public function statuses_update($status, $in_reply_to_status_id = false, $lat = false, $long = false) { 
		$sReturned = $this->_post($this->_sApiHost.'statuses/update.'.$this->_sType,array(
			'status' 					=> $status,
			'in_reply_to_status_id' 	=> $in_reply_to_status_id,
			'lat' 						=> $lat,
			'long' 						=> $long,
		));
		return $this->_parse_returned_value($sReturned);	
	}
	
	public function statuses_destroy($id = '') 							{ 
		if(!empty($id)) {
			$sReturned = $this->_post($this->_sApiHost.'statuses/destroy/'.$id.'.'.$this->_sType);
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	public function statuses_retweet($id = '') 							{ 
		if(!empty($id)) {
			$sReturned = $this->_post($this->_sTestApiHost.'statuses/retweet/'.$id.'.'.$this->_sType);
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	public function statuses_retweets($id = '',$count = false)			{
		if(!empty($id)) {
			$sReturned = $this->_get($this->_sTestApiHost.'statuses/retweets/'.$id.'.'.$this->_sType,array(
				'count' => $count,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	/* User methods */
	public function users_show($id = '',$user_id = false, $screen_name = false) { 
		if(!empty($id) || !empty($user_id) || !empty($screen_name)) {
			$sReturned = $this->_get($this->_sApiHost.'users/show'.(!empty($id) ? '/'.$id.'.' : '.').$this->_sType,array(
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	public function users_search($q = '',$per_page = false,$page = false) { 
		if(!empty($q)) {
			$sReturned = $this->_get($this->_sTestApiHost.'users/search.'.$this->_sType,array(
				'q' 		=> $q,
				'per_page' 	=> $per_page,
				'page' 		=> $page,
			));
			return $this->_parse_returned_value($sReturned);
		}
	}
	
	public function statuses_friends($id = '',$user_id = false,$screen_name = false, $cursor = false) {
		$sReturned = $this->_get($this->_sApiHost.'statuses/friends.'.$this->_sType,array(
			'id' 			=> $id,
			'user_id' 		=> $user_id,
			'screen_name' 	=> $screen_name,
			'cursor' 		=> $cursor,
		));
		return $this->_parse_returned_value($sReturned);
	}
	
	public function statuses_followers($id = '',$user_id = false,$screen_name = false, $cursor = false) {
		$sReturned = $this->_get($this->_sApiHost.'statuses/followers.'.$this->_sType,array(
			'id' 			=> $id,
			'user_id' 		=> $user_id,
			'screen_name' 	=> $screen_name,
			'cursor' 		=> $cursor,
		));
		return $this->_parse_returned_value($sReturned);
	}
	
	/* Direct Message methods */
	public function direct_messages($since_id = false, $max_id = false, $count = false, $page = false) { 
		$sReturned = $this->_get($this->_sApiHost.'direct_messages.'.$this->_sType,array(
			'since_id' 	=> $since_id,
			'max_id' 	=> $max_id,
			'count' 	=> $count,
			'page' 		=> $page,
		));
		return $this->_parse_returned_value($sReturned);
	}
	
	public function direct_messages_sent($since_id = false, $max_id = false, $count = false, $page = false) { 
		$sReturned = $this->_get($this->_sApiHost.'direct_messages/sent.'.$this->_sType,array(
			'since_id' 	=> $since_id,
			'max_id' 	=> $max_id,
			'count' 	=> $count,
			'page' 		=> $page,
		));
		return $this->_parse_returned_value($sReturned);	
	}
	
	public function direct_messages_new($user = '', $user_id = 0, $screen_name = false,$text = '') { 
		if((!empty($user) || !empty($user_id) || !empty($screen_name)) && !empty($text)) {
			$sReturned = $this->_post($this->_sApiHost.'direct_messages/new.'.$this->_sType,array(
				'user' 			=> $user,
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
				'text' 			=> $text,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	public function direct_messages_destroy($id = 0) 					{ 
		if(!empty($id)) {
			$sReturned = $this->_post($this->_sApiHost.'direct_messages/destroy/'.$id.'.'.$this->_sType);
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	/* Friendships methods */
	public function friendships_create($id = '', $user_id = 0, $screen_name = '', $follow = false) {
		if((!empty($id) || !empty($user_id) || !empty($screen_name))) {
			$sReturned = $this->_post($this->_sApiHost.'friendships/create'.(!empty($id) ? '/'.$id.'.' : '.').$this->_sType,array(
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
				'follow' 		=> $follow,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;	
	}	
	
	public function friendships_destroy($id = '', $user_id = 0, $screen_name = '')		{ 
		if((!empty($id) || !empty($user_id) || !empty($screen_name))) {
			$sReturned = $this->_post($this->_sApiHost.'friendships/destroy'.(!empty($id) ? '/'.$id.'.' : '.').$this->_sType,array(
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	public function friendships_exists($user_a,$user_b)					{ 
		$sReturned = $this->_get($this->_sApiHost.'friendships/exists.'.$this->_sType,array(
			'user_a' 	=> $user_a,
			'user_b' 	=> $user_b,
		));
		return $this->_parse_returned_value($sReturned);
	}
	
	public function friendships_show($source_id = '', $source_screen_name = 0, $target_id = '', $target_screen_name = 0) {
		if((!empty($source_id) || !empty($source_screen_name)) && (!empty($target_id) || !empty($target_screen_name)))
		$sReturned = $this->_get($this->_sApiHost.'friendships/show.'.$this->_sType,array(
			'source_id' 			=> $source_id,
			'source_screen_name' 	=> $source_screen_name,
			'target_id' 			=> $target_id,
			'target_screen_name' 	=> $target_screen_name,
		));
		return $this->_parse_returned_value($sReturned);
	}
	
	/* Social Graph methods */
	public function friends_ids($id = '',$user_id = 0, $screen_name = '', $cursor = false) {
		if(!empty($id) || !empty($user_id) || !empty($screen_name)) {
			$sReturned = $this->_get($this->_sApiHost.'friends/ids'.(!empty($id) ? '/'.$id.'.' : '.').$this->_sType,array(
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
				'cursor' 		=> $cursor,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	public function followers_ids($id = '',$user_id = 0, $screen_name = '', $cursor = false)										{
		if(!empty($id) || !empty($user_id) || !empty($screen_name)) {
			$sReturned = $this->_get($this->_sApiHost.'followers/ids'.(!empty($id) ? '/'.$id.'.' : '.').$this->_sType,array(
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
				'cursor' 		=> $cursor,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	/* Account methods */
	public function account_verify_credentials()						{ 
		$sReturned = $this->_get($this->_sApiHost.'account/verify_credentials.'.$this->_sType);
		return $this->_parse_returned_value($sReturned);
	}
	
	public function account_rate_limit_status($check_ip = false)		{ 
		$sReturned = $this->_get($this->_sApiHost.'account/rate_limit_status.'.$this->_sType,false,$check_ip);
		return $this->_parse_returned_value($sReturned);
	}
	
	public function account_end_session()								{ 
		$sReturned = $this->_post($this->_sApiHost.'account/end_session.'.$this->_sType);
		return $this->_parse_returned_value($sReturned);
	}
	
	public function account_update_delivery_device($device = 'none')	{ 
		$sReturned = $this->_post($this->_sApiHost.'account/update_delivery_device.'.$this->_sType.'?device='.$device);
		return $this->_parse_returned_value($sReturned);
	}
	
	public function account_update_profile_colors($profile_background_color = '',$profile_text_color = '', $profile_link_color = '', $profile_sidebar_fill_color = '', $profile_sidebar_border_color = '') { 
		if(!empty($profile_background_color) || !empty($profile_text_color) || !empty($profile_link_color) || !empty($profile_sidebar_fill_color) || !empty($profile_sidebar_border_color)) {
			$sReturned = $this->_post($this->_sApiHost.'account/update_profile_colors.'.$this->_sType,array(
			    'profile_background_color' 		=> $profile_background_color,
		    	'profile_text_color'			=> $profile_text_color,
		    	'profile_link_color'			=> $profile_link_color,
		    	'profile_sidebar_fill_color'	=> $profile_sidebar_fill_color,
		    	'profile_sidebar_border_color'	=> $profile_sidebar_border_color,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	public function account_update_profile_image($image = '')				{ 
		if(!empty($image) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$image)) {
			$info = getimagesize($_SERVER['DOCUMENT_ROOT'].'/'.$image);
			$image = '@'.$image.';type='.$info['mime'];
		
			$sReturned = $this->_post($this->_sApiHost.'account/update_profile_image.'.$this->_sType,array(
			    'image' 		=> $image,
			),false,true);
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	public function account_update_profile_background_image($image = '',$tile = false) { 
		if(!empty($image) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$image)) {
			$info = getimagesize($_SERVER['DOCUMENT_ROOT'].'/'.$image);
			$image = '@'.$image.';type='.$info['mime'];
		
			$sReturned = $this->_post($this->_sApiHost.'account/update_profile_background_image.'.$this->_sType,array(
			    'image' 		=> $image,
			    'tile'			=> $tile,
			),false,true);
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	
	public function account_update_profile($name = '', $url = '', $location = '', $description = '') { 
		if(!empty($name) || !empty($url) || !empty($location) || !empty($description)) {
			$sReturned = $this->_post($this->_sApiHost.'account/update_profile.'.$this->_sType,array(
			    'name' 			=> $name,
		    	'url'			=> $url,
		    	'location'		=> $location,
		    	'description'	=> $description,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	/* Favorite methods */
	public function favorites($id = '', $page = 0)						{ 
		$sReturned = $this->_get($this->_sApiHost.'favorites'.(!empty($id) ? '/'.$id.'.' : '.').$this->_sType,array(
			'page' => $page,
		));
		return $this->_parse_returned_value($sReturned);
	}
	
	public function favorites_create($id)								{
		if(!empty($id)) {
			$sReturned = $this->_post($this->_sApiHost.'favorites/create/'.$id.'.'.$this->_sType);
			return $this->_parse_returned_value($sReturned);
		}
		return false;	
	}
	
	public function favorites_destroy($id)								{ 
		if(!empty($id)) {
			$sReturned = $this->_post($this->_sApiHost.'favorites/destroy/'.$id.'.'.$this->_sType);
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	/* Notifications methods */
	public function notifications_follow($id = '',$user_id = 0, $screen_name = '') { 
		if(!empty($id) || !empty($user_id) || !empty($screen_name)) {
			$sReturned = $this->_post($this->_sApiHost.'notifications/follow'.(!empty($id) ? '/'.$id.'.' : '.').$this->_sType,array(
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	public function notifications_leave($id = '',$user_id = 0, $screen_name = '') { 
		if(!empty($id) || !empty($user_id) || !empty($screen_name)) {
			$sReturned = $this->_post($this->_sApiHost.'notifications/leave'.(!empty($id) ? '/'.$id.'.' : '.').$this->_sType,array(
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	/* Block methods */
	public function blocks_create($id = '',$user_id = 0, $screen_name = '')										{ 
		if(!empty($id) || !empty($user_id) || !empty($screen_name)) {
			$sReturned = $this->_post($this->_sApiHost.'blocks/create'.(!empty($id) ? '/'.$id.'.' : '.').$this->_sType,array(
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	public function blocks_destroy($id = '',$user_id = 0, $screen_name = '')										{ 
		if(!empty($id) || !empty($user_id) || !empty($screen_name)) {
			$sReturned = $this->_post($this->_sApiHost.'blocks/destroy'.(!empty($id) ? '/'.$id.'.' : '.').$this->_sType,array(
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	public function blocks_exists($id = '',$user_id = 0, $screen_name = '')										{ 
		if(!empty($id) || !empty($user_id) || !empty($screen_name)) {
			$sReturned = $this->_get($this->_sApiHost.'blocks/exists'.(!empty($id) ? '/'.$id.'.' : '.').$this->_sType,array(
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	public function blocks_blocking($page = 0)							{ 
		$sReturned = $this->_get($this->_sApiHost.'blocks/blocking.'.$this->_sType,array(
			'page' 		=> $page,
		));
		return $this->_parse_returned_value($sReturned);
	}
	
	public function blocks_blocking_ids()								{ 
		$sReturned = $this->_get($this->_sApiHost.'blocks/blocking/ids.'.$this->_sType);
		return $this->_parse_returned_value($sReturned);
	}
	
	/* Spam Reporting methods */
	public function report_spam($id = '',$user_id = 0, $screen_name = '') {
		if(!empty($id) || !empty($user_id) || !empty($screen_name)) {
			$sReturned = $this->_post($this->_sApiHost.'report_spam'.$this->_sType,array(
				'id' 			=> $id,
				'user_id' 		=> $user_id,
				'screen_name' 	=> $screen_name,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;	
	}
	
	/* Saved Searches methods */
	public function saved_searches()									{
		$sReturned = $this->_get($this->_sApiHost.'saved_searches.'.$this->_sType);
		return $this->_parse_returned_value($sReturned);	
	}
	public function saved_searches_show($id = '')						{
		if(!empty($id)) {
			$sReturned = $this->_get($this->_sApiHost.'saved_searches/show/'.$id.'.'.$this->_sType);
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	public function saved_searches_create($query = '')					{ 
		if(!empty($query)) {
			$sReturned = $this->_post($this->_sApiHost.'saved_searches/create.'.$this->_sType,array(
				'query' => $query,
			));
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	public function saved_searches_destroy($id)							{ 
		if(!empty($id)) {
			$sReturned = $this->_post($this->_sApiHost.'saved_searches/destroy/'.$id.'.'.$this->_sType);
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	/* OAuth methods */
	/* Will be implemented later */
	
	/* Local Trends methods */
	public function trends_available($lat = '', $long = '')				{ 
		$sReturned = $this->_get($this->_sTestApiHost.'trends/available.'.$this->_sType,array(
			'lat' 	=> $lat,
			'long'	=> $long,
		));
		return $this->_parse_returned_value($sReturned);
	}
	public function trends_location($woeid = '')						{
		if(!empty($woeid)) {
			$sReturned = $this->_get($this->_sTestApiHost.'trends/'.$woeid.'.'.$this->_sType);
			return $this->_parse_returned_value($sReturned);
		}
		return false;
	}
	
	/* Help methods */
	public function help_test()											{ 
		$sReturned = $this->_get($this->_sApiHost.'help/test.'.$this->_sType);
		return $this->_parse_returned_value($sReturned);
	}
	
	/* Twitter Class System methods */
 	public  function __construct($aParams = false)						{
		if(!function_exists('log_message')) {
			$this->_bLog = false;
		}
		$this->_bLog = false;
 		if(!function_exists('curl_init')) {
 			if($this->_bLog) {
 				log_message('error', 'Twitter Class: cURL module not found. Use --with-curl when compiling PHP.');
 			} else {
 				die('Twitter Class: cURL module not found. Use --with-curl when compiling PHP.');
 			}
 		}
 		
 		if(!function_exists('json_decode') && $this->_sType == 'json') {
 			if($this->_bLog) {
 				log_message('error', 'Twitter Class: JSON Functions were not found. Compile PHP with JSON Functions.');
 			} else {
 				die('Twitter Class: JSON Functions were not found. Compile PHP with JSON Functions.');
 			}
 		}
 		
 		if(is_array($aParams) && count($aParams) > 0 && !empty($aParams['username']) && !empty($aParams['password'])) {
 			return $this->auth($aParams['username'],$aParams['password']);
 		}
 		
 		return true;
 	}
 	
	public  function auth($username = false,$password = false) 			{ 
		if(!empty($username) && !empty($password)) {
			$this->_sUsername = $username;
			$this->_sPassword = $password;
		}
		
		if(!empty($this->_sUsername) && !empty($this->_sPassword)) {
			if($this->_aUser = $this->account_verify_credentials()) {
				return true;
			};
		}
		
		return false;
	}
	
	public	function get_current_user() 								{
		if($this->_aUser) {
			return $this->_aUser;	
		}									
		return false;
	}
	
 	private function _get($sUrl,$aParameters = NULL,$bNotAuthorize = false)						{ 
 		$sParams = $this->_build_url_parameters($aParameters);
		
 		$rCurl = curl_init();
		curl_setopt($rCurl, CURLOPT_URL, $sUrl.$sParams);
		if(!$bNotAuthorize) {
			curl_setopt($rCurl, CURLOPT_USERPWD, $this->_sUsername.':'.$this->_sPassword);
		}
		curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, true); 
		$sReturned = curl_exec($rCurl);
		$iHttpCode = curl_getinfo($rCurl, CURLINFO_HTTP_CODE);
		curl_close($rCurl);
		if (200 == $iHttpCode){
			return $sReturned;
		} else {
			if($this->_bLog) {
				log_message('error', 'Twitter Class: '.$iHttpCode.' HTTP code returned when trying to fetch data.');
			} else {
				echo ('Twitter Class: '.$iHttpCode.' HTTP code returned when trying to fetch data.');
			}
			return false;
		}
 	}
	private function _post($sUrl,$aParameters = NULL,$bNotAuthorize = false,$bSendArray = false) { 
		$sParams = $this->_build_url_parameters($aParameters,true,$bSendArray);
		
		$rCurl = curl_init();
		curl_setopt($rCurl, CURLOPT_URL, $sUrl);
		if(!$bNotAuthorize) {
			curl_setopt($rCurl, CURLOPT_USERPWD, $this->_sUsername.':'.$this->_sPassword);
		}
		curl_setopt($rCurl, CURLOPT_POST, true);
		curl_setopt($rCurl, CURLOPT_POSTFIELDS, $sParams);
		curl_setopt($rCurl, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, true);
		$sReturned = curl_exec($rCurl);
		$iHttpCode = curl_getinfo($rCurl, CURLINFO_HTTP_CODE);
		curl_close($rCurl);
		if (200 == $iHttpCode){
			return $sReturned;
		} else {
			if($this->_bLog) {
				log_message('error', 'Twitter Class: '.$iHttpCode.' HTTP code returned when trying to fetch data.');
			} else {
				echo ('Twitter Class: '.$iHttpCode.' HTTP code returned when trying to fetch data.');
			}
			return false;
		}
	}
	private function _parse_returned_value($sReturned)						{ 
		if(!$this->bReturnAsIs) {
			switch($this->_sType) {
				case 'xml':
				case 'atom':
				case 'rss':
					$aReturn = json_decode(json_encode(new SimpleXMLElement($sReturned)),true);
					unset($aReturn['@attributes']);
					if(count($aReturn) == 1) {
						foreach($aReturn as $sKey=>$aValue)	{
							unset($aReturn[$sKey]);
							$aReturn = $aValue;	
						}
					}
				break;
				
				case 'json':
					$aReturn = json_decode($sReturned,true);
				break;
			}
			return $aReturn;
		}
		return $sReturned;
	}
	
	private function _parse_tweet($sMessage)			 					{ 
		return $sMessage;
	}
	
 	private function _build_url_parameters($aParameters,$bPost = false,$bArray = false)	{ 
 		if(is_array($aParameters)) {
 			$aParameters = array_filter($aParameters);
 			if(count($aParameters) > 0) {
 				if(!$bArray) {
 					$sReturn = '';
 					foreach($aParameters as $sKey=>$sValue) {
			 			if(!empty($sReturn)) {
 							$sReturn .= '&';
 						} 
 						$sValue = ($sValue === true ? 'true' : $sValue);
 						$sReturn .= urlencode($sKey).'='.(!$bPost ? urlencode($sValue) : $sValue);
 					}
	 		
 					$sReturn = (!$bPost) ? '?'.$sReturn : $sReturn ;
 					return $sReturn;
 				}
 				foreach($aParameters as $sKey=>$sValue) {
 					$aParameters[$sKey] = ($sValue === true ? 'true' : $sValue);
 				}
 				return $aParameters;
 			}
 		}
 		return false;
 	} 
 	
 	public  function __destruct()										{ 
 		unset($this);
 	}
	
	
 }
 
 // END Twitter class

/* End of file Twitter.php */