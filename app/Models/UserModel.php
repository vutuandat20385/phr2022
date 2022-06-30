<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $returnType     = 'array';

    protected $allowedFields = ['user_id','username', 'password', 'salt', 'secret_question', 'secret_answer', 'creator', 'person_id', 'uuid', 'activation_key', 'email', 'status'];
}