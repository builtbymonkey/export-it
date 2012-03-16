<?php
class Json_ordering
{
	public $dbprefix = FALSE;
	
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->settings = $this->EE->export_it_lib->get_settings();	
		$this->EE->load->helper('text');
		$this->dbprefix = $this->EE->db->dbprefix;
	}
	
	public function member_ordering($perpage, $url_base)
	{
		$col_map = array('member_id', 'username', 'screen_name', 'email', 'join_date', 'group_title');
		$id = ($this->EE->input->get_post('id')) ? $this->EE->input->get_post('id') : '';
		$keywords = ($this->EE->input->get_post('order_keywords')) ? $this->EE->input->get_post('order_keywords') : FALSE;
		$group_id = ($this->EE->input->get_post('group_id') && $this->EE->input->get_post('group_id') != '') ? $this->EE->input->get_post('group_id') : FALSE;
		$date_range = ($this->EE->input->get_post('date_range') && $this->EE->input->get_post('date_range') != '') ? $this->EE->input->get_post('date_range') : FALSE;
		$keyword = ($this->EE->input->get_post('k_search') && $this->EE->input->get_post('k_search') != '') ? $this->EE->input->get_post('k_search') : FALSE;
			
		$perpage = ($this->EE->input->get_post('perpage')) ? $this->EE->input->get_post('perpage') : $this->settings['members_list_limit'];
		$offset = ($this->EE->input->get_post('iDisplayStart')) ? $this->EE->input->get_post('iDisplayStart') : 0; // Display start point
		$sEcho = $this->EE->input->get_post('sEcho');

		$order = array();
		
		if ($this->EE->input->get('iSortCol_0') !== FALSE)
		{
			for ( $i=0; $i < $this->EE->input->get('iSortingCols'); $i++ )
			{
				if (isset($col_map[$this->EE->input->get('iSortCol_'.$i)]))
				{
					$order[$col_map[$this->EE->input->get('iSortCol_'.$i)]] = ($this->EE->input->get('sSortDir_'.$i) == 'asc') ? 'asc' : 'desc';
				}
			}
		}

		$tdata = array();
		$i = 0;
		
		if (count($order) == 0)
		{
			$order = $this->dbprefix."members.member_id DESC";
		}
		else
		{
			$sort = '';
			foreach($order AS $key => $value)
			{
				$sort = $key.' '.$value;
			}
			$order = $sort;
		}

		$where = $this->build_member_filter_where($keyword, $date_range, $group_id);
		
		$total = $this->EE->member_data->get_total_members();
		$j_response['sEcho'] = $sEcho;
		$j_response['iTotalRecords'] = $total;
		$j_response['iTotalDisplayRecords'] = $this->EE->member_data->get_total_members($where);

		$data = $this->EE->member_data->get_members($where, FALSE, FALSE, $perpage, $offset, $order);
		foreach ($data as $item)
		{			
			$m[] = '<a href="?D=cp&C=myaccount&id='.$item['member_id'].'">'.$item['member_id'].'</a>';
			
			$m[] = $item['username'];
			$m[] = $item['screen_name'];
			$m[] = '<a href="mailto:'.$item['email'].'">'.$item['email'].'</a>';
			$m[] = m62_convert_timestamp($item['join_date']);
			$m[] = $item['group_title'];
			$tdata[$i] = $m;
			$i++;
			unset($m);
		}		

		$j_response['aaData'] = $tdata;	
		return $this->EE->javascript->generate_json($j_response, TRUE);	
	}

	public function build_member_filter_where($keyword = FALSE, $date_range = FALSE, $group_id = FALSE)
	{
		$where = $this->dbprefix."members.member_id > 0 ";
		if($group_id)
		{
			$where .= " AND ".$this->dbprefix."members.group_id = '".$group_id."'";
		}
		
		if($date_range && $date_range != 'custom_date')
		{
			if(is_numeric($date_range))
			{
				$where .= " AND join_date > ".(mktime()-($date_range*24*60*60));
			}
			else
			{
				$parts = explode('to', $date_range);
				if(count($parts) == '2')
				{
					$start = strtotime($parts['0']);
					$end = strtotime($parts['1']);
					$where .= " AND join_date BETWEEN $start AND $end";
				}
			}
		}
		
		if($keyword)
		{
			$cols = array();
			$fields = $this->EE->member_model->get_custom_member_fields()->result_array();
			foreach($fields AS $field)
			{
				$cols[] = "m_field_id_".$field['m_field_id']." LIKE '%$keyword%'";
			}
			
			$more = array('email', 'username','screen_name');
			foreach($more AS $field)
			{
				$cols[] = $field." LIKE '%$keyword%'";
			}
			
			if(count($cols) >= 1)
			{
				$where .= " AND (".implode(' OR ', $cols).") ";
			}
		}
				
		return $where;
	}	
}