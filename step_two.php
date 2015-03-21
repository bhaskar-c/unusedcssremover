<?php
session_start();

require_once 'simple_html_dom.php';
require_once 'css_parser.php';

$total_html_content = str_get_html($_SESSION["total_html_content"]);
$parsed_css = $_SESSION["css"];

$used = array();
$unused = array();

foreach(array_keys($parsed_css) as $parsed_cssitem)
	(null !==($total_html_content->find(trim(explode(':', $parsed_cssitem, 2)[0]), 0)) ? array_push($used, $parsed_cssitem) : array_push($unused, $parsed_cssitem));

//$_SESSION["used"] = $used;
$_SESSION["unused"] = $unused;

$step_two_form_return_data['used'] = $used; 
$step_two_form_return_data['unused']  = $unused;
$step_two_form_return_data['success'] = true;

echo json_encode($step_two_form_return_data);

?>
