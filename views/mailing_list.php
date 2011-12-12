<?php $this->load->view('errors'); ?>
<?php echo form_open($query_base.'mailing_list', array('id' => 'mailing_list')); ?>
	<input type="hidden" name="export_mailing_list" value="yes" />
	<div>
		<fieldset>
			<legend><?=lang('export')?></legend>
			<p>
				<?=form_label(lang('mailing_list'), 'list_id')?>&nbsp;
				<?=form_dropdown('list_id', $mailing_lists, FALSE, 'id="list_id"')?> 
	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<?=form_label(lang('export_format'), 'export_format')?>&nbsp;
				<?=form_dropdown('export_format', $export_format, '', 'id="export_format"')?> 
	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				<?=form_label(lang('exclude_duplicates'), 'exclude_duplicates')?>&nbsp;
				<?=form_checkbox('exclude_duplicates', '1', '1', 'id="exclude_duplicates"')?> 
	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			
				
								


				<?=form_submit('submit', lang('export'), 'id="export" class="submit"')?>
			</p>
		</fieldset>
	</div>
<?=form_close()?>