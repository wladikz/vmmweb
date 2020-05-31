<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/XML_misc.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/misc.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/SessionHandler.php');
    MySQLSessionHandler::session_start();

    $xml = new SimpleXMLElement('<xml version="1.0"/>');
    $xml->addAttribute('encoding',"iso-8859-1");
    $menu=$xml->addChild("menu");
    $isEnabled=TRUE;
    $item=$menu->addChild("item");
    $item->addAttribute("id", "DeployTemplate");
    $item->addAttribute("text","Deploy Template");
    $item->addAttribute("enabled","true");
    $item->addAttribute("img",GetPageURL('/images/vm/DeployST.png'));
//    $item->addChild("href")->addCData($link);

    Header('Content-type: text/xml');
    $tmp=$xml->asXML();
    $result=$menu->asXML();
    print($result);

