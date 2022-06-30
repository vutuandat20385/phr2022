<?php

namespace App\Models;

use CodeIgniter\Model;

class GuestViewModel extends Model{
    protected $table = 'annual_guest_view';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = ['phone_number', 'code_phr', 'examination_date', 'last_date', 'type'];
}