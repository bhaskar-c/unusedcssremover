<?php
	session_start();
	header('Content-Type', 'application/json');
	require_once 'simple_html_dom.php';
    require_once 'css_parser.php';

	$MAX_NUM_OF_URLS_ALLOWED = 5;

	$urls = sanitise_input($_POST['urls']);
	$css_url = sanitise_input($_POST['css_url']);

    $total_html_content = "";
    $html_content = null;
    
	$errors = array(); 
    $step_one_form_return_data = array(); 

    
    //initial validation
    (empty($urls)) ? ($errors['urls'] = 'URLs cannot be blank\n'):null;
    (($css_url =="")) ? ($errors['urls'] = 'CSS URL cannot be blank\n'):null;
    $urls_array = explode("\n", $urls);
	(count($urls_array) > $MAX_NUM_OF_URLS_ALLOWED) ? ($errors['urls'] = "Max allowed number of URLs: ".$MAX_NUM_OF_URLS_ALLOWED):null;
	$urls_array = array_filter($urls_array, 'trim');		
	foreach($urls_array as $url)
			is_valid_url($url) ? null : ($errors['urls'] .= "Invalid url:".$url);
	are_from_same_domain($urls_array) ? null:($errors['urls'] .= "Multiple domain queries not allowed");
	is_valid_css_url($css_url) ? null:($errors['urls'] .= "Invalid css url:".$css_url);
	
	//apparantly OK - so start processing. there may be more errors as you proceed
	if (empty($errors)) {
		foreach($urls_array as $url){
			$html_content = file_get_html($url);
			if($html_content !=null){ 
					$total_html_content .= $html_content;
				}
				else {
					$errors['urls'] =  "could not fetch url ".$url; break;
					}
			$html_content = null;
		}
		
		$_SESSION["total_html_content"] = utf8_encode($total_html_content);
		
		$parsed_css =  parse($css_url);
		$_SESSION["css"] = $parsed_css;
		
		$css_string = file_get_contents($css_url);
		$_SESSION["css_string"] = $css_string;
}

	if (empty($errors)) { 
		$step_one_form_return_data['success'] = true; 
		$step_one_form_return_data['noerrors'] = array("hooray");
    } else { 
    	$step_one_form_return_data['success'] = false; 
    	$step_one_form_return_data['errors']  = $errors;
	}
    
    //Return the data back to form.php
    echo json_encode($step_one_form_return_data);


	/* Util Functions*/
	
	
	function get_domain($url){
	  $pieces = parse_url($url);
	  $domain = isset($pieces['host']) ? $pieces['host'] : '';
	  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
		return $regs['domain'];
	  }
	  return false;
	}
	
	
	function are_from_same_domain($urlarray){
		$domainname = 	get_domain($urlarray[0]);
		foreach($urlarray as $url)
			if(get_domain($url) != $domainname) {return false;}
		return true;	
		}
	
	
	
	function is_valid_css_url($entered_url){
		return (is_valid_url($entered_url) and (stripos(strrev(reset(explode('?', $entered_url))), "ssc.") === 0)); //valid url + ends with .css reversed needle
		}

	
	
    function is_valid_url($entered_url){
		$pattern = "#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#i";
		return preg_match($pattern,$entered_url);
		}

	function sanitise_input($data) {
	   $data = trim($data);
	   $data = stripslashes($data);
	   $data = htmlspecialchars($data);
	   return $data;
	}

	
