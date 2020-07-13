<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\IUser;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;

class UserRepository extends BaseRepository implements IUser
{
    public function model()
    {
        return User::class;
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function search(Request $request)
    {
        $query = (new $this->model)->newQuery();
        if ($request->has_designs) {
            $query->has('designs');
        }

        // check for available to hire
        if ($request->available_to_hire) {
            $query->where('available_to_hire', true);
        }

        // Geographic search
        $lat = $request->latitude;
        $lng = $request->longitude;
        $distance = $request->distance;
        $unit = $request->unit;

        if ($lat && $lng) {
            $point = new Point($lat, $lng);
            $unit == 'km'? $distance *= 1000: $distance *=1609;
            $query->distanceSphereExcludingSelf('location', $point, $distance);
        }

        if ($request->order_by_latest) {
            $query->latest();
        }

        $query->oldest();

        return $query->get();
    }
}
