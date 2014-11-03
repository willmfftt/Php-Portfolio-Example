<?php

require_once 'Slim/Slim.php';
require_once 'database/UserTable.php';

define('DEBUG', true);

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->group('/api', function () use ($app) {

    $app->group('/v1', function () use ($app) {

        $app->group('/user', function () use ($app) {

            $app->post('/newUser/:username/:password', function ($username, $password) {
                $response = array();

                $user = new UserTable();
                $result = $user->addNewUser($username, $password);
                $user = null;

                if ($result == false) {
                    $response['error'] = true;
                    $response['message'] = 'Error creating new user';
                } else {
                    $response['error'] = false;
                    $response['message'] = $result;
                }

                echo json_encode($response);
            });

            $app->get('/login/:username/:password', function ($username, $password) {
                $user = new UserTable();
                $password = $user->hashPassword($username, $password);

                $response = array();

                $result = $user->readUser($username, $password);
                $user = null;

                if ($result == false) {
                    $response['error'] = true;
                    $response['message'] = 'Login failed';
                } else {
                    $response['error'] = false;
                    $response['message'] = $result;
                }

                echo json_encode($response);
            });

            $app->delete('/delUser/:id/:signature', function ($id, $signature) {
                $userTable = new UserTable();

                $result = null;
                if (DEBUG) {
                    $result = $userTable->deleteUser($id);
                } else {
                    
                }
                $userTable = null;

                $response = array();
                if ($result == false) {
                    $response['error'] = true;
                    $response['msg'] = 'Cannot delete user';
                } else {
                    $response['error'] = false;
                    $response['status'] = $result;
                }

                echo json_encode($response);
            });

            $app->post('/changePass/:id/:oldpassword/:newpassword/:signature', function ($id, $oldpassword, $newpassword, $signature) {
                $userTable = new UserTable();

                $result = null;
                if (DEBUG) {
                    $result = $userTable->changePassword($id, $oldpassword, $newpassword);
                } else {
                    
                }
                $userTable = null;

                $response = array();
                if ($result == false) {
                    $response['error'] = true;
                    $response['msg'] = 'Couldn\'t change password';
                } else {
                    $response['error'] = false;
                    $response['status'] = $result;
                }

                echo json_encode($response);
            });
            
            $app->post('addRegistrationId/:id/:registrationId/:signature', function ($id, $registrationId, $signature) {
                $userTable = new UserTable();
                
                $result = null;
                if (DEBUG) {
                    $result = $userTable->addRegistrationId($id, $registrationId);
                } else {
                    
                }
                $userTable = null;
                
                $response = array();
                if ($result == false) {
                    $response['error'] = true;
                    $response['msg'] = 'Error adding registration id';
                } else {
                    $response['error'] = false;
                    $response['status'] = $result;
                }
                
                echo json_encode($response);
            });

            $app->get('/checkUser/:username', function ($username) {
                $userTable = new UserTable();

                $result = $userTable->doesUserExist($username);

                $response = array();
                $response['userExists'] = $result;

                echo json_encode($response);
            });
            
        });
        
        $app->group('/image', function () use ($app) {
            
            $app->post('/upload/:username/:img/:signature', function($username, $img, $signature) {
                
            });
            
        });
        
    });
    
});

$app->run();

