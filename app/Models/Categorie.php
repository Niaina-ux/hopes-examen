<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    protected $fillable = ['nom','slug'];

    public function profs()
    {
        return $this->hasMany(Prof::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
