<?php
include_once "files.php";
if (!str_contains($_SESSION['asa'], '+#+&+^+-+$%[+#+[+[+@_(+$_&')) {
    include "./source/forbidden.html";
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Myriware Source File Manager</title>
    <script type="module" src="/source/console.js"></script>
    <link rel="stylesheet" href="/source/console.css">
</head>
<?php
    include "./source/console.html";
?>
</html>
<?php
}