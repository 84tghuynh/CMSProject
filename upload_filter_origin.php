<?php
    // namespace Gumlet;
    include "ImageResize.php";


    // file_upload_path() - Safely build a path String that uses slashes appropriate for our OS.
    // Default upload path is an 'uploads' sub-folder in the current folder.
    function file_upload_path($original_filename, $upload_subfolder_name = 'uploads') {
       $current_folder = dirname(__FILE__);

       // Build an array of paths segment names to be joins using OS specific slashes.
       $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];

       // The DIRECTORY_SEPARATOR constant is OS specific.
       return join(DIRECTORY_SEPARATOR, $path_segments);
    }

    // file_is_an_image() - Checks the mime-type & extension of the uploaded file for "image-ness".
    function file_is_an_image($temporary_path, $new_path) {
        $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
        $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

        $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
        $actual_mime_type        = getimagesize($temporary_path)['mime'];

        $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
        $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);

        return $file_extension_is_valid && $mime_type_is_valid;
    }

    function image_upload_detected()
    {
        $image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);

        return  $image_upload_detected;
    }

    function upload_error_detected()
    {
        $upload_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);

        return $upload_error_detected;
    }

    function resizeFile($temporary_image_path,$new_image_path)
    {
        $image_filename        = $_FILES['image']['name'];
        move_uploaded_file($temporary_image_path, $new_image_path);
        $actual_file_extension   = pathinfo($new_image_path, PATHINFO_EXTENSION);


        $len = strlen($image_filename) - strlen( $actual_file_extension) - 1;
        $actual_file_name_no_extension = substr( $image_filename,0,$len);

        $image = new Gumlet\ImageResize($new_image_path);
        $image->resizeToWidth(300);
        $new_image_path= file_upload_path($actual_file_name_no_extension.'_medium.'.$actual_file_extension);
        $image->save($new_image_path);

        $image->resizeToWidth(160);
        $new_image_path= file_upload_path($actual_file_name_no_extension.'_standard.'.$actual_file_extension);
        $image->save($new_image_path);

        $image->resizeToWidth(170);
        $new_image_path= file_upload_path($actual_file_name_no_extension.'_deluxe.'.$actual_file_extension);
        $image->save($new_image_path);

        $image->resizeToWidth(180);
        $new_image_path= file_upload_path($actual_file_name_no_extension.'_premium.'.$actual_file_extension);
        $image->save($new_image_path);


    }

    /**
      * Not used yet
    */
    function checkFileUpload()
    {
        if (image_upload_detected()) {
            $image_filename        = $_FILES['image']['name'];
            $temporary_image_path  = $_FILES['image']['tmp_name'];
            $new_image_path        = file_upload_path($image_filename);
            if (file_is_an_image($temporary_image_path, $new_image_path)) {
                resizeFile($temporary_image_path,$new_image_path);
            }

        }
    }

?>
