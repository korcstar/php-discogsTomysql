<?php

    include ("vendor/autoload.php");
    
    include 'config.php';
    
    use xml2sql\Artists_Parser;
    use xml2sql\Labels_Parser;
    use xml2sql\Masters_Parser;
    use xml2sql\Releases_Parser;
    
    if ($artists_xml != ""){
        $artist = new Artists_Parser($dbhost, $dbname, $username, $password);
        $artist->parse($artists_xml);
    }
    
    if ($level_xml != ""){
        $level = new Labels_Parser($dbhost, $dbname, $username, $password);
        $level->parse($level_xml);
    }
    
    if ($master_xml != ""){
        $master = new Masters_Parser($dbhost, $dbname, $username, $password);
        $master->parse($master_xml);
    }
   
    if ($release_xml != ""){
        $release = new Releases_Parser($dbhost, $dbname, $username, $password);
        $release->parse($release_xml);
    }
?>
