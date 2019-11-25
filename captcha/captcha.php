<?php 

    /**
     * This function will retunr a random string of 6 characters in length. 
     * Random characters get from the string $characters.
     */
     
    function getRandomString() 
    { 
        $characters = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ'; 
        $randomString = ''; 
    
        for ($i = 0; $i < 6; $i++) { 
            $index = rand(0, strlen($characters) - 1); 
            $randomString .= $characters[$index]; 
        } 
        return $randomString; 
    } 

    session_start();
    
    header("Content-Type: image/png");
    $im = imagecreate(100, 20);
    $background_color = imagecolorallocate($im, 204, 229, 255);
    $text_color = imagecolorallocate($im, 0, 0, 102);
    $captcha = getRandomString();
    $_SESSION['captcha'] =  $captcha;
    imagestring($im, 5, 20, 2,$captcha, $text_color);
    imagepng($im);
    imagedestroy($im);

?> 