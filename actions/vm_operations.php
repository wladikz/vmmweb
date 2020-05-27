<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/vmm_restapi.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/SessionHandler.php');
    MySQLSessionHandler::session_start();
    
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (!empty($_GET['operation']) && !empty($_GET['id'])) {
            preg_match("/^svc_|^ct_|^vm_/",$_GET['id'],$matches);
            $type=$matches[0];
            $id=substr($_GET["id"], strlen($type));
            $result="";
            $xml = new SimpleXMLElement('<xml version="1.0"/>');
            $root=$xml->addChild("Task");
            $vmm=new VMM();
            $vmm->authHeader=$_SESSION["AuthToken"];
            switch ($_GET['operation']) {
                case 'DeleteSnapshot':
                case 'DelWChildsSnapshot':
                    $params=new DeleteSnapshotParams();
                    $params->SnapshotID=substr($_GET["snapID"], 3);
                    $params->SnapshotName=$_GET["snapName"];
                    switch ($type) {
                        case 'svc_':
                            $params->ServiceID=$id;
                            break;
                        case 'ct_':
                            $params->TierID=$id;
                            break;
                        case 'vm_':
                            $params->VmID=$id;
                            break;
                        default:
                            break;
                    }
                    $params->RemoveChildren=($_GET['operation'] == 'DelWChildsSnapshot' );
                    $params->Force=($_GET['operation'] == 'DelWChildsSnapshot' );
                    $res=$vmm->DeleteSnapshot($params);
                    $root->addChild("Status", $res->Status);
                    break;
                case 'CreateSnapshot':
                    $params=new CreateSnapshotParams();
                    $params->SnapshotName=$_GET['SnapshotName'];
                    $params->Description=$_GET['Description'];
                    $params->WithMemory=($_GET['WithMemory'] == 1);
                    switch ($type) {
                        case 'svc_':
                            $params->ServiceID=$id;
                            break;
                        case 'ct_':
                            $params->TierID=$id;
                            break;
                        case 'vm_':
                            $params->VmID=$id;
                            break;
                        default:
                            break;
                    }
                    $res=$vmm->CreateSnapshot($params);
                    $root->addChild("Status", $res->Status);
                    break;
                case 'RevertSnapshot':
                    $params=new RevertSnapshotParams();
                    $params->SnapshotID=substr($_GET["snapID"], 3);
                    $params->SnapshotName=$_GET["snapName"];
                    switch ($type) {
                        case 'svc_':
                            $params->ServiceID=$id;
                            break;
                        case 'ct_':
                            $params->TierID=$id;
                            break;
                        case 'vm_':
                            $params->VmID=$id;
                            break;
                        default:
                            break;
                    }
                    $res=$vmm->RevertSnapshot($params);
                    $root->addChild("Status", $res->Status);
                    break;
                case 'PowerOn':
                    $params=new VMOpsParams();
                    switch ($type) {
                        case 'svc_':
                            $params->ServiceID=$id;
                            break;
                        case 'ct_':
                            $params->TierID=$id;
                            break;
                        case 'vm_':
                            $params->VmID=$id;
                            break;
                    }
                    $res=$vmm->PowerOn($params);
                    $root->addChild("Status", $res->Status);
                    break;
                case 'PowerOff':
                    $params=new VMOpsParams();
                    switch ($type) {
                        case 'svc_':
                            $params->ServiceID=$id;
                            break;
                        case 'ct_':
                            $params->TierID=$id;
                            break;
                        case 'vm_':
                            $params->VmID=$id;
                            break;
                    }
                    $res=$vmm->PowerOff($params);
                    $root->addChild("Status", $res->Status);
                    break;

                case 'DeleteItem':
                    switch ($type) {
                        case 'svc_':
                            $res=$vmm->DeleteService($id);
                            break;
                        case 'ct_':
                            $params->TierID=$id;
                            break;
                        case 'vm_':
                            $params->VmID=$id;
                            break;
                    }
                    $root->addChild("Status", $res->Status);
                    break;
                default:
                    $root->addChild("Status", "Success");
                    break;
            }
        }
    }
    $result=$root->asXML();
    Header('Content-type: text/xml');
    print($result);    