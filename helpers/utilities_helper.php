<?php
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