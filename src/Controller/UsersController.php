<?php

namespace App\Controller;

use App\Controller\AppController;
//use Cake\Auth\DefaultPasswordHasher;

class UsersController extends AppController
{
    
    public function login()
    {
//        $hasher = new DefaultPasswordHasher();
//        echo $hasher->hash('test');
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error('Your username or password is incorrect.');
        }
    }
    
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }



}