<?php

function parse($css){
    
    $css = preg_replace('/@import[^;]*;/', '', $css); // ignore all imports
    $css = preg_replace('!/\*.*?\*/!s', '', $css); // remove all multiline comments
    preg_match_all( '/(?<selector>(?:(?:[^,{]+),?)*?)\{(?:(?<name>[^}:]+):?(?<value>[^};]+);?)*?\}/', $css, $arr);
    $result = array();
    foreach ($arr[0] as $i => $x){
        $selector = trim($arr[1][$i]);
        $rules = explode(';', trim($arr[2][$i]));
        $rules_arr = array();
        foreach ($rules as $strRule){
            if (!empty($strRule)){
                $rule = explode(":", $strRule);
                $rules_arr[trim($rule[0])] = isset($rule[1]) ? trim($rule[1]) : null;
                
            }
        }

        $selectors = explode(',', trim($selector));
        foreach ($selectors as $strSel){
            $result[trim($strSel)] = $rules_arr;
        }
    }
    return $result;
}

?>
