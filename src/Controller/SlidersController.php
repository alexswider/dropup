<?php

namespace App\Controller;

use Cake\I18n\Time;

class  SlidersController extends AppController
{
    public function index()
    {
        $this->loadModel('Clients');
        
        $clients = $this->Clients->find('All');
        $this->set(compact('clients'));
    }
    public function displayProjects($clientName)
    {
        $this->loadModel('Projects');
        $this->loadModel('Clients');
        
        $client = $this->Clients
                ->find()
                ->where(['urlName' => $clientName])
                ->first();
        
        $projects = $this->Projects
                ->find('All')
                ->where(['idClient' => $client->idClient]);
        
        $this->set(compact('projects', 'client'));
    }
    
    public function displayItems($clientName, $projectName)
    {
        $this->loadModel('Projects');
        $this->loadModel('Clients');
        $this->loadModel('Items');
        
        $client = $this->Clients
                ->find()
                ->where(['urlName' => $clientName])
                ->first();
        
        $project = $this->Projects
                ->find()
                ->where(['urlName' => $projectName])
                ->first();
        
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
        $this->loadModel('Assets');
        $isNew = false;
        
        $assets = $this->Assets
                ->find()
                ->where(['idItem' => $idItem])
                ->order(['orderAsset' => 'ASC']);
        
        if ($idItem == 'new') {
            $isNew = true;
        }
        
        if ($this->request->is('post')) {
            if ($isNew) {
                $idItem = $this->newItem($projectName, $this->request->data['item_name']);
            }
            
            $nextOrder = $assets->count();
            $this->saveAsset($idItem, $projectName, $clientName, $nextOrder, $this->request->data);
        }
        
        $this->set(compact('assets', 'isNew', 'idItem'));
    }
    
    public function saveOrder($idItem)
    {
        $this->loadModel('Assets');
        
        $order = json_decode($this->request->data['orderAsset'], true);
        
        foreach ($order as $key => $id) {
            $query = $this->Assets->query();
            $query->update()
                    ->set(['orderAsset' => $key])
                    ->where(['idItem' => $idItem, 'idAsset' => $id])
                    ->execute();
        }
        $this->redirect($this->request->data['refpage']);
    }
    
    private function saveAsset($idItem, $projectName, $clientName, $nextOrder, $requestData) {
        $this->loadModel('Assets');
        
        $asset = $this->Assets->newEntity([
            'idItem' => $idItem, 
            'name' => $requestData['name'],
            'description' => $requestData['description'],
            'imagePath' => $this->saveImage($requestData['image']),
            'orderAsset' => $nextOrder
        ]);

        if ($this->Assets->save($asset)) {
            $this->Flash->success(__('Asset has been saved.'));
            $this->redirect($clientName . '/' . $projectName . '/' . $idItem);
        } else {
            $this->Flash->error(__('Unable to add your asset.'));
        }
    }

    private function saveImage($imageData) {
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
        
        $file = "uploads/" . uniqid() . $format;
	file_put_contents($file, $imageData);
        
        return $file;
    }
    
    private function newItem($projectName, $name) {
        $this->loadModel('Projects');
        $this->loadModel('Items');
        
        $idProject = $this->Projects
                ->find()
                ->select('idProject')
                ->where(['urlName' => $projectName])
                ->first();
        
        $item = $this->Items->newEntity([
            'idProject' => $idProject->idProject, 
            'name' => $name, 
            'date' => Time::now()
        ]);
        
        $result = $this->Items->save($item);
                
        if ($result) {
            $this->Flash->success(__('Item has been saved.'));
        } else {
            $this->Flash->error(__('Unable to add your item.'));
        }
        
        return isset($result->idItem) ? $result->idItem : false;
    }
}
