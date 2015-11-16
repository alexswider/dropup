<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Auth\DefaultPasswordHasher;

class UsersTable extends Table
{
    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }
}
