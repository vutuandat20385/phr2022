<?php

namespace App\Models;

use CodeIgniter\Model;

class ReferenceResultRangeModel extends Model{
    protected $table = 'reference_result_range';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'gender', 'data'];
}