<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/vmm_restapi.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/database.class.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/mysql.sessions.php');
    Session::session_start();
    function CreateServiceSubtree($service,$parent) {
        $svcItem = $parent->addChild("item");
        $svcItem->addAttribute("id","svc_" . $service->ID);
        $pattern='/_\{?[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}\}?$/';
        $svcName=preg_replace($pattern, "", $service->Name);
        $svcItem->addAttribute("text",$svcName);
        $svcItem->addAttribute("kids","true");  
        foreach($service->ComputerTiers as $ComputerTier) {
            $item=$svcItem->addChild("item");
            $item->addAttribute("id","ct_" . $ComputerTier->ID);
            $item->addAttribute("text",$ComputerTier->Name);
            $item->addAttribute("kids","false");             
        }
              
    }
    function CreateServiceChild($service,$parent,$tree_node_status) {
        $svcItem = $parent->addChild("item");
        $id="svc_" . $service->ID;
        $svcItem->addAttribute("id",$id);
        $pattern='/_\{?[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}\}?$/';
        $svcName=preg_replace($pattern, "", $service->Name);
        $svcItem->addAttribute("text",$svcName);
        $svcItem->addAttribute("child",1);
        $svcItem->addAttribute('im0','vm/Service.png');
        $svcItem->addAttribute('im1','vm/Service.png');
        $svcItem->addAttribute('im2','vm/Service.png');
        SetItemStatus($id,$svcItem,$tree_node_status);
        $svcItem->addChild("userdata",$service->IsSDN)->addAttribute("name","isSDN");
        $svcItem->addChild("userdata",$service->VMStatus)->addAttribute("name","VMStatus");
    }
    function CreateComputerTierChild($service,$ComputerTier,$parent,$tree_node_status) {
        if ($service->IsSDN === true && $ComputerTier->InstanceCurrentCount === 0) {
            return;
        }
        $item=$parent->addChild("item");
        $id="ct_" . $ComputerTier->ID;
        SetItemStatus($id,$item,$tree_node_status);
        $item->addAttribute("id",$id);
        $item->addAttribute("text",$ComputerTier->Name);
        $item->addAttribute("child","0");             
        $item->addAttribute('im0','vm/ComputerTier.png');
        $item->addAttribute('im1','vm/ComputerTier.png');
        $item->addAttribute('im2','vm/ComputerTier.png');
        $item->addChild("userdata",$ComputerTier->InstanceMaximumCount)->addAttribute("name","MaximumCount");
        $item->addChild("userdata",$ComputerTier->InstanceMinimumCount)->addAttribute("name","MinimumCount");
        $item->addChild("userdata",$ComputerTier->InstanceCurrentCount)->addAttribute("name","CurrentCount");
        $item->addChild("userdata",$ComputerTier->VMStatus)->addAttribute("name","VMStatus");
    }
    function GetServices(bool $reload=FALSE) {
        if (isset($_SESSION["Services"]) && !$reload) {
            return $_SESSION["Services"];
        } else {
            $vmm=new VMM();
            $vmm->authHeader=$_SESSION["AuthToken"];
            $services=$vmm->GetAllServices();
            $_SESSION["Services"]=$services;
            return $services;
        }
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
    function SetItemStatus($id,$item,$tree_node_status){
        if (array_search($id, $tree_node_status) !== false) {
            $item->addAttribute('open','1');
        }
    }
    if (!isset($_SESSION["AuthToken"])) {
        exit;
    }
    if (!empty($_SESSION['VMS_tree_node_status'])) {
        $tree_node_status=$_SESSION['VMS_tree_node_status'];
    } else {
        $tree_node_status=[];
    }
    if (isset($_GET['mode'])) {
        $key = array_search($_GET['id'], $tree_node_status);
        switch ($_GET['mode']) {
            case 0:
                if($key === false) {
                    array_push($tree_node_status, $_GET['id']);
                }
                break;
            case 1:
                if($key !== false) {
                    unset($tree_node_status[$key]);
                }                    
                break;
        }
        $_SESSION['VMS_tree_node_status']=$tree_node_status;
        exit;
    }
    if ( isset($_GET["id"]) ) {
        $xml = new SimpleXMLElement('<xml version="1.0"/>');
        $xml->addAttribute('encoding',"iso-8859-1");
        $tree = $xml->addChild('tree');
        $tree->addAttribute('id',$_GET['id']);
        if (is_numeric($_GET["id"]) && $_GET["id"] == 0) {
            $tmp=GetServices(TRUE);
            $tmp=GetVMs(TRUE);
            $item = $tree->addChild('item');
            $item->addAttribute("id",1);
            $item->addAttribute("text","All");
            $item->addAttribute("child","1");
            $item->addAttribute('open','1');
        } else {
            if (is_numeric($_GET["id"]) && $_GET["id"] == 1) {
                $services=GetServices();
                foreach($services as $service) {
                    CreateServiceChild($service,$tree,$tree_node_status);
                }
            }elseif (substr($_GET["id"],0,4) === "svc_") {
                $services=GetServices();
                $id=substr($_GET["id"], 4);
                $indx=array_search($id,array_column($services,'ID'));
                foreach($services[$indx]->ComputerTiers as $ComputerTier) {
                    CreateComputerTierChild($services[$indx],$ComputerTier,$tree,$tree_node_status);
                }                
            } 
        } 
    }
    $_SESSION['VMS_tree_node_status']=$tree_node_status;
    Header('Content-type: text/xml');
    $tmp=$xml->asXML();
    print($xml->asXML());
