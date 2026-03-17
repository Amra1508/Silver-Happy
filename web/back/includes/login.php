<?php
if (!isset($_COOKIE['session_token']) || empty($_COOKIE['session_token'])) {
    header("Location: /front/account/signin.php");
    exit();
}
