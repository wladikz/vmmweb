var libTree, libGrid, libTreeMenu;
libTree = libGrid = libTreeMenu = null;
function LoadLibraryView(myLayout) {
    myLayout.cells("a").showView("library");
    myLayout.cells("b").showView("library");
    myLayout.cells("c").showView("library");
    libTreeMenu=new dhtmlXMenuObject();
    libTreeMenu.renderAsContextMenu();
    libTreeMenu.attachEvent("onClick", function(id, zoneId, cas){
        var itemId=GetFromSession('libCntxMenu');
        window[id](myLayout,itemId);                        
    });
    libTreeMenu.loadStruct("ajax/forms/main/libraryview/libTreeMenu.php");    
    if (libTree == null) {
        libTree=myLayout.cells("a").attachTree();
        libTree.attachEvent("onXLS", function(){
            myLayout.cells("a").progressOn();
        });
        libTree.attachEvent("onXLE", function(){
            myLayout.cells("a").progressOff();
        });
        libTree.enableDragAndDrop(false);
        libTree.setChildCalcMode('disabled');
        libTree.setImagePath("images/");
        libTree.setXMLAutoLoadingBehaviour("function");
        libTree.enableCheckBoxes(0);
        libTree.enableTreeLines(true);
        libTree.setXMLAutoLoading(function(id) {
            libTree.load(`ajax/forms/main/libraryview/libTree.php?id=${id}`,"xml");
        });
        libTree.enableSmartXMLParsing(true);
        libTree.attachEvent("onOpenStart", function(id, state){
            var uri=`ajax/forms/main/libraryview/libTree.php?id=${id}&mode=${state}`;
            var xhr = dhx.ajax.getSync(uri);
            return true; 
        });
        ReloadTemplateTree(0);
        libTree.attachEvent("onSelect", function(id){
            if (id === 0) {
                myLayout.cells("b").detachObject(true);
                libGrid = null;
                return;
            }                                
            if (libGrid == null) {
                libGrid = myLayout.cells("b").attachGrid("root");
                libGrid.setImagePath("images/"); 
                libGrid.enableMultiline(true);
                libGrid.attachEvent("onXLE", function(grid_obj,count){
                    libGrid.setUserData('','name','libGrid');
                    var tmp=GetColWidth(libGrid).split(',');
                    for(i = 0; i < tmp.length; i++){
                        if(tmp[i] != '') {
                            libGrid.setColWidth(i,tmp[i]);
                        }
                    }
                });
            }
            libGrid.load(`ajax/forms/main/libraryview/libgrid.php?id=${id}`,"xml");
            libGrid.enableColumnAutoSize(true);
            libGrid.attachEvent("onResizeEnd", function(obj){
                SaveColWidth(obj);
            });
        });
        libTree.enableContextMenu(libTreeMenu);
        libTree.attachEvent("onBeforeContextMenu",function(id){
            var res=LibOnBeforeContextMenu(libTreeMenu,id);
            return res;
        });

    }
}
function LibOnBeforeContextMenu(MenuObj,itemId,ind = -1,obj = null){
    if (obj === null) {
        obj=libTree;
    }
    if (isNaN(itemId)) {
        Save2Session('libCntxMenu',itemId);
        return true;
    } else {
        return false;
    }
}
function ReloadTemplateTree(id = -1) {
    var SelectedId=libTree.getSelectedItemId()
    libTree.load(`ajax/forms/main/libraryview/libTree.php?id=${id}`,function(){
        libTree.selectItem(SelectedId,true,false)
        },"xml");
}                
