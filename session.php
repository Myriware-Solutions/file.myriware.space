<?php
// Confirm session integrety/logged in
function confirm_session_integrety(): bool {
    $keys = ["muid", "username", "asa"];
    foreach ($keys as $key) {
        if (!array_key_exists($key, $_SESSION)) {
            return false;
        }
    }
    return true;
}
// output account json, or nothing if not logged in
$json = [];
if (confirm_session_integrety()) {
    $json['signin'] = true;
    $json['muid'] = $_SESSION['muid'];
    $json['username'] = $_SESSION['username'];
    $json['asa'] = $_SESSION['asa'];
} else {
    $json['signin'] = false;
    $json['muid'] = null;
    $json['username'] = null;
    $json['asa'] = null;
}
// echo information
header('Content-Type: application/json');
echo json_encode($json);