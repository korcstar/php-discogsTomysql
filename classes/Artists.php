<?php

namespace xml2sql;

use Prewk\XmlStringStreamer;
use Prewk\XmlStringStreamer\Stream\File;
use Prewk\XmlStringStreamer\Parser\StringWalker;

use xml2sql\Database;

class Artists_Parser extends Database
{
    
    public function parse($xml_file){      
        $totalSize = filesize($xml_file);
        
        if ($totalSize > 0){
            $this->remove_artists();
        }
        
        $stream = new File($xml_file, 16384, function($chunk, $readBytes) use ($totalSize) {
            $percentage = $readBytes / $totalSize * 100;
            echo "Artists Progress: $percentage"." %".PHP_EOL;
        });
        
        // Construct the parser
        $parser = new StringWalker();
        
        // Construct the streamer
        $streamer = new XmlStringStreamer($parser, $stream);
        
        while ($node = $streamer->getNode()) {
            $artist = simplexml_load_string($node);
            
            $artist_id = $this->parse_artist($artist);
            
            if (!($artist_id > 0)) return false;
            
            $this->parse_images($artist_id, $artist);
            $this->parse_aliases($artist_id, $artist);
            $this->parse_groups($artist_id, $artist);
            $this->parse_members($artist_id, $artist);
            $this->parse_namevariations($artist_id, $artist);
            $this->parse_urls($artist_id, $artist);
        }
        
        return true;
    }
    
    /**
     * Insert data into artist table
     * @param \SimpleXMLElement $artist
     */
    private function parse_artist($artist){
        
        if ($artist->id == null || $artist->id =='')
            return 0;
        
        $artist_data['artistID']     = mysqli_real_escape_string($this->connection, $artist->id);
        $artist_data['name']         = mysqli_real_escape_string($this->connection, $artist->name);
        $artist_data['realname']     = mysqli_real_escape_string($this->connection, $artist->realname);
        $artist_data['profile']      = mysqli_real_escape_string($this->connection, $artist->profile);
        $artist_data['data_quality'] = mysqli_real_escape_string($this->connection, $artist->data_quality);
        
        return $this->insert_table('artists', $artist_data);
    }

    /**
     * Insert data into artists_images table
     * @param integer $artist_id
     * @param \SimpleXMLElement $artist
     */
    private function parse_images($artist_id, $artist){
        $number = 0;
        $images = $artist->images->image;
        
        if ($images != null){
            foreach ($images as $image){
                $image_data['artist']   = $artist_id;
                $image_data['number']   = $number;
                $image_data['type']     = mysqli_real_escape_string($this->connection, $image['type']);
                $image_data['width']    = $image['width'];
                $image_data['height']   = $image['height'];
                $image_data['uri']      = mysqli_real_escape_string($this->connection, $image['uri']);
                $image_data['uri150']   = mysqli_real_escape_string($this->connection, $image['uri150']);
            
                $this->insert_table('artists_images', $image_data);   

                unset($image_data);
                $number++;
            }
        }
    }
    
    /**
     * parse urls of artist
     * @param integer $artist_id
     * @param \SimpleXMLElement $artist
     */
    private function parse_urls($artist_id, $artist){
        $urls = $artist->urls->url;
        $number = 0;
        
        if ($urls != null){
            foreach ($urls as $url){
                $url_data['artist']   = $artist_id;
                $url_data['number']   = $number;
                $url_data['url']     = mysqli_real_escape_string($this->connection, $url);
            
                $this->insert_table('artists_urls', $url_data);
                
                unset($url_data);
                $number++;
            }
        }
    }
    
    /**
     * parse namevariations
     * @param unknown $artist_id
     * @param unknown $artist
     */
    private function parse_namevariations($artist_id, $artist){
        $namevariations = $artist->namevariations->name;
        $number = 0;
    
        if ($namevariations != null){
            foreach ($namevariations as $name){
                $namevariations_data['artist']   = $artist_id;
                $namevariations_data['number']   = $number;
                $namevariations_data['name']     = mysqli_real_escape_string($this->connection, $name);
        
                $this->insert_table('artists_namevariations', $namevariations_data);
                
                unset($namevariations_data);
                $number++;
            }
        }
    }
    
    /**
     * parse aliases
     * @param unknown $artist_id
     * @param unknown $artist
     */
    private function parse_aliases($artist_id, $artist){
        $aliases = $artist->aliases->name;
        $number = 0;
    
        if ($aliases != null){
            foreach ($aliases as $name){
                $aliases_data['artist']   = $artist_id;
                $aliases_data['number']   = $number;
                $aliases_data['name']     = mysqli_real_escape_string($this->connection, $name);
                
                $this->insert_table('artists_aliases', $aliases_data);
                
                unset($aliases_data);            
                $number++;
            }
        }
    }
    
    /**
     * parse groups
     * @param unknown $artist_id
     * @param unknown $artist
     */
    private function parse_groups($artist_id, $artist){
        $groups = $artist->groups->name;
        $number = 0;
    
        if ($groups != null){
            foreach ($groups as $name){
                $group_data['artist']   = $artist_id;
                $group_data['number']   = $number;
                $group_data['name']     = mysqli_real_escape_string($this->connection, $name);

                $this->insert_table('artists_groups', $group_data);
                
                unset($group_data);
                $number++;
            }
        }
    }
    
    /**
     * parse members
     * @param unknown $artist_id
     * @param unknown $artist
     */
    private function parse_members($artist_id, $artist){
        $member_ids = $artist->members->id;
        $member_names = $artist->members->name;
        $number = 0;
    
        if ($member_ids != null){
            foreach ($member_ids as $mid){
                $memeber_data['artist']      = $artist_id;
                $memeber_data['number']      = $number;
                $memeber_data['member_id']   = $mid;
                $memeber_data['member_name'] = mysqli_real_escape_string($this->connection, $member_names[$number]);
            
                $this->insert_table('artists_members', $memeber_data);
            
                unset($memeber_data);            
                $number++;
            }
        }
    }
    
}