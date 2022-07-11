<?php

namespace App\Models;

use CodeIgniter\Model;

class User2Model extends Model{
    protected $table = 'users2';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = ['username', 'password', 'salt', 'email','fullname','role','device_token'];
}