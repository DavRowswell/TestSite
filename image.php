<?php 
	session_start();

	$db = (array_key_exists('db', $_GET)) ? $_GET['db'] : '';
	$layout =  (array_key_exists('layout', $_GET)) ? $_GET['layout'] : '';
	$rec_id = (array_key_exists('recid', $_GET)) ? $_GET['recid'] : '';
	$ass_num =  (array_key_exists('ass_num', $_GET)) ? $_GET['ass_num'] : '';
	$layout_search = str_replace('results', 'search', $layout);
	$layout_details = str_replace('results', 'details', $layout);
	$layout_results = str_replace('details', 'results', $layout);
    $file = str_replace('.fmp12','',$db);

	$page_content = '<p class="h1"><a href="details.php?db=' . $db . '&layout=' . $layout_details . '&recid=' . $rec_id . '&ass_num=' . $ass_num . '"><< Return to Details</a>';
	$page_content .= ' | <a href="search_results.php?db=' . $db . '&layout=' . $layout_results . '">Return to Search Results</a>';
	$page_content .= ' | <a href="search.php?db=' . $db . '&layout=' . $layout_search . '">Try a different Search</a></p>';	
	$page_content .= '<img src="images/' . $file . '_images/Large_web/' . $ass_num . '.jpg" />';
	
	include 'includes/header.php';
	echo $page_content;
	include 'includes/footer.php';
?>
