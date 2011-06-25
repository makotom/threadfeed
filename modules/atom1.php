<?php
/*
Threadfeed - Feed threads on 2ch BBS
Copyright (C) 2010-2011 Makoto Mizukami.
http://threadfeed.www1.biz/
*/

header("Content-Type: application/atom+xml; charset=UTF-8");
$feed_date = date("c", $feed_time);

echo <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title>スレッド一覧: $feed_title</title>
  <subtitle>Listed by ThreadFeed</subtitle>
  <link href="$feed_url" rel="self" />
  <link href="http://$srv.2ch.net/$bkey/" />
  <id>http://$srv.2ch.net/$bkey/subject.txt</id>
  <updated>$feed_date</updated>

EOD;

function put_item($item_tkey, $item_title){
	global $srv_real, $bkey_real, $feed_title;
	$item_date = date("c", $item_tkey);
	echo <<<EOD

  <entry>
    <title>$item_title</title>
    <link href="http://$srv_real.2ch.net/test/read.cgi/$bkey_real/$item_tkey/" />
    <author><name>$feed_title</name></author>
    <id>http://$srv_real.2ch.net/$bkey_real/dat/$item_tkey.dat</id>
    <updated>$item_date</updated>
  </entry>

EOD;
}

function put_notice($title, $url){
	global $feed_title, $feed_date;
	echo <<<EOD

  <entry>
    <title>$title</title>
    <link href="$url" />
    <author><name>$feed_title</name></author>
    <id>$url</id>
    <updated>$feed_date</updated>
  </entry>

EOD;
}

function postrun(){
	echo "\n</feed>";
}
?>
