<!DOCTYPE html>
<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/SessionHandler.php');
    MySQLSessionHandler::session_start();

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
        <script src="js/misc_xml.js"></script>
        <script src="js/MainForm/MiscMainForm.js"></script>
        <script src="js/MainForm/VMsView.js"></script>
        <script src="js/MainForm/JobsView.js"></script>
        <script src="js/vmoperations/CreateSnapshot.js"></script>
        <script src="js/vmoperations/SnapshotManager.js"></script>
        <script src="js/vmoperations/RevertSnapshot.js"></script>
        <script src="js/vmoperations/PowerOps.js"></script>
        <script src="js/vmoperations/OtherOps.js"></script>
        <script src="js/cookie.umd.js"></script>
        <script src="js/Misc_Cookies.js"></script>
        
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
                var myLayout, myToolbar, myMainMenu, myTimerID;
                myLayout = myToolbar = myMainMenu = myTimerID = null;

                function ChangeView() {
                    var viewType=GetFromSession("viewType","vms");
                    switch (viewType) {
                        case "vms":
                            LoadVMsView(myLayout);
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
                    myTimerID=setInterval(TimerRefresh, 120000);
                }
                function TimerRefresh() {
                    dhx.ajax.get("actions/ReloadCache.php", function(r){
                        var items = [];
                        var xml = r.xmlDoc.responseXML;
                        var VMsUpdated = xml.getElementsByTagName("VMsUpdated")[0].textContent;
                        var SvcUpdated = xml.getElementsByTagName("SvcUpdated")[0].textContent;
                        if (VMsUpdated === "true") {
                            ReloadVmsGrid();
                        }
                        if (SvcUpdated === "true") {
                            ReloadServiceTree(0);
                        }
                    });
                }
                function doOnUnload() {
                    if (myTimerID != null) {
                        clearInterval(myTimerID);
                    }
                }
            </script>
    </head>
    <body onload="doOnLoad();" onunload="doOnUnload();">
        <div id="layoutObj" ></div>
    </body>
</html>
