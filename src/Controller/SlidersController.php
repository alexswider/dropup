<?php

namespace App\Controller;

use Cake\I18n\Time;
use Cake\Event\Event;

class  SlidersController extends AppController
{
    public function beforeFilter(Event $event)
    {
        $this->Auth->allow(['index', 'displayProjects', 'displayItems', 'displayItem']);
    }
    
    public function index()
    {
        $this->loadModel('Clients');
        $user = $this->Auth->user();
        
        if($user['type'] == 'admin') {
            $clients = $this->Clients->find();
        } else {
            $clients = $this->Clients->find()->where(['private' => 0]);
        }
        if($user['type'] == 'client') {
            $privateClients = $this->Clients->find()->where(['private' => 1, 'idClient' => $user['idClient']]);
            $clients = array_merge($privateClients->toArray(), $clients->toArray());
        }
                
        $this->set(compact('clients', 'privateClients'));
    }
    public function displayProjects($clientName)
    {
        $this->loadModel('Clients');
        $this->loadModel('Projects');
        
        $client = $this->Clients->getByUrlName($clientName);
        
        $projects = $this->Projects
                ->find('All')
                ->where(['idClient' => $client->idClient]);
        
        if ($this->request->is('post')) {
            if ($this->Auth->user('type') != 'admin') {
                $this->Flash->error(__('You do not have permission.'));
                return $this->redirect(['controller' => 'users', 'action' => 'login']);
            }
            $project = $this->Projects->newEntity();
            $this->Projects->patchEntity($project, $this->request->data);
            $project->idClient = $client->idClient;
            if ($this->Projects->save($project)) {
                $this->Flash->success(__('New project has been saved.'));
            } else {
                $this->Flash->error(__('Unable to add new project.'));
            }
        }
        
        $this->set(compact('projects', 'client'));
    }
    
    public function displayItems($clientName, $projectName)
    {
        $this->loadModel('Clients');
        $this->loadModel('Projects');
        $this->loadModel('Items');
        
        $client = $this->Clients->getByUrlName($clientName);
        $project = $this->Projects->getByUrlName($projectName);
        
        $itemsDate = $this->Items
                ->find()
                ->select('date')
                ->where(['idProject' => $project->idProject])
                ->order(['date' => 'DESC'])
                ->group('date');
        
        foreach ($itemsDate as $key => $date) {
            $items[$key] = $this->Items
                ->find('All')
                ->where(['idProject' => $project->idProject, 'date' => $date->date]);
        }
        
        $this->set(compact('items', 'itemsDate', 'project', 'client'));
    }
    
    public function displayItem($clientName, $projectName, $idItem)
    {
        $this->loadModel('Clients');
        $this->loadModel('Projects');
        $this->loadModel('Items');
        $this->loadModel('Assets');
        
        $client = $this->Clients->getByUrlName($clientName);
        $project = $this->Projects->getByUrlName($projectName);
        
        $assets = $this->Assets
                ->find()
                ->where(['idItem' => $idItem])
                ->order(['orderAsset' => 'ASC']);
        
        if ($idItem == 'new') {
            $isNew = true;
        } else {
            $isNew = false;
            $item = $this->Items->get($idItem);
        }
        
        if ($this->request->is('post')) {
            if ($this->Auth->user('type') != 'admin') {
                $this->Flash->error(__('You do not have permission.'));
                return $this->redirect(['controller' => 'users', 'action' => 'login']);
            }
            if ($isNew) {
                $idItem = $this->newItem($projectName, $this->request->data['item_name']);
            }
            
            $nextOrder = $assets->count();
            $this->saveAsset($idItem, $projectName, $clientName, $nextOrder, $this->request->data);
        }
        
        $this->set(compact('assets', 'isNew', 'idItem' , 'item', 'project', 'client'));
    }
    
    public function saveOrder($idItem)
    {
        if ($this->Auth->user('type') != 'admin') {
            $this->Flash->error(__('You do not have permission.'));
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
        
        $this->loadModel('Assets');
        
        $order = json_decode($this->request->data['orderAsset'], true);
        
        foreach ($order as $key => $id) {
            $query = $this->Assets->query();
            $query->update()
                    ->set(['orderAsset' => $key])
                    ->where(['idItem' => $idItem, 'idAsset' => $id])
                    ->execute();
        }
        
        if ($query) {
            $this->Flash->success(__('Order has been saved.'));
        }
        $this->redirect($this->request->data['refpage']);
    }
    
    private function saveAsset($idItem, $projectName, $clientName, $nextOrder, $requestData) 
    {
        $this->loadModel('Assets');
        
        $asset = $this->Assets->newEntity([
            'idItem' => $idItem, 
            'name' => $requestData['name'],
            'description' => $requestData['description'],
            'imagePath' => $this->saveImage($requestData['image'], $idItem),
            'orderAsset' => $nextOrder
        ]);
        if ($idItem && $this->Assets->save($asset)) {
            $this->Flash->success(__('Asset has been saved.'));
            return $this->redirect($clientName . '/' . $projectName . '/' . $idItem);
        }
        $this->Flash->error(__('Unable to add your asset.'));
    }

    private function saveImage($imageData, $idItem) 
    {
        $format = substr($imageData, 11, 3);
        switch ($format) {
            case "png":
                $format = ".png";
                break;
            case "jpe":
                $format = ".jpg";
                break;
            case "gif":
                $format = ".gif";
                break;
            default :
                return false;
        }
        
        $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageData));
        
        $file = "uploads/" . $idItem . '/' . uniqid() . $format;
	file_put_contents($file, $imageData);
        
        return $file;
    }
    
    private function newItem($projectName, $name) 
    {
        $this->loadModel('Projects');
        $this->loadModel('Items');
        
        $idProject = $this->Projects->getByUrlName($projectName, ['idProject']);
        
        $item = $this->Items->newEntity([
            'idProject' => $idProject->idProject, 
            'name' => $name, 
            'date' => Time::now()
        ]);
        
        $result = $this->Items->save($item);
                
        if ($result && mkdir('uploads/' . $result->idItem)) {
            $this->Flash->success(__('Item has been saved.'));
        } else {
            $this->Flash->error(__('Unable to add your item.'));
        }
        
        return isset($result->idItem) ? $result->idItem : false;
    }
}
