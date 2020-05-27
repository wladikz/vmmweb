<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/SessionHandler.php');
    MySQLSessionHandler::session_start();

    $xml = new SimpleXMLElement('<xml version="1.0"/>');
    $xml->addAttribute('encoding',"iso-8859-1");
    $tree = $xml->addChild('tree');
    $tree->addAttribute("id","root");
    $item = $tree->addChild('item');
    $item->addAttribute("id","weblog");
    $item->addAttribute("text","Web Log");
    $item->addAttribute('select',"yes");
//    $item->addAttribute('open','1');
    $item = $tree->addChild('item');
    $item->addAttribute("id","vmmlog");
    $item->addAttribute("text","VMM Log");
//    $item->addAttribute('open','1');
    Header('Content-type: text/xml');
    $tmp=$xml->asXML();
    print($xml->asXML());
