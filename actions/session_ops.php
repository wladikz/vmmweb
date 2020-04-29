<?php
    session_start();
    if (isset($_GET['operation'])) {
        $varName=$_GET['name'];
        if (isset($_GET['value'])) {
            $varValue=$_GET['value'];
        }
        switch ($_GET['operation']) {
            case 'save':
                $_SESSION[$varName]=$varValue;
                $result='1';
                break;
            case 'get':
                if (isset($_SESSION[$varName]) && !empty($_SESSION[$varName])) {
                    $result=$_SESSION[$varName];
                } else {
                    $result="";
                }
                
                break;
        }
    }
    Header('Content-type: text/xml');
    $xml = new SimpleXMLElement('<xml version="1.0"/>');
    $xml->addAttribute('encoding',"iso-8859-1");
    $root= $xml->addChild('root')->addAttribute('result',$result);
    print($xml->asXML());
