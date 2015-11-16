<?php

namespace App\Controller;

use App\Controller\AppController;

class UsersController extends AppController
{
    public function index() {
        $this->loadModel('Clients');
        $clientsName = [];
        
        $admins = $this->Users
                ->find()
                ->where(['type' => 'admin']);
        
        $clients = $this->Users
                ->find()
                ->where(['type' => 'client']);
        
        $query = $this->Clients
                ->find()
                ->select(['idClient', 'name']);
        
        foreach ($query->toArray() as $a) {
            $clientsName += [$a->idClient => $a->name];
        }
        
        $this->set(compact('admins', 'clients', 'clientsName'));
    }
    public function login()
    {
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
    
    public function addAdmin() {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            $user->type = 'admin';
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['controller' => 'users']);
            }
            $this->Flash->error(__('Unable to add the user.'));
        }
        $this->set('user', $user);
    }
    
    public function addClient() {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            $user->type = 'client';
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['controller' => 'users']);
            }
            $this->Flash->error(__('Unable to add the user.'));
        }
        $this->set('user', $user);
    }
}