<?php
	if (isset($_GET['Database'])) {
		require_once ('databases/'.$_GET['Database'].'db.php');
	} else {
		require_once ('databases/miwdb.php');
	}
?>
