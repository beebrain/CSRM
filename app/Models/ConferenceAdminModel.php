<?php

namespace App\Models;

use CodeIgniter\Model;

class ConferenceAdminModel extends Model
{
    protected $table            = 'conference_admins';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['conference_id', 'user_id'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
}
