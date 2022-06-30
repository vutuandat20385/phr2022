<?php

namespace App\Models;

use CodeIgniter\Model;

class WalletModel extends Model{
    protected $table = 'wallet';
    protected $primaryKey = 'wallet_id';
    protected $returnType     = 'array';

    protected $allowedFields = [];
}