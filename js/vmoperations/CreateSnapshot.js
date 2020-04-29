var CreateSnapshotForm, CreateSnapshotpopupWindow;
function CreateSnapshotOnAfterValidate(status){
    if (status) {
        CreateSnapshotForm.enableItem("btnsubmit");                    
    } else {
        CreateSnapshotForm.disableItem("btnsubmit");
    }
}
function CreateSnapshotOnChange(name, value) {
    CreateSnapshotForm.validate();
}
function CreateSnapshotOnButtonClick(name) {
    if (name === "btncancel") {
        CreateSnapshotpopupWindow.close();
        CreateSnapshotpopupWindow=null;
        return;
    }
    if (name === "btnsubmit" && CreateSnapshotForm.isItemEnabled("btnsubmit")) {
        if ( confirm("Are you sure?") ) {
            var vmid=CreateSnapshotForm.getItemValue("vmid");
            CreateSnapshotForm.send(`actions/vm_operations.php?operation=CreateSnapshot&id=${vmid}`,"get", function(loader, response){
                var xml=StringToXMLDom(response);
                var status = xml.getElementsByTagName("Status")[0].childNodes[0].nodeValue;
                if (status === "success") {
                    alert("Completed");
                } else if (status === "error") {
                    var errormsg=xml.getElementsByTagName("ErrorMessage")[0].childNodes[0].nodeValue;
                    alert(errormsg);
                }                            
                CreateSnapshotpopupWindow.close();
                CreateSnapshotpopupWindow=null;
            });
        }
    }
}
function CreateSnapshot(Layout,itemID){
    CreateSnapshotpopupWindow = Layout.dhxWins.createWindow("CreateSnapshotpopupWindow",100,100,400,280);
    CreateSnapshotpopupWindow.setModal(true);
    CreateSnapshotpopupWindow.centerOnScreen();
    CreateSnapshotpopupWindow.setText("Take Snapshot");
    CreateSnapshotpopupWindow.button("minmax").hide();
    CreateSnapshotpopupWindow.button("close").hide();
    CreateSnapshotpopupWindow.button("park").hide();
    CreateSnapshotForm=CreateSnapshotpopupWindow.attachForm();
    CreateSnapshotForm.loadStruct(`ajax/forms/main/vm_operations_forms.php?formtype=CreateSnapshot&vmID=${itemID}`);
    CreateSnapshotForm.attachEvent("onButtonClick",CreateSnapshotOnButtonClick);
    CreateSnapshotForm.attachEvent("onAfterValidate",CreateSnapshotOnAfterValidate);
    CreateSnapshotForm.attachEvent("onChange",CreateSnapshotOnChange); 
    CreateSnapshotForm.enableLiveValidation(true);
    CreateSnapshotForm.validate();    
}