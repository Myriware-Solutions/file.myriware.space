<?php
session_start();
//Verify POST integrety
$keys = ["meta", "username", "muid", "asa", "redirect", "validation"];
foreach ($keys as $key) {
    if (!array_key_exists($key, $_POST)) {
        // Incomplete POST request
        exit;
    }
}
// Verify right endpoint
if ($_POST['meta'] != "ACCOUNT_REDIRECT") {
    // Wrong Endopoint
    exit;
}
// Verify validity
if (!$_POST['validation']) {
    // Somehow, it was false but allowed to pass
    exit;
}
// Now that everthing is confirmed, set Session
$_SESSION = [
    "muid" => $_POST['muid'],
    "username" => $_POST['username'],
    "asa" => $_POST['asa']
];
// Redirect user
$redirect = "";
if ($_POST['redirect'] == "") {
    $redirect = "/";
} else {
    $redirect = $_POST['redirect'];
}
header("Location: $redirect");