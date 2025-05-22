<?php

if (!isset($_SESSION['muid'])) {
    $h_redirect = (($_SERVER['SERVER_NAME']==='localhost')?"http":"https")."://".$_SERVER['SERVER_NAME'];
    $signin_link = "https://account.myriware.space/login?host_redirect=$h_redirect&redirect=/download/".$_GET['link'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download</title>
    <link rel="stylesheet" href="/public/assets/css/download.css">
</head>
<body>
    <div class="link-display">
        <p class="big-text">Please Signin to access Myriware Files.</p>
        <a class="big-text" href="<?php echo $signin_link ?>">Signin</a>
    </div>
</body>
</html>
<?php
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download</title>
    <link rel="stylesheet" href="/public/assets/css/download.css">
</head>
<body>
    <div class="link-display">
        <p>Account: <?php echo $_SESSION['username']; ?> [<?php echo $_SESSION['muid']; ?>]</p>
        <hr>
        <p class="big-text">Click the link below to download the requested Myriware File Asset.</p>
        <a class="big-text" href="/FILE_DIR/<?php echo $_GET['link'] ?>" download>Download</a>
    </div>
</body>
</html>
<?php
}