<table<?php echo HTML::attributes($table_attr); ?>>
	<tbody>
<?php foreach ($columns as $col): ?>
		<tr>
			<th<?php echo HTML::attributes($col->heading_attr ?: array()); ?>><?php echo $col->heading; ?></th>
<?php foreach ($rows as $row): ?>
			<td<?php echo HTML::attributes(call_user_func($col->cell_attr, $row)); ?>><?php echo call_user_func($col->value, $row); ?></td>
<?php endforeach; ?>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo $pagination ?: ''; ?>