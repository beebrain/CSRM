<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['conference_id', 'user_id', 'payment_method', 'amount', 'slip_path', 'transaction_reference', 'status'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getDetails($conditions = [])
    {
        $builder = $this->select('payments.*, users.email, users.first_name, users.last_name, conferences.year as conference_year')
                        ->join('users', 'users.id = payments.user_id')
                        ->join('conferences', 'conferences.id = payments.conference_id');

        if (!empty($conditions)) {
            $builder->where($conditions);
        }

        return $builder->findAll();
    }
}
