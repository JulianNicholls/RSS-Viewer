<?php

class Sites
{
    private $sites;
    
    public function __construct()
    {
        $mconn  = new MongoClient();
        
        $this->sites  = $mconn->feeds->sites;
    }


    public function insert( $name, $url )
    {
        return $this->sites->insert( array( 'name' => $name, 'url' => $url ) );        
    }


    public function remove_by_id( $name )
    {
        return $this->sites->remove( array( '_id' => new MongoId( $name ) ) );        
    }

    
    public function remove_by_name( $name )
    {
        return $this->sites->remove( array( 'name' => $name ) );        
    }

    
    public function all()
    {
        $cursor = $this->sites->find();
        $urls   = array();
        
        foreach( $cursor as $cur )
            $urls[] = $cur;
            
        return $urls;
    }
    
    
    public function find_by_id( $id )
    {
        $cur = $this->sites->findOne( array( '_id' => new MongoId( $id ) ) );
        
        if( $cur )
            return $cur;
            
        return FALSE;
    }
    
    
    public function find_by_name( $name )
    {
        $cur = $this->sites->findOne( array( 'name' => $name ) );
        
        if( $cur )
            return $cur;
            
        return FALSE;
    }
    
}
