<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/vmm_restapi.php');
    session_start();
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (!empty($_GET['operation']) && !empty($_GET['id'])) {
            preg_match("/^svc_|^ct_|^vm_/",$_GET['id'],$matches);
            $type=$matches[0];
            $id=$id=substr($_GET["id"], strlen($type));
            $result="";
            switch ($_GET['operation']) {
                case 'CreateSnapshot':
                    $vmm=new VMM();
                    $vmm->authHeader=$_SESSION["AuthToken"];
                    switch ($type) {
                        case 'svc_':
                            $res=$vmm->CreateSnapshotForService($id,$_GET['SnapshotName'],$_GET['Description'],($_GET['WithMemory'] == 1) );
                            break;
                        default:
                            break;
                    }
                    $xml = new SimpleXMLElement('<xml version="1.0"/>');
                    $root=$xml->addChild("Task");
                    $root->addChild("Status", $res->Status);
                    $result=$root->asXML();
                    
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    }
    Header('Content-type: text/xml');
    print($result);    