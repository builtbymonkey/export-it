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
 * Export It - Module Class
 *
 * The external module class used for Actions (ACT) and template tags
 *
 * @package 	mithra62:Export_it
 * @author		Eric Lamb
 * @filesource 	./system/expressionengine/third_party/export_it/mod.export_it.php
 */
class Export_it {

	public $return_data	= '';
	
	public function __construct()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		$this->EE->load->model('export_it_settings_model', 'export_it_settings');
		$this->EE->load->library('export_it_lib');
		$this->EE->load->library('Api_server/api_server');
		$this->settings = $this->EE->export_it_lib->get_settings();
		$this->EE->lang->loadfile('export_it');	
	}
	
	public function api()
	{
		$this->EE->api_server->run();
		exit;
	} 	
}