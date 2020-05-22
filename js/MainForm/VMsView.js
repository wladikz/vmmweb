var vmsTree, vmsGrid, vmTreeMenu, vmGridMenu, vmCheckpointTree ;
vmsTree = vmsGrid = vmTreeMenu = vmGridMenu = vmCheckpointTree = null;


function LoadVMsView(myLayout) {
    myLayout.cells("a").showView("vms");
    myLayout.cells("b").showView("vms");
    myLayout.cells("c").showView("vms");
    vmTreeMenu=new dhtmlXMenuObject();
    vmTreeMenu.renderAsContextMenu();
    vmTreeMenu.attachEvent("onClick", function(id, zoneId, cas){
        var itemId=GetFromSession('vmCntxMenu');
        window[id](myLayout,itemId);                        
    });
    vmTreeMenu.loadStruct("ajax/forms/main/vmtreemenu.php");
    vmGridMenu=new dhtmlXMenuObject();
    vmGridMenu.renderAsContextMenu();
    vmGridMenu.attachEvent("onClick", function(id, zoneId, cas){
        var itemId=GetFromSession('vmCntxMenu');
        window[id](myLayout,itemId);                        
    });
    vmGridMenu.loadStruct("ajax/forms/main/vmtreemenu.php");

    if (vmsTree == null) {
        vmsTree=myLayout.cells("a").attachTree();
        vmsTree.attachEvent("onXLS", function(){
            myLayout.cells("a").progressOn();
        });
        vmsTree.attachEvent("onXLE", function(){
            myLayout.cells("a").progressOff();
        });
        vmsTree.enableDragAndDrop(false);
        vmsTree.setChildCalcMode('disabled');
        vmsTree.setImagePath("images/");
        vmsTree.setXMLAutoLoadingBehaviour("function");
        vmsTree.enableCheckBoxes(0);
        vmsTree.enableTreeLines(true);
        vmsTree.setXMLAutoLoading(function(id) {
            vmsTree.load(`ajax/forms/main/vmtree.php?id=${id}`,"xml");
        });
        vmsTree.enableSmartXMLParsing(true);
        ReloadServiceTree(0);
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
                vmsGrid = myLayout.cells("b").attachGrid("root");
                vmsGrid.setImagePath("images/"); 
                vmsGrid.enableMultiline(true);
                vmsGrid.attachEvent("onXLE", function(grid_obj,count){
                    vmsGrid.setUserData('','name','vmsGrid');
                    var tmp=GetColWidth(vmsGrid).split(',');
                    for(i = 0; i < tmp.length; i++){
                        if(tmp[i] != '') {
                            vmsGrid.setColWidth(i,tmp[i]);
                        }
                    }
                    
                });
                vmsGrid.attachEvent("onRowSelect", vmsGridOnRowSelect);
                vmsGrid.enableContextMenu(vmGridMenu);
                vmsGrid.attachEvent("onBeforeContextMenu",function(id,ind,obj){
                    var res=VMsOnBeforeContextMenu(vmGridMenu,id,ind,obj);
                    return res;
                });
            }
            ReloadVmsGrid(id);
            vmsGrid.enableColumnAutoSize(true);
            vmsGrid.attachEvent("onResizeEnd", function(obj){
                SaveColWidth(obj);
            });
        });
        vmsTree.enableContextMenu(vmTreeMenu);
        vmsTree.attachEvent("onBeforeContextMenu",function(id){
            var res=VMsOnBeforeContextMenu(vmTreeMenu,id);
            return res;
        });
        
    }
}
function vmsGridOnRowSelect(id,ind) {
    if (vmCheckpointTree == null) {
        vmCheckpointTree = myLayout.cells("c").attachTree("root");
        vmCheckpointTree.setChildCalcMode('disabled');
        vmCheckpointTree.setDataMode("xml");
        vmCheckpointTree.setImagePath("images/");
        vmCheckpointTree.enableCheckBoxes(0);
        vmCheckpointTree.enableTreeLines(true);
        vmCheckpointTree.enableTreeImages(true);
        vmCheckpointTree.enableDragAndDrop(false);
    }
    vmCheckpointTree.deleteChildItems("root");
    if (id != -1) {
        vmCheckpointTree.load("ajax/forms/main/vmCpTree.php?id="+id);
    }
        
}
function ReloadVmsGrid(id = -1) {
    var state=vmsGrid.getSortingState();
    if (id === -1) {
        id=vmsTree.getSelectedItemId();
    }
    var gridSelected=vmsGrid.getSelectedRowId();
    Save2Session("vmsGridSortState",state);
    Save2Session("vmsGridSelectedId",gridSelected);
    vmsGrid.load("ajax/forms/main/vmgrid.php?id="+id,doAfterVmsGridRefresh,"xml");
}
function ReloadServiceTree(id = -1) {
    var SelectedId=vmsTree.getSelectedItemId()
    vmsTree.load(`ajax/forms/main/vmtree.php?id=${id}`,function(){
        vmsTree.selectItem(SelectedId,true,false)
        },"xml");
}
function doAfterVmsGridRefresh(params) {
    var state=GetFromSession("vmsGridSortState",[]);
    if (typeof state === "string") {
        state=state.split(",");
    }
    if (Array.isArray(state) && state.length > 0){
        var col=state[0];
        var order=state[1];
        var coltype=vmsGrid.fldSort[col];
        vmsGrid.sortRows(col,coltype,order);
        vmsGrid.setSortImgState(true,col,order);
    }
    var gridSelected=GetFromSession("vmsGridSelectedId","");
    
    if (gridSelected != "null") {
        var RowExist=vmsGrid.doesRowExist(gridSelected);
        if (RowExist) {
            vmsGrid.selectRowById(gridSelected,false,true,true);
        } else {
            vmsGridOnRowSelect(-1);
        }
    } else {
        vmsGridOnRowSelect(-1);
    }
}
function VMsOnBeforeContextMenu(MenuObj,itemId,ind = -1,obj = null){
    if (obj === null) {
        obj=vmsTree;
    }
    var type = GetItemType(itemId);
    var isSDN = null;
    var VMStatus = GetVmStatus(itemId,obj);
    switch (type) {
        case 1:
            isSDN = GetIsSDN(itemId,vmsTree);
            break;
        case 2:
            var svcID=vmsTree.getParentId(itemId);
            isSDN=GetIsSDN(svcID,vmsTree);
            break;
        case 4:
            isSDN=2;
            break
    }
    Save2Session('vmCntxMenu',itemId);
    MenuObj.forEachItem(function(itemId){
        SetMenuItemVisibility(MenuObj,itemId,type,isSDN,VMStatus);
    });
    return true;

}