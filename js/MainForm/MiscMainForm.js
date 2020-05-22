function GetItemType(ItemId) {
    const type = ItemId.match(/svc|ct|vm|cp/)[0];
    switch (type) {
        case 'svc':
            return 1;
        case 'ct':
            return 2;
        case 'vm':
            return 4;
        case 'cp':
            return 8;
        default:
            break;
    }
}
function GetIsSDN(ItemId,dhtmlxObj) {
    return dhtmlxObj.getUserData(ItemId,'isSDN');
}
function GetVmStatus(ItemId,dhtmlxObj) {
    return dhtmlxObj.getUserData(ItemId,'VMStatus');
}
function SetMenuItemVisibility(dhtmlxMenu,ItemId,type,isSDN,VMStatus) {
    var mi_isSDN=dhtmlxMenu.getUserData(ItemId,'isSDN');
    var mi_objType=dhtmlxMenu.getUserData(ItemId,'objType');
    var mi_ObjStatus=dhtmlxMenu.getUserData(ItemId,'ObjStatus');
    if ((mi_isSDN == isSDN || mi_isSDN == 2 ) && ((!mi_ObjStatus) || (mi_ObjStatus && VMStatus.match(mi_ObjStatus))) && ((mi_objType & type) == type) ) {
        dhtmlxMenu.showItem(ItemId);
    } else {
        dhtmlxMenu.hideItem(ItemId);
    }
}
function Save2Session(name,value) {
    sessionStorage.setItem(name, value);
}
function GetFromSession(name,defaultValue="") {
    if(!sessionStorage.getItem(name)) {
        return defaultValue;
    } else {
        return sessionStorage.getItem(name);
    }                    

}
