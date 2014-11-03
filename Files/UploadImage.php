<?php

/**
 * 
 *
 * @author William Moffitt
 */
class UploadImage {
    
    public function upload(\Upload\File $file) {
        $new_filename = uniqid();
        $file->setName($new_filename);
        
        $file->addValidations(array(
            new \Upload\Validation\Mimetype('image/png'),
            new \Upload\Validation\Size('5M')
        ));
        
        try {
            $file->upload();
            return array(
                'pathname' => $file->getPathname()
            );
        } catch (Exception $ex) {
            return false;
        }
    }
    
}
