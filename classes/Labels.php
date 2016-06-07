<?php

namespace xml2sql;

use Prewk\XmlStringStreamer;
use Prewk\XmlStringStreamer\Stream\File;
use Prewk\XmlStringStreamer\Parser\StringWalker;

use xml2sql\Database;

class Labels_Parser extends Database
{
    
    public function parse($xml_file){        
        $totalSize = filesize($xml_file);
        
        if ($totalSize > 0){
            $this->remove_labels();
        }
        
        $stream = new File($xml_file, 16384, function($chunk, $readBytes) use ($totalSize) {
            $percentage = $readBytes / $totalSize * 100;
            echo "Levels Progress: $percentage"." %".PHP_EOL;
        });
        
        // Construct the parser
        $parser = new StringWalker();
        
        // Construct the streamer
        $streamer = new XmlStringStreamer($parser, $stream);
        
        while ($node = $streamer->getNode()) {
            $level = simplexml_load_string($node);
            
            $level_id = $this->parse_level($level);
            
            if (!($level_id > 0)) return false;
            
            $this->parse_images($level_id, $level);
            $this->parse_urls($level_id, $level);
            $this->parse_sublabels($level_id, $level);
        }
        
        return true;
    }
    
    /**
     * Insert data into artist table
     * @param \SimpleXMLElement $artist
     */
    private function parse_level($level){
        
        if ($level->id == null || $level->id =='')
            return 0;
        
        $level_data['labelID']     = mysqli_real_escape_string($this->connection, $level->id);
        $level_data['name']         = mysqli_real_escape_string($this->connection, $level->name);
        $level_data['contactinfo']  = mysqli_real_escape_string($this->connection, $level->contactinfo);
        $level_data['profile']      = mysqli_real_escape_string($this->connection, $level->profile);
        $level_data['parentlabel'] = mysqli_real_escape_string($this->connection, $level->parentLabel);
        $level_data['data_quality'] = mysqli_real_escape_string($this->connection, $level->data_quality);
        
        return $this->insert_table('labels', $level_data);
    }

    /**
     * parse images
     * @param integer $level_id
     * @param \SimpleXMLElement $level
     */
    private function parse_images($level_id, $level){
        $number = 0;
        $images = $level->images->image;
        
        if ($images != null){
            foreach ($images as $image){
                $image_data['label']   = $level_id;
                $image_data['number']   = $number;
                $image_data['type']     = mysqli_real_escape_string($this->connection, $image['type']);
                $image_data['width']    = $image['width'];
                $image_data['height']   = $image['height'];
                $image_data['uri']      = mysqli_real_escape_string($this->connection, $image['uri']);
                $image_data['uri150']   = mysqli_real_escape_string($this->connection, $image['uri150']);
            
                $this->insert_table('labels_images', $image_data);   

                unset($image_data);
                $number++;
            }
        }
    }
    
    /**
     * parse urls
     * @param integer $level_id
     * @param \SimpleXMLElement $level
     */
    private function parse_urls($level_id, $level){
        $urls = $level->urls->url;
        $number = 0;
        
        if ($urls != null){
            foreach ($urls as $url){
                $url_data['label']   = $level_id;
                $url_data['number']   = $number;
                $url_data['url']     = mysqli_real_escape_string($this->connection, $url);
            
                $this->insert_table('labels_urls', $url_data);
                
                unset($url_data);
                $number++;
            }
        }
    }
    
    /**
     * parse sublabels
     * @param integer $level_id
     * @param \SimpleXMLElement $level
     */
    private function parse_sublabels($level_id, $level){
        $labels = $level->sublabels->label;
        $number = 0;
    
        if ($labels != null){
            foreach ($labels as $label){
                $data['label']   = $level_id;
                $data['number']   = $number;
                $data['sublabel']     = mysqli_real_escape_string($this->connection, $label);
    
                $this->insert_table('labels_sublabels', $data);
    
                unset($data);
                $number++;
            }
        }
    }
       
}