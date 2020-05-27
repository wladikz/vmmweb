<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/vmm_restapi.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/configuration.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/SessionHandler.php');
    MySQLSessionHandler::session_start();
    
    if (!isset($_SESSION["AuthToken"])) {
        exit;
    }
    function AddMainWebJobsGridConfiguration($parent) {
        $col_name_array=array(
            "Name",
            "Status",
            "Start Date",
            "End Date",
            "Object Name"
            );
        $head=$parent->addChild('head');
        $headerFilter=array();
        foreach ($col_name_array as $value) {
            $col=$head->addChild('column',$value);
            $col->addAttribute('type',"ro");
            switch ($value) {
                case 'Status':
                    $col->addAttribute('width',"100");
                    break;
                case 'Name':
                    $col->addAttribute('width',"200");
                    break;
                case 'Object Name':
                    $col->addAttribute('width',"250");
                    break;
                case 'Start Date':
                case 'End Date':
                    $col->addAttribute('width',"160");
                    break;
                default:
                    $col->addAttribute('width',"*");
                    break;
            }
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
    function AddDetailWebJobsGridConfiguration($parent){
        $col_name_array=array(
            "ID",
            "Name",
            "Status",
            "Start Date",
            "End Date",
            "Object Name"
            );
        $head=$parent->addChild('head');
        $headerFilter=array();
        foreach ($col_name_array as $value) {
            $col=$head->addChild('column',$value);
            $col->addAttribute('type',"ro");
            switch ($value) {
                case 'Status':
                    $col->addAttribute('width',"100");
                    break;
                case 'Name':
                    $col->addAttribute('width',"200");
                    break;
                case 'Object Name':
                    $col->addAttribute('width',"250");
                    break;
                case 'Start Date':
                case 'End Date':
                    $col->addAttribute('width',"160");
                    break;
                default:
                    $col->addAttribute('width',"*");
                    break;
            }
            $col->addAttribute('align',"left");
        }        
    }
    function AddMessagesWebJobsGridConfiguration($parent) {
        $col_name_array=array(
            "Type",
            "Date",
            "Message",
            );
        $head=$parent->addChild('head');
        $headerFilter=array();
        foreach ($col_name_array as $value) {
            $col=$head->addChild('column',$value);
            $col->addAttribute('type',"ro");
            switch ($value) {
                case 'Type':
                    $col->addAttribute('width',"60");
                    break;
                case 'Date':
                    $col->addAttribute('width',"160");
                    break;
                default:
                    $col->addAttribute('width',"*");
                    break;
            }
            
            $col->addAttribute('align',"left");
        }               
    }
    function AddMainWebJobsGridData($parent){
        global $JobsDbServer, $JobsDbUser, $JobsDbPassword, $JobsDbDatabase;
        $conn = new mysqli($JobsDbServer, $JobsDbUser, $JobsDbPassword, $JobsDbDatabase);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        try {
            $sql = "SELECT * FROM log Where ParentID IS NULL";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // output data of each row
                while($rowData = $result->fetch_assoc()) {
                    $row=$parent->addChild("row");
                    $row->addAttribute("id",$rowData['ID']);
                    $row->addChild("cell",$rowData['Name']);
                    $row->addChild("cell",$rowData['Status']);
                    $row->addChild("cell",$rowData['StartDate']);
                    $row->addChild("cell",$rowData['EndDate']);
                    $row->addChild("cell",RemoveGUIDFromName($rowData['ObjectName']));
                }
            }            
        } finally {
            $conn->close();
        }
    }
    function AddDetaiWebJobsGridData($parent,$id){
        global $JobsDbServer, $JobsDbUser, $JobsDbPassword, $JobsDbDatabase;
        $conn = new mysqli($JobsDbServer, $JobsDbUser, $JobsDbPassword, $JobsDbDatabase);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        try {
            $sql = "SELECT * FROM log Where id=".$id." or ParentID=".$id." ORDER BY ID";
            $result = $conn->query($sql);
            $intID=0;
            if ($result->num_rows > 0) {
                // output data of each row
                while($rowData = $result->fetch_assoc()) {
                    $row=$parent->addChild("row");
                    $row->addAttribute("id",$rowData['ID']);
                    if ($intID == 0) {
                        $row->addChild("cell","{$id}");
                    } else {
                        $row->addChild("cell","{$id}.{$intID}");
                    }
                    $row->addChild("cell",$rowData['Name']);
                    $row->addChild("cell",$rowData['Status']);
                    $row->addChild("cell",$rowData['StartDate']);
                    $row->addChild("cell",$rowData['EndDate']);
                    $row->addChild("cell",RemoveGUIDFromName($rowData['ObjectName']));
                    $intID+=1;
                }
            }            
        } finally {
            $conn->close();
        }
    }
    function AddMessagesWebJobsGridData($parent,$id) {
        global $JobsDbServer, $JobsDbUser, $JobsDbPassword, $JobsDbDatabase;
        $conn = new mysqli($JobsDbServer, $JobsDbUser, $JobsDbPassword, $JobsDbDatabase);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        try {
            $sql = "SELECT b.* FROM log a INNER JOIN log_messages b ON (a.ID = b.LogID) WHERE (a.ID = " . $id . " OR a.ParentID = ". $id .") ORDER BY b.id ASC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($rowData = $result->fetch_assoc()) {
                    $row=$parent->addChild("row");
                    $row->addAttribute("id",$rowData['id']);
                    $row->addChild("cell",$rowData['MessageType']);
                    $row->addChild("cell",$rowData['CreateDate']);
                    $row->addChild("cell",$rowData['Message']);
                }
            }            
        } finally {
            $conn->close();
        }
    }
    if (isset($_GET["type"])) {
        $xml = new SimpleXMLElement('<xml version="1.0"/>');
        $xml->addAttribute('encoding',"iso-8859-1");
        $rows= $xml->addChild('rows');
        switch ($_GET["grid"]) {
            case 'main':
                if ($_GET['type'] === "weblog") {
                    AddMainWebJobsGridConfiguration($rows);
                    AddMainWebJobsGridData($rows);
                } 
                break;
            case 'detail':
                if ($_GET['type'] === "weblog") {
                    AddDetailWebJobsGridConfiguration($rows);
                    AddDetaiWebJobsGridData($rows,$_GET['id']);
                }
                break;
            case 'messages':
                if ($_GET['type'] === "weblog") {
                    AddMessagesWebJobsGridConfiguration($rows);
                    AddMessagesWebJobsGridData($rows,$_GET['id']);
                }
                break;
            default:
                # code...
                break;
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