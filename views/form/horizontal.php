<?php 

echo Form::open($form_action, $form_method, array_merge($form_attr, array('class' => 'form-horizontal')));

foreach ($fieldsets as $fieldset) { ?>

	<fieldset<?php echo HTML::attributes($fieldset->attr ?: array()); ?>>
		
		<legend><?php echo $fieldset->name ?: '' ?></legend>

		<?php foreach ($fieldset->controls() as $control) { ?>

			<div class="control-group<?php echo $errors->has($control->name) ? ' errors' : '' ?>">
				<?php echo Form::label($control->name, $control->label); ?>
				<div class="controls">
					<?php echo call_user_func($control->field, $row, $control); ?>
					<?php echo $errors->first($control->name, $error_message); ?>
				</div>
			</div>

		<?php } ?>
	
	</fieldset>
<?php } ?>

<div class="form-actions">
	<button type="submit" class="btn btn-primary">Submit</button>
</div>

<?php echo Form::close(); ?>
