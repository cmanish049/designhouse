<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\ICriterion;

class ForUser implements ICriterion
{
    protected $userId;
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function apply($model)
    {
        return $model->where('user_id', $this->userId);
    }
}
