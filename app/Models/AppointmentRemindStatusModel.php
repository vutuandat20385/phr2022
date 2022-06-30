<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentRemindStatusModel extends Model{
    protected $table = 'appointment_remind_status';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = ['appointment_id','admin_id', 'remind'];
}