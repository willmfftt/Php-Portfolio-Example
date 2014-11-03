<?php

/**
 * User database fields
 *
 * @author William Moffitt
 */
require_once 'Database.php';
require_once 'Files/UploadImage.php';

class UserTable extends Database {

    public function addNewUser($username, $password) {
        if (!(isset($username) || isset($password))) {
            return false;
        }

        if ($this->doesUserExist($username)) {
            return false;
        }

        $apiKey = self::generateApiKey();

        $user = R::dispense('user');
        $user->username = $username;
        $user->password = self::hashPassword($username, $password);
        $user->apiKey = $apiKey;

        $id = R::store($user);
        
        // Create user's folder
        if (!file_exists('../users/' . $username)) {
            mkdir('users/' . $username, 0777, true);
        }

        $result = array(
            'id' => $id,
            'apiKey' => $apiKey
        );

        return $result;
    }

    public function readUser($username, $password) {
        if (!(isset($username) || isset($password))) {
            return false;
        }

        $user = R::findOne('user', 'username = ? AND password = ?', [$username, $password]);
        if ($user == null) {
            return false;
        }

        $result = array(
            'id' => $user->getID(),
            'apiKey' => $user->apiKey,
            'channelId' => isset($user->channelId) ? $user->channelId : 'none'
        );

        return $result;
    }

    public function deleteUser($id) {
        $userBean = R::load('user', $id);

        if ($userBean != null && !$userBean->isEmpty()) {
            // Delete users directory
            if (file_exists('users/' . $userBean->username)) {
                rmdir('users/' . $userBean->username);
            }
            
            R::trash($userBean); 
           
            return true;
        } else {
            return false;
        }
    }

    public function changePassword($id, $oldPass, $newPass) {
        if (!(isset($id) || isset($oldPass) || isset($newPass))) {
            return false;
        }

        $user = R::load('user', $id);
        if ($user == null) {
            return false;
        }

        $username = $user->username;
        $password = $user->password;
        $oldPass = self::hashPassword($username, $oldPass);
        if ($oldPass !== $password) {
            return false;
        }

        $newPass = self::hashPassword($username, $newPass);
        $user->password = $newPass;
        R::store($user);

        return true;
    }
    
    public function addRegistrationId($id, $registrationId) {
        if (!(isset($id) || isset($registrationId))) {
            return false;
        }
        
        $user = R::load('user', $id);
        if ($user == null) {
            return false;
        }
        
        $user->registrationId = $registrationId;
        R::store($user);
        
        return true;
    }
    
    public function getRegistrationId($id) {
        if (!isset($id)) {
            return false;
        }
        
        $user = R::load('user', $id);
        if ($user == null) {
            return false;
        }
        
        return array(
            'registrationId' => $user->registrationId
        );
    }
    
    public function saveImage($id, Upload\File $file) {
        if (!(isset($id) || isset($file))) {
            return false;
        }
        
        $user = R::load('user', $id);
        if ($user == null) {
            return false;
        }
        
        $uploadImg = new UploadImage();
        $pathname = $uploadImg->upload($file);
        
        if ($pathname == false) {
            return false;
        } else {
            $user->imgPathName = $pathname;
        }
    }

    public function doesUserExist($username) {
        $userBean = R::findOne('user', 'username=?', [$username]);

        return $userBean == null ? false : !$userBean->isEmpty(); // Reverse true/false to match function name
    }

    public function getApiKey($id) {
        $userBean = R::load('user', $id);

        return $userBean == null ? false : $userBean->apiKey;
    }

    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }

    public function hashPassword($username, $password) {
        $secret_key = '4a8deF&3$sQ!';
        $secret_key = $secret_key . $username;

        return sha1($secret_key . $password);
    }

}
