<?php

namespace App\Models;

use CodeIgniter\Model;

class StandardValueModel extends Model{
    protected $table = 'standard_value';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = ['id','fullName', 'shortName', 'codeName', 'parentId','minMale','maxMale','textMale','minFemale','maxFemale','textFemale','unit', 'status'];
}