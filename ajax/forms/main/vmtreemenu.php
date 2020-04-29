<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/XML_misc.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/misc.php');
    session_start();

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
    $userDataAny = array('isSDN' => 2,'isService' => 1, 'isComputerTier' => 1, "VMStatus" => "" );
    $userDataAnyPoweredOn = array('isSDN' => 2,'isService' => 1, 'isComputerTier' => 1, "VMStatus" => "(Running|Mixed)" );
    $userDataAnyPoweredOff = array('isSDN' => 2,'isService' => 1, 'isComputerTier' => 1, "VMStatus" => "(Stopped|Mixed)" );
    $userDataAnyService = array('isSDN' => 2,'isService' => 1, 'isComputerTier' => 0, "VMStatus" => "" );
    $userDataSdnService = array('isSDN' => 1,'isService' => 1, 'isComputerTier' => 0, "VMStatus" => "" );
    $userDataNonSdnService = array('isSDN' => 0,'isService' => 1, 'isComputerTier' => 0, "VMStatus" => "" );
    $userDataSdnCT = array('isSDN' => 1,'isService' => 0, 'isComputerTier' => 1, "VMStatus" => "" );
    $userDataNonSdnCT = array('isSDN' => 0,'isService' => 0, 'isComputerTier' => 1, "VMStatus" => "" );
    
    $xml = new SimpleXMLElement('<xml version="1.0"/>');
    $xml->addAttribute('encoding',"iso-8859-1");
    $menu=$xml->addChild("menu");
    $parent=AddMenuItem($menu, "snapshot", "Snapshot", "",GetPageURL('/images/vm/snapshot.png'),$userDataAny,TRUE);
    AddMenuItem($parent, "CreateSnapshot", "Take Snapshot", "",GetPageURL('/images/vm/vm-snapshot.png'),$userDataAny);
    AddMenuItem($parent, "RevertToSnapshot", "Revert Snapshot", "", GetPageURL('/images/vm/vm-snapshot-revert.png'),$userDataAny);            
    $parent=AddMenuItem($menu, "power", "Power", "",NULL,$userDataAny,TRUE);
    AddMenuItem($parent, "PowerOn", "Power On", "",GetPageURL('/images/vm/vm-poweron.png'),$userDataAnyPoweredOff);
    AddMenuItem($parent, "PowerOff", "Power Off", "", GetPageURL('/images/vm/vm-poweroff.png'),$userDataAnyPoweredOn);            
    AddMenuItem($menu, "Scale-Out", "Scale-Out", "",GetPageURL('/images/vm/scale-out.png') ,$userDataNonSdnCT);
    AddMenuItem($menu, "Delete", "Delete", "",GetPageURL('/images/delete.png') ,$userDataAnyService);

    Header('Content-type: text/xml');
    $tmp=$xml->asXML();
    $result=$menu->asXML();
    print($result);
