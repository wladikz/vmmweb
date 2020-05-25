<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/database.class.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/mysql.sessions.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/configuration.php');

    if(session_status() !== PHP_SESSION_ACTIVE) {
        Session::session_start();
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
        $CacheDbServer=CacheDbServer;
        $CacheDbUser=CacheDbUser;
        $CacheDbPassword=CacheDbPassword;
        $CacheDbDatabase=CacheDbDatabase;
        $conn = new mysqli($CacheDbServer, $CacheDbUser, $CacheDbPassword, $CacheDbDatabase);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        $vms=array();
        try {
            $sql = "SELECT * FROM VMCache WHERE Owner='".$user."' OR UserRole='".$userRole."' OR GrantedToList like '%|".$userRole."|%'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // output data of each row
                while($rowData = $result->fetch_assoc()) {
                    $vm=json_decode($rowData["Data"]);
                    $vm->LastChanged=$rowData["LastChanged"];
                    $vms[] = $vm;
                }
            }            
        } finally {
            $conn->close();
        }
        foreach ($vms as $vm) {
            if ($vm->ComputerTier != null) {
                $vm->svcid=$vm->ComputerTier->Service->ID;
                $vm->ctid=$vm->ComputerTier->ID;
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
        $CacheDbServer=CacheDbServer;
        $CacheDbUser=CacheDbUser;
        $CacheDbPassword=CacheDbPassword;
        $CacheDbDatabase=CacheDbDatabase;
        $conn = new mysqli($CacheDbServer, $CacheDbUser, $CacheDbPassword, $CacheDbDatabase);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        $vms=array();
        try {
            $sql = "SELECT * FROM SvcCache WHERE Owner='".$user."' OR UserRole='".$userRole."' OR GrantedToList like '%|".$userRole."|%'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // output data of each row
                while($rowData = $result->fetch_assoc()) {
                    $vm=json_decode($rowData["Data"]);
                    $vm->LastChanged=$rowData["LastChanged"];
                    $vms[] = $vm;
                }
            }            
        } finally {
            $conn->close();
        }
        if ($vms != $curVMs) {
            $_SESSION["Services"]=$vms;
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
    if (str_replace('\\', '/', __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
        $vmsUpdated=UpdateVMs() ? 'true' : 'false';
        $svcUpdated=UpdateServices() ? 'true' : 'false';
        $xml = new SimpleXMLElement('<xml version="1.0"/>');
        $xml->addAttribute('encoding',"iso-8859-1");
        $item = $xml->addChild('VMsUpdated',$vmsUpdated);
        $item = $xml->addChild('SvcUpdated',$svcUpdated);
        Header('Content-type: text/xml');
        $tmp=$xml->asXML();
        print($xml->asXML());
    }
