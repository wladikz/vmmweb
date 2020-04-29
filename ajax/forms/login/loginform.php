<?php
session_start();
$xml = new SimpleXMLElement('<xml version="1.0"/>');
$items=$xml->addChild("items");
$item=$items->addChild("item");
$item->addAttribute("type", "settings");
$item->addAttribute("position","label-left");
$item->addAttribute("labelWidth",100);
$item->addAttribute("inputWidth",150);
$FieldSet=$items->addChild("item");
$FieldSet->addAttribute("type", "block");
$FieldSet->addAttribute("blockOffset",30);
$FieldSet->addAttribute("offsetTop",15);
$FieldSet->addAttribute("width","auto");

$fldLabel1=$FieldSet->addChild("item");
$fldLabel1->addAttribute("type", "label");
$fldLabel1->addAttribute("label", "Please introduce yourself");
$fldLabel1->addAttribute("labelWidth","auto");
$fldLabel1->addAttribute("offsetLeft",35);

$fldUserName=$FieldSet->addChild("item");
$fldUserName->addAttribute("type", "input");
$fldUserName->addAttribute("label", "UserName");
$fldUserName->addAttribute("name", "UserName");
if (!empty($_COOKIE["member_login"])) {
    $fldUserName->addAttribute("value", $_COOKIE["member_login"]);
} else {
    $fldUserName->addAttribute("value", "");
}
$fldUserName->addAttribute("validate","^((verint\\\\.+)|(eislab-il\\\\.+)|admin)$");
$fldUserName->addAttribute("required","TRUE");  
$fldPassword=$FieldSet->addChild("item");
$fldPassword->addAttribute("type", "password");
$fldPassword->addAttribute("label", "Password");
$fldPassword->addAttribute("name", "psw");
if (!empty($_COOKIE["member_password"])) {
    $fldPassword->addAttribute("value", $_COOKIE["member_password"]);
} else {
    $fldPassword->addAttribute("value", "");
}
$fldPassword->addAttribute("validate","^.+");
$fldPassword->addAttribute("required","true");
$fldRememberme=$FieldSet->addChild("item");
$fldRememberme->addAttribute("type", "checkbox");
$fldRememberme->addAttribute("label", "Remember me");
$fldRememberme->addAttribute("name", "rememberme");
if(isset($_COOKIE["member_login"])) {
    $fldRememberme->addAttribute("checked", "true");
}
$field=$FieldSet->addChild("item");
$field->addAttribute("type", "input");
$field->addAttribute("name", "submit");
$field->addAttribute("hidden", "true");        
$field->addAttribute("value", "aaa");
$fldbtnsubmit=$FieldSet->addChild("item");
$fldbtnsubmit->addAttribute("type", "button");
$fldbtnsubmit->addAttribute("name", "btnsubmit");
$fldbtnsubmit->addAttribute("value", "Sign in");
$fldbtnsubmit->addAttribute("offsetLeft", "75");

$tmp=$xml->asXML();
Header('Content-type: text/xml');
print($xml->asXML()); 
