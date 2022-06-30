<?php

namespace App\Models;

use CodeIgniter\Model;

class AutoUpdateCovidHistoryModel extends Model{
    protected $table = 'auto_update_covid_history';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';
    protected $allowedFields = ['id_treatment','iddichvu_data','phoneNumber', 'fullName','exam_date','gender','birthdate','conclusion','result'];
}