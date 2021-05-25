
<?php
	if (isset($_GET['Database']) && $_GET['Database'] != "") {
		if ($_GET['Database'] !== 'all' && (
		$_GET['Database'] == 'avian' ||$_GET['Database'] == 'herpetology' || $_GET['Database'] == 'mammal' ||
		$_GET['Database'] == 'vwsp' || $_GET['Database'] == 'bryophytes' || $_GET['Database'] == 'fungi' || $_GET['Database'] == 'lichen' || $_GET['Database'] == 'algae'||
		$_GET['Database'] === 'entomology' || $_GET['Database'] === 'fish' || $_GET['Database'] === 'mi' || $_GET['Database'] === 'miw' || $_GET['Database'] === 'fossil')) {
			require_once ('databases/'.$_GET['Database'].'db.php');
		}
		else if($_GET['Database'] == 'all') {
			
		}
		else {
			$_SESSION['error'] = "Not a valid database given";
			header('Location: error.php');
			exit;
		}
	} else {
		require_once ('databases/miwdb.php');
	}
?>
