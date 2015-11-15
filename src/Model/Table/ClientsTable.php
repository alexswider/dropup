<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class ClientsTable extends Table
{
    public function getByUrlName($urlName, $fields = array()) 
    {
        return $this->find()->select($fields)->where(['urlName' => $urlName])->first();
    }
}