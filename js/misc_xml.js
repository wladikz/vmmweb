function StringToXMLDom(string){
    var xmlDoc=null;
    if (window.DOMParser) {
        parser=new DOMParser();
        xmlDoc=parser.parseFromString(string,"text/xml");
    } else { // Internet Explorer
        xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
        xmlDoc.async="false";
        xmlDoc.loadXML(string);
    }
    return xmlDoc;
}