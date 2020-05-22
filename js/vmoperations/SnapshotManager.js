var SnapshotManagerForm, SnapshotManagerPopupWindow,SnapshotManagerTree;
function CreateSnapshotManagerForm(Form,id) {
    Form.loadStruct(`ajax/forms/main/vm_operations_forms.php?formtype=SnapshotManager&vmID=${id}`,function() {
        SnapshotManagerTree = new dhtmlXTreeObject(Form.getContainer("CPTree"),"100%", "100%", "root");
        SnapshotManagerTree.load(`ajax/forms/main/vmCpTree.php?id=${id}`);
        SnapshotManagerTree.setChildCalcMode('disabled');
        SnapshotManagerTree.setDataMode("xml");
        SnapshotManagerTree.setImagePath("images/");
        SnapshotManagerTree.enableCheckBoxes(0);
        SnapshotManagerTree.enableTreeLines(true);
        SnapshotManagerTree.enableTreeImages(true);
        SnapshotManagerTree.enableDragAndDrop(false);
        SnapshotManagerTree.attachEvent("onSelect", function(id){
            if (id == 0) {
                SnapshotManagerForm.forEachItem(function(name){
                    if (name == "btnRevert" || name == "btnDelete" || name == "btnDelWChilds") {
                        SnapshotManagerForm.disableItem(name);
                    }
                });                
            } else {
                SnapshotManagerForm.forEachItem(function(name){
                    if (name == "btnRevert" || name == "btnDelete" || name == "btnDelWChilds") {
                        SnapshotManagerForm.enableItem(name);
                    }
                });                
            }
        });        
    });
}
function SnapshotManagerOnButtonClick(name) {
    var operation="";
    switch (name) {
        case "btnRevert":
            operation="RevertSnapshot";
            break;
        case "btnDelete":
            operation="DeleteSnapshot";
            break;
        case "btnDelWChilds":
            operation="DelWChildsSnapshot";
            break;
    }
    if (operation !== "") {
        if ( confirm("Are you sure?") ) {
            var vmid=SnapshotManagerForm.getItemValue("vmid");
            var snapID=SnapshotManagerTree.getSelectedItemId();
            var snapName=SnapshotManagerTree.getSelectedItemText();
            SnapshotManagerForm.send(`actions/vm_operations.php?operation=${operation}&id=${vmid}&snapID=${snapID}&snapName=${snapName}`,"get", function(_loader, response){
                var xml=StringToXMLDom(response);
                var status = xml.getElementsByTagName("Status")[0].childNodes[0].nodeValue;
                if (status.toUpperCase() == "success".toUpperCase()) {
                    alert("Completed");
                } else if (status.toUpperCase() == "error".toUpperCase()) {
                    var errormsg=xml.getElementsByTagName("ErrorMessage")[0].childNodes[0].nodeValue;
                    alert(errormsg);
                }                            
                SnapshotManagerPopupWindow.close();
                SnapshotManagerPopupWindow=null;
            });
        }
    }else {
        SnapshotManagerPopupWindow.close();
        SnapshotManagerPopupWindow=null;
    }
}
function SnapshotManager(Layout,itemID) {
    SnapshotManagerPopupWindow = Layout.dhxWins.createWindow("SnapshotManagerPopupWindow",100,100,640,300);
    SnapshotManagerPopupWindow.setModal(true);
    SnapshotManagerPopupWindow.centerOnScreen();
    SnapshotManagerPopupWindow.setText("Snapshot Manager");
    SnapshotManagerPopupWindow.button("minmax").hide();
    SnapshotManagerPopupWindow.button("close").hide();
    SnapshotManagerPopupWindow.button("park").hide();
    SnapshotManagerPopupWindow.denyResize();
    SnapshotManagerForm=SnapshotManagerPopupWindow.attachForm();
    CreateSnapshotManagerForm(SnapshotManagerForm,itemID);
    SnapshotManagerForm.attachEvent("onButtonClick",SnapshotManagerOnButtonClick);
}