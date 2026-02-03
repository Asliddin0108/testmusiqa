<?php
ob_start();
define('API_KEY','8253736025:AAHmMPac7DmA_fi01urRtI0wwAfd7SAYArE');

function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}

$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$cid = $message->chat->id;
$type = $message->chat->type;
$mid = $message->message_id;
$message_id = $update->callback_query->message->message_id;
$callbackdata = $update->callback_query->data;
$text = $message->text;
$call = $update->callback_query;
$mes = $call->message;
$data = $call->data;
$aa = $call->id;
$call = $mes->chat->id;
$cal = $mes->message_id;
$name = $message->from->first_name;
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
if(strpos($text, "instagram.com") !== false){

$api_url = "https://xuss.us/IG1/?url=".urlencode($text); // <<< YANGI API
$json = json_decode(file_get_contents($api_url), true);
$video_url = $json['video'];

bot('sendMessage',[
'chat_id' => $cid,
'text' => "📥",
]);
sleep(2.8);

bot('deletemessage',[
'chat_id' => $cid,
'message_id' => $mid + 1,
]);
sleep(0.3);

bot('sendVideo',[
'chat_id' => $cid,
'video' => $video_url,
'caption' => "$matin @$botname",
'parse_mode' => 'html',
'reply_markup' => $ortga,
]);
exit();
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
if (mb_stripos($callbackdata, "-") !== false) {

bot("answerCallbackQuery", [
"callback_query_id" => $aa,
"text" => "Iltimos kuting, musiqa yuklanmoqda.",
"show_alert" => false
]);

$explode = explode("-", $callbackdata);
$search_query = $explode[0];
$music_index = (int)$explode[1];

$api_url = "https://api.alijonov.uz/api/music.php?text=".urlencode($search_query)."&page=1"; // <<< YANGI API
$api = json_decode(file_get_contents($api_url), true);

$musicc = $api['data'][$music_index];
$audio_url = $musicc['url'];

$caption = "<b>".$musicc['artist']."</b> - <i>".$musicc['title']."</i>\n\n@$botname orqali yuklab olindi\n\n⏰$soat  📅$date";

bot('sendAudio', [
'chat_id' => $call,
'audio' => $audio_url,
'caption' => $caption,
'reply_markup' => $ortga,
'parse_mode' => "html",
]);
exit();
}

/* ================= MUSIC SEARCH ================= */
if ($text) {

$data = json_decode(
file_get_contents("https://api.alijonov.uz/api/music.php?text=".urlencode($text)."&page=1"), // <<< YANGI API
true
);

if (!$data || empty($data['data'])) {
bot('sendMessage', [
'chat_id' => $cid,
'text' => "<b>😔 Afsuski hech narsa topilmadi</b>",
'parse_mode' => "html",
]);
return; 
}

$data = array_slice($data['data'], 0, 10);
$messages = bot('sendMessage', [
'chat_id' => $cid,
'text' => "🔎",
'parse_mode' => 'html',
])->result->message_id;

$inline_keyboard = [];
$msctitle = "";

foreach ($data as $index => $music) {
$number = $index + 1; 
$artist = $music['artist'];
$title = $music['title'];

$msctitle .= "<b>$number</b>. <i>$artist - $title</i>\n";

if ($index < 5) {
$inline_keyboard[0][] = ['text' => "$number", 'callback_data' => "$text-$index"];
} else {
$inline_keyboard[1][] = ['text' => "$number", 'callback_data' => "$text-$index"];
}
}

$inline_keyboard[] = [['text' => "❌ ", 'callback_data' => "del"]];
$reply_markup = json_encode(['inline_keyboard' => $inline_keyboard]);

bot('sendPhoto', [
'chat_id' => $cid,
'photo' => "https://t.me/malumotlarombor",
'caption' => "<b>🎙<i>$text</i></b>\n\n$msctitle\n\n⏰$soat  📅$date",
'reply_markup' => $reply_markup,
'parse_mode' => "html",
]);

bot('deleteMessage',[
'chat_id' => $cid,
'message_id' => $messages,
]);
}
