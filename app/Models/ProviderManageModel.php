<?php

namespace App\Models;

use CodeIgniter\Model;

class ProviderManageModel extends Model{
    protected $table = 'provider_manage';
    protected $primaryKey = 'provider_manage_id';
    protected $returnType     = 'array';

    protected $allowedFields = ['admin_id','provider_id', 'notify', 'retired'];
}