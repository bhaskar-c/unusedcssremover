<?php

	session_start();
	header('Content-Type', 'application/json');
	require_once 'simple_html_dom.php';
    require_once 'css_parser.php';

	$do_not_remove_items = sanitise_input($_POST['do_not_remove_items']);

//$data = array( 'success' => true);
echo json_encode($data);

?>

