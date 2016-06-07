# discogs-xmlToMysql
Discogs xml to mysql php script

http://data.discogs.com/
last 4 xml to mysql

1. Download script
2. Edit config.php<br>
    for databse

      $dbhost = "your dbhost";<br>
      $dbname = "your dbname";<br>
      $username = "username";<br>
      $password = "password";<br><br>
      
    for xml file<br>
      $artists_xml = "xml/artists.xml";<br>
      $level_xml   = "xml/labels.xml";<br>
      $master_xml  = "xml/masters.xml";<br>
      $release_xml = "xml/releases.xml";<br>
    
3. Run script in console mode<br>
    php xm12sql.php
