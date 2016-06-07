<?php

/**
 * database config
 */
$dbhost = "localhost";
$dbname = "discogs";
$username = "root";
$password = "123";

/**
 * xml file path
 * if xml file is not exist then leave blank string = ''
 */

$artists_xml = "xml/artists.xml";

$level_xml   = "xml/labels.xml";

$master_xml  = "xml/masters.xml";

$release_xml = "xml/releases.xml";

/**
 * include files
 */
include 'classes/Database.php';
include 'classes/Artists.php';
include 'classes/Labels.php';
include 'classes/Masters.php';
include 'classes/Releases.php';