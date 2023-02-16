<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BakeryProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_id', 'user_id', 'when', 'raw_material', 'dough', 'braided', 'baked', 'delivered', 'total',
    ];

    public function action()
    {
        return $this->belongsTo('App\Models\Action');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
