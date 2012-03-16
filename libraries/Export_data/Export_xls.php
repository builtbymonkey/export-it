<?php
class Export_xls
{
	/**
	 * The keys used for the columsn.
	 * Since some data won't have matching keys we have to keep track of things
	 * @var array
	 */
	public $keys = array();
	
	public function __construct()
	{
	
	}
	
	public function create(array $arr, $keys_as_headers = TRUE, $file_name = 'download.txt')
	{
		//we have to clean the array so it's one dimensional
		$arr = $this->make_non_nested($arr);
		if(is_array($arr) && count($arr) >= 1)
		{
			$rows = array();
			$cols = array_keys($arr['0']);
			foreach($arr AS $key => $value)
			{
				foreach($value AS $k => $v)
				{
					foreach($this->keys AS $master)
					{
						if($k == $master)
						{
							$value[$k] = $this->escape_csv_value($v, "\t");
							break;
						}
						else
						{
							$value[$k] = '';
						}
					}
				}
				
				$rows[] = implode("\t", $value);
			}

			$data = implode("\t", $this->keys)."\n";
			$data .= implode("\n", $rows);
			
			return $data;
		}
	}	

	public function make_non_nested_recursive(array &$out, $key, array $in)
	{		
		foreach($in as $k=>$v)
		{
			if(is_array($v))
			{
				$this->make_non_nested_recursive($out, $key . $k . '_', $v);
			}
			else
			{
				$new_key = $key . $k;
				$out[$new_key] = $v;
				if(!in_array($new_key, $this->keys))
				{
					$this->keys[] = $new_key;
				}				
			}
		}
	}
	
	public function make_non_nested(array $in)
	{
		$out = array();
		$count = count($in);
		$return = array();
		for($i=0;$i<$count;$i++)
		{
			$this->make_non_nested_recursive($out, '', $in[$i]);
			$return[$i] = $out;
			unset($in[$i]);
		}
	
		return $return;
	}

	public function escape_csv_value($value, $delim = ',')
	{
		$value = str_replace('"', '""', $value);
		if(preg_match('/'.$delim.'/', $value) or preg_match("/\n/", $value) or preg_match('/"/', $value))
		{
			return '"'.$value.'"';
		}
		else
		{
			return $value;
		}
	}	
}