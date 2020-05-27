<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/configuration.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . 'includes/vmm_restapi.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/SessionHandler.php');
    
    MySQLSessionHandler::session_start();

    function SaveUserPassword($User,$pass,$rememberme,$userRole,$AuthToken) {
        $_SESSION["username"]=$User;
        $_SESSION["password"]=$pass;
        $_SESSION["userRole"]=$userRole;
        $_SESSION["AuthToken"]=$AuthToken;
        if ($rememberme) {
            setcookie ("member_login",$User,time()+ (10 * 365 * 24 * 60 * 60));
            setcookie ("member_password",$pass,time()+ (10 * 365 * 24 * 60 * 60));
            setcookie ("member_UR",$userRole,time()+ (10 * 365 * 24 * 60 * 60));
            setcookie ("member_rememberme",$rememberme,time()+ (10 * 365 * 24 * 60 * 60));
        } else { 
            if(isset($_COOKIE["member_login"])) {
                setcookie ("member_login","");
            }
            if(isset($_COOKIE["member_password"])) {
                setcookie ("member_password","");
            }        
            if(isset($_COOKIE["member_UR"])) {
                setcookie ("member_UR","");
            }        
            if(isset($_COOKIE["member_rememberme"])) {
                setcookie ("member_rememberme","");
            }        

        }
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! empty($_POST['submit'])) {
            if ( isset($_POST['UserName']) && isset($_POST['psw']) ) {
                if (($_POST['UserName'] ==="admin") && ($_POST['psw'] === "ESXpumpkin1" )) {
                    SaveUserPassword($_POST['UserName'],$_POST['psw'],(!empty($_POST["rememberme"])));
                    $_SESSION['IsAdmin']=TRUE;
//                    header("Location: admin.php");
                    print_r("main.php");
                    exit;
                }
                $user=$_POST['UserName'];
                $userpieces= explode("\\", $user);
                $domain=$userpieces[0];
                $username=$userpieces[1];  
                $pass=$_POST['psw'];
            } elseif (isset($_SESSION["username"]) && isset($_SESSION["password"])) {
                $user=$_SESSION["username"];
                $userpieces= explode("\\", $user);
                $domain=$userpieces[0];
                $username=$userpieces[1];  
                $pass=$_SESSION["password"];
            } else {
                $_SESSION["ERROR_MSG"]="You must enter username and password";
                // header("Location: index.php");
                print_r("index.php");
                exit;
            }
        } else {
            exit;
        }
    }
    if (isset($_POST['user_role'])) {
        $userRole=$_POST['user_role'];
    } else {
        $userRole="";
    }

    $vmm=new VMM();
    $vmm->user = $user;
    $vmm->password = $pass;
    $vmm->userrole = $userRole;
    $resCL=$vmm->CheckLogin();
    if ($resCL == "True") {
        $auth=$vmm->Login();
        SaveUserPassword($user,$pass,(!empty($_POST["rememberme"])),$userRole,$auth);
        print_r("main.php");
    } elseif ($resCL == "False") {
        $_SESSION["ERROR_MSG"]="Login Failed";
        print_r("index.php");
    } else {
        SaveUserPassword($user,$pass,(!empty($_POST["rememberme"])),"",""); 
        $_SESSION["AvailableURs"]=$resCL;
        print_r("login_ur.php");
    }
