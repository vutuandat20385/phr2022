<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoryImportModel extends Model{
    protected $table = 'history_import';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';
    protected $allowedFields = ['file_name', 'date','report', 'count_success', 'count_false'];

}