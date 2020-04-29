<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/XML_misc.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/misc.php');
    session_start();
    function AddMenuItem($parent,$id,$caption,$link,$isParent=FALSE) {
        $item=$parent->addChild("item");
        $item->addAttribute("id", $id);
        $item->addAttribute("text",$caption);
        if ($isParent || !empty($link)) {
            $item->addAttribute("enabled","true");
        } else {
            $item->addAttribute("enabled","false");
        }
        if (!empty($link)) {
            $item->addChild("href")->addCData($link);
        }
        return $item;
    }
    function AddMenuRadioItem($parent,$id,$caption,$link,$checkedId,$isParent=FALSE) {
        $group=strval($parent['id']);
        $item=$parent->addChild("item");
        $item->addAttribute("id", $id);
        $item->addAttribute("text",$caption);
        $item->addAttribute("group",$group);
        $item->addAttribute("type","radio");
        if ($isParent || !empty($link)) {
            $item->addAttribute("enabled","true");
        } else {
            $item->addAttribute("enabled","false");
        }
        if (!empty($link)) {
            $item->addChild("href")->addCData($link);
        }
        if ($checkedId == $id) {
            $item->addAttribute("checked","true");
        }
        return $item;
    }

    if (!empty($_GET['mainmenu'])) {
        if (isset($_GET["view"])) {
            $viewtype=$_GET["view"];
        } else {
            $viewtype="vms";
        }
        $xml = new SimpleXMLExtended('<xml version="1.0"/>');
        $menu=$xml->addChild("menu");
        $mnuFile=AddMenuItem($menu, "file", "File","",TRUE);
        AddMenuItem($mnuFile, "logout", "Logout", GetPageURL("logout.php"));
        $parent=AddMenuItem($menu, "view", "View", "",TRUE);
        AddMenuRadioItem($parent,"vms","VMs & Services",GetPageURL("jobstatus.php"),$viewtype);
        AddMenuRadioItem($parent,"library","Library",GetPageURL("jobstatus.php"),$viewtype);
        AddMenuRadioItem($parent,"jobs","Jobs",GetPageURL("jobstatus.php"),$viewtype);
        $tmp=$xml->asXML();
        Header('Content-type: text/xml');
        print($xml->asXML());
    }