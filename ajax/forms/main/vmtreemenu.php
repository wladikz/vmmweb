<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/XML_misc.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/misc.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/SessionHandler.php');
    MySQLSessionHandler::session_start();


    function AddMenuItem($parent,$id,$caption,$link,$image,array $userdata,$isParent=FALSE) {
        $isEnabled=TRUE;
        $item=$parent->addChild("item");
        $item->addAttribute("id", $id);
        $item->addAttribute("text",$caption);
        if ($isParent) {
            $item->addChild("userdata",1)->addAttribute("name","isParent");
        } else {
            $item->addChild("userdata",0)->addAttribute("name","isParent");
        }
        if ($isParent || !empty($link) || $isEnabled) {
            $item->addAttribute("enabled","true");
            
        } else {
            $item->addAttribute("enabled","false");
        }
        if ($image !== NULL) {
            $item->addAttribute("img",$image);
        }
        if (!empty($link)) {
            $item->addChild("href")->addCData($link);
        }
        if (!empty($userdata)) {
            foreach ($userdata as $key => $value) {
                $item->addChild("userdata",$value)->addAttribute("name",$key);    
            }                
        }
        return $item;
    }
    function ConfigureUserData($isSDN=2,$ObjectType=15,$ObjectStatus="") {
        if ($isSDN < 0) {
            $isSDN=2;
        }
        if ($ObjectType <0) {
            $ObjectType = 15;
        }
        $userData = array('isSDN' => $isSDN,'objType' => $ObjectType, 'ObjStatus' => $ObjectStatus );
        return $userData;
    }

    $udOTSvc        = 1;
    $udOTCt         = 2;
    $udOTVm         = 4;
    $udOTCp         = 8;
    $udSDNYes       = 1;
    $udSDNNo        = 0;
    $udSDNAny       = 2;
    $udObjStatOn    = "Running";
    $udObjStatOff   = "Stopped";
    $udObjStatMixed = "Mixed";
    
    $xml = new SimpleXMLElement('<xml version="1.0"/>');
    $xml->addAttribute('encoding',"iso-8859-1");
    $menu=$xml->addChild("menu");
    $ud=ConfigureUserData(-1,($udOTSvc | $udOTCt | $udOTVm));
    $parent=AddMenuItem($menu, "snapshot", "Snapshot", "",GetPageURL('/images/vm/snapshot.png'),$ud,TRUE);
    AddMenuItem($parent, "CreateSnapshot", "Take Snapshot", "",GetPageURL('/images/vm/vm-snapshot.png'),$ud);
    $ud=ConfigureUserData(-1,($udOTSvc | $udOTVm));
    AddMenuItem($parent, "RevertSnapshot", "Revert Snapshot", "", GetPageURL('/images/vm/vm-snapshot-revert.png'),$ud); 
    $ud=ConfigureUserData(-1,($udOTSvc | $udOTVm));
    AddMenuItem($parent, "SnapshotManager", "SnapshotManager", "", GetPageURL('/images/vm/vmm/Snapshot.png'),$ud); 

    $ud=ConfigureUserData(-1,($udOTSvc | $udOTCt | $udOTVm));           
    $parent=AddMenuItem($menu, "power", "Power", "",NULL,$ud,TRUE);
    $ud=ConfigureUserData(-1,($udOTSvc | $udOTCt | $udOTVm),"({$udObjStatOff}|{$udObjStatMixed})");
    AddMenuItem($parent, "PowerOn", "Power On", "",GetPageURL('/images/vm/vm-poweron.png'),$ud);
    $ud=ConfigureUserData(-1,($udOTSvc | $udOTCt | $udOTVm),"({$udObjStatOn}|{$udObjStatMixed})");
    AddMenuItem($parent, "PowerOff", "Power Off", "", GetPageURL('/images/vm/vm-poweroff.png'),$ud);
    $ud=ConfigureUserData($udSDNNo,$udOTCt);
    AddMenuItem($menu, "Scale-Out", "Scale-Out", "",GetPageURL('/images/vm/scale-out.png') ,$ud);
    $ud=ConfigureUserData(-1,($udOTSvc | $udOTVm));
    AddMenuItem($menu, "DeleteItem", "Delete", "",GetPageURL('/images/delete.png') ,$ud);

    Header('Content-type: text/xml');
    $tmp=$xml->asXML();
    $result=$menu->asXML();
    print($result);
