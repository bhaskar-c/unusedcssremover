<?php

	session_start();
	header('Content-Type', 'application/json');
	require_once 'simple_html_dom.php';
    require_once 'css_parser.php';
	require_once('log.php');
    set_time_limit(90);
    
	$data = array();
	
	$do_not_remove_items = array();
	if(isset($_POST['do_not_remove_items'])) {
		  $do_not_remove_items = $_POST['do_not_remove_items'];
	}
	
	$unused = $_SESSION["unused"];
	$css_string = $_SESSION["css_string"];
	
	//remove $do_not_remove_items from $unused i;e treat it as though used
	if(is_array($do_not_remove_items) and !empty($do_not_remove_items)){
		foreach($do_not_remove_items as $item){
			unset($unused[$item]);
		} 
		$unused = array_values($unused); // 'reindex' array	
	}
	

// Now the css generation stuff
	
	foreach($unused as $unuseditem) {
		$unuseditem = preg_quote($unuseditem, '/');
		$unuseditem = '(?:(?<=^|\s)(?=\S|$)|(?<=^|\S)(?=\s|$))'.$unuseditem.'(?:(?<=^|\s)(?=\S|$)|(?<=^|\S)(?=\s|$))';
		$css_string = preg_replace('/'.$unuseditem.'/', "", $css_string);
	}
	
/*	foreach($unused as $unuseditem) {
		$unuseditem = preg_quote($unuseditem, '/');
		$unuseditem = '(?:(?<=^|\s)(?=\S|$)|(?<=^|\S)(?=\s|$))'.$unuseditem.'(?:(?<=^|\s)(?=\S|$)|(?<=^|\S)(?=\s|$)) *,';
		$css_string = preg_replace('/'.$unuseditem.'/', "", $css_string);
	}*/
	
	$css_string = preg_replace("/(,\s*){2,}/", ",", $css_string); // remove multiple instances of comma
	$css_string = preg_replace("/}\s*?(,|>)/", "}", $css_string); // remove deinitions with only comma or > left as selector
	
	do {
	$css_string = preg_replace('/}\s*,?\s*{[^}]*}/S', "}", $css_string, -1, $count); //remove definitions with no selector elements
	} while ($count);
	
	
	do {
	$css_string = preg_replace('/{\s*,?\s*{[^}]*}/S', "{", $css_string, -1, $count);// handle 1st unused definition within media query format like ' { {some definitions here }'
	} while ($count);
	$css_string = preg_replace("/,\s*{/", "{", $css_string); //remove instances like ', {'
	//$css_string = preg_replace("/{\s*,/", "{", $css_string); //remove instances like '{ ,'
	$css_string = str_replace(' , ', ',', $css_string); //
	$css_string = str_replace('}',"}<br>", $css_string);
	$css_string = preg_replace('/\s+/', ' ', $css_string); // remove excess white spaces
	
	//houston we are ready;
	$data['content'] = $css_string ; 
	$data['success']= true;
	echo json_encode($data);
	
	
	

?>

