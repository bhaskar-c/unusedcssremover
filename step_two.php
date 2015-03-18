<?php
session_start();

require_once 'simple_html_dom.php';
require_once 'css_parser.php';

$total_html_content = str_get_html($_SESSION["total_html_content"]);
$css = $_SESSION["css"];


$used = array('u');
$unused = array('un');


		//file_put_contents("totalbhola.txt", $_SESSION["total_html_content"] );
		//file_put_contents("cssbhola.txt", varDumpToString($_SESSION["css"] ) );


foreach(array_keys($css) as $cssitem)
	(null !==($total_html_content->find(trim(explode(':', $cssitem, 2)[0]), 0)) ? array_push($used, $cssitem) : array_push($unused, $cssitem));

$step_two_form_return_data['used'] = $used; 
$step_two_form_return_data['unused']  = $unused;
$step_two_form_return_data['success'] = true;

echo json_encode($step_two_form_return_data);


	/* Util Functions*/
	
	function varDumpToString ($var){
          ob_start();
          var_dump($var);
          $result = ob_get_clean();
          return $result;
	}
	

?>
