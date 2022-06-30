<?php

namespace App\Models;

use CodeIgniter\Model;

class AutoTransferHistoryModel extends Model{
    protected $table = 'auto_transfer_history';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = ['phone_number','transaction_type','amount','transaction_code','note','time_transfer','status','transaction_status','sms_content'];
}