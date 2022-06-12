<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SmsTemplate extends Model
{
    protected $fillable = [
        'name',
        'template',
        'created_by'
    ];

    /**
     * Changed the template variable in the template to value.
     *
     * @param array $variables
     * @param array $values
     * @return string
     */
    public function transformTemplate(array $variables, array $values): string
    {
        return str_replace($variables, $values, $this->attributes['template']);
    }

    /**
     * Get title template for tab list
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        return Str::of($this->attributes['name'])->replace('_', ' ')->title();
    }
}
