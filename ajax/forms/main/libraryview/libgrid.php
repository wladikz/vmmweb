<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/SessionHandler.php');
    MySQLSessionHandler::session_start();

    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/vmm_restapi.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'actions/ReloadCache.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/misc.php');
    function AddLibGridConfiguration($parent) {
        $col_name_array=array(
            "Name",
            "Minimum Count",
            "Maximum Count",
            "CPU Count",
            "Memory"
        );
        $head=$parent->addChild('head');
        $headerFilter=array();
        foreach ($col_name_array as $value) {
            $col=$head->addChild('column',$value);
            $col->addAttribute('type',"ro");
            switch ($value) {
                case "Name":
                    $col->addAttribute('align',"left");
                    break;
                default:
                    $col->addAttribute('align',"center");
                    break;
            }
        }
    }
    function AddSTData($parent,$data){ 
        if (isset($data->ComputerTiers->value)) {
            $cts=$data->ComputerTiers->value;
        } else {
            $cts=[];
            $cts[]=$data->ComputerTiers;
        }
        
        usort($cts, function($a, $b) {
                return strcmp($a->Name, $b->Name);
            });
        foreach ($cts as $ct) {
            $row=$parent->addChild("row");
            $row->addChild("cell",htmlspecialchars($ct->Name)); 
            $row->addChild("cell",$ct->InstanceMinimumCount);
            $row->addChild("cell",$ct->InstanceMaximumCount);
            $row->addChild("cell",$ct->CPUCount); 
            $row->addChild("cell",$ct->Memory); 
        }
    }
    if (!isset($_SESSION["AuthToken"])) {
        exit;
    }
    $xml = new SimpleXMLElement('<xml version="1.0"/>');
    $xml->addAttribute('encoding',"iso-8859-1");
    $rows= $xml->addChild('rows'); 
    if (isset($_GET["id"]) && !is_numeric($_GET["id"])) {
        AddLibGridConfiguration($rows);
        $STs=GetServiceTemplates();
        $id=GetIDwoType($_GET["id"]);
        $indx=array_search($id,array_column($STs,'ID'));
        $data = $STs[$indx];
        AddSTData($rows,$data);
    }
    Header('Content-type: text/xml');
    $tmp=$xml->asXML();
    print($xml->asXML());