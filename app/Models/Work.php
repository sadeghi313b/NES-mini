<?php
//! app/Models/Work.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Work extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'employee_id',
        'work_type',
        'description',
        'status',
        'tags',
        'created_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'status' => 'boolean',
        'date' => 'date:Y/m/d',
    ];

    //. -------------------------------------------------------------------------- */
    //.                                  Relations                                 */
    //. -------------------------------------------------------------------------- */
    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_number');
    }

    // 🔹 Employee belongs to a User (chained relation)
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Employee::class, 'id',        
            'id',        
            'employee_id', 
            'user_id'
        );
    }

    // 🔹 Alternative shortcut accessor for readability
    public function creator()
    {
        return $this->user();
    }
}
