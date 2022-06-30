<?php

namespace App\Models;

use CodeIgniter\Model;

class StandardValueModel2 extends Model{
    protected $table = 'standard_values';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = ['id','fullName', 'shortName', 'codeName', 'parentId','minMale','maxMale','textMale','minFemale','maxFemale','textFemale','unit', 'status'];
}