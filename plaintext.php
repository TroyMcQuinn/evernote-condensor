<?php

/*****************************************************************************
NOTE:  Any plaintext files to import MUST contain the date in their filenames!
*****************************************************************************/

$d = Array(); // Final data array.

// Open directory and read files
$files = scandir('./input_plaintext/');
unset($files[0],$files[1]); // Revmove '..' and '.' entries from the list of files.

// Parse the html data in the files (expected format is html exported from Evernote).
foreach($files as $file){

  $content = strip_tags(file_get_contents(getcwd().'/input_plaintext/'.$file));
  
  $title = $file;
  $title = explode('.',$title);
  $title = explode('(',$title[0]);
  $title = $title[0];
  $datetime = trim(preg_replace("/[^0-9\-]/",'',$title));
  
  $ts = strtotime($datetime);
  $dst = date("I",$ts);
  $tzo = 5 - $dst;
  $ts = $ts + ($tzo * 60 * 60);
  $timestamp = date("Ymd",$ts).'T'.date("His",$ts).'Z';
  
  $d[] = Array(
    
    'title'         => $title,
    'created_time'  => $timestamp,
    'content'       => $content,  
  
  );
  
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
$o = str_replace(Array('Â','ï»¿'),'',$o); // remove 'Â' characters

echo $o;
file_put_contents('./Evernote Plaintext Import.enex',$o);




?>