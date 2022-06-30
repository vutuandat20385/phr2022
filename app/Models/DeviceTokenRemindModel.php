<?php

namespace App\Models;

use CodeIgniter\Model;

class DeviceTokenRemindModel extends Model{
    protected $table = 'device_token_remind';
    protected $primaryKey = 'token_device_id';
    protected $returnType     = 'array';

    protected $allowedFields = ['admin_id', 'token'];

}