<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ClientsTable extends Table
{
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmpty('name')
            ->notEmpty('urlName');

        return $validator;
    }
    
    public function getByUrlName($urlName, $fields = array()) 
    {
        return $this->find()->select($fields)->where(['urlName' => $urlName])->first();
    }
}