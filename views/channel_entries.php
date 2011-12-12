<?php $this->load->view('errors'); ?>
<?=form_open($query_base.'channel_entries', array('id' => 'channel_entries')); ?>
	<input type="hidden" name="export_channel_entries" value="yes" />
	<div>
		<fieldset>
			<legend><?=lang('export')?></legend>
			<p>
				<?=form_label(lang('channel'), 'channel_id')?>&nbsp;
				<?=form_dropdown('channel_id', $comment_channels, FALSE, 'id="channel_id"')?> 
	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				<?=form_label(lang('date_range'), 'date_range')?>&nbsp;
				<?=form_dropdown('date_range', $date_select, FALSE, 'id="date_range"')?> 
	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;												

				<?=form_label(lang('export_format'), 'export_format')?>&nbsp;
				<?=form_dropdown('export_format', $export_format, FALSE, 'id="export_format"')?> 
	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;		

				<?=form_submit('submit', lang('export'), 'id="export" class="submit"')?>
			</p>
		</fieldset>
	</div>
<?=form_close()?>