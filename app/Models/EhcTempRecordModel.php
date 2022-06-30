<?php

namespace App\Models;

use CodeIgniter\Model;

class EhcTempRecordModel extends Model{
    protected $table = 'ehc_temp_records';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = ['treatment_id','phone_number', 'patient_name', 'date', 'status'];
}