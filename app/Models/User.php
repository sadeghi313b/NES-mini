<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GetFields;


class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
    use GetFields;

    /* -------------------------------------------------------------------------- */
    /*                                 definitions                                */
    /* -------------------------------------------------------------------------- */
    const GENDERS = ['male', 'female'];


    /* -------------------------------------------------------------------------- */
    /*                         fiilables & hiddens & casts                        */
    /* -------------------------------------------------------------------------- */
    protected $guarded = ['id', 'created_by'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'gender' => 'string',
        'email_verified_at' => 'datetime:Y/m/d H:i',
        'status' => 'boolean',
        'created_at' => 'datetime:Y/m/d H:i',
        'updated_at' => 'datetime:Y/m/d H:i',
        'deleted_at' => 'datetime:Y/m/d H:i',
    ];

    /* -------------------------------------------------------------------------- */
    /*                                  Accessors                                 */
    /* -------------------------------------------------------------------------- */
    protected $appends = ['full_name'];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }


    /* -------------------------------------------------------------------------- */
    /*                                Relationships                               */
    /* -------------------------------------------------------------------------- */
    /* ---------------------------------- role ---------------------------------- */
    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->withPivot('assigned_by', 'assigned_at')
            ->withTimestamps();
    }

    public function hasRole(string $role): bool
    {
        // dd(auth()->user()->roles()->pluck('name')->toArray());
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->roles()->where('name', $role)->exists()) {
                return true;
            }
        }
        return false;
    }

    /* --------------------------------- phones --------------------------------- */
    public function phones()
    {
        return $this->hasMany(Phone::class)
            // ->select(['id', 'user_id', 'phone'])
        ;
    }

    /* -------------------------------- employee -------------------------------- */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /* ------------------------------- created by ------------------------------- */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* -------------------------------------------------------------------------- */
    /*                                    boot                                    */
    /* -------------------------------------------------------------------------- */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->created_by && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
    }
}
