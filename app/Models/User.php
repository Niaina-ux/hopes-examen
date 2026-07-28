<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $fillable = [
    'name',
    'email',
    'image',
    'password',
    'password_affiche',
    'role',
    ];

    protected $hidden = [
        'password',
        'password_affiche', // <-- tena ilaina eto, tsy hita amin'ny JSON/API response
        'remember_token',
    ];

    public function prof()
    {
        return $this->hasOne(Prof::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function examens()
    {
        return $this->belongsToMany(Examen::class, 'student_examen')
            ->withPivot('termine', 'date_debut', 'date_fin')
            ->withTimestamps();
    }  

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class,'student_id');
    }
    

}
