<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_kana',
        'shortcut',
        'billing_contact_name',
        'postal_code',
        'address',
        'email',
        'tel',
        'fax',
        'invoice_delivery_method',
        'note',
    ];
}
