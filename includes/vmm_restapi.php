<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/configuration.php');
function RemoveGUIDFromName($Name) {
    $pattern='/_\{?[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}\}?$/';
    return preg_replace($pattern, "", $Name);
}
class BaseParams {
    protected  $Tier            = array();
    protected  $TierID          = array();
    protected  $ExcludeTier     = array();
    protected  $Service         = array();
    protected  $ServiceID       = array();
    protected  $ExcludeVM       = array();
    protected  $Vm              = array();
    protected  $VmID            = array();
    protected  $parameterSet    = array();
    protected  $ValidationError = "";
    public function __set($name, $value) {
        if ($name !== 'ValidationError') {
            if (!is_array($value) && !empty($value)) {
                $value=[$value];
            }
        }
        $this->{$name} = $value;
    }
    public function __get($name) {
        return $this->{$name};
    }
    public function __isset (  $name ) {
        return $this->int_isset($this->{$name});
    }
    protected function int_isset( $value) {
        return ((is_array($value) && count($value) > 0) || (!is_array($value) && $value !== ""));
    }
    protected function ValidateTierID() {
        if ( $this->int_isset($this->TierID)) {
            if ($this->int_isset($this->ServiceID) 
                || $this->int_isset($this->Tier)
                || $this->int_isset($this->Service)
                || $this->int_isset($this->ExcludeTier)
                || $this->int_isset($this->ExcludeVM)
                || $this->int_isset($this->Vm)
                || $this->int_isset($this->VmID)) {
                return false;
            } else {
                $this->parameterSet[]="TierID";
            }
        }
        return true;
    }
    protected function ValidateServiceID() {
        if ( $this->int_isset($this->ServiceID)) {
            if (
                $this->int_isset($this->TierID)
                || $this->int_isset($this->Tier)
                || $this->int_isset($this->Service)
                || $this->int_isset($this->ExcludeTier)
                || $this->int_isset($this->ExcludeVM)
                || $this->int_isset($this->Vm)
                || $this->int_isset($this->VmID) ) {
                    return false;
                } else {
                    $this->parameterSet[]="ServiceID";
                }
        } 
        return true;
    }
    protected function ValidateVmID() {
        if ( $this->int_isset($this->VmID) ) {
            if (
                $this->int_isset($this->ServiceID)
                || $this->int_isset($this->Tier)
                || $this->int_isset($this->Service)
                || $this->int_isset($this->ExcludeTier)
                || $this->int_isset($this->ExcludeVM)
                || $this->int_isset($this->Vm)
                || $this->int_isset($this->TierID) ) {
                    return false;
                } else {
                    $this->parameterSet[]="VmID";
                }
        }
        return true;
    }
    protected function ValidateService() {
        if (  $this->int_isset($this->Service)) {
            if ($this->int_isset($this->TierID)
                || $this->int_isset($this->ServiceID)
                || $this->int_isset($this->Tier)
                || $this->int_isset($this->VmID) ) {
                return false;
            } else {
                $this->parameterSet[]="Service";
            }
        }
        return true;
    }
    protected function ValidateTier() {
        if ( $this->int_isset($this->Tier) && $this->int_isset($this->Service)) {
            if ( $this->int_isset($this->TierID)
                || $this->int_isset($this->ServiceID)
                || $this->int_isset($this->ExcludeTier)
                || $this->int_isset($this->ExcludeVM)
                || $this->int_isset($this->Vm)
                || $this->int_isset($this->VmID) ) {
                return false;
            } else {
                $this->parameterSet[]="Tier";
            }
        }
        return true;
    }
    protected function ValidateVM() {
        if ( $this->int_isset($this->Vm) && $this->int_isset($this->Service) ){
            if ( $this->int_isset($this->ServiceID)
                || $this->int_isset($this->TierID)
                || $this->int_isset($this->Tier)
                || $this->int_isset($this->ExcludeTier)
                || $this->int_isset($this->ExcludeVM)
                || $this->int_isset($this->VmID) ) {
                return false;
            } else {
                $this->parameterSet[]="VM";
            }
        }
        return true;
    }
    protected function Validate() {
        $this->ValidateTierID();
        $this->ValidateServiceID();
        $this->ValidateVmID();
        $this->ValidateService();
        $this->ValidateTier();
        $this->ValidateVM();
    }
    protected function GetJsonObject() {
        if (!isset($myObj)) $myObj = new stdClass();
        if ($this->int_isset($this->ServiceID)) {
            $myObj->ServiceID=array($this->ServiceID);
        }
        if ($this->int_isset($this->TierID)) {
            $myObj->TierID=array($this->TierID);
        }
        if ($this->int_isset($this->VmID)) {
            $myObj->VmID=array($this->VmID);
        }
        if ($this->int_isset($this->Tier)) {
            $myObj->Tier=array($this->Tier);
        }
        if ($this->int_isset($this->Service)) {
            $myObj->Service=array($this->Service);
        }
        if ($this->int_isset($this->ExcludeTier)) {
            $myObj->ExcludeTier=array($this->ExcludeTier);
        }
        if ($this->int_isset($this->ExcludeVM)) {
            $myObj->ExcludeVM=array($this->ExcludeVM);
        }
        if ($this->int_isset($this->Vm)) {
            $myObj->Vm=array($this->Vm);
        }
        return $myObj;
    }
}
class CreateSnapshotParams extends BaseParams{
    public  $SnapshotName    = "";
    public  $Description     = "";
    public  $WithMemory      = false;
    public  $Force           = false;
    
    function Validate() {
        if ($this->SnapshotName == "") {
            return false;
        }
        parent::Validate();
        return (count($this->parameterSet) == 1 );
    }
    function GetJson() {
        $this->Validate();
        $myObj=$this->GetJsonObject();
        if ($this->Description !== "") {
            $myObj->Description=$this->Description;
        }
        $myObj->SnapshotName=$this->SnapshotName;
        $myObj->WithMemory=$this->WithMemory;
        $myObj->Force=$this->Force;    
        return json_encode($myObj);
    }
}
class RevertSnapshotParams extends BaseParams{
    public  $SnapshotName    = "";
    public  $SnapshotID      = "";
    
    function Validate() {
        if ($this->SnapshotName == "" && $this->SnapshotID == "") {
            return false;
        }
        parent::Validate();
        return (count($this->parameterSet) == 1 );
    }
    function GetJson() {
        $this->Validate();
        $myObj=$this->GetJsonObject();
        if ($this->SnapshotName != "") {
            $myObj->SnapshotName=$this->SnapshotName;
        }
        if ($this->SnapshotID != "") {
            $myObj->SnapshotID=$this->SnapshotID;
        }
        return json_encode($myObj);
    }
}
class DeleteSnapshotParams extends BaseParams{
    public  $SnapshotName    = "";
    public  $SnapshotID      = "";
    public  $Force           = FALSE;
    public  $RemoveChildren  = FALSE;
    
    function Validate() {
        if ($this->SnapshotName == "" && $this->SnapshotID == "") {
            return false;
        }
        parent::Validate();
        return (count($this->parameterSet) == 1 );
    }
    function GetJson() {
        $this->Validate();
        $myObj=$this->GetJsonObject();
        if ($this->SnapshotName != "") {
            $myObj->SnapshotName=$this->SnapshotName;
        }
        if ($this->SnapshotID != "") {
            $myObj->SnapshotID=$this->SnapshotID;
        }
        $myObj->Force=$this->Force;
        $myObj->RemoveChildren=$this->RemoveChildren;
        return json_encode($myObj);
    }
}
class VMOpsParams extends BaseParams {
    public $Operation       = "";
    public $OperationParams = "";
    function Validate() {
        if ($this->Operation == "") {
            return false;
        }
        parent::Validate();
        return (count($this->parameterSet) == 1 );
    }
    function GetJson() {
        $this->Validate();
        $myObj=$this->GetJsonObject();
        $myObj->Operation=$this->Operation;
        if ($this->OperationParams !== "") {
            $myObj->OperationParams=$this->OperationParams;
        }
        return json_encode($myObj);
    }    
}
class VMM {
    private $restserver;
    private $vmmServer;
    public $user;
    public $password;
    public $authHeader;
    public $userrole;
    public $cloud;
    
    function __construct() {
        $this->restserver = RESTAPIServer;
        $this->vmmServer = vmmServer;
        $this->user = "";
        $this->password = "";
        $this->authHeader = "";
        $this->cloud = "";
        $this->userrole = "";
    }
    function CheckLogin() {
        $BaseURL = "{$this->restserver}/rest.php?uri=checklogin.ps1";
        $authHeader="";
        if (!empty($this->authHeader)) {
            $authHeader=$this->authHeader;
        } else {
            $auth=$this->vmmServer . "\\" . $this->user . ":" .$this->password;
            $authHeader='Basic '. base64_encode($auth);
            if (!empty($this->cloud)) {
                $BaseURL = $BaseURL . "&cloud=" . $this->cloud;
            } elseif (!empty ($this->userrole)) {
                $BaseURL = $BaseURL . "&userRole=" . $this->userrole;
            }
        }
        $headers = ['Content-Type:application/json',
            'Authorization: ' . $authHeader
            ];

        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL,$BaseURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            return trim($return);
        } catch ( Exception $e ) {
            
        } finally {
            curl_close($ch);
        }
    }
    function Login() {
        $BaseURL = "{$this->restserver}/rest.php?uri=login.ps1";
        $authHeader="";
        if (!empty($this->authHeader)) {
            $authHeader=$this->authHeader;
        } else {
            $auth=$this->vmmServer . "\\" . $this->user . ":" .$this->password;
            $authHeader='Basic '. base64_encode($auth);
            if (!empty($this->cloud)) {
                $BaseURL = $BaseURL . "&cloud=" . $this->cloud;
            } elseif (!empty ($this->userrole)) {
                $BaseURL = $BaseURL . "&userRole=" . $this->userrole;
            }
        }
        $headers = ['Content-Type:application/json',
            'Authorization: ' . $authHeader
            ];

        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL,$BaseURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            return "INT ". trim($return);
        } catch ( Exception $e ) {
            
        } finally {
            curl_close($ch);
        }
    }
    function GetAllServices() {
        $BaseURL = "{$this->restserver}/rest.php?uri=service/getservice.ps1";
        if (empty($this->authHeader)) {
            throw new Exception('Not Authorized');
        }
        $headers = ['Content-Type:application/json',
        'Authorization: ' . $this->authHeader
        ];
        if (!isset($myObj)) $myObj = new stdClass();
        $myObj->All="true";
        $myObj->ParameterSetName="ALL";
        $myJSON = json_encode($myObj);
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL,$BaseURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $myJSON);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            return json_decode($return);
        } catch ( Exception $e ) {
            
        } finally {
            curl_close($ch);
        }
    }
    function GetAllVMs() {
        $BaseURL = "{$this->restserver}/rest.php?";
        if (empty($this->authHeader)) {
            throw new Exception('Not Authorized');
        }
        $headers = ['Content-Type:application/json',
        'Authorization: ' . $this->authHeader
        ];
        $query=array(
            'uri'=>'get_vm.ps1',
            'ALL'=>'true'
        );
        $BaseURL = $BaseURL . http_build_query($query);
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL,$BaseURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            return json_decode($return);
        } catch ( Exception $e ) {
            
        } finally {
            curl_close($ch);
        }        
    }
    function CreateSnapshot(CreateSnapshotParams $params) {
        $BaseURL = "{$this->restserver}/rest.php?uri=snapshot/createsnapshot.ps1&async=1";
        if (empty($this->authHeader)) {
            throw new Exception('Not Authorized');
        }
        $headers = ['Content-Type:application/json',
        'Authorization: ' . $this->authHeader
        ];
        $myJSON = $params->GetJson();
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL,$BaseURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $myJSON);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            return json_decode($return);
        } catch ( Exception $e ) {
            
        } finally {
            curl_close($ch);
        }

    }
    function RevertSnapshot(RevertSnapshotParams $params) {
        $BaseURL = "{$this->restserver}/rest.php?uri=snapshot/revertsnapshot.ps1&async=1";
        if (empty($this->authHeader)) {
            throw new Exception('Not Authorized');
        }
        $headers = ['Content-Type:application/json',
        'Authorization: ' . $this->authHeader
        ];
        $myJSON = $params->GetJson();
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL,$BaseURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $myJSON);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            return json_decode($return);
        } catch ( Exception $e ) {
            
        } finally {
            curl_close($ch);
        }

    }
    function DeleteSnapshot(DeleteSnapshotParams $params) {
        $BaseURL = "{$this->restserver}/rest.php?uri=snapshot/deletesnapshot.ps1&async=1";
        if (empty($this->authHeader)) {
            throw new Exception('Not Authorized');
        }
        $headers = ['Content-Type:application/json',
            'Authorization: ' . $this->authHeader
            ];
        $myJSON = $params->GetJson();
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL,$BaseURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $myJSON);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            return json_decode($return);
        } catch ( Exception $e ) {
            
        } finally {
            curl_close($ch);
        }

    }
    function DeleteService($ServiceID) {
        $BaseURL = "{$this->restserver}/rest.php?uri=service/deleteservice.ps1";
        if (empty($this->authHeader)) {
            throw new Exception('Not Authorized');
        }
        $headers = ['Content-Type:application/json',
        'Authorization: ' . $this->authHeader
        ];
        if (!isset($myObj)) $myObj = new stdClass();
        $myObj->ID=$ServiceID;
        $myJSON = json_encode($myObj);
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL,$BaseURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $myJSON);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            return json_decode($return);
        } catch ( Exception $e ) {
            
        } finally {
            curl_close($ch);
        }

    }
    function PowerOn(VMOpsParams $params) {
        $BaseURL = "{$this->restserver}/rest.php?uri=vm_operation.ps1";
        if (empty($this->authHeader)) {
            throw new Exception('Not Authorized');
        }
        $headers = ['Content-Type:application/json',
        'Authorization: ' . $this->authHeader
        ];
        $params->Operation='poweron';
        $params->OperationParams="";
        $myJSON = $params->GetJson();
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL,$BaseURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $myJSON);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            return json_decode($return);
        } catch ( Exception $e ) {
            
        } finally {
            curl_close($ch);
        }
    }
    function PowerOff(VMOpsParams $params) {
        $BaseURL = "{$this->restserver}/rest.php?uri=vm_operation.ps1";
        if (empty($this->authHeader)) {
            throw new Exception('Not Authorized');
        }
        $headers = ['Content-Type:application/json',
        'Authorization: ' . $this->authHeader
        ];
        $params->Operation='poweroff';
        $params->OperationParams="Force";
        $myJSON = $params->GetJson();
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL,$BaseURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $myJSON);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            return json_decode($return);
        } catch ( Exception $e ) {
            
        } finally {
            curl_close($ch);
        }

    }
}
