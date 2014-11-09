<?php

/**
 * Location table for storing users location
 *
 * @author William Moffitt
 */
require_once 'Database.php';

class LocationTable extends Database {
   
    public function addLocation($userId, $latitude, $longitude) {
        if (!(isset($userId) || isset($latitude) || isset($longitude))) {
            return false;            
        }
        
        $location = R::dispense('location');
        $location->userId = $userId;
        $location->latitude = $latitude;
        $location->longitude = $longitude;
        
        R::store($location);
        
        return true;
    }
    
    public function getLocation($userId) {
        if (!isset($userId)) {
            return false;
        }
        
        $location = R::findOne('location', 'userId=?', [ $userId ]);
        
        if ($location == null || $location->isEmpty()) {
            return false;
        }
        
        return array(
            'latitude' => $location->latitude,
            'longitude' => $location->longitude
        );
    }
    
    public function getUsersByLocation($latitude, $longitude) {
        if (!(isset($latitude) || isset($longitude))) {
            return false;
        }
        
        $stmt = 'SELECT userId, ( '
                . '3963 * acos ( '
                . 'cos ( radians(?) ) '
                . '* cos( radians( latitude ) ) '
                . '* cos( radians( longitude ) - radians(?) ) '
                . '+ sin( radians(?) ) '
                . '* sin( radians( latitude ) ) '
                . ') '
                . ') AS distance '
                . 'FROM location '
                . 'HAVING distance < .5 '
                . 'ORDER BY distance ';
                //. 'LIMIT ?, 20';
        $rows = R::getAll($stmt, [ $latitude, $longitude, $latitude ]);
        
        // Will need to return users info (picture, bio, etc)
        
        return $rows;
    }
    
}
