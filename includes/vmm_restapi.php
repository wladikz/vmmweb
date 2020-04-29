<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/configuration.php');

class VMM {
    private $restserver;
    private $vmmServer;
    public $user;
    public $password;
    public $authHeader;
    public $userrole;
    public $cloud;
    
    function __construct() {
        global $RESTAPIServer,$vmmServer;
        $this->restserver = $RESTAPIServer;
        $this->vmmServer = $vmmServer;
        $this->user = "";
        $this->password = "";
        $this->authHeader = "";
        $this->cloud = "";
        $this->userrole = "";
    }
    function CheckLogin() {
        $BaseURL = "http://" . $this->restserver . "/rest.php?uri=checklogin.ps1";
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
        $BaseURL = "http://" . $this->restserver . "/rest.php?uri=login.ps1";
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
        $BaseURL = "http://" . $this->restserver . "/rest.php?uri=service/getservice.ps1";
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
        $BaseURL = "http://" . $this->restserver . "/rest.php?";
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
    function CreateSnapshotForService($ServiceID,$SnapshotName,$Description="",bool $WithMemory=false){
        $BaseURL = "http://" . $this->restserver . "/rest.php?uri=snapshot/createsnapshot.ps1&async=1";
        if (empty($this->authHeader)) {
            throw new Exception('Not Authorized');
        }
        $headers = ['Content-Type:application/json',
        'Authorization: ' . $this->authHeader
        ];
        if (!isset($myObj)) $myObj = new stdClass();
        $myObj->ServiceID=array($ServiceID);
        if ($Description !== "") {
            $myObj->Description=$Description;
        }
        $myObj->SnapshotName=$SnapshotName;
        $myObj->WithMemory=$WithMemory;
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
}
