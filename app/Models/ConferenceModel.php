<?php

namespace App\Models;

use CodeIgniter\Model;

class ConferenceModel extends Model
{
    protected $table            = 'conferences';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['year', 'title', 'host_name', 'description', 'is_active'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
}
