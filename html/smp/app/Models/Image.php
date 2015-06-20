<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'smp_images';

    /**
     * The table primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'image_id';
}