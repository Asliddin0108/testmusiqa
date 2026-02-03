<?php
ob_start();
define('API_KEY','8253736025:AAHmMPac7DmA_fi01urRtI0wwAfd7SAYArE');

/* ================= FETCH JSON (cURL) ================= */
function fetchJson($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0'
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}


$update = json_decode(file_get_contents('php://input'));

// ====== GUARD: bo‘sh webhooklarni to‘xtatish ======
if (
    !isset($update->message) &&
    !isset($update->callback_query)
) {
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
$aa  = $callback->id ?? null;
$call = $callback->message->chat->id ?? null;
$cal  = $callback->message->message_id ?? null;

$name = $message->from->first_name ?? '';
$botname = bot('getme',['bot'])->result->username;
$date = date("d.m.Y");
$soat = date("H:i");


$bosh = json_encode([
'inline_keyboard' => [
[['text' => "➕ Guruhga qoshish",'url' => "https://t.me/$botname?startgroup=new"]],
]
]);

$ortga = json_encode([
'inline_keyboard' => [
[['text' => "❌ ", 'callback_data' => "del"]],
[['text' => "➕ Guruhga qoshish",'url' => "https://t.me/$botname?startgroup=new"]],
]
]);

$matin = "📥Yuklab olindi ushbu bot orqali";

/* ================= START ================= */
if(($text == "/start" || $text == "/start@$botname") && $type == "private"){
bot('sendMessage',[
'chat_id' => $cid,
'text' => "<b>🔥 Assalomu alaykum. @$botname ga Xush kelibsiz.

• Instagram - stories, post va IGTV + audio bilan
• TikTok - suv belgisiz video;
• YouTube - video;

Shazam funksiya:
• Qo'shiq nomi yoki ijrochi ismi
• Qo'shiq matni

😎 Bot guruhlarda ham ishlay oladi!</b>",
'parse_mode' => 'html',
'reply_markup' => $bosh,
]);
exit();
}

/* ================= INSTAGRAM ================= */
if ($text && strpos($text, "instagram.com") !== false) {

    $api_url = "https://xuss.us/IG1/?url=" . urlencode($text);
    $json = fetchJson($api_url);

    // 1️⃣ har xil formatlarni ushlab olish
    $video_url =
        $json['video']
        ?? $json['url']
        ?? $json['data']['video']
        ?? $json['videos'][0]['url']
        ?? null;

    if (!$video_url) {
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "❌ Instagram video topilmadi"
        ]);
        exit;
    }

    // 2️⃣ yuklanmoqda belgisi
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "📥"
    ]);
    sleep(1);

    // 3️⃣ video yuborish
    bot('sendVideo', [
        'chat_id' => $cid,
        'video' => $video_url,
        'caption' => "$matin @$botname",
        'parse_mode' => 'html',
        'reply_markup' => $ortga,
    ]);
    exit;
}


/* ================= TIKTOK (TEGMADIK) ================= */
if (strpos($text, "vt.tiktok.com") !== false) {
$api = $text;
$TikTok = json_decode(file_get_contents("https://tikwm.com/api/?url=$api"));
$tiktok = $TikTok->data;
$play = $tiktok->play;

bot('sendMessage',[
'chat_id'=>$cid , 
'text'=>"📥",
]);
sleep(2.8);

bot('deletemessage',[
'chat_id'=>$cid , 
'message_id'=>$mid + 1,
]);
sleep(3); 

bot('sendVideo',[
'chat_id' => $cid,
'video'=>new CURLFile($play),
'caption' => "$matin @$botname",
'parse_mode' => 'html',
'reply_markup' => $ortga,
]);
exit();
}

/* ================= YOUTUBE (HOZIRCHA TEGMADIK) ================= */
if(strpos($text, "youtu.be") !== false || strpos($text, "youtube.com") !== false){
$video_url = $text;
$api_url = "https://4503091-gf96974.twc1.net/Api/YouTube.php?url=" . urlencode($video_url); 
$natija = json_decode(file_get_contents($api_url), true);
$video_title = $natija['title'];
$video_url = $natija['video_with_audio'][0]['url'];

bot('sendMessage',[
'chat_id'=>$cid , 
'text'=>"📥",
]);
sleep(0.3);

bot('deletemessage',[
'chat_id'=>$cid , 
'message_id'=>$mid + 1,
]);
sleep(0.2); 

bot('sendVideo', [
'chat_id' => $cid,
'video'=> $video_url,
'caption' => "$video_title\n\n$matin @$botname",
'parse_mode' => 'html',
'reply_markup' => $ortga,
]);
exit();
}

/* ================= DELETE ================= */
if($data == "del"){
bot('deleteMessage',[
'chat_id'=>$call,
'message_id'=>$cal
]);
exit();
}

/* ================= MUSIC DOWNLOAD ================= */
if ($callbackdata && strpos($callbackdata, "-") !== false) {

    bot("answerCallbackQuery", [
        "callback_query_id" => $aa,
        "text" => "🎧 Musiqa yuborilmoqda...",
        "show_alert" => false
    ]);

    [$query, $index] = explode("-", $callbackdata);

    $api_url = "https://api.alijonov.uz/api/music.php?text=" . urlencode($query) . "&page=1";
    $api = json_decode(file_get_contents($api_url), true);

    $music = $api['data'][$index] ?? null;

    if (!$music || !isset($music['url'])) {
        bot('sendMessage', [
            'chat_id' => $call,
            'text' => "❌ Musiqa yuklab bo‘lmadi"
        ]);
        exit;
    }

    $artist = $music['artist'] ?? 'Unknown';
    $title  = $music['title'] ?? 'Unknown';
    $audio  = $music['url'];

    $caption = "<b>$artist</b> - <i>$title</i>\n\n@$botname\n⏰$soat  📅$date";

    bot('sendAudio', [
        'chat_id' => $call,
        'audio' => $audio,
        'caption' => $caption,
        'parse_mode' => 'html'
    ]);
    exit;
}


/* ================= MUSIC SEARCH ================= */
if ($text && !$callbackdata) {

    $api_url = "https://api.alijonov.uz/api/music.php?text=" . urlencode($text) . "&page=1";
    $api = fetchJson($api_url);

    if (!isset($api['data']) || empty($api['data'])) {
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "😔 Musiqa topilmadi"
        ]);
        exit;
    }

    $list = array_slice($api['data'], 0, 10);
    $inline_keyboard = [];
    $msctitle = "";

    foreach ($list as $index => $music) {
        $number = $index + 1;

        $artist = $music['artist'] ?? 'Unknown';
        $title  = $music['title'] ?? 'Unknown';

        $msctitle .= "<b>$number</b>. <i>$artist - $title</i>\n";

        if ($index < 5) {
            $inline_keyboard[0][] = [
                'text' => "$number",
                'callback_data' => "$text-$index"
            ];
        } else {
            $inline_keyboard[1][] = [
                'text' => "$number",
                'callback_data' => "$text-$index"
            ];
        }
    }

    $inline_keyboard[] = [['text' => "❌ ", 'callback_data' => "del"]];
    $reply_markup = json_encode(['inline_keyboard' => $inline_keyboard]);

    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "🎵 <b>$text</b>\n\n$msctitle",
        'parse_mode' => 'html',
        'reply_markup' => $reply_markup
    ]);
    exit;
}

