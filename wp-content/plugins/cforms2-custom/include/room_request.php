<?php 

if(empty($gen_data)) return;
foreach($gen_data as $r){
	echo "<b>$r->field_name: </b> $r->field_val<br/>";
}

if(empty($room_data)) return; ?>
<br/>
<table>
<tr><th>Room Name</th><th>Units</th></tr>
<?php 
foreach($room_data as $r){
	echo "<tr><td>$r->field_name</td><td>$r->field_val</td></tr>";
}
?>
</table>