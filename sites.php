<?php
//---------------------------------------------------------------------------
// Class to attach to MongoDB
//  Database:   feeds
//  Collection: sites
//---------------------------------------------------------------------------

class Sites
{
    private $sites;

    // Attach to MongoDB and select sites collection from feeds DB.
    
    public function __construct()
    {
        $mconn  = new MongoClient();
        
        $this->sites  = $mconn->feeds->sites;
    }


    // Insert a new feed
    
    public function insert( $data )
    {
        return $this->sites->insert( $data );        
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

    
    // Return all the feed sites, sorted by name
    
    public function all()
    {
        $cursor = $this->sites->find();
        $urls   = array();
        
        foreach( $cursor as $cur )
            $urls[] = $cur;
            
        usort( $urls, array( "Sites", "cmp_sites" ) );
        
        return $urls;
    }
    
    
    // Compare two site entries by name
    
    static function cmp_sites( $a, $b )
    {
        return strcmp( $a['name'], $b['name'] );
    }
    
    
    // Return the data for a feed by its ID.
    
    public function find_by_id( $id )
    {
        $cur = $this->sites->findOne( array( '_id' => new MongoId( $id ) ) );
        
        return $cur ? $cur : FALSE;
    }
    
    
    // Return the data for a feed by its name.
    
    public function find_by_name( $name )
    {
        $cur = $this->sites->findOne( array( 'name' => $name ) );
        
        return $cur ? $cur : FALSE;
    }
    
}
