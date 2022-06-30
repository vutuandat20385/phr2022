<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceDefineModel extends Model{
    protected $table = 'service_define';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = ['id','name', 'codeName', 'parentId', 'status'];
}