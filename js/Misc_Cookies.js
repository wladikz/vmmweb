function SaveColWidth(dhtmlGrid) {
    const gridName = dhtmlGrid.getUserData('', 'name');
    var colwidth = new Array(dhtmlGrid.getColumnsNum());
    for (i = 0; i < dhtmlGrid.getColumnsNum(); i++) {
        var tmp=dhtmlGrid.getColWidth(i);
        if (!isNaN(tmp)) {
            colwidth[i] = tmp
        }
        
    }
    const sColWidth = colwidth.toString();
    cookie.set(gridName, sColWidth, { expires: 365 })
}
function GetColWidth(dhtmlGrid) {
    const gridName = dhtmlGrid.getUserData('', 'name');
    var sColWidth=cookie.get(gridName);
    if(sColWidth == null){
        var colwidth = new Array(dhtmlGrid.getColumnsNum());
        for (i = 0; i < dhtmlGrid.getColumnsNum(); i++) {
            var tmp=dhtmlGrid.getColWidth(i);
            if (!isNaN(tmp)) {
                colwidth[i] = tmp
            }
        }
        sColWidth = colwidth.toString();
    }    
    return sColWidth;
}