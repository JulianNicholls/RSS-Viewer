<?php
//---------------------------------------------------------------------------
// Class to attach to MongoDB
//  Database:   feeds
//  Collection: sites
//---------------------------------------------------------------------------

class Sites
{
    private $sites;

    // Attach to MongoDB and select sites from feeds.
    
    public function __construct()
    {
        $mconn  = new MongoClient();
        
        $this->sites  = $mconn->feeds->sites;
    }


    // Insert a new feed
    
    public function insert( $name, $url )
    {
        return $this->sites->insert( array( 'name' => $name, 'url' => $url ) );        
    }


    // Remove a feed by its ID.
    
    public function remove_by_id( $id )
    {
        return $this->sites->remove( array( '_id' => new MongoId( $id ) ) );        
    }

    
    // Remove a feed by its name.
    
    public function remove_by_name( $name )
    {
        return $this->sites->remove( array( 'name' => $name ) );        
    }
    
    // Update a feed by its ID.
    
    public function update( $id, $data )
    {
        return $this->sites->update( array( '_id' => new MongoId( $id ) ), $data );        
    }

    
    // Return all the feed sites
    
    public function all()
    {
        $cursor = $this->sites->find();
        $urls   = array();
        
        foreach( $cursor as $cur )
            $urls[] = $cur;
            
        return $urls;
    }
    
    
    // Return the data for a feed by its ID.
    
    public function find_by_id( $id )
    {
        $cur = $this->sites->findOne( array( '_id' => new MongoId( $id ) ) );
        
        if( $cur )
            return $cur;
            
        return FALSE;
    }
    
    
    // Return the data for a feed by its name.
    
    public function find_by_name( $name )
    {
        $cur = $this->sites->findOne( array( 'name' => $name ) );
        
        if( $cur )
            return $cur;
            
        return FALSE;
    }
    
}
