function PowerOff(Layout,itemID) {
    if (confirm("Are you sure?")) {
        var r = window.dhx.ajax.getSync(`actions/vm_operations.php?operation=PowerOff&id=${itemID}`);
        var xml=r.xmlDoc.responseXML;
        var status = xml.getElementsByTagName("Status")[0].childNodes[0].nodeValue;
        if (status === "success") {
            TreeOnClick(mytree.getSelectedId());
        } else if (status === "error") {
            var errormsg=xml.getElementsByTagName("ErrorMessage")[0].childNodes[0].nodeValue;
            dhtmlx.alert({
                    type:"alert-error",
                    text:errormsg,
                    title:"Error!",
                    ok:"ok"
              });
        }
    }
}
function PowerOn(Layout,itemID) {
    if (confirm("Are you sure?")) {
        var r = window.dhx.ajax.getSync(`actions/vm_operations.php?operation=PowerOn&id=${itemID}`);
        var xml=r.xmlDoc.responseXML;
        var status = xml.getElementsByTagName("Status")[0].childNodes[0].nodeValue;
        if (status === "success") {
            TreeOnClick(mytree.getSelectedId());
        } else if (status === "error") {
            var errormsg=xml.getElementsByTagName("ErrorMessage")[0].childNodes[0].nodeValue;
            dhtmlx.alert({
                    type:"alert-error",
                    text:errormsg,
                    title:"Error!",
                    ok:"ok"
              });
        }
    }
}