<?php include('inc.php');
$time = strtotime(date("Y-m-d H:i:s"));
############ Configuration ##############
$thumb_square_size 		= 350; //Thumbnails will be cropped to 200x200 
$f_width = 300;
$f_height = 100;
$wmax_image_size 		= 1000; //Maximum image size (height and width)
$hmax_image_size 		= 700; //Maximum image size (height and width)
$file_size 		= 200; //Maximum image filesize 
$image_prefix			= "upq_"; //Normal thumb Prefix
$destination_folder		= 'images/up_quotes/'; //upload directory ends with / (slash)
$jpeg_quality 			= 90; //jpeg quality
##########################################

//continue only if $_POST is set and it is a Ajax request
if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){

	// check $_FILES['ImageFile'] not empty
	if(!isset($_FILES['image_file']) || !is_uploaded_file($_FILES['image_file']['tmp_name'])){
			die('Image file is Missing!'); // output error when above checks fail.
	}
	
	
$_POST['desc'] = trim($_POST['desc']);
$_POST['desc'] = htmlspecialchars(trim($_POST['desc']));



	
	//uploaded file info we need to proceed
	$image_name = $_FILES['image_file']['name']; //file name
	$image_size = $_FILES['image_file']['size']; //file size
	$image_temp = $_FILES['image_file']['tmp_name']; //file temp

	$image_size_info 	= getimagesize($image_temp); //get image size
	
	if($image_size_info){
		$image_width 		= $image_size_info[0]; //image width
		$image_height 		= $image_size_info[1]; //image height
		$image_type 		= $image_size_info['mime']; //image type
		if ($image_width < $f_width){die("<p class=\"alert error\" style=\"font-size: 22px;\">Image too small,upload wider image</p>");}
		if ($image_height < $f_height){die("<p class=\"alert error\" style=\"font-size: 22px;\">Image too small,upload longer image</p>");}
	}else{
		die("Make sure image file is valid!");
	}


$desc = $wp->shorten_words($_POST['desc'],20);
$desc = $wp->code($desc,'up');
	
$desc = str_replace('-','_',$desc);
	
	//Get file extension and name to construct new file name 
		$image_info = pathinfo($image_name);
		$image_extension = strtolower($image_info["extension"]); //image extension
		$image_name_only = 'quote_'.$desc.'_'.$userDetails['id'].'_'.$time;//file name only, no extension
		
		//create a random name for new image (Eg: fileName_293749.jpg) ;
		$new_file_name = $image_name_only . '.' . $image_extension;
		
		//folder path to save resized images and thumbnails
		$image_save_folder 	= $destination_folder . $image_prefix . $new_file_name; 
		

	//switch statement below checks allowed image type 
	//as well as creates new image from given file 
	switch($image_type){
		case 'image/png':
			$image_res =  imagecreatefrompng($image_temp); break;
		case 'image/gif':
			 move_uploaded_file($image_temp,$image_save_folder);//first upload file
			 $image_res = $image_save_folder;
	 break;			
		case 'image/jpeg': case 'image/pjpeg':
			$image_res = imagecreatefromjpeg($image_temp); break;
		default:
			$image_res = false;
	}
	
	if($image_res){
		
		//call normal_resize_image() function to proportionally resize image
		if(normal_resize_image($image_res, $image_save_folder, $image_type, $wmax_image_size,$hmax_image_size,$image_width, $image_height, $jpeg_quality))
		{
			
$sth = $db->prepare("INSERT INTO `quotes` SET Quote=:q,AddedBy = '{$_SESSION['email']}',img=:t,name=:n,Date='$time',concn='{$_SESSION['email']}'");	

    $n = $userDetails['firstname'];
	
    $sth->bindValue (":q", $_POST['desc']);
    $sth->bindValue (":t", $image_save_folder);
	$sth->bindValue (":n", $n);
	
	if($sth->execute()){$lid = $db->lastInsertId();
	$qurl = $site_url_pre.$site_address.'/'.$wp->getquoteurl($lid);
	
	//rename the file to reflect the id
	//create a random name for new image (Eg: fileName_293749.jpg) ;
		$new_file_name2 = $image_name_only .'_'.$lid. '.' . $image_extension;
		//folder path to save resized images and thumbnails
		$image_save_folder2 = $destination_folder . $image_prefix . $new_file_name2; 
	rename($image_save_folder,$image_save_folder2);
	
	$sth2 = $db->prepare("UPDATE quotes SET img=:img WHERE ID='$lid'");
			$sth2->bindValue (":img", $image_save_folder2);
			$sth2->execute();
	
	/* We have succesfully resized and created thumbnail image
			We can now output image to user's browser or store information in the database*/
			echo '<div align="center">';
			echo 'Your Image  has been Successfully Added<br/> You can view it <a style="color: #fff;background: #13779D;" href="'.$qurl.'">HERE</a>';
			echo '</div>';
	}
		}
		
			if ($image_type != 'image/gif')
	{
		imagedestroy($image_res); //freeup memory
	}
	}
}

####  This function will proportionally resize image ##### 
function normal_resize_image($source, $destination, $image_type, $wmax_size, $hmax_size,$image_width, $image_height, $quality){
	
$gr = new gifresizer;	//New Instance Of GIFResizer
$gr->temp_dir = "images/gif_frames"; //Used for extracting GIF Animation Frames
	if($image_width <= 0 || $image_height <= 0){return false;} //return false if nothing to resize
	//do not resize if image is smaller than max size
	if($image_width <= $wmax_size && $image_height <= $hmax_size){
		
		if ($image_type == 'image/gif')
	{
    $gr->resize($source,$destination,$image_width,$image_width); //saving with normal width
	//Didn't use "save_image fucntion" here because "$gr->resize" does it better
	return true;
	}
	else 
	{
		if(save_image($source, $destination, $image_type, $quality)){
				return true;
			}
	}
		
		
		
		
	}
	
	//Construct a proportional size of new image
	$image_scale	= min($wmax_size/$image_width, $hmax_size/$image_height);
	$new_width		= ceil($image_scale * $image_width);
	$new_height		= ceil($image_scale * $image_height);
	
	$new_canvas		= imagecreatetruecolor( $new_width, $new_height ); //Create a new true color image
	
	if ($image_type == 'image/gif')
	{	

     move_uploaded_file( $image_temp,$destination);//first upload file

	$gr->resize($destination,$destination,$new_width,$new_height);
	//Didn't use "save_image fucntion" here because "$gr->resize" does it better
	}
	else 
	{
	
		//Copy and resize part of an image with resampling
			if(imagecopyresampled($new_canvas, $source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height)){
				save_image($new_canvas, $destination, $image_type, $quality); //save resized image
			}
	}

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