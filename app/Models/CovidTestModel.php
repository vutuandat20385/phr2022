<?php

namespace App\Models;

use CodeIgniter\Model;

class CovidTestModel extends Model{
    protected $table = 'd4u_covid_test';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = ['id','patient_id', 'patient_name','phone_number','date', 'type', 'result', 'file_result','status'];
}