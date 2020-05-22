var RevertSnapshotForm, RevertSnapshotPopupWindow,RevertSnapshotTree;
function CreateRevertSnapshotForm(Form,id) {
    Form.loadStruct(`ajax/forms/main/vm_operations_forms.php?formtype=RevertSnapshot&vmID=${id}`,function() {
        RevertSnapshotTree = new dhtmlXTreeObject(Form.getContainer("CPTree"),"100%", "100%", "root");
        RevertSnapshotTree.load(`ajax/forms/main/vmCpTree.php?id=${id}`);
        RevertSnapshotTree.setChildCalcMode('disabled');
        RevertSnapshotTree.setDataMode("xml");
        RevertSnapshotTree.setImagePath("images/");
        RevertSnapshotTree.enableCheckBoxes(0);
        RevertSnapshotTree.enableTreeLines(true);
        RevertSnapshotTree.enableTreeImages(true);
        RevertSnapshotTree.enableDragAndDrop(false);
        RevertSnapshotTree.attachEvent("onSelect", function(id){
            if (id == 0) {
                RevertSnapshotForm.forEachItem(function(name){
                    if (name == "btnRevert" || name == "btnDelete" || name == "btnDelWChilds") {
                        RevertSnapshotForm.disableItem(name);
                    }
                });                
            } else {
                RevertSnapshotForm.forEachItem(function(name){
                    if (name == "btnRevert" || name == "btnDelete" || name == "btnDelWChilds") {
                        RevertSnapshotForm.enableItem(name);
                    }
                });                
            }
        });        
    });
}
function RevertSnapshotOnButtonClick(name) {
    var operation="";
    switch (name) {
        case "btnRevert":
            operation="RevertSnapshot";
            break;
    }
    if (operation !== "") {
        if ( confirm("Are you sure?") ) {
            var vmid=RevertSnapshotForm.getItemValue("vmid");
            var snapID=RevertSnapshotTree.getSelectedItemId();
            var snapName=RevertSnapshotTree.getSelectedItemText();
            RevertSnapshotForm.send(`actions/vm_operations.php?operation=${operation}&id=${vmid}&snapID=${snapID}&snapName=${snapName}`,"get", function(_loader, response){
                var xml=StringToXMLDom(response);
                var status = xml.getElementsByTagName("Status")[0].childNodes[0].nodeValue;
                if (status.toUpperCase() == "success".toUpperCase()) {
                    alert("Completed");
                } else if (status.toUpperCase() == "error".toUpperCase()) {
                    var errormsg=xml.getElementsByTagName("ErrorMessage")[0].childNodes[0].nodeValue;
                    alert(errormsg);
                }                            
                RevertSnapshotPopupWindow.close();
                RevertSnapshotPopupWindow=null;
            });
        }
    }else {
        RevertSnapshotPopupWindow.close();
        RevertSnapshotPopupWindow=null;
    }
}
function RevertSnapshot(Layout,itemID) {
    RevertSnapshotPopupWindow = Layout.dhxWins.createWindow("RevertSnapshotPopupWindow",100,100,640,300);
    RevertSnapshotPopupWindow.setModal(true);
    RevertSnapshotPopupWindow.centerOnScreen();
    RevertSnapshotPopupWindow.setText("Revert Snapshot");
    RevertSnapshotPopupWindow.button("minmax").hide();
    RevertSnapshotPopupWindow.button("close").hide();
    RevertSnapshotPopupWindow.button("park").hide();
    RevertSnapshotPopupWindow.denyResize();
    RevertSnapshotForm=RevertSnapshotPopupWindow.attachForm();
    CreateRevertSnapshotForm(RevertSnapshotForm,itemID);
    RevertSnapshotForm.attachEvent("onButtonClick",RevertSnapshotOnButtonClick);
}