<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table
{
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmpty('username', 'A username is required')
            ->notEmpty('password', 'A password is required')
            ->add('username', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Username already exists'
            ]);

        return $validator;
    }
}
