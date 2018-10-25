<?php 
	session_start();	

	include 'includes/header.php';

	$db = (array_key_exists('db', $_GET)) ? htmlspecialchars($_GET['db']) : '';
	$save_search = (array_key_exists('save_search', $_GET)) ? htmlspecialchars($_GET['save_search']) : false;
	$layout_name = str_replace('.fmp12','',$db);
	$page_content = '';
	$layout_name_search = $layout_name . '_web_search';
	$layout_name_results = $layout_name . '_web_results';

    switch ($layout_name)
    {
        case 'vwsp';
            $db_title = 'Vascular';
            break;
        case 'ubcalgae';
            $db_title = 'Algae';
            break;
        default:
            $db_title = ucfirst($layout_name);
            break;
    }

	if (!empty($db)) {
		$page_content = '<h3>' . $db_title . ' Plant Database - University of British Columbia Herbarium</h3>';
		$search_string = 'search_results.php?db=' . $db . '&layout=' . $layout_name_results;
		$page_content .= '<form action="' . $search_string . '" method="post" name="herb_search">';
	} else {
		echo '<h3>Error:</h3>';			
		echo '<p>Please supply a database name to search.</p>';
		echo '<meta http-equiv="refresh" content="5;url=index.php" />';
		exit;
	}

	require_once 'db.inc.php';

	# this is the include for the API for PHP
	require_once ('FileMaker.php');

	# instantiate a new FileMaker object
	$fm = new FileMaker(FM_FILE, FM_HOST, FM_USER, FM_PASS);

	# get the layout as an object
	#echo '<p>'.$layout_name_search.'</p>';
	#$layout_name = listLayouts($fm);
	#$layout_list=$fm->listLayouts();
	foreach ($fm->listLayouts() as $key => $value)
		echo '<p>'.$key." has the value".$value'</p>';
	$layout_object = $fm->getLayout($layout_name_search);

	# check for errors
	if (FileMaker::isError($layout_object)) {
		die('<p>'.$layout_object->getMessage().' (error '.$layout_object->code.')</p>');
	}

	# get the fields as an array of objects
	$field_objects = $layout_object->getFields();

	$use_custom_titles = false;
	if (is_file('includes/' . $layout_name . '_web_search_titles.txt')) {
		$use_custom_titles = true;	
		$titles = file('includes/' . $layout_name . '_web_search_titles.txt');
		foreach ($titles as $line_num => $title_pair)
		{
			$title_pair = trim($title_pair);		
			if (!empty($title_pair)) {		
				$titles_add = explode(':', $title_pair);
				$field_name = trim($titles_add[0]);
				$title = trim($titles_add[1]);		
				$display_array[$field_name] = $title;
			}
		}
	}

	$page_content .= '<table cellpadding="2" cellspacing="2" border="0">';
	foreach($field_objects as $field_object) {
		$field_name = $field_object->getName();
		if ($use_custom_titles) {		
			if (array_key_exists($field_name, $display_array)) {
				$field_title = $display_array[$field_name];
			}
		} else {
			$field_title = $field_name;
		}
		$field_name = str_replace(' ','_',$field_name);
        /*		
        if (isset($_SESSION['search_criteria']) && !empty($_SESSION['search_criteria']) && $save_search) {
			if (array_key_exists($field_name, $_SESSION['search_criteria'])) { 			
				$value = $_SESSION['search_criteria'][$field_name];
			}
		} else {
			$value = '';
		}
        */
        $value = '';
		$page_content .= '<tr><td class="h1">' . $field_title . '</td><td class="h1"><input name="'. $field_name .'" type="text" size="25" value="' . $value .'" /></td></tr>';	
	}
	$page_content .= '</table>';
	echo $page_content;
?>
<input type="submit" value="Search" />    
</form>
<?php
	include 'includes/footer.php'; 
?>
