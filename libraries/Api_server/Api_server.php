<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * mithra62 - Export It
 *
 * @package		mithra62:Export_it
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2011, mithra62, Eric Lamb.
 * @link		http://mithra62.com/projects/view/export-it/
 * @version		1.0
 * @filesource 	./system/expressionengine/third_party/export_it/
 */
 
 /**
 * Export It - Api Server Library
 *
 * Contains all the method/wrappers for the Api 
 *
 * @package 	mithra62:Export_it
 * @author		Eric Lamb
 * @filesource 	./system/expressionengine/third_party/export_it/libraries/Api_server/Api_server.php
 */
class Api_server
{
	public $channel_id = FALSE;
	public $entry_id = FALSE;
	public $member_id = FALSE;
	public $list_id = FALSE;
	public $comment_id = FALSE;
	public $format = 'json';
	public $settings;
	public $method = FALSE;
	
	private $extension_ran = FALSE;
	
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->EE->lang->loadfile('export_it');
		$this->EE->load->library('javascript');
		$this->EE->load->library('member_data');
		$this->EE->load->library('channel_data');
		$this->EE->load->library('Export_data/export_data');
		$this->EE->load->add_package_path(PATH_MOD.'mailinglist/'); 
		$this->EE->load->model('mailinglist_model');
		$this->EE->load->library('encrypt');
		$this->EE->load->library('Api_server/Api_auth');		
		$this->settings = $this->EE->export_it_lib->get_settings();
		$this->_setup_api();
		$this->EE->export_data->disable_download = TRUE;			
	}
	
	public function run()
	{
		$method = $this->method;
		
		if(!$this->extension_ran)
		{
			$this->$method();
		}
		
		if ($this->EE->extensions->active_hook('export_it_api_end') === TRUE)
		{
			$this->EE->extensions->call('export_it_api_end', $this);
			if ($this->EE->extensions->end_script === TRUE) return;
		}		
		exit;
	}
	
	public function get_members()
	{
		$group_id = $this->EE->input->get_post('group_id');
		$include_custom_fields = $this->EE->input->get_post('include_custom_fields');
		$complete_select = $this->EE->input->get_post('complete_select');
		$this->EE->export_data->export_members($this->format, $group_id, $include_custom_fields, $complete_select);
	}
	
	public function get_member()
	{
		$member_id = $this->EE->input->get_post('member_id');
		if(!$member_id || $member_id == '')
		{
			$this->error(lang('member_id_missing'), 500);
			exit;
		}		
		$include_custom_fields = $this->EE->input->get_post('include_custom_fields');
		$complete_select = $this->EE->input->get_post('complete_select');
		$this->EE->export_data->export_member($this->format, $member_id, $include_custom_fields, $complete_select);
	}	
	
	public function get_channel_entries()
	{
		$date_range = $this->EE->input->get_post('date_range');
		$channel_id = $this->EE->input->get_post('channel_id');
		if(!$channel_id || $channel_id == '')
		{
			$this->error(lang('channel_id_missing'), 500);
			exit;
		}
				
		$this->EE->export_data->export_channel_entries($this->format, $channel_id, $date_range);		
	}
	
	public function get_channel_entry()
	{
		$entry_id = $this->EE->input->get_post('entry_id');
		$url_title = $this->EE->input->get_post('url_title');
		$channel_id = $this->EE->input->get_post('channel_id');
		if((!$entry_id || $entry_id == '') && (!$url_title || $url_title == ''))
		{
			$this->error(lang('entry_id_url_title_missing'), 500);
			exit;
		}
				
		$this->EE->export_data->export_channel_entry($this->format, $entry_id, $url_title);		
	}	
	
	public function get_comments()
	{
		$date_range = $this->EE->input->get_post('date_range');
		$status = $this->EE->input->get_post('status');
		$channel_id = $this->EE->input->get_post('channel_id');
		$entry_id = $this->EE->input->get_post('entry_id');
		if((!$channel_id || $channel_id == '') && (!$entry_id || $entry_id == ''))
		{
			$this->error(lang('channel_entry_id_missing'), 500);
			exit;
		}
				
		$this->EE->export_data->export_comments($this->format, $date_range, $status, $channel_id, $entry_id);		
	}
	
	public function get_comment()
	{
		$comment_id = $this->EE->input->get_post('comment_id');
		if((!$comment_id || $comment_id == ''))
		{
			$this->error(lang('comment_id_missing'), 500);
			exit;
		}
				
		$this->EE->export_data->export_comment($this->format, $comment_id);		
	}
	
	public function get_mailing_list()
	{
		$list_id = $this->EE->input->get_post('list_id');
		$exclude_duplicates = $this->EE->input->get_post('exclude_duplicates');
		$this->EE->export_data->export_mailing_list($this->format, $exclude_duplicates, $list_id);			
	}
	
	public function get_category()
	{
		$cat_id = $this->EE->input->get_post('cat_id');
		if((!$cat_id || $cat_id == ''))
		{
			$this->error(lang('cat_id_missing'), 500);
			exit;
		}
		$this->EE->export_data->export_category($this->format, $cat_id);		
	}
	
	public function get_category_posts()
	{
		$cat_id = $this->EE->input->get_post('cat_id');
		if((!$cat_id || $cat_id == ''))
		{
			$this->error(lang('cat_id_missing'), 500);
			exit;
		}
		$this->EE->export_data->export_category_posts($this->format, $cat_id);		
	}
	
	public function get_categories()
	{
		$entry_id = $this->EE->input->get_post('entry_id');
		if((!$entry_id || $entry_id == ''))
		{
			$this->error(lang('entry_id_missing'), 500);
			exit;
		}
		$this->EE->export_data->export_categories($this->format, $entry_id);		
	}
	
	public function check_credentials()
	{
		$username = $this->EE->input->get_post('username');
		$password = $this->EE->input->get_post('password');
		if((!$username || $username == '') || (!$password || $password == ''))
		{
			$this->error(lang('username_password_missing'), 500);
			exit;
		}
		
		$this->EE->load->library('auth');
		$this->EE->lang->loadfile('login');
		$authorized = $this->EE->auth->authenticate_username($username, $password);
		if ( ! $authorized)
		{
			$this->error(lang('username_password_invalid'), 500);
			exit;			
		}

		$this->EE->export_data->export_member($this->format, $authorized->member('member_id'));
	}
	
	public function get_file()
	{
		$this->error(lang('bad_method'), 500);
	}
	
	public function get_files()
	{
		$this->error(lang('bad_method'), 500);
	}
	
	private function _setup_api()
	{
		if($this->settings['enable_api'] != '1')
		{
			$this->error(lang('api_disabled'), 500);
			exit;
		}
				
		$this->format = $this->_val_format($this->EE->input->get_post('format'));
		if(!$this->format || $this->format == '')
		{
			$this->error(lang('missing_format'), 500);
			exit;			
		}

		$this->key = $this->EE->input->get_post('key');
		if(!$this->key || $this->key == '' || $this->key != $this->EE->encrypt->decode($this->settings['api_key']))
		{
			$this->error(lang('bad_key'), 500);
			exit;			
		}
		
		$this->method = $this->EE->input->get_post('method');
		if ($this->EE->extensions->active_hook('export_it_api_start') === TRUE)
		{
			if($this->EE->extensions->call('export_it_api_start', $this))
			{
				if ($this->EE->extensions->end_script === TRUE) return;
			}
		}

		if(!$this->method || $this->method == '' || !method_exists($this, $this->method))
		{
			$this->error(lang('bad_method'), 500);
			exit;				
		}
	}
	
	private function _val_format($format)
	{
		$return = 'json';
		switch($format)
		{
			case 'json':
			case 'xml':
				return $format;
			break;
		}		
	}
	
	public function error($output, $http_code, $format = 'json')
	{
		$return = json_encode(array('status' => $http_code, 'message' => $output));
		header('HTTP/1.1: ' . $http_code);
		header('Status: ' . $http_code);
		header('Content-Length: ' . strlen($return));
		echo $return;
	}
}