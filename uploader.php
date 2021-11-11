<?php
# Params
$domain_url = 'https://i.ant0ine64.fr/';
$lengthofstring = 4; //Length of the "uid" of the image file name
$key = '{{UPLOAD_KEY}}';
$storage_path = '/var/www/uploads/';

function RandomString($length) {
    $keys = array_merge(range(0,9), range('a', 'z'));
    $uid = '';
    for($i=0; $i < $length; $i++) {
        $uid .= $keys[mt_rand(0, count($keys) - 1)];
    }
    return $uid;
}

function DatedFileName($length) {
    global $storage_path;
    $uid = RandomString($length);
    $dir = date('Y/m/d/');
    mkdir($storage_path . $dir, 0755, true);
    $name = $dir . $uid;
    return $name;
}

if (!isset($_POST['key']) || $_POST['key'] != $key) {
    http_response_code(403);
    die('Error, wrong key value, info at contact@ant0ine64.fr');
}

$target_file = $_FILES["image"]["name"];
$fileType = pathinfo($target_file, PATHINFO_EXTENSION);

$image_extensions = ["png", "jpeg", "jpg", "gif"];
if (in_array(strtolower($fileType), $image_extensions)) {
    //give a date and "unique" tag if this is an image (suposing it's a screenshot)
    $filename = DatedFileName($lengthofstring);
}
 else {
    //leave the default filename
    $filename = pathinfo($target_file,PATHINFO_FILENAME);

    //eventualy add a index if exist already
    while (file_exists($filename . "." . $fileType)) {
        if(!isset($index)) {
            $original_filename = $filename;
            $index = 1;
        }
        $filename = $original_filename . $index++;
    }
}

if (move_uploaded_file($_FILES["image"]["tmp_name"], $storage_path . $filename.'.'.$fileType)) {
    echo $domain_url.$filename.'.'.$fileType;
} else {
	http_response_code(500);
    echo 'File upload failed : File too big ? Permissions error ?';
}
?>
