<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}