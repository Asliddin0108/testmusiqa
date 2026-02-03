<?php
ob_start();
define('API_KEY', '8253736025:AAHmMPac7DmA_fi01urRtI0wwAfd7SAYArE');

// ========== Faylda qidiruv so'rovlarini saqlash ==========
$cache_file = __DIR__ . '/search_cache.json';

function saveSearch($id, $query) {
    global $cache_file;
    $data = file_exists($cache_file) ? json_decode(file_get_contents($cache_file), true) : [];
    $data[$id] = ['query' => $query, 'time' => time()];
    // Eski yozuvlarni tozalash (1 soatdan eski)
    foreach ($data as $key => $val) {
        if (time() - $val['time'] > 3600) unset($data[$key]);
    }
    file_put_contents($cache_file, json_encode($data));
}

function getSearch($id) {
    global $cache_file;
    if (!file_exists($cache_file)) return null;
    $data = json_decode(file_get_contents($cache_file), true);
    return $data[$id]['query'] ?? null;
}

/* ================= BOT FUNKSIYA ================= */
function bot($method, $data = []) {
    $url = "https://api.telegram.org/bot" . API_KEY . "/$method";
    $ch = curl_init($url);
    
    // Agar fayl bo'lsa multipart ishlatish
    $hasFile = false;
    foreach ($data as $val) {
        if ($val instanceof CURLFile) {
            $hasFile = true;
            break;
        }
    }
    
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 120,
    ];
    
    if ($hasFile) {
        $options[CURLOPT_POSTFIELDS] = $data;
    } else {
        $options[CURLOPT_POSTFIELDS] = http_build_query($data);
    }
    
    curl_setopt_array($ch, $options);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res);
}

/* ================= FETCH JSON ================= */
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

/* ================= FAYLNI YUKLAB OLISH ================= */
function downloadFile($url, $filename) {
    $ch = curl_init($url);
    $fp = fopen($filename, 'w+');
    curl_setopt_array($ch, [
        CURLOPT_FILE => $fp,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0'
    ]);
    $success = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    
    if ($success && $httpCode == 200 && filesize($filename) > 1000) {
        return true;
    }
    @unlink($filename);
    return false;
}

/* ================= MUSIQA QIDIRISH FUNKSIYASI ================= */
function searchMusic($query) {

    // 1️⃣ API: alijonov
    $api1 = fetchJson("https://api.alijonov.uz/api/music.php?text=" . urlencode($query) . "&page=1");
    if (isset($api1['data']) && is_array($api1['data']) && count($api1['data']) > 0) {
        return $api1['data'];
    }

    // 2️⃣ API: bfrr (Yandex Music mirror)
    $api2 = fetchJson("https://bfrr-api.vercel.app/api/music?query=" . urlencode($query));
    if (isset($api2['result']) && is_array($api2['result']) && count($api2['result']) > 0) {
        $out = [];
        foreach ($api2['result'] as $m) {
            if (!empty($m['download']) || !empty($m['url'])) {
                $out[] = [
                    'artist' => $m['artist'] ?? 'Unknown',
                    'title' => $m['title'] ?? 'Unknown',
                    'url' => $m['download'] ?? $m['url'],
                    'duration' => $m['duration'] ?? ''
                ];
            }
        }
        if ($out) return $out;
    }

    return null;
}


// ========== UPDATE ==========
$update = json_decode(file_get_contents('php://input'));

if (!isset($update->message) && !isset($update->callback_query)) {
    http_response_code(200);
    exit;
}

$message  = $update->message ?? null;
$callback = $update->callback_query ?? null;

$cid  = $message->chat->id ?? null;
$type = $message->chat->type ?? null;
$mid  = $message->message_id ?? null;
$text = $message->text ?? null;

$callbackdata = $callback->data ?? null;
$callback_id  = $callback->id ?? null;
$call_cid     = $callback->message->chat->id ?? null;
$call_mid     = $callback->message->message_id ?? null;
$call_user_id = $callback->from->id ?? null;

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
    ]
]);

$matin = "📥 @$botname orqali yuklandi";

/* ================= DELETE CALLBACK ================= */
if ($callbackdata == "del") {
    bot('answerCallbackQuery', [
        'callback_query_id' => $callback_id,
        'text' => "✅ O'chirildi"
    ]);
    bot('deleteMessage', [
        'chat_id' => $call_cid,
        'message_id' => $call_mid
    ]);
    exit;
}

/* ================= MUSIC DOWNLOAD CALLBACK ================= */
if ($callbackdata && preg_match('/^m_(\d+)_(\d+)$/', $callbackdata, $matches)) {
    
    $search_id = $matches[1];
    $index = (int)$matches[2];
    
    // Qidiruv so'rovini olish
    $query = getSearch($search_id);
    
    if (!$query) {
        bot("answerCallbackQuery", [
            "callback_query_id" => $callback_id,
            "text" => "❌ Vaqt o'tdi, qaytadan qidiring!",
            "show_alert" => true
        ]);
        exit;
    }
    
    bot("answerCallbackQuery", [
        "callback_query_id" => $callback_id,
        "text" => "🎧 Musiqa yuklanmoqda...",
        "show_alert" => false
    ]);
    
    // Musiqalarni qidirish
    $musicList = searchMusic($query);
    
    if (!$musicList || !isset($musicList[$index])) {
        bot('sendMessage', [
            'chat_id' => $call_cid,
            'text' => "❌ Musiqa topilmadi, qaytadan qidiring"
        ]);
        exit;
    }
    
    $music = $musicList[$index];
    $artist = $music['artist'] ?? 'Unknown';
    $title  = $music['title'] ?? 'Unknown';
    $audio_url = $music['url'] ?? null;
    
    if (!$audio_url) {
        bot('sendMessage', [
            'chat_id' => $call_cid,
            'text' => "❌ Audio URL topilmadi"
        ]);
        exit;
    }
    
    // Loading xabari
    $loading = bot('sendMessage', [
        'chat_id' => $call_cid,
        'text' => "⏳ <b>$artist - $title</b>\n\n📥 Yuklanmoqda...",
        'parse_mode' => 'html'
    ]);
    $loading_mid = $loading->result->message_id ?? null;
    
    // Faylni serverga yuklash
    $temp_file = __DIR__ . '/temp_' . uniqid() . '.mp3';
    $downloaded = downloadFile($audio_url, $temp_file);
    
    if ($downloaded && file_exists($temp_file)) {
        // Faylni Telegramga yuborish
        $caption = "🎵 <b>$artist</b> - <i>$title</i>\n\n$matin";
        
        $result = bot('sendAudio', [
            'chat_id' => $call_cid,
            'audio' => new CURLFile($temp_file, 'audio/mpeg', "$artist - $title.mp3"),
            'caption' => $caption,
            'parse_mode' => 'html',
            'title' => $title,
            'performer' => $artist
        ]);
        
        // Temp faylni o'chirish
        @unlink($temp_file);
        
        // Loading xabarini o'chirish
        if ($loading_mid) {
            bot('deleteMessage', ['chat_id' => $call_cid, 'message_id' => $loading_mid]);
        }
        
        if (!$result->ok) {
            bot('sendMessage', [
                'chat_id' => $call_cid,
                'text' => "❌ Audio yuborishda xatolik"
            ]);
        }
    } else {
        // Agar yuklash ishlamasa, to'g'ridan-to'g'ri URL orqali sinash
        if ($loading_mid) {
            bot('deleteMessage', ['chat_id' => $call_cid, 'message_id' => $loading_mid]);
        }
        
        $result = bot('sendAudio', [
            'chat_id' => $call_cid,
            'audio' => $audio_url,
            'caption' => "🎵 <b>$artist</b> - <i>$title</i>\n\n$matin",
            'parse_mode' => 'html'
        ]);
        
        if (!$result->ok) {
            bot('sendMessage', [
                'chat_id' => $call_cid,
                'text' => "❌ Musiqa yuklab bo'lmadi. Boshqa qo'shiqni tanlang."
            ]);
        }
    }
    exit;
}

/* ================= START ================= */
if ($text == "/start" || $text == "/start@$botname") {
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>🔥 Assalomu alaykum, @$botname ga Xush kelibsiz!

📥 Yuklovchi:
• Instagram - video, rasm, reels
• TikTok - suv belgisiz video
• YouTube - video

🎵 Musiqa qidirish:
Shunchaki qo'shiq nomini yozing!

Masalan: <code>Imron Nega ketding</code>

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
        'text' => "📥 Instagram yuklanmoqda..."
    ]);
    $loadingMid = $loading->result->message_id ?? null;

    $api = fetchJson("https://api.rapidsave.com/api/convert?url=" . urlencode($text));

    if ($loadingMid) {
        bot('deleteMessage', ['chat_id' => $cid, 'message_id' => $loadingMid]);
    }

    $video = $api['media'][0]['url'] ?? null;

    if (!$video) {
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "❌ Instagram video topilmadi (API javob bermadi)"
        ]);
        exit;
    }

    bot('sendVideo', [
        'chat_id' => $cid,
        'video' => $video,
        'caption' => $matin,
        'reply_markup' => $ortga
    ]);
    exit;
}

/* ================= TIKTOK ================= */
if ($text && (strpos($text, "tiktok.com") !== false)) {

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
        'video' => $play,
        'caption' => $matin,
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
        'caption' => "$video_title\n\n$matin",
        'parse_mode' => 'html',
        'reply_markup' => $ortga,
    ]);
    exit;
}

/* ================= MUSIC SEARCH ================= */
if ($text && !preg_match('/^\//', $text) && !preg_match('/https?:\/\//', $text)) {

    $loading = bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "🔍 <b>Qidirilmoqda:</b> $text",
        'parse_mode' => 'html'
    ]);
    $loading_mid = $loading->result->message_id ?? null;

    $musicList = searchMusic($text);

    if ($loading_mid) {
        bot('deleteMessage', ['chat_id' => $cid, 'message_id' => $loading_mid]);
    }

    if (!$musicList || empty($musicList)) {
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "😔 <b>\"$text\"</b> bo'yicha musiqa topilmadi",
            'parse_mode' => 'html'
        ]);
        exit;
    }

    // Qidiruvni saqlash (unik ID bilan)
    $search_id = time() . rand(100, 999);
    saveSearch($search_id, $text);

    $list = array_slice($musicList, 0, 10);
    $inline_keyboard = [];
    $msctitle = "";

    foreach ($list as $index => $music) {
        $number = $index + 1;
        $artist = $music['artist'] ?? 'Unknown';
        $title  = $music['title'] ?? 'Unknown';
        $duration = $music['duration'] ?? '';

        $msctitle .= "<b>$number.</b> $artist - $title";
        if ($duration) $msctitle .= " [$duration]";
        $msctitle .= "\n";

        // Callback data: m_searchID_index (qisqa!)
        $callback_data = "m_{$search_id}_{$index}";
        
        $row = $index < 5 ? 0 : 1;
        $inline_keyboard[$row][] = [
            'text' => "🎵 $number",
            'callback_data' => $callback_data
        ];
    }

    $inline_keyboard[] = [['text' => "❌ Yopish", 'callback_data' => "del"]];
    $reply_markup = json_encode(['inline_keyboard' => $inline_keyboard]);

    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "🎵 <b>Natijalar:</b> $text\n\n$msctitle\n📥 Raqamni bosing yuklab olish uchun!",
        'parse_mode' => 'html',
        'reply_markup' => $reply_markup
    ]);
    exit;
}