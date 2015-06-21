<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    // Mapping for Image table since it's not "Image"
    protected $table = 'smp_images';
    // Table primary key since it's not "id"
    protected $primaryKey = 'image_id';

    /*
        Description: Gets the number of images that are "published" (ie. not deleted)

        @param 
        @returns The number of images that are published as an associative array.
    */
    public static function getNumberOfImages() {
        return DB::select('SELECT count(*) AS number_images FROM smp_images WHERE image_status = ?', [1]);
    }

    /*
        Description: Gets list of images that are "published" (ie. not deleted)

        @param offset The number of rows we should skip.
        @param images_per_pages The number of rows we should return.
        @returns An associative array of images that are published.
    */
    public static function getImages($offset = 0, $images_per_page = 20) {
        // Ensure that we aren't getting nulls passed in
        if (isset($offset, $images_per_page)) {
            // Cast to int
            $offset = (int)$offset;
            $images_per_page = (int)$images_per_page;

            // Only try to run the query when the number of images per page > 0!
            if ($images_per_page > 0) {
                return DB::table('smp_images')->orderBy('created_at', 'DESC')->where('image_status', '=', '1')->skip($offset)->take($images_per_page)->get();
            }
        }

        // Worst case we return a blank array
        return array();
    }

    /*
        Description: Gets the information for an image with a specified GUID

        @param guid The GUID of the image to be retrieved from the database
        @returns An associative array containing the image status, title, image path and thumbnail path.
    */
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