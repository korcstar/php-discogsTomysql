<?php

namespace xml2sql;

use Prewk\XmlStringStreamer;
use Prewk\XmlStringStreamer\Stream\File;
use Prewk\XmlStringStreamer\Parser\StringWalker;

use xml2sql\Database;

class Masters_Parser extends Database
{
    
    public function parse($xml_file){        
        $totalSize = filesize($xml_file);
        
        if ($totalSize > 0){
            $this->remove_masters();
        }
        
        $stream = new File($xml_file, 16384, function($chunk, $readBytes) use ($totalSize) {
            $percentage = $readBytes / $totalSize * 100;
            echo "Masters Progress: $percentage"." %".PHP_EOL;
        });
        
        // Construct the parser
        $parser = new StringWalker();
        
        // Construct the streamer
        $streamer = new XmlStringStreamer($parser, $stream);
        
        while ($node = $streamer->getNode()) {
            $master = simplexml_load_string($node);
            
            $master_id = $this->parse_master($master);
            
            if (!($master_id > 0)) return false;
            
            $this->parse_artists($master_id, $master);
            $this->parse_genres($master_id, $master);
            $this->parse_images($master_id, $master);
            $this->parse_styles($master_id, $master);
            $this->parse_videos($master_id, $master);
        }
        
        return true;
    }
    
    /**
     * Insert data into master table
     * @param \SimpleXMLElement master node
     */
    private function parse_master($node){
        
        if ($node['id'] == null || $node['id'] =='')
            return 0;
        
        $data['masterID']       = $node['id'];
        $data['main_release']   = $node->main_release;
        $data['year']           = $node->year;
        $data['title']          = mysqli_real_escape_string($this->connection, $node->title);
        $data['data_quality']   = mysqli_real_escape_string($this->connection, $node->data_quality);
        
        return $this->insert_table('masters', $data);
    }
    
    /**
     * parse images
     * @param integer $level_id
     * @param \SimpleXMLElement $level
     */
    private function parse_images($node_id, $node){
        $number = 0;
        $images = $node->images->image;
    
        if ($images != null){
            foreach ($images as $image){
                $image_data['master']    = $node_id;
                $image_data['number']   = $number;
                $image_data['type']     = mysqli_real_escape_string($this->connection, $image['type']);
                $image_data['width']    = $image['width'];
                $image_data['height']   = $image['height'];
                $image_data['uri']      = mysqli_real_escape_string($this->connection, $image['uri']);
                $image_data['uri150']   = mysqli_real_escape_string($this->connection, $image['uri150']);
    
                $this->insert_table('masters_images', $image_data);
    
                unset($image_data);
                $number++;
            }
        }
    }

    /**
     * parse artists
     * @param integer $node_id
     * @param \SimpleXMLElement $node
     */
    private function parse_artists($node_id, $node){
        $number = 0;
        $datas = $node->artists->artist;
        
        if ($datas != null){
            foreach ($datas as $row){
                $data['master']   = $node_id;
                $data['number']   = $number;
                $data['artist_id']   = $row->id;
                $data['name']     = mysqli_real_escape_string($this->connection, $row->name);
                $data['anv']     = mysqli_real_escape_string($this->connection, $row->anv);
                $data['jn']     = mysqli_real_escape_string($this->connection, $row->join);
                $data['role']     = mysqli_real_escape_string($this->connection, $row->role);
                $data['tracks']     = mysqli_real_escape_string($this->connection, $row->tracks);
            
                $this->insert_table('masters_artists', $data);   

                unset($data);
                $number++;
            }
        }
    }
    
    /**
     * parse genres
     * @param integer $node_id
     * @param \SimpleXMLElement $node
     */
    private function parse_genres($node_id, $node){
        $genres = $node->genres->genre;
        $number = 0;
    
        if ($genres != null){
            foreach ($genres as $row){
                $data['master']   = $node_id;
                $data['number']   = $number;
                $data['genre']     = mysqli_real_escape_string($this->connection, $row);
    
                $this->insert_table('masters_genres', $data);
    
                unset($data);
                $number++;
            }
        }
    }
    
    /**
     * parse styles
     * @param integer $node_id
     * @param \SimpleXMLElement $node
     */
    private function parse_styles($node_id, $node){
        $styles = $node->styles->style;
        $number = 0;
    
        if ($styles != null){
            foreach ($styles as $row){
                $data['master']   = $node_id;
                $data['number']   = $number;
                $data['style']     = mysqli_real_escape_string($this->connection, $row);
    
                $this->insert_table('masters_styles', $data);
    
                unset($data);
                $number++;
            }
        }
    }
    
    /**
     * parse videos
     * @param integer $node_id
     * @param \SimpleXMLElement $node
     */
    private function parse_videos($node_id, $node){
        $videos = $node->videos->video;
        $number = 0;
    
        if ($videos != null){
            foreach ($videos as $row){
                $data['master']         = $node_id;
                $data['number']         = $number;
                $data['duration']       = $row['duration'];
                $data['src']            = mysqli_real_escape_string($this->connection, $row['src']);
                $data['embed']          = $row['embed'];
                $data['title']          = mysqli_real_escape_string($this->connection, $row->title);
                $data['description']    = mysqli_real_escape_string($this->connection, $row->description);
    
                $this->insert_table('masters_videos', $data);
    
                unset($data);
                $number++;
            }
        }
    }
       
}