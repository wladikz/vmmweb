<!DOCTYPE html>
<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/database.class.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/mysql.sessions.php');
    Session::session_start();
    if ( !isset($_SESSION["AuthToken"])) {
        header("Location: index.php");
        exit;
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="fontawesome-free-5.13.0-web/css/fontawesome.css">
        <link rel="stylesheet" type="text/css" href="dhtmlx/codebase/fonts/font_roboto/roboto.css"/> 
        <link rel="stylesheet" href="dhtmlx/skins/web/dhtmlx.css"> 
        <script src="dhtmlx/codebase/dhtmlx.js"></script>
        <script src="js/vmoperations/CreateSnapshot.js"></script>
        <title>Main</title>
    	<style type="text/css">
            div#layoutObj {
                position: relative;
                margin-top: 10px;
                margin-left: 10px;
                width: 99%;
                height: 95vh;
                align: center;
            }
            html, body {
                width: 100%;
                height: 100%;
                margin: 0px;
                overflow: hidden;
            }            
        </style> 
        <script>
                var myLayout, myToolbar, myMainMenu;
                myLayout = myToolbar = myMainMenu = null;
                var vmsTree, vmsGrid, vmTreeMenu, vmGridMenu, vmGridMenuLoaded ;
                vmsTree = vmsGrid = vmTreeMenu = vmGridMenu = null;
                vmGridMenuLoaded= false;
                var jobsTree, jobsGrid;
                jobsTree = jobsGrid = null;

                function Save2Session(name,value) {
                    var r = dhx.ajax.getSync("actions/session_ops.php?operation=save&name="+name+"&value="+value);
                    var xml = r.xmlDoc.responseXML;
                    if (xml != null) {
                        var root = xml.getElementsByTagName("root")[0];
                        var response = root.getAttribute("result");
                    }
                }
                function GetFromSession(name,defaultValue="") {
                    var r = dhx.ajax.getSync("actions/session_ops.php?operation=get&name="+name);
                    var xml = r.xmlDoc.responseXML;
                    if (xml != null) {
                        var root = xml.getElementsByTagName("root")[0];
                        var response = root.getAttribute("result");
                    }
                    if ((typeof response === 'undefined') || (response === "")) {
                        response=defaultValue;
                    }
                    return response;
                }
                function LoadJobsView() {
                    myLayout.cells("a").showView("jobs");
                    myLayout.cells("b").showView("jobs");
                    myLayout.cells("c").showView("jobs");
                    if (jobsTree == null) {
                        jobsTree=myLayout.cells("a").attachTree("root");
                        jobsTree.attachEvent("onXLS", function(){
                            myLayout.cells("a").progressOn();
                        });
                        jobsTree.attachEvent("onXLE", function(){
                            myLayout.cells("a").progressOff();
                        });
                        jobsTree.enableDragAndDrop(false);
                        jobsTree.setChildCalcMode('disabled');
                        jobsTree.setImagePath("images/");
                        jobsTree.setXMLAutoLoadingBehaviour("function");
                        jobsTree.enableCheckBoxes(0);
                        jobsTree.enableTreeLines(true);
                        jobsTree.load("ajax/forms/main/jobstree.php","xml");
                        jobsTree.attachEvent("onSelect", function(id){
                            if (id === 0) {
                                myLayout.cells("b").detachObject(true);
                                jobsGrid = null;
                                return;
                            }                                
                            if (jobsGrid == null) {
                                jobsGrid = myLayout.cells("b").attachGrid();
                                jobsGrid.setImagePath("images/"); 
                                jobsGrid.enableMultiline(true); 
                            }
                            jobsGrid.load("ajax/forms/main/jobsGrid.php?id="+id+"&grid=main"); 
                        });

                    }                    
                }
                function LoadVMsView() {
                    myLayout.cells("a").showView("vms");
                    myLayout.cells("b").showView("vms");
                    myLayout.cells("c").showView("vms");
                    vmTreeMenu=new dhtmlXMenuObject();
                    vmTreeMenu.renderAsContextMenu();
			        vmTreeMenu.attachEvent("onClick", function(id, zoneId, cas){
                        var itemId=GetFromSession('vmTreeMenu');
                        window[id](myLayout,itemId);                        
                    });
                    if (vmsTree == null) {
                        vmsTree=myLayout.cells("a").attachTree();
                        vmsTree.attachEvent("onXLS", function(){
                            myLayout.cells("a").progressOn();
                        });
                        vmsTree.attachEvent("onXLE", function(){
                            myLayout.cells("a").progressOff();
                            var tmp=vmTreeMenu.getUserData(0,'loaded');
                            if (! vmGridMenuLoaded) {
                                vmTreeMenu.loadStruct("ajax/forms/main/vmtreemenu.php");
                                vmGridMenuLoaded = true;
                            }
                           
                        });
                        vmsTree.enableDragAndDrop(false);
                        vmsTree.setChildCalcMode('disabled');
                        vmsTree.setImagePath("images/");
                        vmsTree.setXMLAutoLoadingBehaviour("function");
                        vmsTree.setChildCalcMode("child");
                        vmsTree.enableCheckBoxes(0);
                        vmsTree.enableTreeLines(true);
                        vmsTree.setXMLAutoLoading(function(id) {
                            vmsTree.load("ajax/forms/main/vmtree.php?id="+id,"xml");
                        });
                        vmsTree.enableSmartXMLParsing(true);
                        vmsTree.load("ajax/forms/main/vmtree.php?id=0","xml");
                        vmsTree.attachEvent("onOpenStart", function(id, state){
                            var uri="ajax/forms/main/vmtree.php?id="+id+"&mode="+state;
                            var xhr = dhx.ajax.getSync(uri);
                            return true; 
                        });
                        vmsTree.attachEvent("onSelect", function(id){
                            if (id === 0) {
                                myLayout.cells("b").detachObject(true);
                                vmsGrid = null;
                                return;
                            }                                
                            if (vmsGrid == null) {
                                vmsGrid = myLayout.cells("b").attachGrid();
                                vmsGrid.setImagePath("images/"); 
                                vmsGrid.enableMultiline(true); 
                            }
                            vmsGrid.load("ajax/forms/main/vmgrid.php?id="+id,"xml"); 
                        });
                        vmsTree.enableContextMenu(vmTreeMenu);
                        vmsTree.attachEvent("onBeforeContextMenu",function(itemId){
                            var type = itemId.match(/svc|ct|vm/)[0];
                            var isSDN = null;
                            var VMStatus = "";
                            switch (type) {
                                case "svc":
                                    isSDN = vmsTree.getUserData(itemId,'isSDN');
                                    VMStatus = vmsTree.getUserData(itemId,'VMStatus');
                                    break;
                                case "ct":
                                    var svcID=vmsTree.getParentId(itemId);
                                    VMStatus = vmsTree.getUserData(itemId,'VMStatus');
                                    isSDN=vmsTree.getUserData(svcID,'isSDN');
                                    break;
                            }
                            Save2Session('vmTreeMenu',itemId);
                            vmTreeMenu.forEachItem(function(itemId){
                                var mi_isSDN=vmTreeMenu.getUserData(itemId,'isSDN');
                                var mi_isService=vmTreeMenu.getUserData(itemId,'isService');
                                var mi_isComputerTier=vmTreeMenu.getUserData(itemId,'isComputerTier');
                                var mi_VMStatus=vmTreeMenu.getUserData(itemId,'VMStatus');
                                vmTreeMenu.hideItem(itemId);
                                switch (type) {
                                    case "svc":
                                        if ((mi_isService == 1) && (mi_isSDN == isSDN || mi_isSDN == 2 ) && ((!mi_VMStatus) || (mi_VMStatus && VMStatus.match(mi_VMStatus))) ) {
                                            vmTreeMenu.showItem(itemId);
                                        }
                                        break;
                                    case "ct":
                                        if ((mi_isComputerTier == 1) && (mi_isSDN == isSDN || mi_isSDN == 2 ) && ((!mi_VMStatus) || (mi_VMStatus && VMStatus.match(mi_VMStatus)))) {
                                                vmTreeMenu.showItem(itemId);
                                        }
                                        break;
                                }
                            });
                            return true;
                        });    
                    }
                }
                function ChangeView() {
                    var viewType=GetFromSession("viewType","vms");
                    switch (viewType) {
                        case "vms":
                            LoadVMsView();
                            break;
                        case "library":
                            break;
                        case "jobs":
                            LoadJobsView();
                            break;
                    }
                }
                function doOnLoad() {
                    myLayout = new dhtmlXLayoutObject({
                            parent: document.getElementById("layoutObj"),
                            pattern: "3L",
                            cells: [    
                                        {
                                            id:             "a",        // id of the cell you want to configure
                                            header:         false,      // hide header on init
                                            width:          322,        // cell init width
                                            collapse:       false
                                        },                         
                                        {
                                            id:             "b",        // id of the cell you want to configure
                                            header:         false,      // hide header on init
                                            height:         "70%",        // cell init height
                                            collapse:       false
                                        },                         
                                        {
                                            id:             "c",        // id of the cell you want to configure
                                            header:         false,      // hide header on init
                                            collapse:       false
                                        }                         

                                    ]
                    });
                    var viewType=GetFromSession("viewType","vms");
                    myMainMenu=myLayout.attachMenu();
                    myMainMenu.loadStruct("ajax/forms/main/mainmenu.php?mainmenu=1&view="+viewType);
                    myMainMenu.attachEvent("onRadioClick", function(group, idChecked, idClicked, zoneId, cas){
                        if (idChecked != idClicked) {
                            Save2Session("viewType",idClicked);
                            ChangeView();
                        }
                        return true;
                    });
                    myToolbar=myLayout.attachToolbar();
                    ChangeView();
                }
            </script>
    </head>
    <body onload="doOnLoad();">
        <div id="layoutObj" ></div>
    </body>
</html>
