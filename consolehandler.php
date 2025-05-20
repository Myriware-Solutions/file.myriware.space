<?php

include_once "files.php";
if (!str_contains($_SESSION['asa'], '+#+&+^+-+$%[+#+[+[+@_(+$_&')) {
    include "./source/forbidden.html";
    exit;
}

//TODO finish handeling the commands passed from the user.