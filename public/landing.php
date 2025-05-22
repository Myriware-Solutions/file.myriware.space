<?php
$h_redirect = (($_SERVER['SERVER_NAME']==='localhost')?"http":"https")."://".$_SERVER['SERVER_NAME'];
$signin_link = "https://account.myriware.space/login?host_redirect=$h_redirect&redirect=/console";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>WARNING: This site is for admin ONLY. If you do not possess ASA <code>+#+&+^+-+$%[+#+[+[+@_(+$_&</code>, please Leave</p>
    <p><a href="<?php echo $signin_link; ?>">Admin Login</a></p>
</body>
</html>