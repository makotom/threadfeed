<?php
/*
Threadfeed - Feed threads on 2ch BBS
Copyright (C) 2010-2011 Makoto Mizukami.
http://threadfeed.www1.biz/
*/

foreach($_GET as $getq){
	if(preg_match("/[^[:alnum:]]/", $getq)){
		exit("Evil request.");
	}
}

if(!(isset($_GET["s"]) && isset($_GET["k"]) && isset($_GET["f"]))){
	exit("Some of mandatory arguments are lacking.");
}
$srv = $_GET["s"];
$bkey = $_GET["k"];
$format = $_GET["f"];
if($format == "rss"){
	$format = "rss2";
}
if($format == "atom"){
	$format = "atom1";
}	

if(!file_exists("modules/$format.php")){
	exit("Feed format \"$format\" is not supported.");
}

$splice = 100;
if(!empty($_GET["n"])){
	if(!is_numeric($_GET["n"]) || $_GET["n"] < 1 || $_GET["n"] > 300){
		exit("Invalid number: {$_GET["n"]}");
	}
	$splice = (int) $_GET["n"];
}

$scheme = "http";
if(!empty($_SERVER["HTTPS"])){
	$scheme = "https";
}
$feed_url = "$scheme://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

ini_set("user_agent", "ThreadFeed: $feed_url");
$srv_real = $srv;
$bkey_real = $bkey;

$titlefile = mb_convert_encoding(file_get_contents("http://menu.2ch.net/bbsmenu.html"), "UTF-8", "SHIFT_JIS");
for($i = 0; $i < 20; $i++){
	if(preg_match("@.*http://$srv_real.2ch.net/$bkey_real/.*@is", $titlefile)){
		break;
	}
	$boardpage = file_get_contents("http://$srv_real.2ch.net/$bkey_real/");
	if(!preg_match("@^.*window.location.href=\"http://[^.]+.2ch.net/[^.]+/\".*$@s", $boardpage)){
		exit("Board not found: http://$srv.2ch.net/$bkey/");
	}
	$srv_real = preg_replace("@^.*window.location.href=\"http://([^.]+).2ch.net/[^.]+/\".*$@s", "$1", $boardpage);
	$bkey_real = preg_replace("@^.*window.location.href=\"http://[^.]+.2ch.net/([^.]+)/\".*$@s", "$1", $boardpage);
	$needrefresh = 1;
}
$feed_title = preg_replace("@.*http://$srv_real.2ch.net/$bkey_real/>([^<]+)</a>.*@is", "\\1", $titlefile);

$subfile = explode("\n", mb_convert_encoding(file_get_contents("http://$srv_real.2ch.net/$bkey_real/subject.txt"), "UTF-8", "SHIFT_JIS"));
if(!preg_match("/<>/", $subfile[0])){
	exit("Cannot retrieve a valid subject list: http://$srv.2ch.net/$bkey/subject.txt");
}
rsort($subfile);

$feed_time = time();
include("modules/$format.php");

if(!empty($needrefresh)){
	$newurl = "$scheme://".$_SERVER["HTTP_HOST"]."/$srv_real/$bkey_real/".$_GET["f"];
	if(!empty($_GET["n"])){
		$newurl .= "/$splice";
	}
	put_notice("【".date("c", $feed_time)." お知らせ】$feed_title 板の移転に伴いこのフィードの URL も $newurl に変更されました。フィードリーダーの再設定にご協力下さい。", $newurl);
}

for($preset = 0; preg_replace("/^(\d+)\.dat<>.*$/s", "\\1", $subfile[$preset]) > 2147483647; $preset++);
$items = array_slice($subfile, $preset, $splice);

foreach($items as $item){
	if(empty($item)){
		break;
	}
	list($item_dat, $item_title) = explode("<>", trim($item));
	$item_dat = preg_replace("/\.dat/", "", $item_dat);
	$item_title = preg_replace("| \(\d+\)$|", "\\1", $item_title);
	put_item($item_dat, $item_title);
}
postrun();
?>
