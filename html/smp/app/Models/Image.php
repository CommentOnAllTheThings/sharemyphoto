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
                return DB::table('smp_images')
                            ->orderBy('created_at', 'DESC')
                            ->where('image_status', '=', '1')
                            ->skip($offset)
                            ->take($images_per_page)
                            ->get();
            }
        }

        // Worst case we return a blank array
        return array();
    }

    /*
        Description: Gets the information for an image with a specified GUID

        @param guid The GUID of the image to be retrieved from the database
        @param published_only Should we retrieve information for only images that are enabled?
        @oaran delete_key The deletion key we used to ensure that we are deleting the correct image
        @returns An associative array containing the image status, title, image path and thumbnail path.
    */
    public static function getImageInformationFromGUID($guid, $published_only = true, $delete_key = null) {
        // Check if we are trying to delete an image, and if we are make sure that the published_only flag is set to true
        if (isset($delete_key) && strlen($delete_key) > 0) {
            $published_only = true;
        }

        // Determine whether we need to get information for a published image or for any image regardless of status
        if ($published_only === true) {
            // Get information for a published image only!
            // Are we trying to delete an image?
            if (isset($delete_key) && strlen($delete_key) > 0) {
                // Yes, now check the guid and deletion key to make sure they match!
                $image_status = Image::where('image_guid', '=', $guid)
                                        ->where('image_status', '=', 1)
                                        ->where('image_delete_key', '=', $delete_key)
                                        ->firstOrFail();
            }
            else {
                // No, now check the guid and get the image data
                $image_status = Image::where('image_guid', '=', $guid)
                                        ->where('image_status', '=', 1)
                                        ->firstOrFail();
            }
        }
        else {
            // Get information for an image regardless of whether it is published or not!
            $image_status = Image::where('image_guid', '=', $guid)
                                    ->firstOrFail();
        }

        // Return data
        $image_return_data = array();
        $image_return_data['status'] = (int)$image_status->image_status;
        $image_return_data['title'] = $image_status->image_title;
        $image_return_data['image_description'] = $image_status->image_description;
        $image_return_data['file_path'] = $image_status->image_file_path;
        $image_return_data['thumb_path'] = $image_status->image_thumb_path;
        $image_return_data['delete_key'] = $image_status->image_delete_key;

        return $image_return_data;
    }

    /*
        Description: Gets the information for an image with a specified GUID

        @param guid The GUID of the image to be retrieved from the database
        @returns An associative array containing the image status, title, image path and thumbnail path.
    */
    public static function deleteImageFromGUID($guid) {
        $image_object = Image::where('image_guid', '=', $guid)->firstOrFail();
        $image_object->image_status = 5;
        return $image_object->save();
    }
}