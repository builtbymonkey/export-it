<?php $this->load->view('errors'); ?>
<?php 

$tmpl = array (
	'table_open'          => '<table class="mainTable" border="0" cellspacing="0" cellpadding="0">',

	'row_start'           => '<tr class="even">',
	'row_end'             => '</tr>',
	'cell_start'          => '<td style="width:50%;">',
	'cell_end'            => '</td>',

	'row_alt_start'       => '<tr class="odd">',
	'row_alt_end'         => '</tr>',
	'cell_alt_start'      => '<td>',
	'cell_alt_end'        => '</td>',

	'table_close'         => '</table>'
);

$this->table->set_template($tmpl); 
$this->table->set_empty("&nbsp;");
?>
<div class="clear_left shun"></div>

<?php echo form_open($query_base.'settings', array('id'=>'my_accordion'))?>
<input type="hidden" value="yes" name="go_settings" />
<div>
	<?php 
	if (version_compare(APP_VER, '2.2', '<') || version_compare(APP_VER, '2.2', '>'))
	{
		$this->table->set_heading(lang('settings'),' ');
	}
	
	$this->table->add_row('<label for="enable_api">'.lang('enable_api').'</label><div class="subtext">'.lang('enable_api_instructions').'</div>', form_checkbox('enable_api', '1', $settings['enable_api'], 'id="enable_api"'));
	$this->table->add_row('<label for="api_key">'.lang('api_key').'</label><div class="subtext">'.lang('api_key_instructions').'</div>', form_input('api_key', $settings['api_key'], 'id="api_key"'));
	$this->table->add_row('<label for="api_url">'.lang('api_url').'</label><div class="subtext">'.lang('api_url_instructions').'</div>', $api_url);
	$this->table->add_row('<label for="license_number">'.lang('license_number').'</label>', form_input('license_number', $settings['license_number'], 'id="license_number"'));	
	
	echo $this->table->generate();
	$this->table->clear();	
	?>
</div>

<br />
<div class="tableFooter">
	<div class="tableSubmit">
		<?php echo form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'));?>
	</div>
</div>	
<?php echo form_close()?>