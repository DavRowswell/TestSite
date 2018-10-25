<?php
	session_start();

	// Or maybe pass along the session id, if needed
	//echo '<br /><a href="page2.php?' . SID . '">page 2</a>';

	// "vwsp_web_search", "vwsp_web_results", and "vwsp_web_details"
	include 'includes/header.php';

	$db = (array_key_exists('db', $_GET)) ? htmlspecialchars($_GET['db']) : '';
	$layout =  (array_key_exists('layout', $_GET)) ? htmlspecialchars($_GET['layout']) : '';

	if (empty($db) || empty($layout)) {
		echo '<p>Please supply a database and/or layout.</p>';
		echo '<meta http-equiv="refresh" content="5;url=index.php" />';
		exit;
	}

	require_once 'db.inc.php';
	
	# grab the sort column, if any has been sent
	$column = (array_key_exists('column', $_GET)) ? $_GET['column'] : '';
	$skip = (array_key_exists('skip', $_GET)) ? $_GET['skip'] : 0;
	$max = (array_key_exists('max', $_GET)) ? $_GET['max'] : 100;

	$records_found = 1;

	# set the layout name for this page
	$layout_name = $layout;

	# set convenience var
	$this_page = $_SERVER['PHP_SELF'];

	# initialize our output var
	$page_content = '';

	# this is the include for the API for PHP
	require_once ('FileMaker.php');

	# instantiate a new FileMaker object
	$fm = new FileMaker(FM_FILE, FM_HOST, FM_USER, FM_PASS);

	# get the layout as an object
	$layout_object = $fm->getLayout($layout_name);
	
	# check for errors
	if (FileMaker::isError($layout_object)) {
		die('<p>'.$record->getMessage().' (error '.$record->code.')</p>');
	}

	# get the fields as an array of objects
	$field_objects = $layout_object->getFields();

	# create a new search transaction
	$request =& $fm->newFindCommand($layout_name);
	
	# indicate that we want an OR search
	$request->setLogicalOperator(FILEMAKER_FIND_AND);

	# grab fields to search from $_POST array
	if (isset($_POST) && !empty($_POST)) {
		$field_names_to_search = $_POST;
		$_SESSION['search_criteria'] = $_POST;
	} else {
		$field_names_to_search = $_SESSION['search_criteria'];
	}
    
	#$my_array=$fm->listLayouts();
	$my_spring=implode(",",$field_names_to_search);
	
	foreach($field_objects as $field_object) {
		$field_name = $field_object->getName();
	
		if ($field_name != 'Province_State') { $tmp_field_name = str_replace(' ','_',$field_name); } else { $tmp_field_name = 'Province_State'; }
#		echo '<p>'.$tmp_field_name.'</p>';
		if (in_array($tmp_field_name, array_keys($field_names_to_search))) {	
			if (!empty($field_names_to_search[$tmp_field_name])) { 
				$criteria = $field_names_to_search[$tmp_field_name];
				# format the criteria appropriately for the current field data type
				if ($field_object->getResult() == 'date') {
					if (strtotime($criteria)) {
						$request->addFindCriterion($field_name, date('n/j/Y', strtotime($criteria)));
					}
				} elseif ($field_object->getResult() == 'time') {
					if (strtotime($criteria)) {
						$request->addFindCriterion($field_name, date('H:i:s', strtotime($criteria)));
					}
				} elseif ($field_object->getResult() == 'timestamp') {
					if (strtotime($criteria)) {
						$request->addFindCriterion($field_name, date('n/j/Y H:i:s', strtotime($criteria)));
					}
				} elseif ($field_object->getResult() == 'container') {
				# skip this field because it is a container (like a blob) and can't be searched for text
				} else {
					$request->addFindCriterion($field_name, $criteria);
				}
			}
		}
	}

	# specify sort column (aka, field), if any
	if ($column!='') $request->addSortRule($column, 1);

	# set number of results to return per page
	$request->setRange($skip, $max);
	
	# execute the search transaction
	$result = $request->execute();

	# check for errors (including no records found)
	if (FileMaker::isError($result)) {
		$records_found = 0;
		$no_records_msg = '<p class="h1"><font color="red">' . $result->getMessage() . '.</font></p>';
		$no_records_msg .= '<p class="h1"><a href="search.php?db=' . $db . '&layout=' . $layout . '"><< Please try another search.</a></p>';
	}

	if ($records_found) {
		# display the found count
		$total = $result->getTableRecordCount();
		$found = $result->getFoundSetCount();
		$s = ($found==1) ? '' : 's';
		$header = explode('.', $db);
		$page_content .= '<h3>' . ucfirst($header[0]) . ' Database</h3>';
		$to = $skip + $max;

		if (!empty($criteria)) {
			$page_content .= '<p class="h1">Your search for "' . $criteria . '" returned ' . $found . ' record' . $s . '.';
			// of " . $total . ' total</p>';
		} else {
			$page_content .= '<p class="h1">Listing ' . $skip . ' - ' . $to . ' records.';
			// of ' . $total . ' total</p>';
		}

		# get the result record set as an array of record objects
		$record_objects = $result->getRecords();
	
		$use_custom_titles = false;
		$file = str_replace('.fmp12','',$db);
		if (is_file('includes/' . $file . '_web_results_titles.txt')) {
			$use_custom_titles = true;
			$titles = file('includes/' . $file . '_web_results_titles.txt');			
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

		$formatted_results_array = array();

		$i=0;
		foreach ($record_objects as $record_object) {
			foreach($field_objects as $field_object) {
				$field_name = $field_object->getName();
				$field_val = $record_object->getField($field_name);
				$field_val = htmlspecialchars($field_val, ENT_QUOTES);
				$field_val = nl2br($field_val);
				if ($use_custom_titles) {				
					if (array_key_exists($field_name, $display_array)) {
						$formatted_results_array[$i]['rec_id'] = $record_object->getRecordId();
						$formatted_results_array[$i][$field_name] = $field_val;
					}
				} else {
					$formatted_results_array[$i]['rec_id'] = $record_object->getRecordId();
					$formatted_results_array[$i][$field_name] = $field_val;
				}
			}
			$i++;
		}

		# start compiling our record output
		$page_content .= '<table border="0" cellspacing="1" cellpadding="3" class="h1">';
		$page_content .= '<tr bgcolor="#CCCCCC">';
	
		# loop through array of field objects to draw header
		foreach($field_objects as $field_object) {
			$field_name = $field_object->getName();
			if ($use_custom_titles) {
				if (array_key_exists($field_name, $display_array)) {				
					$field_title = $display_array[$field_name];
					$page_content .= '<td><strong><a href="' . $this_page . '?db=' . $db . '&layout=' . $layout . '&column=' . $field_name. '"><font color="black">' . $field_title . '</font></a></strong></td>';
				}	
			} else {
				$field_title = $field_name;
				$page_content .= '<td><strong><a href="' . $this_page . '?db=' . $db . '&layout=' . $layout . '&column=' . $field_name. '"><font color="black">' . $field_title . '</font></a></strong></td>';
			}
		}
		$page_content .= '</tr>';
		 
		# loop through formatted results array
		$layout_details = str_replace('results','details',$layout);
		$i=0;
		foreach($formatted_results_array as $formatted_results) {
			if ($i % 2 == 0) { $bgc = "#EEEEEE"; } else { $bgc = "#FFFFFF"; }	
			$page_content .= '<tr bgcolor="'.$bgc.'">';
			foreach($formatted_results as $key => $val) {
                $field_value = htmlspecialchars_decode($val, ENT_NOQUOTES);
		        $field_value = nl2br($field_value);	
				if (preg_match('/Accession/', $key)) { 
					$page_content .= '<td>'; 
					$page_content .= '<a href="details.php?db=' . $db . '&layout=' . $layout_details . '&recid=' . $formatted_results_array[$i]['rec_id'] . '&ass_num=' . $field_value . '">' . $field_value . '</a>';
					if(is_file('images/' . $file . '_images/Thumbnail_web/' . $field_value . '.jpg')) {				
						$page_content .= '&nbsp;<a href="image.php?db=' . $db . '&layout=' . $layout . '&recid=' . $formatted_results_array[$i]['rec_id'] . '&ass_num=' . $field_value . '"><img src="images/ico_camera.gif" border="0" /></a>';
					}					
					$page_content .= '</td>';	
				} elseif ($key == 'Date') {
					$page_content .= '<td nowrap="nowrap">' . $field_value . '</td>';			
				} else {
					if ($key != 'rec_id') {
						$page_content .= '<td>' . $field_value . '</td>';		
					}
				}
			}
			$page_content .= '</tr>';
			$i++;
		}

		$page_content .= '</table>'."\n";

		# skip trough results 25 records at a time
		if ($found > $max) {
			$skip = $skip + $max;
			$page_content .= '<p class="h1"><a href="search.php?db=' . $db . '&layout=' . $layout . '"><< Try a different search</a>';		
			$page_content .= ' | <a href="search_results.php?db=' . $db . '&layout=' . $layout . '&column=' . $column . '&skip=' . $skip . '&max=' . $max .'">Next ' . $max . ' Results >></a></p>' . "\n";
		} else {
			$page_content .= '<p class="h1"><a href="search.php?db=' . $db . '&layout=' . $layout . '"><< Try a different search</a>';
			$page_content .= ' | Next ' . $max . ' Results >></p>' . "\n";
		}
	} else {
		$page_content = $no_records_msg;
	}

echo $page_content; 
include 'includes/footer.php'; ?>
