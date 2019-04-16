<?php
	if (isset($_GET['Database']) && $_GET['Database'] != "") {
		if ($_GET['Database'] !== 'all')
		require_once ('databases/'.$_GET['Database'].'db.php');
	} else {
		require_once ('databases/miwdb.php');
	}
?>
