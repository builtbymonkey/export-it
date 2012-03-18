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
 * Export It - CP Class
 *
 * Control Panel class
 *
 * @package 	mithra62:Export_it
 * @author		Eric Lamb
 * @filesource 	./system/expressionengine/third_party/export_it/mcp.export_it.php
 */
class Export_it_mcp 
{
	public $url_base = '';
	
	public $perpage = '20';
	
	/**
	 * The name of the module; used for links and whatnots
	 * @var string
	 */
	private $mod_name = 'export_it';
	
	public function __construct()
	{
		$this->EE =& get_instance();
		
		//load EE stuff
		$this->EE->load->library('javascript');
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		$this->EE->load->model('export_it_settings_model', 'export_it_settings');
		$this->EE->load->library('export_it_lib');
		$this->EE->load->library('export_it_js');
		$this->EE->load->library('member_data');
		$this->EE->load->library('channel_data');
		$this->EE->load->library('mailinglist_data');
		$this->EE->load->library('comment_data');
		$this->EE->load->library('encrypt');
		$this->EE->load->library('json_ordering');
		$this->EE->load->library('Export_data/export_data');
		
		$this->EE->load->add_package_path(PATH_MOD.'mailinglist/'); 
		$this->EE->load->model('mailinglist_model');		

		$this->settings = $this->EE->export_it_lib->get_settings();		

		$this->query_base = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->mod_name.AMP.'method=';
		$this->url_base = BASE.AMP.$this->query_base;
		$this->EE->export_it_lib->set_url_base($this->url_base);
		
		$this->EE->cp->set_variable('url_base', $this->url_base);
		$this->EE->cp->set_variable('query_base', $this->query_base);	
		
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->mod_name, $this->EE->lang->line('export_it_module_name'));
		$this->EE->cp->set_right_nav($this->EE->export_it_lib->get_right_menu());	
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('export_it_module_name'));
		
		$this->errors = $this->EE->export_it_lib->error_check();
		$this->EE->cp->set_variable('errors', $this->errors);
		$this->EE->cp->set_variable('settings', $this->settings);
		
	}
	
	public function index()
	{
		$this->EE->functions->redirect($this->url_base.'members');
		exit;
	}
	
	public function members()
	{	
		$total = $this->EE->member_data->get_total_members();
		$vars['members'] = $this->EE->member_data->get_members(FALSE, FALSE, TRUE, $this->settings['members_list_limit'], '0', 'members.member_id ASC');		

		$this->EE->cp->add_js_script(array('plugin' => 'dataTables','ui' => 'datepicker'));
		$dt = $this->EE->export_it_js->get_members_datatables('export_members_ajax_filter', 6, 1, $this->settings['members_list_limit']);
		$this->EE->javascript->output($dt);
		$this->EE->javascript->compile();
		$this->EE->load->library('pagination');


		$vars['pagination'] = $this->EE->export_it_lib->create_pagination('export_members_ajax_filter', $total, $this->settings['members_list_limit']);
				
		$vars['total_members'] = $total;
		$vars['date_selected'] = '';
		$vars['member_keywords'] = '';
		
		$first_date = $this->EE->member_data->get_first_date();
		if($first_date)
		{
			$vars['default_start_date'] = m62_convert_timestamp($first_date, '%Y-%m-%d');
		}
		else
		{
			$vars['default_start_date'] = m62_convert_timestamp(mktime(), '%Y-%m-%d');
		}		

		$vars['perpage_select_options'] = $this->EE->export_it_lib->perpage_select_options();
		$vars['date_select_options'] = $this->EE->export_it_lib->date_select_options();		
				
		$vars['member_groups_dropdown'] = $this->EE->member_data->get_member_groups();
		$vars['export_format'] = $this->EE->export_it_lib->export_formats('members');
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('members'));
		return $this->EE->load->view('members', $vars, TRUE);		
	}
	
	public function export_members_ajax_filter()
	{
		die($this->EE->json_ordering->member_ordering($this->perpage, $this->url_base));
	}		
	
	public function channel_entries()
	{	
		$this->EE->channel_data->translate_cft = FALSE; //disable checking custom field data
		$total = $this->EE->channel_data->get_total_entries();
		$vars['entries'] = $this->EE->channel_data->get_entries(FALSE, $this->settings['channel_entries_list_limit']);
		
		$this->EE->cp->add_js_script(array('plugin' => 'dataTables','ui' => 'datepicker'));
		$dt = $this->EE->export_it_js->get_channel_entries_datatables('export_channel_entries_ajax_filter', 3, 1, $this->settings['channel_entries_list_limit'],'"aaSorting": [[ 3, "desc" ]],');
		$this->EE->javascript->output($dt);		
		$this->EE->javascript->compile();
		$this->EE->load->library('pagination');
		
		$vars['pagination'] = $this->EE->export_it_lib->create_pagination('export_channel_entries_ajax_filter', $total, $this->settings['channel_entries_list_limit']);
		$vars['total_entries'] = $total;
		
		$vars['date_selected'] = '';
		$vars['keywords'] = '';
		$vars['perpage_select_options'] = $this->EE->export_it_lib->perpage_select_options();
		
		$first_date = $this->EE->channel_data->get_first_date();
		if($first_date)
		{
			$vars['default_start_date'] = m62_convert_timestamp($first_date, '%Y-%m-%d');
		}
		else
		{
			$vars['default_start_date'] = m62_convert_timestamp(mktime(), '%Y-%m-%d');
		}
		
		$vars['date_select_options'] = $this->EE->export_it_lib->date_select_options();
		$vars['date_select'] = $this->EE->export_it_lib->get_date_select();
		$vars['status_select'] = $this->EE->export_it_lib->get_status_select();		
		
		$vars['export_format'] = $this->EE->export_it_lib->export_formats('channel_entries');
		$vars['channel_options'] = $this->EE->export_it_lib->get_comment_channels();
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('channel_entries'));
		return $this->EE->load->view('channel_entries', $vars, TRUE);			
	}
	
	public function export_channel_entries_ajax_filter()
	{
		die($this->EE->json_ordering->channel_entries_ordering($this->perpage, $this->url_base));
	}	
	
	public function comments()
	{
		
		$total = $this->EE->comment_data->get_total_comments();
		$vars['comments'] = $this->EE->comment_data->get_comments(FALSE, $this->settings['comments_list_limit']);
		
		$this->EE->cp->add_js_script(array('plugin' => 'dataTables','ui' => 'datepicker'));
		$dt = $this->EE->export_it_js->get_comments_datatables('export_comments_ajax_filter', 3, 1, $this->settings['comments_list_limit']);
		$this->EE->javascript->output($dt);
		$this->EE->javascript->compile();
		$this->EE->load->library('pagination');
		
		$vars['pagination'] = $this->EE->export_it_lib->create_pagination('export_comments_ajax_filter', $total, $this->settings['comments_list_limit']);
		$vars['total_comments'] = $total;
		
		$vars['date_selected'] = '';
		$vars['keywords'] = '';
		$vars['perpage_select_options'] = $this->EE->export_it_lib->perpage_select_options();
		$vars['export_format'] = $this->EE->export_it_lib->export_formats('mailing_list');
		$vars['mailing_lists'] = $this->EE->mailinglist_data->get_mailing_lists();
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('mailing_list'));		
		
		$first_date = $this->EE->comment_data->get_first_date();
		if($first_date)
		{
			$vars['default_start_date'] = m62_convert_timestamp($first_date, '%Y-%m-%d');
		}
		else
		{
			$vars['default_start_date'] = m62_convert_timestamp(mktime(), '%Y-%m-%d');
		}

		$vars['date_select_options'] = $this->EE->export_it_lib->date_select_options();
		$vars['export_format'] = $this->EE->export_it_lib->export_formats('comments');
		$vars['comment_channels'] = $this->EE->export_it_lib->get_comment_channels();
		$vars['date_select'] = $this->EE->export_it_lib->get_date_select();
		$vars['status_select'] = $this->EE->export_it_lib->get_status_select();
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('comments'));
		return $this->EE->load->view('comments', $vars, TRUE);		
	}
	
	public function export_comments_ajax_filter()
	{
		die($this->EE->json_ordering->comments_ordering($this->perpage, $this->url_base));
	}	
	
	public function mailing_list()
	{		
		
		$total = $this->EE->mailinglist_data->get_total_emails();
		$vars['emails'] = $this->EE->mailinglist_data->get_list_emails(FALSE, $this->settings['mailing_list_limit']);
		
		$this->EE->cp->add_js_script(array('plugin' => 'dataTables'));
		$dt = $this->EE->export_it_js->get_mailing_list_datatables('export_mailing_list_ajax_filter', 3, 1, $this->settings['mailing_list_limit']);
		$this->EE->javascript->output($dt);
		$this->EE->javascript->compile();
		$this->EE->load->library('pagination');
		
		$vars['pagination'] = $this->EE->export_it_lib->create_pagination('export_mailing_list_ajax_filter', $total, $this->settings['mailing_list_limit']);
		$vars['total_emails'] = $total;
		$vars['date_selected'] = '';
		$vars['keywords'] = '';
		$vars['perpage_select_options'] = $this->EE->export_it_lib->perpage_select_options();
		$vars['export_format'] = $this->EE->export_it_lib->export_formats('mailing_list');
		$vars['mailing_lists'] = $this->EE->mailinglist_data->get_mailing_lists();
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('mailing_list'));
		return $this->EE->load->view('mailing_list', $vars, TRUE);		
	}
	
	public function export_mailing_list_ajax_filter()
	{
		die($this->EE->json_ordering->mailing_list_ordering($this->perpage, $this->url_base));
	}	

	public function settings()
	{
		if(isset($_POST['go_settings']))
		{		
			if($this->EE->export_it_settings->update_settings($_POST))
			{	
				$this->EE->logger->log_action($this->EE->lang->line('log_settings_updated'));
				$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('settings_updated'));
				$this->EE->functions->redirect($this->url_base.'settings');		
				exit;			
			}
			else
			{
				$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('settings_update_fail'));
				$this->EE->functions->redirect($this->url_base.'settings');	
				exit;					
			}
		}
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('settings'));
		
		$this->EE->cp->add_js_script('ui', 'accordion'); 
		$this->EE->javascript->output($this->EE->export_it_js->get_accordian_css()); 		
		$this->EE->javascript->compile();	

		$this->settings['api_key'] = $this->EE->encrypt->decode($this->settings['api_key']);
		$vars = array();
		$vars['api_url'] = $this->EE->config->config['site_url'].'?ACT='.$this->EE->cp->fetch_action_id('Export_it', 'api').'&key='.$this->settings['api_key'];
		$vars['settings'] = $this->settings;
		return $this->EE->load->view('settings', $vars, TRUE);
	}
	
	public function export()
	{
		$type = $this->EE->input->get_post('type');
		$export_format = $this->EE->input->get_post('format');
		switch($type)
		{
			case 'mailinglist':
				$format = $this->EE->input->get_post('export_format');
				$exclude_duplicates = $this->EE->input->get_post('exclude_duplicates');
				$keywords = ($this->EE->input->get_post('keywords')) ? $this->EE->input->get_post('keywords') : FALSE;
				$list_id = ($this->EE->input->get_post('list_id') && $this->EE->input->get_post('list_id') != '') ? $this->EE->input->get_post('list_id') : FALSE;
				$where = array();
				if($list_id)
				{
					$where['mailing_list.list_id'] = $list_id;
				}
	
				if($keywords)
				{
					$where['search'] = $keywords;
				}
	
				$data = $this->EE->mailinglist_data->get_list_emails($where);
				$this->EE->export_data->export_mailing_list($data, $format);
				break;
					
			case 'comments':
				$format = $this->EE->input->get_post('format');
				$date_range = $this->EE->input->get_post('date_range');
				$status = $this->EE->input->get_post('status');
				$channel_id = $this->EE->input->get_post('channel_id');
				$keywords = $this->EE->input->get_post('keywords');
				$where = array();
				if($channel_id)
				{
					$where['comments.channel_id'] = $channel_id;
				}
					
				if($keywords)
				{
					$where['search'] = $keywords;
				}
	
				if($status)
				{
					$where['comments.status'] = $status;
				}
	
				if($date_range)
				{
					$where['date_range'] = $date_range;
				}
	
				$data = $this->EE->comment_data->get_comments($where);
				$this->EE->export_data->export_comments($data, $format);
				break;
					
			case 'channel_entries':
	
				$keywords = ($this->EE->input->get_post('k_search')) ? $this->EE->input->get_post('k_search') : FALSE;
				$channel_id = ($this->EE->input->get_post('channel_id') && $this->EE->input->get_post('channel_id') != '') ? $this->EE->input->get_post('channel_id') : FALSE;
				$status = ($this->EE->input->get_post('status') && $this->EE->input->get_post('status') != '') ? $this->EE->input->get_post('status') : FALSE;
				$date_range = ($this->EE->input->get_post('date_range') && $this->EE->input->get_post('date_range') != '') ? $this->EE->input->get_post('date_range') : FALSE;
	
				$where = array();
				if($channel_id)
				{
					$where['ct.channel_id'] = $channel_id;
				}
	
				if($keywords)
				{
					$where['search'] = $keywords;
				}
	
				if($status)
				{
					$where['status'] = $status;
				}
	
				if($date_range)
				{
					$where['date_range'] = $date_range;
				}
				
				$data = $this->EE->channel_data->get_entries($where);
				$this->EE->export_data->export_channel_entries($data, $export_format);
	
				break;
					
			case 'members':
			default:
				$export_format = $this->EE->input->get_post('format');
				$group_id = $this->EE->input->get_post('group_id');
				$include_custom_fields = $this->EE->input->get_post('include_custom_fields');
				$complete_select = $this->EE->input->get_post('complete_select');
	
				$group_id = ($this->EE->input->get_post('group_id') && $this->EE->input->get_post('group_id') != '') ? $this->EE->input->get_post('group_id') : FALSE;
				$date_range = ($this->EE->input->get_post('date_range') && $this->EE->input->get_post('date_range') != '') ? $this->EE->input->get_post('date_range') : FALSE;
				$keyword = ($this->EE->input->get_post('member_keywords') && $this->EE->input->get_post('member_keywords') != '') ? $this->EE->input->get_post('member_keywords') : FALSE;
	
				$where = $this->EE->json_ordering->build_member_filter_where($keyword, $date_range, $group_id);
				$data = $this->EE->member_data->get_members($where, $include_custom_fields, $complete_select, FALSE, 0, FALSE);
	
				$this->EE->export_data->export_members($data, $export_format);
				break;
		}
	
		exit;
	}	
}