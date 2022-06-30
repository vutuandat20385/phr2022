<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = ['settingType','settingName', 'settingValue'];
}