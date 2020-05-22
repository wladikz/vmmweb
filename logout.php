<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/database.class.php');
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/mysql.sessions.php');
    Session::session_start();
    if (empty($_SESSION["username"]) || empty($_SESSION["password"])) {
        header("Location: index.php");
    } else {
        session_unset();
        session_destroy();
        header("Location: index.php");
    }
