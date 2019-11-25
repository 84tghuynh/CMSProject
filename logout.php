<?php
    session_start(); // Fires off a session cookie.
    
    $_SESSION = [];
    header("Location: login.php");
    exit();
                        
?>