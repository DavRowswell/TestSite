<?php
	session_start();

	$db = (array_key_exists('db', $_GET)) ? $_GET['db'] : '';
	$layout =  (array_key_exists('layout', $_GET)) ? $_GET['layout'] : '';
	$recid = (array_key_exists('recid', $_GET)) ? $_GET['recid'] : '';
	$ass_num =  (array_key_exists('ass_num', $_GET)) ? $_GET['ass_num'] : '';

	if (empty($db) || empty($layout)) {
		echo '<p>Please supply a database and/or layout.</p>';
		echo '<meta http-equiv="refresh" content="5;url=database.php" />';
		exit;
	}

	if (empty($recid)) {
		echo '<p>Please supply a record ID.</p>';
		echo '<meta http-equiv="refresh" content="5;url=database.php" />';
		exit;
	}

	require_once 'db.inc.php';

	# set the layout name for this page
	$layout_name = $layout;

	# initialize our output var
	$page_content = '';

	# this is the include for the API for PHP
	require_once ('FileMaker.php');

	# instantiate a new FileMaker object
	$fm = new FileMaker(FM_FILE, FM_HOST, FM_USER, FM_PASS);

	# get the record by its id
	$record = $fm->getRecordById($layout_name, $recid);

	# check for errors
	if (FileMaker::isError($record)) {
		die('<p>'.$record->getMessage().' (error '.$record->code.')</p>');
	}

	# get the layout as an object
	$layout_object = $record->getLayout();

	# get the fields from the layout as an array of objects
	$field_objects = $layout_object->getFields();

	$use_custom_titles = false;
	$file = str_replace('.fmp12','',$db);
	if (is_file('includes/' . $file . '_web_details_titles.txt')) {
		$use_custom_titles = true;
		$titles = file('includes/' . $file . '_web_details_titles.txt');			
		foreach ($titles as $line_num => $title_pair)
		{
			$title_pair = trim($title_pair);		
			if (!empty($title_pair)) {		
				$titles_add = explode(';', $title_pair);
				$field_name = trim($titles_add[0]);
				$title = trim($titles_add[1]);		
				$display_array[$field_name] = $title;
			}
		}
	}

	$layout_results = str_replace('details', 'results', $layout);
	$layout_search = str_replace('details', 'search', $layout);
	$page_content .= '<h3>Details for: ' . $ass_num . '</h3>'."\n";
	$page_content .= '<table border="0" cellspacing="1" cellpadding="3">'."\n";

	if(is_file('images/' . $file . '_images/Thumbnail_web/' . $ass_num . '.jpg')) {
		$page_content .= '<tr><td valign="top"><a href="image.php?db=' . $db . '&layout=' . $layout . '&recid=' . $recid . '&ass_num=' . $ass_num . '"><img src="images/' . $file . '_images/Thumbnail_web/' . $ass_num . '.jpg" border="0" /></a></td>'."\n";
	}

	$page_content .= '<td>'."\n";;
	$page_content .= '<table border="0" cellspacing="1" cellpadding="3">'."\n";

	$field_names = $layout_object->listFields();

	$i=0;
	foreach($field_objects as $field_object) {
		$field_name = $field_object->getName();		
		if ($use_custom_titles) {		
			if (array_key_exists($field_name, $display_array)) {
				$field_title = $display_array[$field_name];
			}
		} else {
			$field_title = $field_name;
		}	
		$field_value = $record->getField($field_name);
		$field_value = htmlspecialchars_decode($field_value, ENT_NOQUOTES);
		$field_value = nl2br($field_value);
		if ($i % 2 == 0) { $bgc = "#EEEEEE"; } else { $bgc = "#FFFFFF"; }
		$page_content .= '<tr class="h1" bgcolor="' . $bgc . '"><td>' . $field_title . '</td><td>' . $field_value . '</td></tr>'."\n";
		$i++;
	}

	$page_content .= '</table>'."\n";
	$page_content .= '</tr>'."\n";
	$page_content .= '</table>'."\n";
	$page_content .= '<p class="h1"><a href="search_results.php?db=' . $db . '&layout=' . $layout_results . '"><< Return to Search Results</a>';
	$page_content .= ' | <a href="search.php?db=' . $db . '&layout=' . $layout_search . '">Try a different Search</a></p>';
 
	include 'includes/header.php';
	echo $page_content;
	include 'includes/footer.php';
?>
