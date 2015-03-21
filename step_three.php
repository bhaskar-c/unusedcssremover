<?php

	session_start();
	header('Content-Type', 'application/json');
	require_once 'simple_html_dom.php';
    require_once 'css_parser.php';
    
    require_once('FirePHPCore/FirePHP.class.php');
    ob_start();
    $firephp = FirePHP::getInstance(true);

	$data = array();
	$do_not_remove_items = $_POST['do_not_remove_items']; //not sanitised

	$firephp->log($do_not_remove_items);
	
	//$_SESSION["total_html_content"] = utf8_encode($total_html_content);
	//$_SESSION["css"] = $parsed_css;
	$_SESSION["css_string"] = $css_string;
	//$_SESSION["used"] = $used;
	$unused = $_SESSION["unused"];
	
	//remove $do_not_remove_items from $unused i;e treat it as though used
	foreach($do_not_remove_items as $item){
		unset($unused[$item]);
		} 
	$unused = array_values($unused); // 'reindex' array	


	

	$data['content'] = $unused; 
	$data['success']= true;
	echo json_encode($data);
	
	
	

?>

