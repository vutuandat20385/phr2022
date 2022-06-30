<?php

namespace App\Models;

use CodeIgniter\Model;

class UserActiveModel extends Model{
    protected $table = 'user_active_log';
    protected $primaryKey = 'user_active_log_id';
    protected $returnType     = 'array';

    protected $allowedFields = ['user_active_log_id','phone_number', 'user_name', 'creator', 'date_created'];
}