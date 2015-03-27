<?php

function parse($css){
 
    $css = preg_replace('/@import[^;]*;/', '', $css); // ignore all imports
    $css = preg_replace('~\/\*[^*]*\*+([^/*][^*]*\*+)*\/~', '', $css); // remove all multiline comments
    preg_match_all( '/([^\r\n,{}]+)(,(?=[^}]*{)|\s*{)/', $css, $arr);
    $result = array();
    foreach ($arr[0] as $i => $x){
        $selector = trim($arr[1][$i]);
        $selectors = explode(',', trim($selector));
        foreach ($selectors as $strSel){
            $result[trim($strSel)] = null;
        }
    }
    return $result;
 
 }

?>
