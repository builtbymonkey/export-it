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
 * Export It - General Library Class
 *
 * Contains all the generic methods for Export It
 *
 * @package 	mithra62:Export_it
 * @author		Eric Lamb
 * @filesource 	./system/expressionengine/third_party/export_it/libraries/Export_it_lib.php
 */
class Export_it_lib
{
	/**
	 * Preceeds URLs 
	 * @var mixed
	 */
	private $url_base = FALSE;
	
	/**
	 * The full path to the log file for the progress bar
	 * @var string
	 */
	public $progress_log_file;	
	
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->settings = $this->get_settings();
	}
	
	public function get_settings()
	{
		if (!isset($this->EE->session->cache['export_it']['settings'])) 
		{	
			$this->EE->session->cache['export_it']['settings'] = $this->EE->export_it_settings->get_settings();
		}
		
		return $this->EE->session->cache['export_it']['settings'];
	}
	
	/**
	 * Sets up the right menu options
	 * @return multitype:string
	 */
	public function get_right_menu()
	{
		return array(
				'members'		=> $this->url_base.'members',
				'channel_entries'	=> $this->url_base.'channel_entries',
				'comments'	=> $this->url_base.'comments',
				'mailing_list'	=> $this->url_base.'mailing_list',
				'settings'	=> $this->url_base.'settings'
		);
	}

	/**
	 * Wrapper that runs all the tests to ensure system stability
	 * @return array;
	 */
	public function error_check()
	{
		$errors = array();
		if($this->settings['license_number'] == '')
		{
			$errors['license_number'] = 'missing_license_number';
		}
		return $errors;
	}
	
	public function export_formats($type = 'channel_entries')
	{
		switch($type)
		{
			case 'members':
				return array('xls' => 'Excel', 'xml' => 'XML', 'json' => 'JSON', 'ee_xml' => 'EE Member XML');
			break; 
			
			case 'mailing_list':
				return array('xls' => 'Excel', 'xml' => 'XML', 'json' => 'JSON');
			break;
			
			case 'comments':
				return array('disqus' => 'Disqus', 'xml' => 'XML', 'json' => 'JSON');
			break;	

			case 'channel_entries':
				return array('xml' => 'XML', 'json' => 'JSON');
			break;
		}
	}
	
	public function get_mailing_lists()
	{
		$lists = $this->EE->mailinglist_model->get_mailinglists();
		if($lists->num_rows == '0')
		{
			return array();
		}
		else
		{
			$arr = array(null => 'All');
			$lists = $lists->result_array();
			foreach($lists AS $list)
			{
				$arr[$list['list_id']] = $list['list_title'];
			}
		}
		return $arr;
	}
	
	public function get_comment_channels()
	{
		if (!$this->EE->cp->allowed_group('can_moderate_comments') && !$this->EE->cp->allowed_group('can_edit_all_comments'))
		{
			$query = $this->EE->channel_model->get_channels(
									(int) $this->EE->config->item('site_id'), 
									array('channel_title', 'channel_id', 'cat_group'));
		}
		else
		{
			$this->EE->db->select('channel_title, channel_id, cat_group');
			$this->EE->db->where('site_id', (int) $this->EE->config->item('site_id'));
			$this->EE->db->order_by('channel_title');
		
			$query = $this->EE->db->get('channels'); 
		}
		
		if ( ! $query)
		{
			return array();
		}

		foreach ($query->result() as $row)
		{
			$opts[$row->channel_id] = $row->channel_title;
		}

		return $opts;		
	}
	
	 public function get_date_select()
	 {
	 	$data = array(
	 		'' => lang('all'),
	 		1 => lang('past_day'),
	 		7 => lang('past_week'),
	 		31 => lang('past_month'),
	 		182 => lang('past_six_months'),
	 		365 => lang('past_year')
		);
		
		return $data;
	 }

	public function get_status_select()
	{
		$data = array(
			'' => lang('all'),
			'p' => lang('pending'),
			'o' => lang('open'),
			'c' => lang('closed')
		);
		
		return $data;
	}	
	
	/**
	 * Wrapper to handle CP URL creation
	 * @param string $method
	 */
	public function _create_url($method)
	{
		return $this->url_base.$method;
	}

	/**
	 * Creates the value for $url_base
	 * @param string $url_base
	 */
	public function set_url_base($url_base)
	{
		$this->url_base = $url_base;
	}
	
	public function export_data($type, $type)
	{
		
	}
}