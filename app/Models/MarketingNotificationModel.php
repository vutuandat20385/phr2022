<?php

namespace App\Models;

use CodeIgniter\Model;

class MarketingNotificationModel extends Model{
    protected $table = 'manage_marketing_notifications';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = ['content', 'link','public_time', 'status'];
}