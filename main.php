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
        <script src="js/misc_xml.js"></script>
        <script src="js/vmoperations/CreateSnapshot.js"></script>
        <script src="js/vmoperations/SnapshotManager.js"></script>
        <script src="js/vmoperations/RevertSnapshot.js"></script>
        <script src="js/vmoperations/PowerOps.js"></script>
        <script src="js/vmoperations/OtherOps.js"></script>
        <script src="js/cookie.umd.js"></script>
        <script src="js/Misc_Cookies.js"></script>
        <script src="js/MainForm/MiscMainForm.js"></script>
        <script src="js/MainForm/VMsView.js"></script>
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

                var jobsTree, jobsMainGrid, jobDetailGrid, jobsDetailTabs, jobsDetailMsgGrid;
                jobsTree = jobsMainGrid = jobDetailGrid = jobsDetailTabs = jobsDetailMsgGrid = null;

                function LoadJobsView() {
                    myLayout.cells("a").showView("jobs");
                    myLayout.cells("b").showView("jobs");
                    myLayout.cells("c").showView("jobs");
                    if (jobsDetailTabs == null) {
                        jobsDetailTabs = myLayout.cells("c").attachTabbar({
                            mode:         "top",
                            close_button: false,
                            arrows_mode:  "auto",
                            tabs: [
                                {
                                    id:     "details",
                                    text:   "Details",
                                    active: true
                                },
                                {
                                    id:     "messages",
                                    text:   "Messages",
                                    enabled: true
                                }
                            ]
                        });
                    }
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
                                jobsMainGrid = null;
                                return;
                            }                                
                            if (jobsMainGrid == null) {
                                jobsMainGrid = myLayout.cells("b").attachGrid();
                                jobsMainGrid.setImagePath("images/"); 
                                jobsMainGrid.enableMultiline(true); 
                                jobsMainGrid.attachEvent("onRowSelect", function(id,ind){
                                    var GridType=jobsTree.getSelectedItemId();
                                    if (jobDetailGrid == null) {
                                        jobDetailGrid = jobsDetailTabs.tabs("details").attachGrid();
                                        jobDetailGrid.setImagePath("images/"); 
                                        jobDetailGrid.enableColumnAutoSize(true);
                                        jobDetailGrid.attachEvent("onResizeEnd", function(obj){
                                            SaveColWidth(obj);
                                        });
                                        jobDetailGrid.attachEvent("onXLE", function(grid_obj,count){
                                            jobDetailGrid.setUserData('','name','jobDetailGrid');
                                            var tmp=GetColWidth(jobDetailGrid).split(',');
                                            for(i = 0; i < tmp.length; i++){
                                                if(tmp[i] != '') {
                                                    jobDetailGrid.setColWidth(i,tmp[i]);
                                                }
                                            }
                                            
                                        });

                                    }
                                    jobDetailGrid.load("ajax/forms/main/jobsGrid.php?type="+GridType+"&grid=detail&id="+id)
                                    if (jobsDetailMsgGrid == null) {
                                        jobsDetailMsgGrid =jobsDetailTabs.tabs("messages").attachGrid();
                                        jobDetailGrid.setImagePath("images/"); 
                                    }
                                    jobsDetailMsgGrid.load("ajax/forms/main/jobsGrid.php?type="+GridType+"&grid=messages&id="+id);
                                });
                                jobsMainGrid.enableColumnAutoSize(true);
                                jobsMainGrid.attachEvent("onResizeEnd", function(obj){
                                    SaveColWidth(obj);
                                });
                                jobsMainGrid.attachEvent("onXLE", function(grid_obj,count){
                                    jobsMainGrid.setUserData('','name','jobsMainGrid');
                                    var tmp=GetColWidth(jobsMainGrid).split(',');
                                    for(i = 0; i < tmp.length; i++){
                                        if(tmp[i] != '') {
                                            jobsMainGrid.setColWidth(i,tmp[i]);
                                        }
                                    }
                                    
                                });
                            }
                            jobsMainGrid.load("ajax/forms/main/jobsGrid.php?type="+id+"&grid=main");
                        });

                    }                    
                }

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
