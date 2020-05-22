<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/database.class.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/mysql.sessions.php');
    Session::session_start();
    if ( isset($_SESSION["AuthToken"]) && !empty($_SESSION["AuthToken"]) ) {
        header("Location: main.php");
        exit;
    }        
?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Hyper-V VM Manager Login</title>
        <script src="dhtmlx/codebase/dhtmlx.js"></script>     
        <link rel="stylesheet" href="dhtmlx/codebase/dhtmlx.css"> 
        <style>
            iframe.submit_iframe {
                    position: absolute;
                    width: 1px;
                    height: 1px;
                    left: -100px;
                    top: -100px;
                    font-size: 1px;
            }
            div.login_form {
                    position: relative;
                    margin-top: 200px;
                    margin-left: auto;
                    margin-right: auto;
                    height: 205px;
                    width: 350px;
                    box-shadow: 0px 0px 8px rgba(127, 127, 127, 0.4);
                    border: 1px solid #c0c0c0;
                    border-radius: 2px;
                    background-color: white;
            }
        </style>         
        <script>
            var myForm=null;
            function LoadForm(){
                myForm = new dhtmlXForm("dhxForm"); 
                myForm.loadStruct("ajax/forms/login/loginform_ur.php");
                myForm.enableLiveValidation(true);
                myForm.attachEvent("onButtonClick", function(name) {
                        // submit real form when user clicks Submit button on a dhtmlx form
                        if (name === "btnsubmit") {
                                myForm.send("actions/login.php","post", function(loader, response){
                                                        window.location.href = response;
                                                });
                        }
                });
            }
        </script>        
    </head>
    <body onload="LoadForm();">
        <?php
            if (isset($_SESSION["ERROR_MSG"])) {
                echo "<p style=\"font-size: medium;color: #FF0000;font-weight: bold;\">" . $_SESSION["ERROR_MSG"] . "</p>";
                unset($_SESSION["ERROR_MSG"]);
            }
        ?>    
        <div class="login_form">
            <form id="realForm" action="actions/login.php" method="POST" target="submit_ifr">
                <div id="dhxForm"></div>
            </form>
        </div>
        <iframe border="0" frameBorder="0" name="submit_ifr" class="submit_iframe"></iframe>
    </body>
</html>