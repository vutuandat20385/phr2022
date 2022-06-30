<?php

namespace App\Models;

use CodeIgniter\Model;

class AutoUpdateHistoryModel extends Model{
    protected $table = 'auto_update_history';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';
    protected $allowedFields = ['id_treatment','annual_checkup_id','phoneNumber', 'fullName','type','exam_date','gender','birthdate','conclusion','result'];
}