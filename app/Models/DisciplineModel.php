<?php

namespace App\Models;

use CodeIgniter\Model;

class DisciplineModel extends Model
{
    protected $table            = 'disciplines';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['track_id', 'name'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getWithTrack()
    {
        return $this->select('disciplines.*, tracks.name as track_name')
                    ->join('tracks', 'tracks.id = disciplines.track_id')
                    ->findAll();
    }
}
