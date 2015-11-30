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
        
        if ($this->request->is('post')) {
            if ($this->Auth->user('type') != 'admin') {
                $this->Flash->error(__('You do not have permission.'));
                return $this->redirect(['controller' => 'users', 'action' => 'login']);
            }
            $client = $this->Clients->newEntity();
            $this->Clients->patchEntity($client, $this->request->data);
            if ($this->Clients->save($client)) {
                $this->Flash->success(__('New client has been saved.'));
            } else {
                $this->Flash->error(__('Unable to add new client.'));
            }
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
            $itemId = $this->newItem($project->idProject, $this->request->data['name'], $this->request->data['type']);
            if($itemId) {
                $this->redirect("$clientName/$projectName/$itemId");
            }
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
            $this->displayAssets($idItem, $projectName, $clientName);
        } else if ($item->type == 'media') {
            $this->displayMedia($idItem, $projectName, $clientName);
        } else if ($item->type == 'video') {
            $this->displayVideo($idItem, $projectName, $clientName);
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
    
    public function deleteItem($idItem) 
    {
        if ($this->Auth->user('type') != 'admin') {
            $this->Flash->error(__('You do not have permission.'));
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
        $this->loadModel('Items');
        
        $item = $this->Items->get($idItem);
        if($this->Items->delete($item) && $this->rrmdir('uploads'. DS . $idItem)) {
            $this->Flash->success(__('The item has been deleted.'));
            return $this->redirect('/');
        }
    }
    
    public function deleteProject($idProject) 
    {
        if ($this->Auth->user('type') != 'admin') {
            $this->Flash->error(__('You do not have permission.'));
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
        $this->loadModel('Items');
        $this->loadModel('Projects');
        
        $items = $this->Items
                ->find()
                ->where(['idProject' => $idProject]);
        
        foreach ($items as $item) {
            $this->deleteItem($item->idItem);
        }
        
        $project = $this->Projects->get($idProject);
        if($this->Projects->delete($project)) {
            $this->Flash->success(__('The project has been deleted.'));
            return $this->redirect('/');
        }
    }

    private function displayMedia($idItem, $projectName, $clientName) 
    {
        $data = $this->loadMedia($idItem);
        
        if ($this->request->is('post')) {
            if ($this->Auth->user('type') != 'admin') {
                $this->Flash->error(__('You do not have permission.'));
                return $this->redirect(['controller' => 'users', 'action' => 'login']);
            }
            $this->saveMedia($idItem, $projectName, $clientName, $this->request->data);
        }
        
        $this->set(compact('data'));
        $this->render('display_media');
    }
    
    private function displayVideo($idItem, $projectName, $clientName) 
    {
        $data = $this->loadVideo($idItem);
        
        if ($this->request->is('post')) {
            if ($this->Auth->user('type') != 'admin') {
                $this->Flash->error(__('You do not have permission.'));
                return $this->redirect(['controller' => 'users', 'action' => 'login']);
            }
            $this->saveVideo($idItem, $projectName, $clientName, $this->request->data);
        }
        
        $this->set(compact('data'));
        $this->render('display_video');
    }
    
    private function displayAssets($idItem, $projectName, $clientName) 
    {
        $data = $this->loadAssets($idItem);
        
        if ($this->request->is('post')) {
            if ($this->Auth->user('type') != 'admin') {
                $this->Flash->error(__('You do not have permission.'));
                return $this->redirect(['controller' => 'users', 'action' => 'login']);
            }
            $nextOrder = $data->count();
            $this->saveAsset($idItem, $projectName, $clientName, $nextOrder, $this->request->data);
        }
        
        $this->set(compact('data'));
        $this->render('display_assets');
    }
    
    private function saveMedia($idItem, $projectName, $clientName, $requestData) {
        $this->loadModel('Media');
        
        $media = $this->Media->newEntity([
            'idItem' => $idItem, 
            'description' => $requestData['description'],
            'height' => $requestData['height'],
            'width' => $requestData['width'],
            'path' => $this->unzip($requestData['base64'], $idItem),
        ]);
        
        if ($idItem && $this->Media->save($media)) {
            $this->Flash->success(__('Media has been saved.'));
            return $this->redirect($clientName . '/' . $projectName . '/' . $idItem);
        }
        $this->Flash->error(__('Unable to add your media.'));
    }
    
    private function saveVideo($idItem, $projectName, $clientName, $requestData) 
    {
        $this->loadModel('Videos');
        
        $video = $this->Videos->newEntity([
            'idItem' => $idItem, 
            'description' => $requestData['description'],
            'height' => $requestData['height'],
            'width' => $requestData['width'],
            'videoPath' => $this->saveMp4($requestData['base64'], $idItem),
        ]);
        
        if ($idItem && $this->Videos->save($video)) {
            $this->Flash->success(__('Video has been saved.'));
            return $this->redirect($clientName . '/' . $projectName . '/' . $idItem);
        }
        $this->Flash->error(__('Unable to add your video.'));
    }
    
    private function unzip($zipData, $idItem) 
    {
        $path = 'uploads'. DS;
        //$zipData = preg_replace('/^data:.+base64,/', '', $zipData);
        $zipData = explode(",", $zipData);
        file_put_contents($path.'tmp.zip', base64_decode($zipData[1]));
        $zip = new ZipArchive();
        $res = $zip->open($path.'tmp.zip');
        
        if($res == TRUE) {
            $path = 'uploads'. DS . $idItem . DS;
            $zip->extractTo($path);
            $zip->close();
        } else {
            $this->Flash->error(__('Unable to unzip.'));
        }
        
        return $path;
    }
    
    private function saveMp4($videoData, $idItem) {
        $path = 'uploads'. DS . $idItem . DS . 'video.mp4';
        $videoData = explode(",", $videoData);
        file_put_contents($path, base64_decode($videoData[1]));

        return $path;
    }
    
    private function loadAssets($idItem) 
    {
        $this->loadModel('Assets');
        $assets = $this->Assets
                ->find()
                ->where(['idItem' => $idItem])
                ->order(['orderAsset' => 'ASC']);
        
        return $assets;
    }
    
    private function loadMedia($idItem) 
    {
        $this->loadModel('Media');
        $media = $this->Media
            ->find()
            ->where(['idItem' => $idItem])
            ->first();
        
        return $media;
    }
    
    private function loadVideo($idItem) {
        $this->loadModel('Videos');
        $video = $this->Videos
            ->find()
            ->where(['idItem' => $idItem])
            ->first();
        
        return $video;
    }
    
    private function saveAsset($idItem, $projectName, $clientName, $nextOrder, $requestData) 
    {
        $this->loadModel('Assets');
        
        $asset = $this->Assets->newEntity([
            'idItem' => $idItem, 
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
                
        if ($result && mkdir('uploads'. DS . $result->idItem)) {
            $this->Flash->success(__('Item has been saved.'));
            return $result->idItem;
        } else {
            $this->Flash->error(__('Unable to add your item.'));
        }
    }

    private function rrmdir($dir) 
    { 
        if (is_dir($dir)) { 
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object); 
                } 
            } 
            reset($objects); 
            return rmdir($dir); 
        } 
    }
}