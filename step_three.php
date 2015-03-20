<?php

	session_start();
	header('Content-Type', 'application/json');
	require_once 'simple_html_dom.php';
    require_once 'css_parser.php';


	$data = array();
	$do_not_remove_items = $_POST['do_not_remove_items']; // not sanitised

	$data['content'] = $do_not_remove_items; //for now sending back just what was received
	$data['success']= true;
	echo json_encode($data);

?>

