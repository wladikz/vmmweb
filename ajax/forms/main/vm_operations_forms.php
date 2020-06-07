<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/SessionHandler.php');
    MySQLSessionHandler::session_start();
    
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/vmm_restapi.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'actions/ReloadCache.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/misc.php');
    
    function CreateSnapshotFormXML($vmID) {
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
    function SnapshotManagerFormXML($vmID) {
        $xml = new SimpleXMLElement('<xml version="1.0"/>');
        $items=$xml->addChild("items");
        $item=$items->addChild("item");
        $item->addAttribute("type", "settings");
        $item->addAttribute("position","absolute");
        $item->addAttribute("labelWidth",80);
        $item->addAttribute("inputWidth",250);

        $field=$items->addChild("item");
        $field->addAttribute("type", "container");
        $field->addAttribute("name", "CPTree");
        $field->addAttribute("label", "Snapshots");
        $field->addAttribute("labelWidth",425);  
        $field->addAttribute("inputWidth",425);
        $field->addAttribute("inputHeight",215);
        $field->addAttribute("labelLeft",120);
        $field->addAttribute("labelTop",5);
        $field->addAttribute("inputLeft",5);
        $field->addAttribute("inputTop",21);
        
        $field=$items->addChild("item");
        $field->addAttribute("type", "input");
        $field->addAttribute("name", "vmid");
        $field->addAttribute("hidden", "true");        
        $field->addAttribute("value", $vmID);
        
        $field=$items->addChild("item");
        $field->addAttribute("type", "button");
        $field->addAttribute("name", "btnRevert");
        $field->addAttribute("disabled", "true");
        $field->addAttribute("label", "Revert");
        $field->addAttribute("value", "Revert");
        $field->addAttribute("width", 150);
        $field->addAttribute("inputWidth", 150);
        $field->addAttribute("inputLeft", 450);
        $field->addAttribute("inputTop", 5);

        $field=$items->addChild("item");
        $field->addAttribute("type", "button");
        $field->addAttribute("name", "btnDelete");
        $field->addAttribute("disabled", "true");
        $field->addAttribute("label", "Delete");
        $field->addAttribute("value", "Delete");
        $field->addAttribute("width", 150);
        $field->addAttribute("inputWidth", 150);
        $field->addAttribute("inputLeft", 450);
        $field->addAttribute("inputTop", 32);

        $field=$items->addChild("item");
        $field->addAttribute("type", "button");
        $field->addAttribute("name", "btnDelWChilds");
        $field->addAttribute("disabled", "true");
        $field->addAttribute("label", "Delete With Childs");
        $field->addAttribute("value", "Delete With Childs");
        $field->addAttribute("width", 150);
        $field->addAttribute("inputWidth", 150);
        $field->addAttribute("inputLeft", 450);
        $field->addAttribute("inputTop", 64);

        $field=$items->addChild("item");
        $field->addAttribute("type", "button");
        $field->addAttribute("name", "btnClose");
        $field->addAttribute("disabled", "false");
        $field->addAttribute("label", "Close");
        $field->addAttribute("value", "Close");
        $field->addAttribute("width", 150);
        $field->addAttribute("inputWidth", 150);
        $field->addAttribute("inputLeft", 450);
        $field->addAttribute("inputTop", 100);
        return $xml;
    }    
    function RevertSnapshotFormXML($vmID) {
        $xml = new SimpleXMLElement('<xml version="1.0"/>');
        $items=$xml->addChild("items");
        $item=$items->addChild("item");
        $item->addAttribute("type", "settings");
        $item->addAttribute("position","absolute");
        $item->addAttribute("labelWidth",80);
        $item->addAttribute("inputWidth",250);

        $field=$items->addChild("item");
        $field->addAttribute("type", "container");
        $field->addAttribute("name", "CPTree");
        $field->addAttribute("label", "Snapshots");
        $field->addAttribute("labelWidth",425);  
        $field->addAttribute("inputWidth",425);
        $field->addAttribute("inputHeight",215);
        $field->addAttribute("labelLeft",120);
        $field->addAttribute("labelTop",5);
        $field->addAttribute("inputLeft",5);
        $field->addAttribute("inputTop",21);
        
        $field=$items->addChild("item");
        $field->addAttribute("type", "input");
        $field->addAttribute("name", "vmid");
        $field->addAttribute("hidden", "true");        
        $field->addAttribute("value", $vmID);
        
        $field=$items->addChild("item");
        $field->addAttribute("type", "button");
        $field->addAttribute("name", "btnRevert");
        $field->addAttribute("disabled", "true");
        $field->addAttribute("label", "Revert");
        $field->addAttribute("value", "Revert");
        $field->addAttribute("width", 150);
        $field->addAttribute("inputWidth", 150);
        $field->addAttribute("inputLeft", 450);
        $field->addAttribute("inputTop", 5);

        $field=$items->addChild("item");
        $field->addAttribute("type", "button");
        $field->addAttribute("name", "btnClose");
        $field->addAttribute("disabled", "false");
        $field->addAttribute("label", "Close");
        $field->addAttribute("value", "Close");
        $field->addAttribute("width", 150);
        $field->addAttribute("inputWidth", 150);
        $field->addAttribute("inputLeft", 450);
        $field->addAttribute("inputTop", 32);
        return $xml;
    }    
    function DeployTemplateFormXML($vmID,$type,$fields) {
        $xml = new SimpleXMLElement('<xml version="1.0"/>');
        if (!isset($fields)) {
            return $xml;
        }
        usort($fields, function($a, $b) {
            return strcmp($a->Name, $b->Name);
        });
        $items=$xml->addChild("items");
        $item=$items->addChild("item");
        $item->addAttribute("type", "settings");
        $item->addAttribute("position","label-top");
        $item->addAttribute("labelWidth",150);
        $item->addAttribute("inputWidth",300);

        $FieldSet=$items->addChild("item");
        $FieldSet->addAttribute("type", "block");
        $FieldSet->addAttribute("blockOffset",15);
        $FieldSet->addAttribute("width","auto");
        
        $field=$FieldSet->addChild("item");
        $field->addAttribute("type", "input");
        $field->addAttribute("name", "ServiceName");
        $field->addAttribute("label", "Service Name");
        $field->addAttribute("validate",".+");
        $field->addAttribute("required","true");
    
        if ($type == "SDN") {
            $field=$FieldSet->addChild("item");
            $field->addAttribute("type", "radio");
            $field->addAttribute("name", "type");
            $field->addAttribute("label", "Full Clone");
            $field->addAttribute("checked","false");
            $field->addAttribute("value","Full");
            $field=$FieldSet->addChild("item");
            $field->addAttribute("type", "radio");
            $field->addAttribute("name", "type");
            $field->addAttribute("label", "Customized");
            $field->addAttribute("checked","true");
            $field->addAttribute("value","Custom");
            $orgFieldSet=$FieldSet;
            $FieldSet=$field;
            $itemsPerCol=intdiv(count($fields),3);
            $CurPerCol=0;
            $colCount=1;
            foreach ($fields as $value) {
                if ($itemsPerCol == $CurPerCol && $colCount <= 2){
                    $field=$FieldSet->addChild("item");
                    $field->addAttribute("type", "newcolumn");
                    $field->addAttribute("offset", "10");
                    $colCount += 1;
                    $CurPerCol = 1;
                } else {
                    $CurPerCol+=1;
                }
                $field=$FieldSet->addChild("item");
                $field->addAttribute("type", "combo");
                $field->addAttribute("readonly", "TRUE");
                $field->addAttribute("inputWidth", "auto");
                $field->addAttribute("label", htmlspecialchars($value->Name));
                $field->addAttribute("name", "fld_".bin2hex($value->Name));
                for ($x = 0; $x <= $value->MaximumCount; $x++) {
                    $option=$field->addChild("option");
                    $option->addAttribute("text", $x);
                    $option->addAttribute("value", $x);
                  } 
            }
            $FieldSet=$orgFieldSet;
        } else {
            foreach ($fields as $value) {
                $field=$FieldSet->addChild("item");
                if ( !empty($value->PossibleValues)) {
                    $field->addAttribute("type", "combo");
                    $field->addAttribute("readonly", "TRUE");
                    $field->addAttribute("inputWidth", 300);
                    $field->addAttribute("label", htmlspecialchars($value->Name));
                    $field->addAttribute("name", "fld_".bin2hex($value->Name));
                    foreach ($value->PossibleValues as $comboitem) {
                        $option=$field->addChild("option");
                        $option->addAttribute("text", $comboitem->Name);
                        $option->addAttribute("value", $comboitem->ID);
                    } 
                } else {
                    $field->addAttribute("type", "input");
                    $field->addAttribute("name", "fld_".bin2hex($value->Name));
                    $field->addAttribute("label", htmlspecialchars($value->Name));
                    $field->addAttribute("validate",".+");
                }
                if ($value->Mandatory) {
                    $field->addAttribute("required","true");
                }
                if (!empty($value->Description)) {
                    $field->addAttribute("info", "TRUE");
                    $field->addChild("userdata",$value->Description)->addAttribute("name","info");
                }
            }
        }
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
        
        
        $block=$items->addChild("item");
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
                $items= CreateSnapshotFormXML($_GET['vmID']);
                $result=$items->asXML();
                break; 
            case "SnapshotManager" :
                $items= SnapshotManagerFormXML($_GET['vmID']);
                $result=$items->asXML();
                break;
            case "RevertSnapshot" :
                $items= RevertSnapshotFormXML($_GET['vmID']);
                $result=$items->asXML();
                break;
            case "DeployTemplate" :
                $STs=GetServiceTemplates();
                $id=GetIDwoType($_GET["vmID"]);
                $indx=array_search($id,array_column($STs,'ID'));
                $data = $STs[$indx];
                $vmm=new VMM();
                $vmm->authHeader=$_SESSION["AuthToken"];
                $fields=$vmm->GetServiceTemplateDeployParams($data->Type,$data->ID);
                $items= DeployTemplateFormXML($_GET['vmID'],$data->Type,$fields);
                $result=$items->asXML();
                break;
        }

        Header('Content-type: text/xml');
        print($result);

    }    