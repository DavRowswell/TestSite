
<?php
	# loads the database files necessary to access the database

	$databases = ['algae', 'avian', 'bryophytes', 'entomology', 'fish',
		'fossil', 'fungi', 'herpetology', 'lichen', 'mammal', 'mi', 'miw', 'vwsp'];

	if (isset($_GET['Database']) && $_GET['Database'] != "") {
		if (in_array($_GET['Database'], $databases)) {
			require_once ('databases/'.$_GET['Database'].'db.php');
		}
		else if($_GET['Database'] == 'all') {
			# TODO add something here?
		}
		else {
			$_SESSION['error'] = "Not a valid database given";
			header('Location: error.php');
			exit;
		}
	} else {
		$_SESSION['error'] = "Do database given!";
		header('Location: error.php');
		exit;
	}
