<?php $this->load->view('errors'); ?>
<?=form_open($query_base.'members', array('id' => 'member_export')); ?>
	<input type="hidden" name="export_members" value="yes" />
	<div>
		<fieldset>
			<legend><?=lang('export')?></legend>
			<p>
				<?=form_label(lang('member_group'), 'group_id')?>&nbsp;
				<?=form_dropdown('group_id', $member_groups_dropdown, FALSE, 'id="group_id"')?> 
	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<?=form_label(lang('export_format'), 'export_format')?>&nbsp;
				<?=form_dropdown('export_format', $export_format, FALSE, 'id="export_format"')?> 
	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				<?=form_label(lang('include_custom_fields'), 'include_custom_fields')?>&nbsp;
				<?=form_checkbox('include_custom_fields', '1', '1', 'id="include_custom_fields"')?> 
	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	
				
				<?=form_label(lang('complete_select'), 'complete_select')?>&nbsp;
				<?=form_checkbox('complete_select', '1', FALSE, 'id="complete_select"')?> 
	
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;						
				
								


				<?=form_submit('submit', lang('export'), 'id="export" class="submit"')?>
			</p>
		</fieldset>
	</div>
<?=form_close()?>