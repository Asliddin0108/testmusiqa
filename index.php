<?php
ob_start();
error_reporting(0);
date_default_timezone_set("Asia/Tashkent");


define("API_KEY","8253736025:AAHmMPac7DmA_fi01urRtI0wwAfd7SAYArE");
$administrator = "8238730404";


/*
Instagram Save Bot Kodi

Manba: @Org_Coder (chopilmasin)
Tarqatildi: @TexnoPHPuz kanalida!
*/


function bot($method,$steps=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$steps);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}


$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$mid = $message->message_id;
$chat_id = $message->chat->id;
$cid = $message->chat->id;
$callcid = $update->callback_query->message->chat->id;
$cmid = $update->callback_query->message->message_id; 
$data = $update->callback_query->data;
$qid = $update->callback_query->id;
$cid2 = $update->callback_query->message->chat->id;
$mid2 = $update->callback_query->message->message_id;
$uid = $message->from->id;
$name = $message->chat->first_name;
$text = $message->text;  
$tx= $message->text;  
$cty = $update->message->chat->type;
$type = $update->message->chat->type;
$uid= $message->from->id;
$ismi = $update->message->from->first_name;
$ismi2 = $update->message->from->last_name;
$username= $update->message->from->username;
$name = "<a href='tg://user?id=$uid'> $ismi $ismi2 </a>";
$time = date('H:i');
$sana = date('d.m.Y');
$Bot = bot('getme',['bot'])->result->username;

$bot = bot('getme',['bot'])->result->username; //botiz userini qoyasiz
$text = $message->text;
$back = "◀️ Ortga";
$step = file_get_contents("step/$cid/$cid.txt");
$blocks = file_get_contents("data/blocks.txt");
$holat = file_get_contents("data/bot.txt");
$kanal = file_get_contents("data/kanal.txt");
$channel = file_get_contents("data/channel.txt");
$statistika = file_get_contents("data/statistika.txt");
$admins = file_get_contents("data/admins.txt");
$admin = array($administrator,$admins);

#---------------------------------------
mkdir("data");
mkdir("step");
mkdir("step/$cid");
#---------------------------------------

$panel = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"📝 Pochta tizimi"],['text'=>"📊 Statistika"]],
[['text'=>"📢 Kanallar boshqaruvi"],['text'=>"🔐 Blok tizimi"]],
[['text'=>"⚙ Bot sozlamalari"],['text'=>"⭐️ Adminlar boshqaruvi"]],
[['text'=>"$back"]],
]
]);

$message_manager = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"💬 Forward xabar yuborish"],],
[['text'=>"👨🏻‍💻 Boshqaruv paneli"],],
]
]);

$channel_manager = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"📢 Kanal qoʻshish"],['text'=>"📢 Kanalni oʻchirish"],],
[['text'=>"📋 Kanallar roʻyxati"],['text'=>"📋 Kanallar roʻyxatini oʻchirish"],],
[['text'=>"👨🏻‍💻 Boshqaruv paneli"],],
]
]);

$blok_manager = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"✅ Blokdan olish"],['text'=>"❌ Bloklash"],],
[['text'=>"📋 Bloklanganlar roʻyxati"],['text'=>"📋 Bloklanganlar roʻyxatini oʻchirish"],],
[['text'=>"👨🏻‍💻 Boshqaruv paneli"],],
]
]);

$bot_manager = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"✅ Botni yoqish"],['text'=>"❌ Botni o‘chirish"],],
[['text'=>"👨🏻‍💻 Boshqaruv paneli"],],
]
]);

$admins_manager = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"➕ Admin qoʻshish"],['text'=>"🛑 Adminlikdan olish"],],
[['text'=>"📋 Adminlar roʻyxati"],['text'=>"📋 Adminlar roʻyxatini oʻchirish"],],
[['text'=>"👨🏻‍💻 Boshqaruv paneli"],],
]
]);

$ortga = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"$back"],],
]
]);

if(isset($message)){
$get = file_get_contents("data/statistika.txt");
if(mb_stripos($get,$uid)==false){
file_put_contents("data/statistika.txt", "$get\n$uid");
}
}

if(in_array($cid,$admin)){}
elseif(mb_stripos($blocks, $uid)!==false){
bot('sendMessage',[
'chat_id' =>$cid,
'text'=>"<b>⚠️ Kechirasiz <a href = 'tg://user?id=$cid'>$name</a>

📛 Siz botdan bloklangansiz!

👨🏻‍💻 Blokdan chiqish uchun bot administratoriga murojaat qiling!</b>",
'parse_mode' =>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"👨🏻‍💻 Administrator",'url'=>"tg://user?id=$administrator"],],
]
])
]);
return false;
}

if(in_array($cid,$admin)){}
elseif($holat == "off"){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"<b>🛠 Texnik xizmat davom etmoqda!

▪ Bot maʼmuriyati ushbu bot ichida baʼzi texnik ishlarni olib bormoqda.
▪ Shu sababdan menyu adminlar tomonidan oʻchirilgan va hozirda foydalanuvchilar uchun mavjud emas.
▪ Barcha funksiyalar tugallangandan keyin tiklanadi.

🔰 Agar siz ushbu botning administratori boʻlsangiz, ushbu rejimni oʻchirib qoʻyishingiz mumkin!
👉👨🏻‍💻 Boshqaruv paneli | ⚙ Bot sozlamalari.

📝 Boshqalar uchun:
ℹ️ Keyinroq qaytib keling va bot holatini tekshirish uchun /start tugmasini bosing!</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'remove_keyboard'=>true,
])
]);
return false;
}

if(isset($message) and ($channel == "true")){
$ids = explode("\n",$kanal);
$soni = substr_count($kanal,"@");

foreach($ids as $id){
$keyboards = [];
$k=[];
for ($for = 1; $for <= $soni; $for++) {
$kanall=str_replace("@","",$ids[$for]);

$keyboards[]=["text"=>"$for- kanal","url"=>"https://t.me/$kanall"];
}

$keyboard2=array_chunk($keyboards, 1);
$keyboard=json_encode([
'inline_keyboard'=>$keyboard2,
]);
}

$get = bot('getChatMember',[
'chat_id'=>$id,
'user_id'=>$uid,
])->result->status;

if(in_array($cid,$admin)){}
elseif($get == "member" or $get == "administrator" or $get == "creator"){
}else{
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"⛔️ <b>Botdan to'liq foydalanish uchun quyidagi kanallarga obuna bo'ling!</b>",
'parse_mode'=>'html',
'reply_markup'=>$keyboard,
]); 
return false;
}
}

/*
Instagram Save Bot Kodi

Manba: @Org_Coder (chopilmasin)
Tarqatildi: @TexnoPHPuz kanalida!
*/

if($text == "/start" or $text == $back){
unlink("step/$cid/$cid.txt");
unlink("step/$cid/@$bot.mp3");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>🔗 Instagramdan reels havolasini olib botga yuboring!</b>",
'parse_mode'=>'html',
]);
}

if(mb_stripos($text, "instagram.com/reel") !== false){
	bot('sendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b>📥 Yuklanmoqda...</b>",
	'parse_mode'=>html,
	]);
	$json = json_decode(file_get_contents("https://xuss.us/IG1/?url=$text"), true);
	$url = $json['media'][0]['url'];
	$description = $json['description'];
	$author = $json['author'];
	$comment_count = $json['comment_count'];
	$count = $json['count'];
if($count != "0"){
	bot('deleteMessage',[
	'chat_id'=>$cid,
	'message_id'=>$mid + 1,
	]);
	bot('sendVideo',[
	'chat_id'=>$cid,
	'video'=>$url,
	'caption'=>"<b>✅ Video yuklandi!</b>

<blockquote><b>💬 Kommentariyalar soni:</b> $comment_count ta</blockquote>

<b>📰 Tavsifi:</b> <code>$description</code>",
	'parse_mode'=>html,
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[['text'=>"🗑 O'chirish",'callback_data'=>"ochirish"]],
	]])
	]);
	} else {
		bot('deleteMessage',[
	'chat_id'=>$cid,
	'message_id'=>$mid + 1,
	]);
	bot('sendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b>‼️ Video topilmadi, qaytadan to'g'ri havolani yuboring!</b>",
	'parse_mode'=>html,
	]);
	}
	}
	
if($data == "ochirish"){
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
	]);
	bot('sendMessage',[
	'chat_id'=>$cid2,
	'text'=>"<b>‼️ O'chirish yakunlandi!
<blockquote>🔗 Instagramdan reels havolasini olib botga yuboring!</blockquote></b>",
	'parse_mode'=>html,
	]);
	}


if($text == "📊 Statistika"){
$get = substr_count($statistika,"\n");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>👥 Bot foydalanuvchilari: $get ta
⏰ Soat: $time | 📆 Sana: $sana</b>",
'parse_mode'=>'html',
'reply_markup'=>$ortga,
]);
}

if($text == "/panel" or $text == "👨🏻‍💻 Boshqaruv paneli"){
if(in_array($cid,$admin)){
unlink("step/$cid/$cid.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>👨🏻‍💻 Boshqaruv paneliga xush kelibsiz!
📋 Quyidagi boʻlimlardan birini tanlang!</b>",
'parse_mode'=>'html',
'reply_markup'=>$panel,
]);
}else{
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>👨🏻‍💻 Bu bo‘limni faqat bot administratori ishlata oladi!</b>",
'parse_mode'=>'html',
'reply_markup'=>$menyu,
]);
}
}

if(in_array($cid,$admin)){
if($text == "📝 Pochta tizimi"){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📝 Pochta tizimi boʻlimidasiz!
📋 Quyidagi boʻlimlardan birini tanlang!</b>",
'parse_mode'=>'html',
'reply_markup'=>$message_manager,
]);
}
}

if($text == "💬 Forward xabar yuborish"){
file_put_contents("step/$cid/$cid.txt","forward");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>👥 Foydalanuvchilarga yuboriladigan xabarni forward qiling!</b>",
'parse_mode'=>'html',
'reply_markup'=>$ortga,
'disable_web_page_preview'=>true,
]);
}

if($step == "forward" and $text!= "/start" and $text!= $back and $text!= "👨🏻‍💻 Boshqaruv paneli"){
unlink("step/$cid/$cid.txt");
$explode = explode("\n",$statistika);
foreach($explode as $id){
$forward = bot('forwardMessage',[
'chat_id' =>$id, 
'from_chat_id' =>$cid, 
'message_id' =>$mid, 
]);
}
}

if($forward){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>👥 Forward xabaringiz barcha bot foydalanuvchilariga yuborildi!✅</b>",
'parse_mode'=>'html',
'reply_markup'=>$message_manager,
]);
}

if(in_array($cid,$admin)){
if($text == "📢 Kanallar boshqaruvi"){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📢 Kanallar boshqaruvi boʻlimidasiz!
📋 Quyidagi boʻlimlardan birini tanlang!</b>",
'parse_mode'=>'html',
'reply_markup'=>$channel_manager,
]);
}
}

if(in_array($cid,$admin)){
if($text == "📢 Kanal qoʻshish"){
file_put_contents("step/$cid/$cid.txt","kanal");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📡 Kanal qo‘shish uchun kanal havolasini yuboring:

🔰 Masalan: @ITMaktabi_Pro</b>",
'parse_mode'=>'html',
'reply_markup'=>$ortga,
]);
}
}

/*
Instagram Save Bot Kodi

Manba: @Org_Coder (chopilmasin)
Tarqatildi: @TexnoPHPuz kanalida!
*/

if($step == "kanal" and $text!= "/start" and $text!= $back and $text!= "👨🏻‍💻 Boshqaruv paneli"){
if(mb_stripos($kanal,"$text")!==false){
}else{
file_put_contents("data/kanal.txt","$kanal\n$text");
file_put_contents("data/channel.txt","true");
unlink("step/$cid/$cid.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📡 Kanalingiz botga muvaffaqiyatli qo‘shildi!
🤖 Endi botni kanalingizga admin qiling!</b>",
'parse_mode'=>'html',
'reply_markup'=>$channel_manager,
]);
}
}

if(in_array($cid,$admin)){
if($text == "📢 Kanalni oʻchirish"){
file_put_contents("step/$cid/$cid.txt","delete");
$ids = explode("\n",$kanal);
$soni = substr_count($kanal,"@");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📡 Kanalni oʻchirish uchun kanal havolasini yuboring!

🔰 Masalan: @iUzBlogs

👇 Botga ulangan kanallar:
$kanal

📝 Jami kanallar soni: $soni ta
</b>",
'parse_mode'=>'html',
'reply_markup'=>$ortga,
]);
}
}


if($step == "delete" and $text!= "/start" and $text!= $back and $text!= "👨🏻‍💻 Boshqaruv paneli"){
if(mb_stripos($kanal,"$text")!==false){
$k = str_replace("\n".$text."","",$kanal);
file_put_contents("data/kanal.txt",$k);
unlink("step/$cid/$cid.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>🔰 $text muvaffaqiyatli oʻchirildi! ✅</b>",
'parse_mode'=>'html',
'reply_markup'=>$channel_manager,
]);
}
}

if(in_array($cid,$admin)){
if($text == "📋 Kanallar roʻyxati"){
if($kanal == null){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 Botga ulangan kanallar mavjud emas!</b>",
'parse_mode'=>'html',
'reply_markup'=>$channel_manager,
]);
}else{
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 Kanallar roʻyxati:
$kanal</b>",
'parse_mode'=>'html',
'reply_markup'=>$channel_manager,
]);
}
}
}

if(in_array($cid,$admin)){
if($text == "📋 Kanallar roʻyxatini oʻchirish"){
if($kanal == null){
unlink("data/kanal.txt");
unlink("data/channel.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 Botga ulangan kanallar mavjud emas!</b>",
'parse_mode'=>'html',
'reply_markup'=>$channel_manager,
]);
}else{
unlink("data/kanal.txt");
unlink("data/channel.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 Kanallar roʻyxati muvaffaqiyatli oʻchirildi!</b>",
'parse_mode'=>'html',
'reply_markup'=>$channel_manager,
]);
}
}
}

if(in_array($cid,$admin)){
if($text == "🔐 Blok tizimi"){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>🔐 Blok tizimi boʻlimidasiz!
📋 Quyidagi boʻlimlardan birini tanlang!</b>",
'parse_mode'=>'html',
'reply_markup'=>$blok_manager,
]);
}
}

if(in_array($cid,$admin)){
if($text == "✅ Blokdan olish"){
file_put_contents("step/$cid/$cid.txt","unblock");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>🚫 Blokdan olinadigan foydalanuvchini ID raqamini kiriting!</b>",
'parse_mode'=>'html',
'reply_markup'=>$ortga,
]);
}
}

if(in_array($cid,$admin)){
if($step == "unblock" and $text!= "/start" and $text!= $back and $text!= "👨🏻‍💻 Boshqaruv paneli"){
unlink("step/$cid/$cid.txt");
if(mb_stripos($blocks, $text)==false){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>👨🏻‍💻 Ushbu foydalanuvchi botdan bloklanmagan!</b>",
'parse_mode'=>'html',
'reply_markup'=>$blok_manager,
]);
}else{
$bl = str_replace("$text", " ", $blocks);
file_put_contents("data/blocks.txt", "$bl");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>🔰 Foydalanuvchi blokdan olindi! ✅</b>",
'parse_mode'=>'html',
'reply_markup'=>$blok_manager,
]);
bot('sendMessage',[
'chat_id'=>$text,
'text'=>"<b>🎉 Siz blokdan muvaffaqiyatli olindingiz!

🔄 Yana botni ishlatishingiz mumkin!

🤖 Botga qayta /start bosing ✅</b>",
'parse_mode'=>'html',
'reply_markup'=>$menyu,
]);
}
}
}

if(in_array($cid,$admin)){
if($text == "❌ Bloklash"){
file_put_contents("step/$cid/$cid.txt","block");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>🚫 Bloklanadigan foydalanuvchini ID raqamini kiriting!</b>",
'parse_mode'=>'html',
'reply_markup'=>$ortga,
]);
}
}

if(in_array($cid,$admin)){
if($step == "block" and $text!= "/start" and $text!= $back and $text!= "👨🏻‍💻 Boshqaruv paneli"){
if(mb_stripos($blocks, $text)==false){
file_put_contents("data/blocks.txt", "$blocks\n$text");
unlink("step/$cid/$cid.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>🔰 Foydalanuvchi bloklandi! ✅</b>",
'parse_mode'=>'html',
'reply_markup'=>$blok_manager,
]);
bot('sendMessage',[
'chat_id'=>$text,
'text'=>"<b>🚫 Siz bizning botimizdan bloklandingiz!

🔄 Endi botdan foydalana olmaysiz!

👨‍💻 Blokdan chiqish uchun bot administratoriga murojaat qiling!</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'remove_keyboard'=>true,
])
]);
}else{
unlink("step/$cid/$cid.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>👨🏻‍💻 Ushbu foydalanuvchi botdan allaqachon bloklangan!</b>",
'parse_mode'=>'html',
'reply_markup'=>$blok_manager,
]);
}
}
}

if(in_array($cid,$admin)){
if($text == "📋 Bloklanganlar roʻyxati"){
if($blocks == null){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 Botdan bloklanganlar mavjud emas!</b>",
'parse_mode'=>'html',
'reply_markup'=>$blok_manager,
]);
}else{
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 Botdan bloklanganlar roʻyxati:
$blocks</b>",
'parse_mode'=>'html',
'reply_markup'=>$blok_manager,
]);
}
}
}

/*
Instagram Save Bot Kodi

Manba: @Org_Coder (chopilmasin)
Tarqatildi: @TexnoPHPuz kanalida!
*/

if(in_array($cid,$admin)){
if($text == "📋 Bloklanganlar roʻyxatini oʻchirish"){
if($blocks == null){
unlink("data/blocks.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 Botdan bloklanganlar mavjud emas!</b>",
'parse_mode'=>'html',
'reply_markup'=>$blok_manager,
]);
}else{
unlink("data/blocks.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 Bloklanganlar roʻyxati muvaffaqiyatli oʻchirildi!</b>",
'parse_mode'=>'html',
'reply_markup'=>$blok_manager,
]);
}
}
}

if(in_array($cid,$admin)){
if($text == "⚙ Bot sozlamalari"){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>⚙ Bot sozlamalari boʻlimidasiz!
Quyidagi boʻlimlardan birini tanlang!</b>",
'parse_mode'=>'html',
'reply_markup'=>$bot_manager,
]);
}
}

if(in_array($cid,$admin)){
if($text == "✅ Botni yoqish"){
unlink("data/bot.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>⚠️ Bot muvaffaqiyatli yoqildi!</b>",
'parse_mode'=>'html',
'reply_markup'=>$bot_manager,
]);
}
}

if(in_array($cid,$admin)){
if($text == "❌ Botni o‘chirish"){
file_put_contents("data/bot.txt","off");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>⚠️ Bot muvaffaqiyatli oʻchirildi!</b>",
'parse_mode'=>'html',
'reply_markup'=>$bot_manager,
]);
}
}

if(in_array($cid,$admin)){
if($text == "⭐️ Adminlar boshqaruvi"){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>⭐️ Adminlar boshqaruvi boʻlimidasiz!
📋 Quyidagi boʻlimlardan birini tanlang!</b>",
'parse_mode'=>'html',
'reply_markup'=>$admins_manager,
]);
}
}

if(in_array($cid,$admin)){
if($text == "➕ Admin qoʻshish"){
file_put_contents("step/$cid/$cid.txt","setadmins");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>👨🏻‍💻 Administrator qoʻshish uchun foydalanuvchi ID raqamini kiriting</b>",
'parse_mode'=>'html',
'reply_markup'=>$ortga,
]);
}
}

if($step == "setadmins" and $text!= "/start" and $text!= $back and $text!= "👨🏻‍💻 Boshqaruv paneli"){
if(is_numeric($text)){
if(mb_stripos($statistika,$text)!==false){
file_put_contents("data/admins.txt","$admins\n$text");
unlink("step/$cid/$cid.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📝 <a href = 'tg://user?id=$text'>$text</a> ID raqamli foydalanuvchi botga administrator qilib tayinlandi!</b>",
'parse_mode'=>'html',
'reply_markup'=>$admins_manager,
]);
bot('sendMessage',[
'chat_id'=>$text,
'text'=>"<b>👨‍💻 Siz botga administrator qilib tayinlandingiz!</b>",
'parse_mode'=>'html',
'reply_markup'=>$menyu,
]);
}else{
unlink("step/$cid/$cid.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>👨‍💻 Ushbu foydalanuvchi bazada mavjud emas!</b>",
'parse_mode'=>'html',
'reply_markup'=>$admins_manager,
]);
}
}else{
unlink("step/$cid/$cid.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 ID raqam kiritayotganda faqat raqamlardan foydalaning!</b>",
'parse_mode'=>'html',
'reply_markup'=>$admins_manager,
]);
}
}

if(in_array($cid,$admin)){
if($text == "🛑 Adminlikdan olish"){
if($admins == null){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 Botda administratorlar mavjud emas!</b>",
'parse_mode'=>'html',
'reply_markup'=>$admins_manager,
]);
}else{
file_put_contents("step/$cid/$cid.txt","deladmins");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>👨‍💻 Administratorni olib tashlash uchun foydalanuvchi ID raqamini kiriting</b>",
'parse_mode'=>'html',
'reply_markup'=>$ortga,
]);
}
}
}

if($step == "deladmins" and $text!= "/start" and $text!= $back and $text!= "👨🏻‍💻 Boshqaruv paneli"){
if(is_numeric($text)){
if(mb_stripos($admins,$text)!==false){
unlink("step/$cid/$cid.txt");
$ad = str_replace("\n".$text."","",$admins);
file_put_contents("data/admins.txt",$ad);
unlink("step/$cid/$cid.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 <a href = 'tg://user?id=$text'>$text</a> ID raqamli foydalanuvchi bot administratorligidan olib tashlandi!</b>",
'parse_mode'=>'html',
'reply_markup'=>$admins_manager,
]);
bot('sendMessage',[
'chat_id'=>$text,
'text'=>"<b>👨‍💻 Siz bot administratorligidan olib tashlandingiz!</b>",
'parse_mode'=>'html',
'reply_markup'=>$menyu,
]);
}else{
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 <a href = 'tg://user?id=$text'>$text</a> ID raqamli foydalanuvchi botda administrator emas!</b>",
'parse_mode'=>'html',
'reply_markup'=>$admins_manager,
]);
}
}else{
unlink("step/$cid/$cid.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 ID raqam kiritayotganda faqat raqamlardan foydalaning!</b>",
'parse_mode'=>'html',
'reply_markup'=>$admins_manager,
]);
}
}

if(in_array($cid,$admin)){
if($text == "📋 Adminlar roʻyxati"){
if($admins == null){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 Botda administratorlar mavjud emas!</b>",
'parse_mode'=>'html',
'reply_markup'=>$admins_manager,
]);
}else{
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 Administratorlar roʻyxati:
$admins</b>",
'parse_mode'=>'html',
'reply_markup'=>$admins_manager,
]);
}
}
}

if(in_array($cid,$admin)){
if($text == "📋 Adminlar roʻyxatini oʻchirish"){
if($admins == null){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 Botda administratorlar mavjud emas!</b>",
'parse_mode'=>'html',
'reply_markup'=>$admins_manager,
]);
}else{
unlink("data/admins.txt");
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>📋 Administratorlar roʻyxati muvaffaqiyatli oʻchirildi!</b>",
'parse_mode'=>'html',
'reply_markup'=>$admins_manager,
]);
}
}
}

/*
Instagram Save Bot Kodi

Manba: @Org_Coder (chopilmasin)
Tarqatildi: @TexnoPHPuz kanalida!
*/
?>