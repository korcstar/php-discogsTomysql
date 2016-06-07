<?php

namespace xml2sql;

use Prewk\XmlStringStreamer;
use Prewk\XmlStringStreamer\Stream\File;
use Prewk\XmlStringStreamer\Parser\StringWalker;

use xml2sql\Database;

class Releases_Parser extends Database
{
    
    public function parse($xml_file){        
        $totalSize = filesize($xml_file);
        
        $this->remove_releases();
        
		$stream = new File($xml_file, 163840, function($chunk, $readBytes) use ($totalSize) {
            //$percentage = $readBytes / $totalSize * 100;
            echo "Releases Progress: $readBytes : $totalSize".PHP_EOL;
        });
        
        // Construct the parser
        $parser = new StringWalker();
        
        // Construct the streamer
        $streamer = new XmlStringStreamer($parser, $stream);
        
        while ($node = $streamer->getNode()) {
            $release = simplexml_load_string($node);
            
            $release_id = $this->parse_release($release);
            
            if (!($release_id > 0)) return false;
            
            $this->parse_artists($release_id, $release);
            $this->parse_images($release_id, $release);
            $this->parse_companies($release_id, $release);
            $this->parse_extraartists($release_id, $release);
            $this->parse_formats($release_id, $release);
            $this->parse_genres($release_id, $release);
            $this->parse_identifiers($release_id, $release);
            $this->parse_levels($release_id, $release);
            $this->parse_styles($release_id, $release);
            $this->parse_tracklist($release_id, $release);
            $this->parse_videos($release_id, $release);
        }
        
        return true;
    }
    
    /**
     * Insert data into master table
     * @param \SimpleXMLElement master node
     */
    private function parse_release($node){
        
        if ($node['id'] == null || $node['id'] =='')
            return 0;
        
        $data['releaseID']       = $node['id'];
        $data['status']         = $node['status'];
        $data['title']          = mysqli_real_escape_string($this->connection, $node->title);
        $data['country']        = mysqli_real_escape_string($this->connection, $node->country);
        $data['releasedate']    = mysqli_real_escape_string($this->connection, $node->released);
        $data['notes']          = mysqli_real_escape_string($this->connection, $node->notes);
        $data['master_id']      = $node->master_id;
        $data['data_quality']   = $node->data_quality;
        
        return $this->insert_table('releases', $data);
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
                $image_data['release_id']    = $node_id;
                $image_data['number']   = $number;
                $image_data['type']     = mysqli_real_escape_string($this->connection, $image['type']);
                $image_data['width']    = $image['width'];
                $image_data['height']   = $image['height'];
                $image_data['uri']      = mysqli_real_escape_string($this->connection, $image['uri']);
                $image_data['uri150']   = mysqli_real_escape_string($this->connection, $image['uri150']);
    
                $this->insert_table('releases_images', $image_data);
    
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
                $data['release_id']   = $node_id;
                $data['number']   = $number;
                $data['artist_id']   = $row->id;
                $data['name']     = mysqli_real_escape_string($this->connection, $row->name);
                $data['anv']     = mysqli_real_escape_string($this->connection, $row->anv);
                $data['jn']     = mysqli_real_escape_string($this->connection, $row->join);
                $data['role']     = mysqli_real_escape_string($this->connection, $row->role);
                $data['tracks']     = mysqli_real_escape_string($this->connection, $row->tracks);
            
                $this->insert_table('releases_artists', $data);   

                unset($data);
                $number++;
            }
        }
    }
    
    /**
     * parse level
     * @param integer $node_id
     * @param \SimpleXMLElement $node
     */
    private function parse_levels($node_id, $node){
        $number = 0;
        $datas = $node->artists->artist;
    
        if ($datas != null){
            foreach ($datas as $row){
                $data['release_id'] = $node_id;
                $data['number']     = $number;
                $data['catno']      = mysqli_real_escape_string($this->connection, $row['catno']);
                $data['name']       = mysqli_real_escape_string($this->connection, $row['name']);
    
                $this->insert_table('releases_labels', $data);
    
                unset($data);
                $number++;
            }
        }
    }
    
    /**
     * parse extraartists
     * @param integer $node_id
     * @param \SimpleXMLElement $node
     */
    private function parse_extraartists($node_id, $node){
        $number = 0;
        $datas = $node->extraartists->artist;
    
        if ($datas != null){
            foreach ($datas as $row){
                $data['release_id']     = $node_id;
                $data['number']         = $number;
                $data['artist_id']      = $row->id;
                $data['name']           = mysqli_real_escape_string($this->connection, $row->name);
                $data['anv']            = mysqli_real_escape_string($this->connection, $row->anv);
                $data['jn']           = mysqli_real_escape_string($this->connection, $row->join);
                $data['role']           = mysqli_real_escape_string($this->connection, $row->role);
                $data['tracks']         = mysqli_real_escape_string($this->connection, $row->tracks);
    
                $this->insert_table('releases_extraartists', $data);
    
                unset($data);
                $number++;
            }
        }
    }
    
    /**
     * parse formats
     * @param integer $node_id
     * @param \SimpleXMLElement $node
     */
    private function parse_formats($node_id, $node){
        $number = 0;
        $datas = $node->formats->format;
    
        if ($datas != null){
            foreach ($datas as $row){
                $data['release_id'] = $node_id;
                $data['number']     = $number;
                $data['text']       = mysqli_real_escape_string($this->connection, $row['text']);
                $data['name']       = mysqli_real_escape_string($this->connection, $row['name']);
                $data['quantity']   = $row['quantity'];
    
                $format_id = $this->insert_table('releases_formats', $data);
                $this->parse_formats_descriptions($format_id, $row->descriptions);
    
                unset($data);
                $number++;
            }
        }
    }
    
    private function parse_formats_descriptions($format_id, $descriptions){
        $number = 0;
        $datas = $descriptions->description;
        
        if ($datas != null){
            foreach ($datas as $row){
                $data['release_format'] = $format_id;
                $data['number']         = $number;
                $data['description']    = mysqli_real_escape_string($this->connection, $row);
            
                $format_id = $this->insert_table('releases_formats_descriptions', $data);
            
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
                $data['release_id']   = $node_id;
                $data['number']   = $number;
                $data['genre']     = mysqli_real_escape_string($this->connection, $row);
    
                $this->insert_table('releases_genres', $data);
    
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
                $data['release_id']   = $node_id;
                $data['number']   = $number;
                $data['style']     = mysqli_real_escape_string($this->connection, $row);
    
                $this->insert_table('releases_styles', $data);
    
                unset($data);
                $number++;
            }
        }
    }
    
    /**
     * parse tracklist
     * @param integer $node_id
     * @param \SimpleXMLElement $node
     */
    private function parse_tracklist($node_id, $node){
        $number = 0;
        $datas = $node->tracklist->track;
    
        if ($datas != null){
            foreach ($datas as $row){
                $data['release_id'] = $node_id;
                $data['number']     = $number;
                $data['position']   = mysqli_real_escape_string($this->connection, $row->position);
                $data['title']      = mysqli_real_escape_string($this->connection, $row->title);
                $data['duration']   = mysqli_real_escape_string($this->connection, $row->duration);
    
                $track_id = $this->insert_table('releases_tracks', $data);
                
                $this->parse_tracks_artists($track_id, $row->artists);
                $this->parse_tracks_extraartists($track_id, $row->extraartists);
    
                unset($data);
                $number++;
            }
        }
    }
    
    private function parse_tracks_artists($track_id, $artists){
        $number = 0;
        $datas = $artists->artist;
    
        if ($datas != null){
            foreach ($datas as $row){
                $data['track'] = $track_id;
                $data['number']         = $number;
                $data['artist_id']      = $row->id;
                $data['name']           = mysqli_real_escape_string($this->connection, $row->name);
                $data['anv']            = mysqli_real_escape_string($this->connection, $row->anv);
                $data['jn']           = mysqli_real_escape_string($this->connection, $row->join);
                $data['role']           = mysqli_real_escape_string($this->connection, $row->role);
                $data['tracks']         = mysqli_real_escape_string($this->connection, $row->tracks);
        
                $format_id = $this->insert_table('releases_tracks_artists', $data);
        
                unset($data);
                $number++;
            }
        }
    }
    
    private function parse_tracks_extraartists($track_id, $extraartists){
        $number = 0;
        $datas = $extraartists->artist;
    
        if ($datas != null){
            foreach ($datas as $row){
                $data['track'] = $track_id;
                $data['number']         = $number;
                $data['artist_id']      = $row->id;
                $data['name']           = mysqli_real_escape_string($this->connection, $row->name);
                $data['anv']            = mysqli_real_escape_string($this->connection, $row->anv);
                $data['jn']           = mysqli_real_escape_string($this->connection, $row->join);
                $data['role']           = mysqli_real_escape_string($this->connection, $row->role);
                $data['tracks']         = mysqli_real_escape_string($this->connection, $row->tracks);
        
                $format_id = $this->insert_table('releases_tracks_extraartists', $data);
        
                unset($data);
                $number++;
            }
        }
    }
    
    /**
     * parse identifiers
     * @param integer $node_id
     * @param \SimpleXMLElement $node
     */
    private function parse_identifiers($node_id, $node){
        $number = 0;
        $datas = $node->identifiers->identifier;
    
        if ($datas != null){
            foreach ($datas as $row){
                $data['release_id'] = $node_id;
                $data['number']     = $number;
                $data['type']      = mysqli_real_escape_string($this->connection, $row['type']);
                $data['value']       = mysqli_real_escape_string($this->connection, $row['value']);
    
                $this->insert_table('releases_identifiers', $data);
    
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
                $data['release_id']     = $node_id;
                $data['number']         = $number;
                $data['duration']       = $row['duration'];
                $data['src']            = mysqli_real_escape_string($this->connection, $row['src']);
                $data['embed']          = $row['embed'];
                $data['title']          = mysqli_real_escape_string($this->connection, $row->title);
                $data['description']    = mysqli_real_escape_string($this->connection, $row->description);
    
                $this->insert_table('releases_videos', $data);
    
                unset($data);
                $number++;
            }
        }
    }
    
    /**
     * parse companies
     * @param integer $node_id
     * @param \SimpleXMLElement $node
     */
    private function parse_companies($node_id, $node){
        $number = 0;
        $datas = $node->companies->company;
    
        if ($datas != null){
            foreach ($datas as $row){
                $data['release_id']     = $node_id;
                $data['number']         = $number;
                $data['company_id']     = $row->id;
                $data['name']           = mysqli_real_escape_string($this->connection, $row->name);
                $data['catno']          = mysqli_real_escape_string($this->connection, $row->catno);
                $data['entity_type']    = mysqli_real_escape_string($this->connection, $row->entity_type);
                $data['entity_type_name'] = mysqli_real_escape_string($this->connection, $row->entity_type_name);
                $data['resource_url']     = mysqli_real_escape_string($this->connection, $row->resource_url);
    
                $this->insert_table('releases_companies', $data);
    
                unset($data);
                $number++;
            }
        }
    }
}