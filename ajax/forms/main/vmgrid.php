<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/vmm_restapi.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/database.class.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/mysql.sessions.php');
    Session::session_start();
    function AddVMGridConfiguration($parent) {
        $col_name_array=array(
            "Name",
            "Status",
            "Service",
            "Computer Tier",
            "CPU Count",
            "Memory",
            "Tag"
            );
        $head=$parent->addChild('head');
        $headerFilter=array();
        foreach ($col_name_array as $value) {
            $col=$head->addChild('column',$value);
            $col->addAttribute('type',"ro");
            $col->addAttribute('width',"*");
            switch ($value) {
                case "Name":
                case "Service":
                case "Status":
                case "Computer Tier":
                    $col->addAttribute('align',"left");
                    break;
                default:
                    $col->addAttribute('align',"center");
                    break;
            }
            $col->addAttribute('sort',"str");
            switch ($value) {
                case "Name":
                case "Tag":
                    array_push($headerFilter, "#text_filter");
                    break;
                default:
                    array_push($headerFilter, "#combo_filter");
                    break;
            }
            
        }
        $tmp=$head->addChild('afterInit');
        $tmp=$tmp->addChild('call');
        $tmp->addAttribute('command','attachHeader');
        $tmp->addChild('param', implode(",", $headerFilter));
        $tmp1=$tmp->asXML();
        $tmp1;
    }
    function AddVMData($parent,$data){ 
        foreach ($data as $vm) {
            $row=$parent->addChild("row");
            $id="vm_" . $vm->ID;
            $row->addAttribute("id",$id);
            $row->addChild("cell",$vm->Name); 
            $row->addChild("cell",$vm->StatusString);
            $pattern='/_\{?[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}\}?$/';
            $svcName=preg_replace($pattern, "", $vm->ComputerTier->Service->Name); 
            $row->addChild("cell",htmlspecialchars($svcName)); 
            $row->addChild("cell",htmlspecialchars($vm->ComputerTier->Name)); 
            $row->addChild("cell",$vm->CPUCount); 
            $row->addChild("cell",$vm->Memory); 
            $row->addChild("cell",htmlspecialchars($vm->Tag)); 
        }
    }
    if (!isset($_SESSION["AuthToken"])) {
        exit;
    }
    function GetVMs(bool $reload=FALSE) {
        if (isset($_SESSION["VMs"]) && !$reload) {
            return $_SESSION["VMs"];
        } else {
            $vmm=new VMM();
            $vmm->authHeader=$_SESSION["AuthToken"];
            $vms=$vmm->GetAllVMs();
            foreach ($vms as $vm) {
                $vm->svcid=$vm->ComputerTier->Service->ID;
                $vm->ctid=$vm->ComputerTier->ID;
            }
            $_SESSION["VMs"]=$vms;
            return $vms;
        }
    }
    if (isset($_GET["id"])) {
        $xml = new SimpleXMLElement('<xml version="1.0"/>');
        $xml->addAttribute('encoding',"iso-8859-1");
        $rows= $xml->addChild('rows'); 
        AddVMGridConfiguration($rows);
        $vms=GetVMs();
        if (substr($_GET["id"],0,4) === "svc_") {
            $id=substr($_GET["id"], 4);
            $a=array_keys(array_column($vms,'svcid'),$id);
            $data = array();
            foreach ($a as $indx) {
                $data[] = $vms[$indx];
            }
        } elseif (substr($_GET["id"],0,3) === "ct_") {
            $id=substr($_GET["id"], 3);
            $a=array_keys(array_column($vms,'ctid'),$id);
            $data = array();
            foreach ($a as $indx) {
                $data[] = $vms[$indx];
            }
        } else {
            $data = $vms;
        }
        AddVMData($rows,$data);
    }
    Header('Content-type: text/xml');
    $tmp=$xml->asXML();
    print($xml->asXML());