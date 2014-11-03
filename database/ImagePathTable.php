<?php

/**
 * 
 *
 * @author William Moffitt
 */
class ImagePathTable {
    
    public function addNewImagePath($userId, $pathname) {
        if (!(isset($userId) || isset($pathname))) {
            return false;
        }
        
        $imageBean = R::dispense('image');
        $imageBean->userId = $userId;
        $imageBean->pathname = $pathname;
        
        $id = R::store($imageBean);
        
        return array(
            'id' => $id
        );
    }
    
    public function deleteImagePath($id) {
        if (!isset($id)) {
            return false;
        }
        
        $imageBean = R::load('image', $id);
        if ($imageBean == null) {
            return false;
        }
        
        R::trash($imageBean);
        
        return true;
    }
    
}
