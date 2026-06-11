<?php

namespace App\Models;

use CodeIgniter\Model;

class PaperModel extends Model
{
    protected $table            = 'papers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['conference_id', 'title', 'abstract', 'file_path', 'author_id', 'discipline_id', 'status', 'payment_status', 'keywords', 'presentation_score'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getDetails($conditions = [])
    {
        $builder = $this->select('papers.*, users.email as author_email, users.first_name as author_first_name, users.last_name as author_last_name, disciplines.name as discipline_name, tracks.name as track_name, conferences.year as conference_year')
                        ->join('users', 'users.id = papers.author_id')
                        ->join('disciplines', 'disciplines.id = papers.discipline_id')
                        ->join('tracks', 'tracks.id = disciplines.track_id')
                        ->join('conferences', 'conferences.id = papers.conference_id');

        if (!empty($conditions)) {
            $builder->where($conditions);
        }

        return $builder->findAll();
    }
}
