<?php

require('./xml.php');

$d = Array(); // Final data array.

// Open directory and read files
$files = scandir('./input/');
unset($files[0],$files[1]); // Revmove '..' and '.' entries from the list of files.

// Parse the html data in the files (expected format is html exported from Evernote).
foreach($files as $file){

  $xml = new xml();
  $file_xml = file_get_contents(getcwd().'/input/'.$file);
  $xml->parse($file_xml);
  $file_data = $xml->getData();
  
  $title = $file_data['html'][0]['head'][0]['title'][0]['contents'];
  $datetime = $file_data['html'][0]['body'][0]['div'][0]['table'][0]['tr'][0]['td'][1]['i'][0]['contents'];
  
  $ts = strtotime($datetime);
  $dst = date("I",$ts);
  $tzo = 5 - $dst;
  $ts = $ts + ($tzo * 60 * 60);
  $timestamp = date("Ymd",$ts).'T'.date("His",$ts).'Z';
  
  $content = explode('<body>',$file_xml);
  $content = explode('</body>',$content[1]);
  $content = $content[0];
  
  $d[] = Array(
    
    'title'         => $title,
    'created_time'  => $timestamp,
    'content'       => $content,  
  
  );
  
  unset($xml);
  
}

// Create final output

$o  = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE en-export SYSTEM "http://xml.evernote.com/pub/evernote-export2.dtd">
<en-export export-date="20140902T201054Z" application="Evernote/Windows" version="5.x">';

foreach($d as $item){

  $o .= '<note>';
  $o .= '<title>'.$item['title'].'</title>';
  $o .= '<content><![CDATA[<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd"><en-note>'.$item['content'].'</en-note>]]></content>';
  $o .= '<created>'.$item['created_time'].'</created>';
  $o .= '</note>';

}

$o .= '</en-export>';

$o = utf8_encode($o);
$o = str_replace('Â','',$o); // remove 'Â' characters

echo $o;
file_put_contents('./Evernote Import.enex',$o);




?>