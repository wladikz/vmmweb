<?php
    
function GetPageURL($reqURL) {
    $pageURL = 'http';
    if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"];
    }
    $pageURL .= $_SERVER['CONTEXT_PREFIX'];
    if (!empty($reqURL)) {
        if ($reqURL[0] != "/") {
            $pageURL .="/" . $reqURL;
        } else {
            $pageURL .= $reqURL;
        }
    }
    return $pageURL;
}
function GetTypeFromID($itemID) {
    preg_match("/^svc_|^ct_|^vm_/",$itemID,$matches);
    return $matches[0];    
}
function GetIDwoType($itemID) {
    $type=GetTypeFromID($itemID);
    return substr($itemID, strlen($type));
}