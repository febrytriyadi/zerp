<?php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class PaymentTerm extends Model
{
    protected $fillable = ['code', 'name', 'days'];
}
