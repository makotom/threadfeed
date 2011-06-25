<?php
/*
Threadfeed - Feed threads on 2ch BBS
Copyright (C) 2010-2011 Makoto Mizukami.
http://threadfeed.www1.biz/
*/

header("Content-Type: application/rss+xml; charset=UTF-8");

echo <<<EOD
<?xml version="1.0"?>
<rdf:RDF xmlns="http://purl.org/rss/1.0/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/">
  <channel rdf:about="$feed_url">
    <title>スレッド一覧: $feed_title</title>
    <link>http://$srv.2ch.net/$bkey/</link>
    <description>Listed by ThreadFeed</description>
 
    <items>
      <rdf:Seq>

EOD;

$out_list = array();
$out_body = array();

function put_item($item_tkey, $item_title){
	global $srv_real, $bkey_real, $out_list, $out_body;
	$out_list[] = "        <rdf:li rdf:resource=\"http://$srv_real.2ch.net/test/read.cgi/$bkey_real/$item_tkey\" />\n";
	$out_body[] = <<<EOD

  <item rdf:about="http://$srv_real.2ch.net/test/read.cgi/$bkey_real/$item_tkey/">
    <title>$item_title</title>
    <link>http://$srv_real.2ch.net/test/read.cgi/$bkey_real/$item_tkey/</link>
  </item>

EOD;
}

function put_notice($title, $url){
	global $feed_date, $out_list, $out_body;
	$out_list[] = "        <rdf:li rdf:resource=\"$url\" />\n";
	$out_body[] = <<<EOD

  <item rdf:about="$url">
    <title>$title</title>
    <link>$url</link>
  </item>

EOD;
}

function postrun(){
	global $out_list, $out_body;
	$out_list = implode("", $out_list);
	$out_body = implode("", $out_body);
	echo <<<EOD
$out_list      </rdf:Seq>
    </items>
  </channel>
$out_body
</rdf:RDF>
EOD;
}
?>
