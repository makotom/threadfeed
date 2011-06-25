<?php
/*
Threadfeed - Feed threads on 2ch BBS
Copyright (C) 2010 Makoto Mizukami.
http://threadfeed.www1.biz/
*/

header("Content-Type: application/rss+xml; charset=UTF-8");
$feed_date = date("r", $feed_time);

echo <<<EOD
<?xml version="1.0"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>スレッド一覧: $feed_title</title>
    <link>http://$srv.2ch.net/$bkey/</link>
    <description>Listed by ThreadFeed</description>
    <pubDate>$feed_date</pubDate>
    <atom:link href="$feed_url" rel="self" type="application/rss+xml" />

EOD;

function put_item($item_tkey, $item_title){
	global $srv_real, $bkey_real;
	$item_date = date("r", $item_tkey);
	$item_id = sha1("http://$srv_real.2ch.net/$bkey_real/dat/$item_tkey.dat");
	echo <<<EOD

    <item>
      <title>$item_title</title>
      <link>http://$srv_real.2ch.net/test/read.cgi/$bkey_real/$item_tkey/</link>
      <guid isPermaLink="false">$item_id</guid>
      <pubDate>$item_date</pubDate>
    </item>

EOD;
}

function put_notice($title, $url){
	global $feed_date;
	$id = sha1("$feed_date $url");
	echo <<<EOD

    <item>
      <title>$title</title>
      <link>$url</link>
      <guid isPermaLink="false">$id</guid>
      <pubDate>$feed_date</pubDate>
    </item>

EOD;
}

function postrun(){
	echo <<<EOD

  </channel>
</rss>
EOD;
}
?>
