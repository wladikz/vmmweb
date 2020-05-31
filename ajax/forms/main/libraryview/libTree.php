<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/SessionHandler.php');
    MySQLSessionHandler::session_start();
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'actions/ReloadCache.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/misc.php');

    function CreateServiceTemplateChild($service,$parent,$tree_node_status) {
        $svcItem = $parent->addChild("item");
        $id="st_" . $service->ID;
        $svcItem->addAttribute("id",$id);
        $pattern='/_\{?[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}\}?$/';
        $svcName=preg_replace($pattern, "", $service->Name);
        $svcItem->addAttribute("text",$svcName);
        $svcItem->addAttribute("child",0);
        $svcItem->addAttribute('im0','vm/ServiceTemplate.png');
        $svcItem->addAttribute('im1','vm/ServiceTemplate.png');
        $svcItem->addAttribute('im2','vm/ServiceTemplate.png');
        SetItemStatus($id,$svcItem,$tree_node_status);
        $svcItem->addChild("userdata",$service->Type)->addAttribute("name","Type");
    }    
    function SetItemStatus($id,$item,$tree_node_status){
        if (array_search($id, $tree_node_status) !== false) {
            $item->addAttribute('open','1');
        }
    }    
    if (!isset($_SESSION["AuthToken"])) {
        exit;
    }
    if (!empty($_SESSION['Lib_tree_node_status'])) {
        $tree_node_status=$_SESSION['Lib_tree_node_status'];
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
        $_SESSION['Lib_tree_node_status']=$tree_node_status;
        exit;
    }
    if ( isset($_GET["id"]) ) {
        $xml = new SimpleXMLElement('<xml version="1.0"/>');
        $xml->addAttribute('encoding',"iso-8859-1");
        $tree = $xml->addChild('tree');
        $tree->addAttribute('id',$_GET['id']);
        if (is_numeric($_GET["id"])) {
            switch ($_GET["id"]) {
                case 0:
                    $tmp=GetServices(TRUE);
                    $tmp=GetVMs(TRUE);
                    $item = $tree->addChild('item');
                    $item->addAttribute("id",1);
                    $item->addAttribute("text","Regular");
                    $item->addAttribute("child","1");
                    $item->addAttribute('open','1');
                    //$item->addAttribute('select','no');
                    $item = $tree->addChild('item');
                    $item->addAttribute("id",2);
                    $item->addAttribute("text","Fenced");
                    $item->addAttribute("child","1");
                    $item->addAttribute('open','1');
                    //$item->addAttribute('select','no');
                    break;
                case 1:
                    $services=GetServiceTemplates();
                    $svc4tree=array();
                    foreach($services as $item) {
                        if ($item->Type == "Regular") {
                            $svc4tree[]=$item;
                        }
                    }
                    foreach($svc4tree as $service) {
                        CreateServiceTemplateChild($service,$tree,$tree_node_status);
                    }
                    break;
                case 2:
                    $services=GetServiceTemplates();
                    $svc4tree=array();
                    foreach($services as $item) {
                        if ($item->Type == "SDN") {
                            $svc4tree[]=$item;
                        }
                    }
                    foreach($svc4tree as $service) {
                        CreateServiceTemplateChild($service,$tree,$tree_node_status);
                    }
                    break;
    
            }
        } else {
            if (GetTypeFromID($_GET["id"]) === "svc_") {
                $services=GetServices();
                $id=GetIDwoType($_GET["id"]);
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
