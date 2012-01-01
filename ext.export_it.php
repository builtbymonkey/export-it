<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
 * Export It - Extension Class
 *
 * Extension class
 *
 * @package 	mithra62:Export_it
 * @author		Eric Lamb
 * @filesource 	./system/expressionengine/third_party/export_it/ext.export_it.php
 */
class Export_it_ext 
{

	public $settings = array();
	
	public $name = 'Export It';
	
	public $version = '1.0.6';
	
	public $description	= 'Extension for modifying how exporting works';
	
	public $settings_exist	= 'y';
	
	public $docs_url = ''; 
	
	public $required_by = array('module');	
		
	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->EE->load->library('email');		
	}
	
	public function settings_form()
	{
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=export_it'.AMP.'method=settings');
	}
	
	public function activate_extension() 
	{
		return TRUE;

	}
	
	public function update_extension($current = '')
	{
		return TRUE;
	}

	public function disable_extension()
	{
		return TRUE;

	}
}