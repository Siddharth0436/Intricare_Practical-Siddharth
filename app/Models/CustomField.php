<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class CustomField extends Model
{
protected $fillable = ['label','name','type','meta','is_required','sort_order'];


protected $casts = [
'meta' => 'array',
'required' => 'boolean',
];
}