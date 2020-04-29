<?php

function xmlEscape($string) {
    return str_replace(array('&', '<', '>', '\'', '"'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $string);
}
class SimpleXMLExtended extends SimpleXMLElement{ 
  public function addCData($cdata_text){ 
   $node= dom_import_simplexml($this); 
   $no = $node->ownerDocument; 
   $node->appendChild($no->createCDATASection($cdata_text)); 
  } 
} 