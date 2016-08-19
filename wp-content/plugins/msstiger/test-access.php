<?php
if (isset($_REQUEST['check']) && $_REQUEST['check'] == "checkdatabase") {
	$mydb = new wpdb($_REQUEST['dbuser'], $_REQUEST['dbpass'], $_REQUEST['dbname'], $_REQUEST['hostname']);
	?>
          <script>
                document.getElementById('vtst-database-test-results').style.fontWeight = "bold";
                document.getElementById('vtst-database-test-results').style.color = "green";
                document.getElementById('vtst-database-test-results').innerHTML = "processing";
         </script>
           <?php
$rows = $mydb->get_results("show tables like 'vtiger_users'"); // This vtiger's user table used only for test purpose
	if (is_object($rows[0])) {
		echo "Success";
	} else {
		echo is_object($mydb);
	}
}
?>
