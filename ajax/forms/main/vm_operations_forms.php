<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/database.class.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/mysql.sessions.php');
    Session::session_start();
    
    function CreateAddSnapshotFormXML($vmID) {
        $xml = new SimpleXMLElement('<xml version="1.0"/>');
        $items=$xml->addChild("items");
        $item=$items->addChild("item");
        $item->addAttribute("type", "settings");
        $item->addAttribute("position","label-top");
        $item->addAttribute("labelWidth",100);
        $item->addAttribute("inputWidth",300);

        $FieldSet=$items->addChild("item");
        $FieldSet->addAttribute("type", "block");
        $FieldSet->addAttribute("blockOffset",15);
        //$FieldSet->addAttribute("offsetTop",15);
        $FieldSet->addAttribute("width","auto");
          
        $field=$FieldSet->addChild("item");
        $field->addAttribute("type", "input");
        $field->addAttribute("name", "SnapshotName");
        $field->addAttribute("label", "Name");
        $field->addAttribute("validate",".+");
        $field->addAttribute("required","true");  
        $field->addAttribute("inputWidth",300);  
        
        
        $field=$FieldSet->addChild("item");
        $field->addAttribute("type", "input");
        $field->addAttribute("label", "Description");
        $field->addAttribute("name", "Description");
        $field->addAttribute("inputWidth",300);

        $field=$FieldSet->addChild("item");
        $field->addAttribute("type", "checkbox");
        $field->addAttribute("label", "Snapshot with virtual machine memory");
        $field->addAttribute("name", "WithMemory");
        $field->addAttribute("position","label-right");
        $field->addAttribute("labelWidth","auto");

        $field=$FieldSet->addChild("item");
        $field->addAttribute("type", "input");
        $field->addAttribute("name", "submit");
        $field->addAttribute("hidden", "true");        
        $field->addAttribute("value", "aaa");

        $field=$FieldSet->addChild("item");
        $field->addAttribute("type", "input");
        $field->addAttribute("name", "vmid");
        $field->addAttribute("hidden", "true");        
        $field->addAttribute("value", $vmID);
        
        
        $block=$FieldSet->addChild("item");
        $block->addAttribute("type", "block");
        $block->addAttribute("position", "label-top");
        $block->addAttribute("blockOffset",1);
        if ( strpos($_SERVER['HTTP_USER_AGENT'],'Firefox') !== TRUE) {
            $block->addAttribute("offsetLeft",1);
        }
        $field=$block->addChild("item");
        $field->addAttribute("type", "button");
        $field->addAttribute("name", "btnsubmit");
        $field->addAttribute("disabled", "true");
        $field->addAttribute("value", "Ok");
        $field->addAttribute("width", 100);
        $field->addAttribute("offsetLeft", 30);
        $field=$block->addChild("item");
        $field->addAttribute("type", "newcolumn");

        $field=$block->addChild("item");
        $field->addAttribute("type", "button");
        $field->addAttribute("name", "btncancel");
        $field->addAttribute("value", "cancel");
        $field->addAttribute("width", 100);
        $field->addAttribute("offsetLeft", 30);
        
        return $xml;
    }
    $result="";
    if (!empty($_GET['formtype'])) {
        switch ($_GET['formtype']) {
            case "CreateSnapshot" :
                $items= CreateAddSnapshotFormXML($_GET['vmID']);
                $result=$items->asXML();
                break;                
        }

        Header('Content-type: text/xml');
        print($result);

    }    