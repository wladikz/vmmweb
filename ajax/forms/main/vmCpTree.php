<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/database.class.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/mysql.sessions.php');
    Session::session_start();

    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/vmm_restapi.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/misc.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'actions/ReloadCache.php');
    
    if (!isset($_SESSION["AuthToken"])) {
        exit;
    }
    function CpArr2FlatArr($parentCpID,$CPs,$parentText="",$NewParentID=""){
        $result=array();
        $a=array_keys(array_column($CPs,'ParentCheckpointID'),$parentCpID);
        $selfid=1;
        foreach ($a as $indx) {
            $self=new stdClass();
            if($NewParentID == "") {
                $self->ID=(string)$selfid;
            } else {
                $self->ID="{$NewParentID}.{$selfid}";
            }
            $self->ParentCheckpointID=$NewParentID;
            $SelfText=$CPs[$indx]->Name;
            if ($parentText !== "") {
                $SelfText="{$parentText}-->$SelfText";
            }
            $self->Path=$SelfText;
            $self->Name=$CPs[$indx]->Name;
            $self->CheckpointID=$self->ID;
            $self->HasSavedState=FALSE;
            $result[]=$self;
            $tmp=CpArr2FlatArr($CPs[$indx]->CheckpointID,$CPs,$SelfText,$self->ID);
            $result=array_merge($result,$tmp);
            $selfid=$selfid+1;
        }
        return $result;
    }
    function GetPathArr($cpArr){
        $result=array();
        foreach ($cpArr as $flValue) {
            $result[]=$flValue->Path;
        }
        return $result;
    }
    function CleanCpArray($cpArr,$refArr) {
        $result=array();
        foreach ($refArr as $refValue) {
            $a=array_keys(array_column($cpArr,'Path'),$refValue);
            foreach ($a as $value) {
                $result[]=$cpArr[$value];
            }
        }
        return $result;
    }
    function AddCPTree($parent,$parentCpID,$CPs,$currentCP) {
        $a=array_keys(array_column($CPs,'ParentCheckpointID'),$parentCpID);
        foreach ($a as $indx) {
            $item = $parent->addChild('item');
            $item->addAttribute("id","cp_{$CPs[$indx]->ID}");
            $item->addAttribute("text",$CPs[$indx]->Name); 
            $childs=array_keys(array_column($CPs,'ParentCheckpointID'),$CPs[$indx]->CheckpointID);
            if (count($childs) > 0) {
                $item->addAttribute("child","1");
                $item->addAttribute('open','1');
            }
            if ($CPs[$indx]->CheckpointID == $currentCP) {
                $item->addAttribute("select","yes");
                if ($CPs[$indx]->HasSavedState) {
                    $item->addAttribute("im0","vm/snapshot-youarehereOn.png");
                    $item->addAttribute("im1","vm/snapshot-youarehereOn.png");
                    $item->addAttribute("im2","vm/snapshot-youarehereOn.png");
                } else {
                    $item->addAttribute("im0","vm/snapshot-youarehere.png");
                    $item->addAttribute("im1","vm/snapshot-youarehere.png");
                    $item->addAttribute("im2","vm/snapshot-youarehere.png");
                }
            } else {
                if ($CPs[$indx]->HasSavedState) {
                    $item->addAttribute("im0","vm/snapshot-poweredOn.png");
                    $item->addAttribute("im1","vm/snapshot-poweredOn.png");
                    $item->addAttribute("im2","vm/snapshot-poweredOn.png");
                } else {
                    $item->addAttribute("im0","vm/snapshot.png");
                    $item->addAttribute("im1","vm/snapshot.png");
                    $item->addAttribute("im2","vm/snapshot.png");
                }
            }
            AddCPTree($item,$CPs[$indx]->CheckpointID,$CPs,$currentCP);
        }
    }
    function MergeMultiVMs($vms,$refArr) {
        if (!isset($vm)) $vm = new stdClass();
        foreach ($refArr as $indx) {
            if (!isset($vm->Checkpoints)) {
                $vm->Checkpoints=CpArr2FlatArr($vms[$indx]->VMId,$vms[$indx]->Checkpoints);
                $basePathArr=GetPathArr($vm->Checkpoints);
            } else {
                $tmp=CpArr2FlatArr($vms[$indx]->VMId,$vms[$indx]->Checkpoints);
                $newPathArr=GetPathArr($tmp);
                $tmp=array_intersect($basePathArr,$newPathArr);
                $vm->Checkpoints=CleanCpArray($vm->Checkpoints,$tmp);
                $basePathArr=GetPathArr($vm->Checkpoints);
            }
        }                
        $vm->VMId="";
        $vm->LastRestoredCheckpointID="";
        return $vm;
    }
    if (isset($_GET["id"])) {
        $type=GetTypeFromID($_GET["id"]);
        $id=GetIDwoType($_GET["id"]);
        $vm=null;
        $vms=GetVMs();
        switch ($type) {
            case 'svc_':
                $a=array_keys(array_column($vms,'svcid'),$id);
                $vm=MergeMultiVMs($vms,$a);
                break;
            case 'ct_':
                $a=array_keys(array_column($vms,'ctid'),$id);
                $vm=MergeMultiVMs($vms,$a);
                break;
            case 'vm_':
                $key = array_search($id, array_column($vms, 'ID'));
                $vm=$vms[$key];
                break;
        }        
        $xml = new SimpleXMLElement('<xml version="1.0"/>');
        $xml->addAttribute('encoding',"iso-8859-1");
        $tree = $xml->addChild('tree');
        $tree->addAttribute("id","root");
        if ($vm != null) {
            AddCPTree($tree,$vm->VMId,$vm->Checkpoints,$vm->LastRestoredCheckpointID);
        }
        Header('Content-type: text/xml');
        $tmp=$xml->asXML();
        print($xml->asXML());        
    }