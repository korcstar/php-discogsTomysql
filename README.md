# discogs-xmlToMysql
Discogs xml to mysql php script

http://data.discogs.com/
last 4 xml to mysql

1. Download script
2. Edit config.php
    for databse

      $dbhost = "your dbhost";<br>
      $dbname = "your dbname";
      $username = "username";
      $password = "password";
      
    for xml file
      $artists_xml = "xml/artists.xml";
      $level_xml   = "xml/labels.xml";
      $master_xml  = "xml/masters.xml";
      $release_xml = "xml/releases.xml";
    
3. Run script in console mode
    php xm12sql.php
