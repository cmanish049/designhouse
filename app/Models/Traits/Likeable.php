<?php

namespace App\Models\Traits;

use App\Models\Like;

trait Likeable
{
    public static function bootLikable()
    {
        static::deleting(function($model) {
            $model->removeLikes();
        });

    }

    public function removeLikes()
    {
        if ($this->likes()->count()) $this->likes()->delete();
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like()
    {
        if (! auth()->check()) return;

        if ($this->isLikedByUser(auth()->id())) return;

        $this->likes()->create(['user_id' => auth()->id()]);
    }

    public function dislike()
    {
        if (! auth()->check()) return;
        if (! $this->isLikedByUser(auth()->id())) return;
        $this->likes()->where('user_id', auth()->id())->delete();
    }


    public function isLikedByUser($userId)
    {
        return (bool) $this->likes()->where('user_id' , $userId)->count();
    }

}
