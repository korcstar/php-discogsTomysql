<?php

namespace xml2sql;

class Database
{
    protected $dbhost = "localhost";
    protected $dbname = "discogs";
    protected $username = "root";
    protected $password = "123";
    
    protected $connection;
    
    public function __construct($host, $database, $username, $password)
    {
        $this->dbhost = $host;
        $this->dbname = $database;
        $this->username = $username;
        $this->password = $password;
        
        $this->connection = $this->connectDB();
        $this->connection->set_charset("utf8");
    }
    
    protected function connectDB() {
        $con = mysqli_connect($this->dbhost, $this->username, $this->password, $this->dbname);
    
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            echo PHP_EOL;
            echo 'Check config.php!';
            exit;
        }
        return $con;
    }
    
    /**
     * Insert data into table
     * @param mysqli_connection $connection
     * @param String $tabel_name
     * @param array $data
     */
    protected function insert_table($tabel_name, $data){
        $columns = implode(", ", array_keys($data));
        $values  = implode("', '", array_values($data));
    
        $sql = "INSERT INTO ".$tabel_name."($columns) VALUES ('$values')";
    
        mysqli_query($this->connection, $sql) or die('Error: ' . mysqli_error($this->connection));
        
        return mysqli_insert_id($this->connection);
    }
    
    /**
     * remove artist data
     */
    protected function remove_artists(){
		echo "Artists Table Remove".PHP_EOL;

        $tables = array('artists', 'artists_aliases', 'artists_groups', 'artists_images', 'artists_members', 'artists_namevariations', 'artists_urls');
        foreach ($tables as $table){
            $sql = "DELETE FROM $table WHERE 1";
            mysqli_query($this->connection, $sql) or die('Error: ' . mysqli_error($this->connection));
        }
    }
    
    /**
     * remove labels data
     */
    protected function remove_labels(){
		echo "Labels Table Remove".PHP_EOL;

        $tables = array('labels', 'labels_images', 'labels_sublabels', 'labels_urls');
        foreach ($tables as $table){
            $sql = "DELETE FROM $table WHERE 1";
            mysqli_query($this->connection, $sql) or die('Error: ' . mysqli_error($this->connection));
        }
    }
    
    /**
     * remove masters data
     */
    protected function remove_masters(){
		echo "Masters Table Remove".PHP_EOL;

        $tables = array('masters', 'masters_artists', 'masters_genres', 'masters_images', 'masters_styles', 'masters_videos');
        foreach ($tables as $table){
            $sql = "DELETE FROM $table WHERE 1";
            mysqli_query($this->connection, $sql) or die('Error: ' . mysqli_error($this->connection));
        }
    }
    
    /**
     * remove releases data
     */
    protected function remove_releases(){
		echo "Releases Table Remove".PHP_EOL;
        $tables = array('releases', 'releases_artists', 'releases_companies', 'releases_extraartists', 'releases_formats', 'releases_styles','releases_tracks', 'releases_tracks_artists', 'releases_tracks_extraartists', 'releases_formats_descriptions', 'releases_genres', 'releases_identifiers', 'releases_images', 'releases_labels', 'releases_videos');
        foreach ($tables as $table){
            $sql = "DELETE FROM $table WHERE 1";
            mysqli_query($this->connection, $sql) or die('Error: ' . mysqli_error($this->connection));
        }
    }
}

?>