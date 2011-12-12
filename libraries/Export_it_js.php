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
 * Export It - JavaScript Library Class
 *
 * Contains all the JavaScript
 *
 * @package 	mithra62:Export_it
 * @author		Eric Lamb
 * @filesource 	./system/expressionengine/third_party/export_it/libraries/Export_it_js.php
 */
class Export_it_js
{
	
	public function __construct()
	{
		$this->EE =& get_instance();
	}
	
	public function get_accordian_css()
	{
		if (version_compare(APP_VER, '2.2', '<') || version_compare(APP_VER, '2.2', '>'))
		{
			return ' $("#my_accordion").accordion({autoHeight: false,header: "h3"}); ';
		}
		else
		{
			return '';
		}
	}
	
	public function get_security_scan_progressbar($progress)
	{
		$progress = (int)$progress;
		return "$('#progressbar').progressbar('option', 'value', ".$progress.");";
	}	
	
	public function get_scan_progressbar($proc_url, $url_base)
	{
		$js = "
			var kill_progress = false;
			
			$.ajax({
				url: '".html_entity_decode($proc_url)."',
				cache: false,
				dataType: 'json',
				success: function(data) {
					$('#progressbar').progressbar('option', 'value', 100);
					kill_progress = true;
					$.ajax({
						url: '".html_entity_decode($url_base)."progress',
						cache: false,
						dataType: 'json',
						success: function(data) {
							$('#active_item').html('');
							$('#total_items').html(data['total_items']);	
							$('#active_item').html(data['msg']);
							$('#item_number').html(data['item_number']);
							$('div.heading h2.edit').html('".lang('backup_progress_bar_stop')."');
							document.title = '".lang('backup_progress_bar_stop')."';
							$('#breadCrumb li:last').html('".lang('backup_progress_bar_stop')."');
							$('#backup_instructions').hide();
						}
					});			
				}
			});
				
			function updateProgress() {
				var progress;
				progress = $('#progressbar').progressbar('option','value');
				if (progress < 100 && !kill_progress) {
		
					$.ajax({
						url: '".html_entity_decode($url_base)."progress',
						cache: false,
						dataType: 'json',
						success: function(data) {
							progress = Math.floor(data['item_number']/data['total_items']*100);
							$('#progressbar').progressbar('option', 'value', progress);
							$('#total_items').html(data['total_items']);	
							$('#active_item').html(data['msg']);
							$('#item_number').html(data['item_number']);
							
						}
					});
					setTimeout(updateProgress, 2000);
				}
				else
				{
		
				}
			}	  
			setTimeout(updateProgress, 2000);		
		";
		return $js;
	}
	
	public function get_check_toggle()
	{
		return array(
						'$(".toggle_all_cron").toggle(
							function(){
								$("input.toggle_cron").each(function() {
									this.checked = true;
								});
							}, function (){
								var checked_status = this.checked;
								$("input.toggle_cron").each(function() {
									this.checked = false;
								});
							}
						);'		
					);		
	}
}