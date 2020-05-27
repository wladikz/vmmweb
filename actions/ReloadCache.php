<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/configuration.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/SessionHandler.php');
    

    if(session_status() !== PHP_SESSION_ACTIVE) {
        MySQLSessionHandler::session_start();
    }
    function UpdateVMs() {
        if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
            $user=$_SESSION["username"];
        } else {
            return FALSE;    
        }
        if (isset($_SESSION["userRole"]) && !empty($_SESSION["userRole"])) {
            $userRole=$_SESSION["userRole"];
        } else {
            return FALSE;    
        }
        if (isset($_SESSION["VMs"])) {
            $curVMs=$_SESSION["VMs"];
        } else {
            $curVMs=array();
        }
        $conn = new PDOWrapper\DB(CacheDbServer, CacheDbDatabase, CacheDbUser, CacheDbPassword);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        $vms=array();
        try {
            $sql = "SELECT * FROM VMCache WHERE Owner=:user OR UserRole=:userRole OR GrantedToList like :userRole1";
            $params = array(
                "user" => $user,
                "userRole" => $userRole,
                "userRole1" =>"%|".$userRole."|%"
            );
            $result = $conn->query($sql,$params);
            if (is_array($result) &&  count($result) > 0) {
                // output data of each row
                foreach($result as $rowData) {
                    $vm=json_decode($rowData["Data"]);
                    $vm->LastChanged=strtotime($rowData["LastChanged"]);
                    $vms[] = $vm;
                }
            }            
        } finally {
            $conn->CloseConnection();
        }
        foreach ($vms as $item) {
            if ($item->ComputerTier != null) {
                $item->svcid=$item->ComputerTier->Service->ID;
                $item->ctid=$item->ComputerTier->ID;
            }
        }
        if ($vms != $curVMs) {
            $_SESSION["VMs"]=$vms;
            return TRUE;
        } else {
            return FALSE;
        }
    }    
    function UpdateServices() {
        if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
            $user=$_SESSION["username"];
        } else {
            return FALSE;    
        }
        if (isset($_SESSION["userRole"]) && !empty($_SESSION["userRole"])) {
            $userRole=$_SESSION["userRole"];
        } else {
            return FALSE;    
        }
        if (isset($_SESSION["Services"])) {
            $curVMs=$_SESSION["Services"];
        } else {
            $curVMs=array();
        }


        $conn = new PDOWrapper\DB(CacheDbServer, CacheDbDatabase, CacheDbUser, CacheDbPassword);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        $vms=array();
        try {
            $sql = "SELECT * FROM SvcCache WHERE Owner=:user OR UserRole=:userRole OR GrantedToList like :userRole1";
            $params = array(
                "user" => $user,
                "userRole" => $userRole,
                "userRole1" =>"%|".$userRole."|%"
            );
            $result = $conn->query($sql,$params);            
            if (is_array($result) &&  count($result) > 0) {
                // output data of each row
                foreach($result as $rowData) {
                    $vm=json_decode($rowData["Data"]);
                    $vm->LastChanged=strtotime($rowData["LastChanged"]);
                    $vms[] = $vm;
                }
            }            
        } finally {
            $conn->CloseConnection();
        }
        if ($vms != $curVMs) {
            $_SESSION["Services"]=$vms;
            return TRUE;
        } else {
            return FALSE;
        }
    } 
    function UpdateServiceTemplates() {
        if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
            $user=$_SESSION["username"];
        } else {
            return FALSE;    
        }
        if (isset($_SESSION["userRole"]) && !empty($_SESSION["userRole"])) {
            $userRole=$_SESSION["userRole"];
        } else {
            return FALSE;    
        }
        if (isset($_SESSION["ServiceTemplate"])) {
            $curVMs=$_SESSION["ServiceTemplate"];
        } else {
            $curVMs=array();
        }
        $conn = new PDOWrapper\DB(CacheDbServer, CacheDbDatabase, CacheDbUser, CacheDbPassword);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        $vms=array();
        try {
            $sql = "SELECT * FROM SvcTmplCache WHERE Owner=:user OR UserRole=:userRole OR GrantedToList like :userRole1";
            $params = array(
                "user" => $user,
                "userRole" => $userRole,
                "userRole1" =>"%|".$userRole."|%"
            );
            $result = $conn->query($sql,$params);
            if (is_array($result) &&  count($result) > 0) {
                // output data of each row
                foreach($result as $rowData) {
                    $vm=json_decode($rowData["Data"]);
                    $vm->LastChanged=strtotime($rowData["LastChanged"]);
                    $vms[] = $vm;
                }
            }            
        } finally {
            $conn->CloseConnection();
        }
        if ($vms != $curVMs) {
            $_SESSION["ServiceTemplate"]=$vms;
            return TRUE;
        } else {
            return FALSE;
        }
    }
    function GetServices(bool $reload=FALSE) {
        if (isset($_SESSION["Services"]) && !$reload) {
            return $_SESSION["Services"];
        } else {
            UpdateServices();
            return $_SESSION["Services"];
        }
    }
    function GetVMs(bool $reload=FALSE) {
        if (isset($_SESSION["VMs"]) && !$reload) {
            return $_SESSION["VMs"];
        } else {
            UpdateVMs();
            return $_SESSION["VMs"];
        }
    }
    function GetServiceTemplates(bool $reload=FALSE) {
        if (isset($_SESSION["ServiceTemplate"]) && !$reload) {
            return $_SESSION["ServiceTemplate"];
        } else {
            UpdateServiceTemplates();
            return $_SESSION["ServiceTemplate"];
        }
    }

    if (str_replace('\\', '/', __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
        $vmsUpdated=UpdateVMs() ? 'true' : 'false';
        $svcUpdated=UpdateServices() ? 'true' : 'false';
        $svcTUpdated=UpdateServiceTemplates() ? 'true' : 'false';
        $xml = new SimpleXMLElement('<xml version="1.0"/>');
        $xml->addAttribute('encoding',"iso-8859-1");
        $item = $xml->addChild('VMsUpdated',$vmsUpdated);
        $item = $xml->addChild('SvcUpdated',$svcUpdated);
        $item = $xml->addChild('SvcTUpdated',$svcTUpdated);
        Header('Content-type: text/xml');
        $tmp=$xml->asXML();
        print($xml->asXML());
    }
