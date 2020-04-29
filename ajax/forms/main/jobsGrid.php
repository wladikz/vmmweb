<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/vmm_restapi.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/configuration.php');
    
    session_start();
    if (!isset($_SESSION["AuthToken"])) {
        exit;
    }
    function AddMainWebJobsGridConfiguration($parent) {
        $col_name_array=array(
            "Name",
            "Start Date",
            "End Date",
            "Status"
            );
        $head=$parent->addChild('head');
        $headerFilter=array();
        foreach ($col_name_array as $value) {
            $col=$head->addChild('column',$value);
            $col->addAttribute('type',"ro");
            $col->addAttribute('width',"*");
            $col->addAttribute('align',"left");
            switch ($value) {
                case 'Start Date':
                case 'End Date':
                    $col->addAttribute('sort',"date");
                    break;
                default:
                    $col->addAttribute('sort',"str");
                    break;
            }
            switch ($value) {
                case 'Start Date':
                case 'End Date':
                case "Parent Task":
                    array_push($headerFilter, "");
                    break;
                default:
                    array_push($headerFilter, "#combo_filter");
                    break;
            }
            
        }
        $afterInit=$head->addChild('afterInit');
        $tmp=$afterInit->addChild('call');
        $tmp->addAttribute('command','attachHeader');
        $tmp->addChild('param', implode(",", $headerFilter));
    }
    function AddMainWebJobsGridData($parent){
        global $dbServer, $dbUser, $dbPassword, $dbDatabase;
        $conn = new mysqli($dbServer, $dbUser, $dbPassword, $dbDatabase);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        try {
            $sql = "SELECT ID, Name, StartDate, EndDate, Status FROM log Where ParentID IS NULL";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // output data of each row
                while($rowData = $result->fetch_assoc()) {
                    $row=$parent->addChild("row");
                    $row->addAttribute("id",$rowData['ID']);
                    $row->addChild("cell",$rowData['Name']);
                    $row->addChild("cell",$rowData['StartDate']);
                    $row->addChild("cell",$rowData['EndDate']);
                    $row->addChild("cell",$rowData['Status']);
                }
            }            
        } finally {
            $conn->close();
        }
    }
    if (isset($_GET["id"])) {
        $xml = new SimpleXMLElement('<xml version="1.0"/>');
        $xml->addAttribute('encoding',"iso-8859-1");
        $rows= $xml->addChild('rows');
        if ($_GET['id'] === "weblog" && $_GET["grid"] ==='main' ) {
            AddMainWebJobsGridConfiguration($rows);
            AddMainWebJobsGridData($rows);
        } 
        
/*         if (substr($_GET["id"],0,4) === "svc_") {
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
*/        
    }
    Header('Content-type: text/xml');
    $tmp=$xml->asXML();
    print($xml->asXML());