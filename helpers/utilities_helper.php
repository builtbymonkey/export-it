<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * mithra62 - Export It
 *
 * @package		mithra62:Export_it
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2011, mithra62, Eric Lamb.
 * @link		http://mithra62.com/projects/view/export-it/
 * @version		1.3.2
 * @filesource 	./system/expressionengine/third_party/export_it/
 */
 
 /**
 * Export It - Helper Functions
 *
 * Helper Functions
 *
 * @package 	mithra62:export_it
 * @author		Eric Lamb
 * @filesource 	./system/expressionengine/third_party/export_it/helpers/utilities_helper.php
 */
if ( ! function_exists('m62_convert_timestamp'))
{
	function m62_convert_timestamp($date, $format = FALSE)
	{
		$EE =& get_instance();
		$EE->load->helper('date');
		if(!$format)
		{
			$format = $EE->export_it_lib->settings['export_it_date_format'];
		}
		
		return mdate($format, $date);		
	}
}