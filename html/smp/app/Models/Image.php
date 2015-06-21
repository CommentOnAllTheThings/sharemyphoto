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

    public static function getImageInformationFromGUID($guid) {
        $image_status = Image::where('image_guid', '=', $guid)->firstOrFail();

        $image_return_data = array();
        $image_return_data['status'] = (int)$image_status->image_status;
        $image_return_data['title'] = $image_status->image_title;
        $image_return_data['file_path'] = $image_status->image_file_path;
        $image_return_data['thumb_path'] = $image_status->image_thumb_path;

        return $image_return_data;
    }
}