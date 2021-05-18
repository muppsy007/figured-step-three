<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * This model handles all the data for the Inventory Applications.
 *
 * Class FertiliserInventoryApplication
 * @package App\Models
 */

class FertiliserInventoryApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'quantity'
    ];
}
