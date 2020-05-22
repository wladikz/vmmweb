<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/database.class.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/mysql.sessions.php');
    Session::session_start();
    
    $xml = new SimpleXMLElement('<xml version="1.0"/>');
    $items=$xml->addChild("items");
    $item=$items->addChild("item");
    $item->addAttribute("type", "settings");
    $item->addAttribute("position","label-left");
    $item->addAttribute("labelWidth",100);
    $item->addAttribute("inputWidth",150);
    $FieldSet=$items->addChild("item");
    $FieldSet->addAttribute("type", "block");
    $FieldSet->addAttribute("blockOffset",30);
    $FieldSet->addAttribute("offsetTop",15);
    $FieldSet->addAttribute("width","auto");

    $fldLabel1=$FieldSet->addChild("item");
    $fldLabel1->addAttribute("type", "label");
    $fldLabel1->addAttribute("label", "Please Select User Role");
    $fldLabel1->addAttribute("labelWidth","auto");
    $fldLabel1->addAttribute("offsetLeft",35);

    $fldURCombo=$FieldSet->addChild("item");
    $fldURCombo->addAttribute("type", "combo");
    $fldURCombo->addAttribute("label", "User Role");
    $fldURCombo->addAttribute("name", "user_role");
    $fldURCombo->addAttribute("required","TRUE");
    $fldURCombo->addAttribute("readonly", "TRUE");
    $fldURCombo->addAttribute("width", 240);
    $option=$fldURCombo->addChild('option',"&#160;");
    $option->addAttribute('value', ''); 
    foreach(explode(",",$_SESSION["AvailableURs"]) as $value) {
        $option=$fldURCombo->addChild('option');
        $option->addAttribute('value', $value);
        $option->addAttribute('text', $value);     
    }
    $field=$FieldSet->addChild("item");
    $field->addAttribute("type", "input");
    $field->addAttribute("name", "submit");
    $field->addAttribute("hidden", "true");        
    $field->addAttribute("value", "aaa");
    $fldbtnsubmit=$FieldSet->addChild("item");
    $fldbtnsubmit->addAttribute("type", "button");
    $fldbtnsubmit->addAttribute("name", "btnsubmit");
    $fldbtnsubmit->addAttribute("value", "Sign in");
    $fldbtnsubmit->addAttribute("offsetLeft", "75");

    $tmp=$xml->asXML();
    Header('Content-type: text/xml');
    print($xml->asXML()); 