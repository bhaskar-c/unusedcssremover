<?php
session_start();

require_once 'simple_html_dom.php';
require_once 'css_parser.php';
require_once('log.php');
set_time_limit(90);
$total_html_content = str_get_html($_SESSION["total_html_content"]);
$parsed_css = $_SESSION["css"];

$used = array();
$unused = array();

foreach(array_keys($parsed_css) as $parsed_cssitem)
	(null !==($total_html_content->find(trim(explode(':', $parsed_cssitem, 2)[0]), 0)) ? array_push($used, $parsed_cssitem) : array_push($unused, $parsed_cssitem));

//$_SESSION["used"] = $used;
$_SESSION["unused"] = $unused;


// lets do some css processing here to make it fit for use in the next step
// doing it here to avoid reaching maximum execution time of 30 seconds in the next step
$css_string = $_SESSION["css_string"];
$css_string = preg_replace('!/\*.*?\*/!s', '', $css_string); // remove all multiline comments
$css_string = preg_replace('/\s+/', ' ', $css_string); // remove excess white spaces
$css_string = str_replace(',', ' , ', $css_string); // important - this is making all the difference to regex working and not working
$css_string = str_replace('{', ' { ', $css_string);
$css_string = str_replace('}', ' } ', $css_string);
$_SESSION["css_string"] = $css_string;


$step_two_form_return_data['used'] = $used; 
$step_two_form_return_data['unused']  = $unused;
$step_two_form_return_data['success'] = true;

echo json_encode($step_two_form_return_data);

?>
