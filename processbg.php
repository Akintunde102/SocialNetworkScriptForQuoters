<?php include('inc.php');
$time = strtotime(date("Y-m-d H:i:s"));
############ Configuration ##############
$thumb_square_size 		= 350; //Thumbnails will be cropped to 200x200 
$f_width = 400;
$f_height = 500;
$wmax_image_size 		= 1000; //Maximum image size (height and width)
$hmax_image_size 		= 700; //Maximum image size (height and width)
$destination_folder		= 'images/up_bg/'; //upload directory ends with / (slash)
$jpeg_quality 			= 90; //jpeg quality
##########################################

//continue only if $_POST is set and it is a Ajax request
if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	// check $_FILES['ImageFile'] not empty
	if(!isset($_FILES['image_file']) || !is_uploaded_file($_FILES['image_file']['tmp_name'])){
			die('Image file is Missing!'); // output error when above checks fail.
	}
	
	//uploaded file info we need to proceed
	$image_name = $_FILES['image_file']['name']; //file name
	$image_size = $_FILES['image_file']['size']; //file size
	$image_temp = $_FILES['image_file']['tmp_name']; //file temp

	$image_size_info 	= getimagesize($image_temp); //get image size
	
	if($image_size_info){
		$image_width 		= $image_size_info[0]; //image width
		$image_height 		= $image_size_info[1]; //image height
		$image_type 		= $image_size_info['mime']; //image type
		if ($image_width < $f_width){die("<p class=\"alert error\" style=\"font-size: 24px;\">Image too small,upload wider image</p>");}
		if ($image_height < $f_height){die("<p class=\"alert error\" style=\"font-size: 24px;\">Image too small,upload longer image</p>");}
	}else{
		die("Make sure image file is valid!");
	}

	//switch statement below checks allowed image type 
	//as well as creates new image from given file 
	switch($image_type){
		case 'image/png':
			$image_res =  imagecreatefrompng($image_temp); break;
		case 'image/gif':
			$image_res =  imagecreatefromgif($image_temp); break;			
		case 'image/jpeg': case 'image/pjpeg':
			$image_res = imagecreatefromjpeg($image_temp); break;
		default:
			$image_res = false;
	}

	if($image_res){
		//Get file extension and name to construct new file name 
		$image_info = pathinfo($image_name);
		$image_extension = strtolower($image_info["extension"]); //image extension
		$image_name_only = $userDetails['id'].'_'.$time.rand(1,754);//file name only, no extension
		
		//create a random name for new image (Eg: fileName_293749.jpg) ;
		$new_file_name = $image_name_only . '.' . $image_extension;
	

        $_SESSION['n_f_n'] = $image_name_only;
		
		//folder path to save resized images and thumbnails
		$image_save_folder 	= $destination_folder . $new_file_name; 
		
		//call normal_resize_image() function to proportionally resize image
		if(normal_resize_image($image_res, $image_save_folder, $image_type, $wmax_image_size,$hmax_image_size,$image_width, $image_height, $jpeg_quality))
		{
			$wp->TextToImage($_POST['I'],$_POST['Q'],$_POST['N'],$_POST['C'],$image_save_folder);
		}
		
		imagedestroy($image_res); //freeup memory
	}
}

####  This function will proportionally resize image ##### 
function normal_resize_image($source, $destination, $image_type, $wmax_size, $hmax_size,$image_width, $image_height, $quality){
	
	if($image_width <= 0 || $image_height <= 0){return false;} //return false if nothing to resize
	//do not resize if image is smaller than max size
	
		save_image($source, $destination, $image_type, $quality); //save resized image
	    return true;
}


##### Saves image resource to file ##### 
function save_image($source, $destination, $image_type, $quality){
	//Before saving.let's rename the files
	switch(strtolower($image_type)){//determine mime type
		case 'image/png': 
			imagepng($source, $destination); return true; //save png file
			break;
		case 'image/gif': 
			imagegif($source, $destination); return true; //save gif file
			break;          
		case 'image/jpeg': case 'image/pjpeg': 
			imagejpeg($source, $destination, $quality); return true; //save jpeg file
			break;
		default: return false;
	}
}