<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/database.class.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/mysql.sessions.php');
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
        $json = file_get_contents('c:/Scripts/DataUpdater/VmCache.json');
        if ($json == "") {
            return FALSE;    
        }
        $json_data = json_decode($json);
        $vms=array();
        foreach ($json_data as $vm) {
            if (strcasecmp($vm->Owner,$user) == 0 || strcasecmp($vm->UserRole,$userRole) == 0 ) {
                $vms[] = $vm;
            } elseif ( isset($vm->GrantedToList->value)  && 
                    (array_search($userRole,$vm->GrantedToList->value) !== FALSE || array_search($user,$vm->GrantedToList->value) !== FALSE) ) {
                    $vms[] = $vm;
            } elseif (is_string($vm->GrantedToList) && (strcasecmp($vm->GrantedToList,$user) == 0 || strcasecmp($vm->GrantedToList,$userRole) == 0)) {
                $vms[] = $vm;
            }
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
        $json = file_get_contents('c:/Scripts/DataUpdater/ServiceCache.json');
        if ($json == "") {
            return FALSE;    
        }
        $json_data = json_decode($json);
        $vms=array();
        foreach ($json_data as $vm) {
            if (strcasecmp($vm->Owner,$user) == 0 || strcasecmp($vm->UserRole,$userRole) == 0 ) {
                $vms[] = $vm;
            } elseif ( isset($vm->GrantedToList->value)  && 
                    (array_search($userRole,$vm->GrantedToList->value) !== FALSE || array_search($user,$vm->GrantedToList->value) !== FALSE) ) {
                    $vms[] = $vm;
            } elseif (is_string($vm->GrantedToList) && (strcasecmp($vm->GrantedToList,$user) == 0 || strcasecmp($vm->GrantedToList,$userRole) == 0)) {
                $vms[] = $vm;
            }
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
