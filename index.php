<?php
ob_start();
define('API_KEY', '8253736025:AAHmMPac7DmA_fi01urRtI0wwAfd7SAYArE');

/* ================= BOT FUNKSIYA (BU YO'Q EDI!) ================= */
function bot($method, $data = []) {
    $url = "https://api.telegram.org/bot" . API_KEY . "/$method";
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 60,
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res);
}

/* ================= FETCH JSON (cURL) ================= */
function fetchJson($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$update = json_decode(file_get_contents('php://input'));

// ====== GUARD: bo'sh webhooklarni to'xtatish ======
if (!isset($update->message) && !isset($update->callback_query)) {
    http_response_code(200);
    exit;
}

// ====== UPDATE DATA ======
$message  = $update->message ?? null;
$callback = $update->callback_query ?? null;

$cid  = $message->chat->id ?? null;
$type = $message->chat->type ?? null;
$mid  = $message->message_id ?? null;
$text = $message->text ?? null;

$callbackdata = $callback->data ?? null;
$aa   = $callback->id ?? null;
$call = $callback->message->chat->id ?? null;
$cal  = $callback->message->message_id ?? null;

$name = $message->from->first_name ?? '';

// Bot username olish
$botInfo = bot('getMe', []);
$botname = $botInfo->result->username ?? 'unknown_bot';

$date = date("d.m.Y");
$soat = date("H:i");

$bosh = json_encode([
    'inline_keyboard' => [
        [['text' => "➕ Guruhga qo'shish", 'url' => "https://t.me/$botname?startgroup=new"]],
    ]
]);

$ortga = json_encode([
    'inline_keyboard' => [
        [['text' => "❌ O'chirish", 'callback_data' => "del"]],
        [['text' => "➕ Guruhga qo'shish", 'url' => "https://t.me/$botname?startgroup=new"]],
    ]
]);

$matin = "📥 Yuklab olindi ushbu bot orqali";

/* ================= DELETE CALLBACK ================= */
if ($callbackdata == "del") {
    bot('answerCallbackQuery', [
        'callback_query_id' => $aa,
        'text' => "✅ O'chirildi"
    ]);
    bot('deleteMessage', [
        'chat_id' => $call,
        'message_id' => $cal
    ]);
    exit;
}

/* ================= MUSIC DOWNLOAD CALLBACK ================= */
if ($callbackdata && strpos($callbackdata, "music_") === 0) {
    
    bot("answerCallbackQuery", [
        "callback_query_id" => $aa,
        "text" => "🎧 Musiqa yuborilmoqda...",
        "show_alert" => false
    ]);

    $parts = explode("_", $callbackdata);
    $index = $parts[1] ?? 0;
    $query = base64_decode($parts[2] ?? '');

    $api_url = "https://api.alijonov.uz/api/music.php?text=" . urlencode($query) . "&page=1";
    $api = fetchJson($api_url);

    $music = $api['data'][$index] ?? null;

    if (!$music || !isset($music['url'])) {
        bot('sendMessage', [
            'chat_id' => $call,
            'text' => "❌ Musiqa yuklab bo'lmadi"
        ]);
        exit;
    }

    $artist = $music['artist'] ?? 'Unknown';
    $title  = $music['title'] ?? 'Unknown';
    $audio  = $music['url'];

    $caption = "<b>$artist</b> - <i>$title</i>\n\n@$botname\n⏰ $soat  📅 $date";

    bot('sendAudio', [
        'chat_id' => $call,
        'audio' => $audio,
        'caption' => $caption,
        'parse_mode' => 'html'
    ]);
    exit;
}

/* ================= START ================= */
if (($text == "/start" || $text == "/start@$botname") && $type == "private") {
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>🔥 Assalomu alaykum, @$botname ga Xush kelibsiz!

• Instagram - stories, post va IGTV + audio bilan
• TikTok - suv belgisiz video
• YouTube - video

🎵 Musiqa qidirish:
Shunchaki qo'shiq nomini yozing!

😎 Bot guruhlarda ham ishlaydi!</b>",
        'parse_mode' => 'html',
        'reply_markup' => $bosh,
    ]);
    exit;
}

/* ================= INSTAGRAM ================= */
if ($text && strpos($text, "instagram.com") !== false) {

    $loading = bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "📥 Yuklanmoqda..."
    ]);
    $loadingMid = $loading->result->message_id ?? null;

    $api_url = "https://xuss.us/IG1/?url=" . urlencode($text);
    $json = fetchJson($api_url);

    $video_url = $json['video'] 
        ?? $json['url'] 
        ?? $json['data']['video'] 
        ?? $json['videos'][0]['url'] 
        ?? null;

    if ($loadingMid) {
        bot('deleteMessage', ['chat_id' => $cid, 'message_id' => $loadingMid]);
    }

    if (!$video_url) {
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "❌ Instagram video topilmadi"
        ]);
        exit;
    }

    bot('sendVideo', [
        'chat_id' => $cid,
        'video' => $video_url,
        'caption' => "$matin\n@$botname",
        'parse_mode' => 'html',
        'reply_markup' => $ortga,
    ]);
    exit;
}

/* ================= TIKTOK ================= */
if ($text && (strpos($text, "tiktok.com") !== false || strpos($text, "vt.tiktok") !== false)) {

    $loading = bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "📥 TikTok yuklanmoqda..."
    ]);
    $loadingMid = $loading->result->message_id ?? null;

    $TikTok = fetchJson("https://tikwm.com/api/?url=" . urlencode($text));
    $play = $TikTok['data']['play'] ?? null;

    if ($loadingMid) {
        bot('deleteMessage', ['chat_id' => $cid, 'message_id' => $loadingMid]);
    }

    if (!$play) {
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "❌ TikTok video topilmadi"
        ]);
        exit;
    }

    bot('sendVideo', [
        'chat_id' => $cid,
        'video' => $play,  // CURLFile kerak emas!
        'caption' => "$matin\n@$botname",
        'parse_mode' => 'html',
        'reply_markup' => $ortga,
    ]);
    exit;
}

/* ================= YOUTUBE ================= */
if ($text && (strpos($text, "youtu.be") !== false || strpos($text, "youtube.com") !== false)) {

    $loading = bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "📥 YouTube yuklanmoqda..."
    ]);
    $loadingMid = $loading->result->message_id ?? null;

    $api_url = "https://4503091-gf96974.twc1.net/Api/YouTube.php?url=" . urlencode($text);
    $natija = fetchJson($api_url);

    if ($loadingMid) {
        bot('deleteMessage', ['chat_id' => $cid, 'message_id' => $loadingMid]);
    }

    $video_title = $natija['title'] ?? '';
    $video_url = $natija['video_with_audio'][0]['url'] ?? null;

    if (!$video_url) {
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "❌ YouTube video topilmadi"
        ]);
        exit;
    }

    bot('sendVideo', [
        'chat_id' => $cid,
        'video' => $video_url,
        'caption' => "$video_title\n\n$matin\n@$botname",
        'parse_mode' => 'html',
        'reply_markup' => $ortga,
    ]);
    exit;
}

/* ================= MUSIC SEARCH ================= */
if ($text && $type == "private" && !preg_match('/^\//', $text)) {

    $api_url = "https://api.alijonov.uz/api/music.php?text=" . urlencode($text) . "&page=1";
    $api = fetchJson($api_url);

    if (!isset($api['data']) || empty($api['data'])) {
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "😔 Musiqa topilmadi: <b>$text</b>",
            'parse_mode' => 'html'
        ]);
        exit;
    }

    $list = array_slice($api['data'], 0, 10);
    $inline_keyboard = [];
    $msctitle = "";
    $encodedQuery = base64_encode($text);

    foreach ($list as $index => $music) {
        $number = $index + 1;
        $artist = $music['artist'] ?? 'Unknown';
        $title  = $music['title'] ?? 'Unknown';

        $msctitle .= "<b>$number.</b> <i>$artist - $title</i>\n";

        $row = $index < 5 ? 0 : 1;
        $inline_keyboard[$row][] = [
            'text' => "$number",
            'callback_data' => "music_{$index}_{$encodedQuery}"
        ];
    }

    $inline_keyboard[] = [['text' => "❌ Yopish", 'callback_data' => "del"]];
    $reply_markup = json_encode(['inline_keyboard' => $inline_keyboard]);

    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "🎵 <b>Natijalar:</b> $text\n\n$msctitle",
        'parse_mode' => 'html',
        'reply_markup' => $reply_markup
    ]);
    exit;
}