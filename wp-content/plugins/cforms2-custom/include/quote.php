<?php
/**
 *  Placeholder for the front end codes of the Quotation Table 
 *  when the reservation form is submitted.
 *  
 *  Important Variables used: 
 *   $room_datas_data
 *  
 */

$currency = "Php ";

?>

<?php if(!isset($room_data) || empty($room_data)) echo('Nothing to see here'); ?>

<h4>Your Quotation</h4>
<table>
	<tr>
		<th>Room</th>
		<th># of Units</th>
		<th>Unit Price</th>
		<th></th>
	</tr>
	<?php foreach($room_data['rooms'] as $r){ ?>
	<tr>
		<td><?php echo $r['name']; ?></td>
		<td><?php echo $r['units']; ?></td>
		<td><?php echo $currency . number_format( $r['unit_price'], 2, '.', ',' ); ?></td>
		<td><?php echo $currency . number_format( $r['total_price'], 2, '.', ',' ); ?></td>
	</tr>
	<?php }	?>
	<tr>
		<td colspan="3"><b>Sub Total</b></td>
		<td><b><?php echo $currency . number_format( $room_data['total_wo_days'], 2, '.', ',' ); ?></b></td>
	</tr>
	<tr>
		<td colspan="4"></td>
	</tr>
	<tr>
		<td colspan="3">x Number of Days (<?php echo $room_data['days']; ?>)</td>
		<td><?php echo $currency . number_format( $room_data['total_wo_tax'], 2, '.', ',' ); ?></td>
	</tr>
	<tr>
		<td colspan="3">Service Charge</td>
		<td><?php echo $currency . number_format( $room_data['tax_10'], 2, '.', ',' ); ?></td>
	</tr><tr>
		<td colspan="3">Tax</td>
		<td><?php echo $currency . number_format( $room_data['tax_12'], 2, '.', ',' ); ?></td>
	</tr><tr>
		<td colspan="3"><b>Total</b></td>
		<td><b><?php echo $currency . number_format( $room_data['total'], 2, '.', ',' ); ?></b></td>
	</tr>
</table>

