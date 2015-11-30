<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class AssetsTable extends Table
{
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmpty('description')
            ->requirePresence('description');

        return $validator;
    }
}
