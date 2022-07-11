<?php

namespace App\Models;

use CodeIgniter\Model;

class Users2RolesModel extends Model{
    protected $table = 'users2_roles';
    protected $primaryKey = 'role_id';
    protected $returnType     = 'array';

    protected $allowedFields = ['role_name'];
}