<?php



function dlog($item){
	require_once('FirePHPCore/FirePHP.class.php');
	ob_start();
	$firephp = FirePHP::getInstance(true);
	$firephp->log($item);
}


?>
