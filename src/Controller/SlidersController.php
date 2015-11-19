<?php

namespace App\Controller;

use Cake\I18n\Time;
use Cake\Event\Event;
use ZipArchive;

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
        
        if ($this->request->is('post')) {
            if ($this->Auth->user('type') != 'admin') {
                $this->Flash->error(__('You do not have permission.'));
                return $this->redirect(['controller' => 'users', 'action' => 'login']);
            }
            $this->newItem($project->idProject, $this->request->data['name'], $this->request->data['type']);
        }
        $this->set(compact('items', 'itemsDate', 'project', 'client'));
    }
    
    public function displayItem($clientName, $projectName, $idItem)
    {
        $this->loadModel('Clients');
        $this->loadModel('Projects');
        $this->loadModel('Items');
        
        $client = $this->Clients->getByUrlName($clientName);
        $project = $this->Projects->getByUrlName($projectName); 
        $item = $this->Items->get($idItem);
        
        $this->set(compact('idItem' , 'item', 'project', 'client'));
        
        if ($item->type == 'assets') {
            $this->displayAssets($this, $idItem, $projectName, $clientName);
        } else if ($item->type == 'media') {
            $this->displayMedia($this, $idItem, $projectName, $clientName);
        }
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
    
    private function displayMedia($scope, $idItem, $projectName, $clientName) {
        $scope->loadModel('Media');
        $data = $scope->Media
            ->find()
            ->where(['idItem' => $idItem]);
        
        if ($scope->request->is('post')) {
            if ($scope->Auth->user('type') != 'admin') {
                $scope->Flash->error(__('You do not have permission.'));
                return $scope->redirect(['controller' => 'users', 'action' => 'login']);
            }
            $scope->saveMedia($idItem, $projectName, $clientName, $scope->request->data);
        }
        
        $scope->set(compact('data'));
        $scope->render('display_media');
    }
    
    private function displayAssets($scope, $idItem, $projectName, $clientName) {
        $data = $scope->loadAssets($idItem);
        
        if ($scope->request->is('post')) {
            if ($scope->Auth->user('type') != 'admin') {
                $scope->Flash->error(__('You do not have permission.'));
                return $scope->redirect(['controller' => 'users', 'action' => 'login']);
            }
            $nextOrder = $data->count();
            $scope->saveAsset($idItem, $projectName, $clientName, $nextOrder, $scope->request->data);
        }
        
        $scope->set(compact('data'));
        $scope->render('display_assets');
    }
    
    private function saveMedia($idItem, $projectName, $clientName, $requestData) {
        $this->loadModel('Media');
        
        $media = $this->Media->newEntity([
            'idItem' => $idItem, 
            'name' => $requestData['name'],
            'description' => $requestData['description'],
            'height' => $requestData['height'],
            'width' => $requestData['width'],
            'path' => $this->unzip($requestData['zipfile'], $idItem),
        ]);
        
        if ($idItem && $this->Assets->save($media)) {
            $this->Flash->success(__('Media has been saved.'));
            return $this->redirect($clientName . '/' . $projectName . '/' . $idItem);
        }
        $this->Flash->error(__('Unable to add your media.'));
    }
    
    private function unzip($zipData, $idItem) {
        $file = tempnam("tmp", "zip");
        $zip = new ZipArchive();
        $res = $zip->open($file, ZipArchive::OVERWRITE);
        
        if($res == TRUE) {
            $zipData = preg_replace('/^data:;base64,/', '', $zipData);
            $zip->addFromString('zip', base64_decode($zipData));

            $path = 'uploads/' . $idItem . '/';
            $zip->extractTo($path);
            $zip->close();
        } else {
            $this->Flash->error(__('Unable to unzip.'));
        }
        
        
        return $path;
    }
    
    private function loadAssets($idItem) {
        $this->loadModel('Assets');
        $assets = $this->Assets
                ->find()
                ->where(['idItem' => $idItem])
                ->order(['orderAsset' => 'ASC']);
        
        return $assets;
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
    
    private function newItem($idProject, $name, $type) 
    {     
        $this->loadModel('Items');
        
        $item = $this->Items->newEntity([
            'idProject' => $idProject, 
            'name' => $name, 
            'date' => Time::now(),
            'type' => $type
        ]);
        
        $result = $this->Items->save($item);
                
        if ($result && mkdir('uploads/' . $result->idItem)) {
            $this->Flash->success(__('Item has been saved.'));
        } else {
            $this->Flash->error(__('Unable to add your item.'));
        }
    }
}