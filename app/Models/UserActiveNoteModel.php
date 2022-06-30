<?php

namespace App\Models;

use CodeIgniter\Model;

class UserActiveNoteModel extends Model{
    protected $table = 'user_active_note';
    protected $primaryKey = 'id';
    protected $returnType     = 'array';

    protected $allowedFields = ['phoneNumber','note', 'user_update', 'username_update'];
}