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
 * Export It - Disqus Export library
 *
 * A wrapper to create a disqus XML file
 *
 * @package 	mithra62:Export_it
 * @author		Eric Lamb
 * @filesource 	./system/expressionengine/third_party/export_it/libraries/Export_data/Export_disqus.php
 */
class Export_disqus
{
	public function __construct()
	{
		header('Content-type: text/xml');
		$this->EE =& get_instance();
	}
	
	public function generate(array $arr)
	{
		$return = '<?xml version="1.0" encoding="UTF-8"?>
					<rss version="2.0"
					  xmlns:content="http://purl.org/rss/1.0/modules/content/"
					  xmlns:dsq="http://www.disqus.com/"
					  xmlns:dc="http://purl.org/dc/elements/1.1/"
					  xmlns:wp="http://wordpress.org/export/1.0/">
					  <channel>
					    <item>
    	';
		$i = 0;
		foreach($arr AS $item)
		{
			if($i == '0')
			{
				$return .= '<title>'.$item['entry_title'].'</title>';
				$url = $item['channel_url'].'/'.$item['entry_url_title'];
				if(substr($item['channel_url'], 0, 7) != 'http://' && substr($item['channel_url'], 0, 8) != 'https://')
				{
					$url = $this->EE->config->config['site_url'].$item['channel_url'].$item['entry_url_title'];
				}
				$return .= '<link>'.$url.'</link>';
				$return .= '<content:encoded><![CDATA['.$item['entry_title'].']]></content:encoded>';
				$return .= '<dsq:thread_identifier>'.$item['entry_id'].'</dsq:thread_identifier>';
				$return .= '<wp:post_date_gmt>'.m62_convert_timestamp($item['entry_date'], "%Y-%m-%d %H:%i:%s").'</wp:post_date_gmt>';
				$return .= '<wp:comment_status>open</wp:comment_status>';
			}
			
			$return .= '<wp:comment>';
			$return .= '<wp:comment_id>'.$item['comment_id'].'</wp:comment_id>';
    		$return .= '<wp:comment_author>'.$item['name'].'</wp:comment_author>';
    		$return .= '<wp:comment_author_email>'.$item['email'].'</wp:comment_author_email>';
			$return .= '<wp:comment_author_url>'.$item['url'].'</wp:comment_author_url>';
    		$return .= '<wp:comment_author_IP>'.$item['ip_address'].'</wp:comment_author_IP>';
			$return .= '<wp:comment_date_gmt>'.m62_convert_timestamp($item['comment_date'], "%Y-%m-%d %H:%i:%s").'</wp:comment_date_gmt>';
			$return .= '<wp:comment_content><![CDATA['.$item['comment'].']]></wp:comment_content>';
    		$return .= '<wp:comment_approved>'.($item['status'] == 'o' ? '1' : '0').'</wp:comment_approved>';
			$return .= '<wp:comment_parent>0</wp:comment_parent>';
			$return .= '</wp:comment>';
			$i++;
		}
		
		$return .= '</item>
				  </channel>
				</rss>
		';

		return $return;
	}
}