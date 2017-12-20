<?php $stime = microtime();
 
ob_start();
session_start();

$site_address = "localhost/quo";

$defcap = 'Leichester,Foxconn,Sharp,Apple,Facebook,Jose Morinho,Donald Trump,Hillary clinton,Twitter,Facebook,War,Jihadist,ISIS'; //Default Capsule Value
 
 $site = array();
 $site['name'] = "Quotehood";
  $site['trademark'] = 'QuoteHood';

  $site['image_default'] = 'images/men.png';
  
  $site['upq_default'] = '[[Image Upload]]';
  
$site['basepath'] = dirname(__FILE__); 
$site['cfq'] = '1577,94,27050,18025,75041,17642'; //Chosen frame quotes 

 $folder = array();
 
  $folder['QuoteBg'] = "images/use"; //where bg images are stored, to be used during TExttoImage conversion for Quote
  
  $folder['QuoteProcess'] = "images/processed"; // Folder for processing images
  
$folder['QuoteTouse'] = "images/touse"; // Folder for materials used

$folder['QuoteImages'] = "images/quoteimages"; // Folder for containing final processed images
 

//This states the id limit of quotes in database
//This is just a random number that falls within limit
 $limit_Quote_id = 125754;  
 
//This states the limit of words limit url
//Yet to be site wide
$limit_url_words = 5;  



//This states type of url processor
$site_url_pre = 'http://';


//THis is to state the working environment of this software
$site_env = 'development';


//Name of cache folder path
$cache_folder = 'cache';



//Replacement words for quotes without author
$without_author = 'anonymous';


//Parameters for the SimpleImage Quote Conversion
define("ALIGN_LEFT", "left");
define("ALIGN_CENTER", "center");
define("ALIGN_RIGHT", "right");
define("VALIGN_TOP", "top");
define("VALIGN_MIDDLE", "middle");
define("VALIGN_BOTTOM", "bottom");
define("ORIENTATION_TOP", "top");
define("ORIENTATION_BOTTOM", "bottom");
define("ORIENTATION_LEFT", "left");
define("ORIENTATION_RIGHT", "right");
define("ORIENTATION_CENTER", "center");


//Database variables
$site_db_type = 'mysql'; //the database type
$site_db_host = '127.0.0.1'; //Database Host
$site_db_name = 'quoto'; //Database name
$site_db_user  = 'root'; //Database user
$site_db_password = ''; //Database Password

//The Details for Nav URL
$navUrl = array();
$navUrl['Home'] = "$site_address";
$navUrl['Home_Name'] = 'Home';
$navUrl['Home_URI'] = 'index.html';
$navUrl['Home_Sub'] = '';
$navUrl['RQ'] ="$site_address/".'%quoteurl%';
$navUrl['RQ_Name'] = 'Random Quote';
$navUrl['RQ_Sub'] = '';
$navUrl['RQ_URI'] = 'quotes.html';
if (!empty($_SESSION['email'])){ //This is an informal process of checking if user is logged in
$navUrl['NTF'] ="$site_address/notifications.html";
$navUrl['NTF_Name'] = 'Notifications';
$navUrl['NTF_URI'] = 'notifications.html';
$navUrl['NTF_Sub'] = '';}
// $navUrl['AL'] = "$site_address/author-list-1";
// $navUrl['AL_Name'] = 'Authors&nbsp;&nbsp;&nbsp;';
// $navUrl['AL_Sub'] = 'List Of Authors';
// $navUrl['AL_URI'] = 'authors.html';
// $navUrl['CL'] = "$site_address/category-list-1";
// $navUrl['CL_Name'] = 'Categories';
// $navUrl['CL_Sub'] = 'List of Categories';
// $navUrl['CL_URI'] = 'categories.html';
// $navUrl['AU'] = "$site_address/about-us";
// $navUrl['AU_Name'] = 'About';
// $navUrl['AU_Sub'] = 'Know Us';
// $navUrl['AU_URI'] = 'about_us.html';
// $navUrl['CU'] = "$site_address/contact-us";
// $navUrl['CU_Name'] = 'Contact';
// $navUrl['CU_Sub'] = 'MAke Enquiries';
// $navUrl['CU_URI'] = 'contact_us.html';

/** Check your file system path! **/
require_once('libs/send/classes/Email.class.php');
require_once('libs/send/classes/Courier.class.php');	
require_once('libs/class.user.php');	
require_once 'libs/pdo_pagination.php';
require_once 'libs/simpleimage.php'; /** Class(es) for Making Quote Pictures ***/
require_once 'libs/m/mobile_detect.php'; /** Class(es) for Mobile detection and data ***/
require_once 'libs/imdet/imdet.php'; /** Class(es) for Mobile detection and data ***/
require_once 'libs/major_class.php';
require_once 'libs/resizer.php'; //for resizing gifs


 
 //To remove 'quote' string from SEarch 
 if (!empty($_GET['q'])){
	 $_GET['q'] = str_replace('quotes','',$_GET['q']);
	 $_GET['q'] = str_replace('quote','',$_GET['q']);
	 $_GET['q'] = str_replace('-',' ',$_GET['q']);
	 }
 
//Calling the connection
$pagination = new PDO_Pagination();

//lets call major_class:first here
$wp = new first;

$imdet = new imdet();
	
	
$db = $pagination->connection('','set');
$user = new user($db);
$user->capsule($defcap);

$gr = new gifresizer;	//New Instance Of GIFResizer
$gr->temp_dir = "frames"; //Used for extracting GIF Animation Frames

if (!empty($_SESSION['email'])){
	
	if ($user->checkUser($_SESSION['email']) == false){unset($_SESSION['email']); header("location: http://$site_address?log=out");}
	
}

if($wp->user->is_logged_in() == false){
	
	if (!empty($_GET['m']) && $_GET['m'] == 'all'){$_SESSION['error'] = 'You have to be logged to view that file'; $_SESSION['refer_back'] = $_SERVER['REQUEST_URI']; header("location: http://$site_address/login.html");}
	
			 $user->autoLogin();
	
	}
	
	
	if (!empty($_SESSION['email'])){
	
	if ($user->checkUser($_SESSION['email']) == false){unset($_SESSION['email']); header("location: http://$site_address?log=out");}
	$userDetails = $user->get_user($_SESSION['email']);
	}

	if($wp->user->is_logged_in() == true && substr($_SERVER['PHP_SELF'],-15,15) != '/interests.html' && empty($_GET['log'])){
	
	if (empty(trim($userDetails['interests']))){$_SESSION['error'] = 'Please Update Your Interests First'; $_SESSION['refer_back'] = $_SERVER['REQUEST_URI']; header("location: http://$site_address/interests.html"); exit;}
	}
	
	
//To turn on caching
if ($wp->user->is_logged_in() == true){$cache_on = false;}
else {$cache_on = false;}

/** To check if file is cached (depends on cache on)**/
if ($cache_on){$wp->cache_in($stime);} 



		     
if (!empty($_GET['qid'])){
//To collect all datas of a quote via id
$QuoteData = array();
$QuoteData = $wp->getquote();
}

//echo $wp->decrypt($_COOKIE['fav']); exit;

//To download image $_GET[a] = dl
If (!empty($_GET['log']) && $_GET['log'] == 'out'){$user->logout();}

//To download image $_GET[a] = dl
If (!empty($_GET['a'])){if ($_GET['a'] == 'dl'){$wp->InitiateDownload($_GET['file'],'jpg');}}

If (!empty($_GET['fav'])){$wp->fav();}

If (!empty($_GET['save'])){$wp->save();}

//State File name,then redirect to url as formatted by header("location ...
$wp->rdr('index.html','http://'.$site_address);

//To redirect fake quote url
$wp->quote_rdr();

$detect = new Mobile_Detect;

$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer'); 

if ($deviceType == 'tablet'){$deviceType = 'phone';}
//To collect data for caching
ob_start();