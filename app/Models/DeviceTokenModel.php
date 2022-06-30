<?php

namespace App\Models;

use CodeIgniter\Model;

class DeviceTokenModel extends Model{
    protected $table = 'device_token';
    protected $primaryKey = 'device_token_id';
    protected $returnType     = 'array';


}