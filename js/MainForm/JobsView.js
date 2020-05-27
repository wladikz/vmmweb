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