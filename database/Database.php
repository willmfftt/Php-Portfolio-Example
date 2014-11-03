<?php

/**
 * Manages the connection to the database
 *
 * @author William Moffitt
 */
require_once 'config.php';
require_once 'orm/rb.php';

class Database {
    
    private static $isSetup = false;
    
    public function __construct() {
        if (DEBUG) {
            if (self::$isSetup == false) {
                R::setup();
                self::$isSetup = true;
            }
        } else {
            // Setup an actual database here or freeze
        }
    }
    
    public function __destruct() {
        R::close();
        self::$isSetup = false;
    }
    
}
