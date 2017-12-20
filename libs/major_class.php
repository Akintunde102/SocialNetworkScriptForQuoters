<?php
class first{

Public function __construct(){
	
Global $limit_Quote_id,$limit_url_words,$site_address,$site_url_pre,$site_env,$cache_folder,$without_author,$folder,$site,$navUrl,$defcap;
$this->limit_Quote_id = $limit_Quote_id;
$this->limit_url_words = $limit_url_words;
$this->site_address = $site_address;
$this->siteurlpre = $site_url_pre;
$this->site_env = $site_env;
$this->site_name = $site['name'];
$this->cache_folder = $cache_folder;
$this->without_author = $without_author;
$this->qbg = $folder['QuoteBg'];
$this->qproc = $folder['QuoteProcess'];
$this->qprod = $folder['QuoteImages'];
$this->qtouse = $folder['QuoteTouse'];
$this->sbp = $site['basepath'];
$this->cfq = $site['cfq'];
$this->NavUrlDetails = $navUrl;
$this->ip = $_SERVER['REMOTE_ADDR'];
$this->defcap = $defcap;

	$pagination = new PDO_Pagination();
	
	$this->pagination = $pagination;
	
	
	$imdet = new imdet();
	
	$this->imdet = $imdet;
	
	$db = $this->pagination->connection('','set');

    $this->db = $db;

	$user = new user($db);
	
	$this->user = $user;
	
if (!empty($_SESSION['email'])){$this->user_d= $this->user->get_user($_SESSION['email']);

	 $this->user_email = strtolower(trim($this->user_d['email']));
}
	$courier = new courier();
	
	$this->courier = $courier;
	
	$image = new SimpleImage();
	
	$this->imgconv = $image;
	
					$detect = new Mobile_Detect;

$this->deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer'); 

					
if (!empty($_GET['type']) && !empty($_GET['rate'])){ $_GET['type'] = $this->type; $_GET['rate'] = $this->rate;}

if (!empty($_GET['tags'])){ $this->tag = $_GET['tags'];}
if (!empty($_GET['fav'])){ $this->fav = $_GET['fav'];}
if (!empty($_GET['save'])){ $this->save = $_GET['save'];}
if (!empty($_GET['qid'])){$this->qid = $_GET['qid'];}
if (!empty($_GET['bgi'])){$this->bgi = $_GET['bgi'];}

$this->uniqueHash = session_id().strtotime(date("Y-m-d H:i:s"));
}

   Public Function InitiateDownload($file,$ext){
$fp = fopen($file, "r");
header("Content-Type:application/$ext");
	header("Content-Disposition:attachment;
	filename=$file");
	fpassthru($fp);}

	Public Function removehtml($in){
		
	$in = str_replace('<u>','',$in);
	$in = str_replace('</u>','',$in);
	$in = str_replace('<strong>','',$in);
	$in = str_replace('</strong>','',$in);
	$in = str_replace('<em>','',$in);
	$in = str_replace('</em>','',$in);
	$in = str_replace('&nbsp;',' ',$in);
	$in = str_replace('&amp;','&',$in);
	$in = str_replace('#','',$in);
	
	$in = str_replace('<br />','
',$in);
   $in = str_replace('<br>','
   
',$in);

return $in;	
		
	}
	
	
	Public Function TextToImage($id = 0,$quotein=0,$namein=0,$c='notset',$upg='none'){
			if ($id == 0){$quote_ret = $this->getquote();  $BgImage = $_GET['bgi'].'.jpg';}
			else if ($upg != 'none'){$quote_ret = array(); $quote_ret['Quote'] = $quotein; $quote_ret['Name'] = $namein;}
			else {
				if ($quotein == 0 || $namein == 0){$quote_ret = $this->getquote($id);}
                else {$quote_ret = array(); $quote_ret['Quote'] = $quotein; $quote_ret['Name'] = $namein;}	
				
			if (empty($_SESSION['bg_arr'])){
				if ( !( $handle = opendir($this->qbg) ) ) die( "Cannot open the directory." );
				$_SESSION['bg_arr'] = array();
 while ( $file = readdir($handle) ) {if ( $file != "." && $file != ".." ){$_SESSION['bg_arr'][] = $file;} }
 closedir($handle);
}

$BgArr = array_rand($_SESSION['bg_arr'],1);
$BgImage = $_SESSION['bg_arr'][$BgArr];}
						
	$quote_ret['Quote'] = $this->removehtml($quote_ret['Quote']);
	
			if (strlen($quote_ret['Quote']) > 400){ 
						$nSen = 1;
					$quote_ret['Quote'] = substr($quote_ret['Quote'],0,400);
					
			while (strlen($this->shorten_sentence($quote_ret['Quote'],$nSen)) < 400){$nSen++;}
		//echo $nSen;
			$quote_ret['Quote'] = htmlspecialchars($quote_ret['Quote'].'....
[SHORTENED]'); }
$text = html_entity_decode('"'.$quote_ret['Quote'].'"

......'.$quote_ret['Name']);

$quote_c = html_entity_decode($quote_ret['Quote']);
$name_c = html_entity_decode($quote_ret['Name']);

	

	$quote_c = $this->shorten_words($quote_ret['Quote'],20);
	$name_c = $name_c; 
	$quote_c = $this->code($quote_c,'up');
	
	    //to state gbi value
		if ($upg != 'none'){$bGi = $_SESSION['n_f_n'];}
		else {if (!empty($_GET['bgi'])){$bGi = $_GET['bgi'];} else {$bGi = $BgImage;}}
	

	if ($id != 0){$uid = $id;}
else {$uid = $_GET['qid'];}
	//To differentiate converted image from auto-generated images
	if (empty($_GET['c']) && empty($BgArr)){
		$ImageName = $this->qprod.'/'.$name_c.'_quotes__'.$quote_c.'_'.$bGi.'__convert_'.$uid.'.jpg';
		}
	else {$ImageName = $this->qprod.'/'.$name_c.'_quotes__'.$quote_c.'_'.$uid.'.jpg';}
	
	if (!empty($_GET['c'])){$ImageName = $this->qprod.'/'.$name_c.'_quotes__'.$quote_c.'_'.$uid.'.jpg';}
	
	if ($upg!='none' && $c == 'b'){$ImageName = $this->qprod.'/'.$name_c.'_quotes__'.$quote_c.'_'.$uid.'.jpg';}	
	

	//$ImageName = $this->qprod.'/'.$name_c.'_quotes__'.$quote_c.'_'.$this->uniqueHash.'.jpg';
	$ImageName = str_replace('-','_',$ImageName);
	$ImageName = str_replace(' ','_',$ImageName);
	$ImageName = str_replace(':','__',$ImageName);
	
	//This processes file only if image does not exist and it's autogenerated
	//Does not work for changing background
	if (!file_exists($ImageName) || !empty($_GET['c']) || $upg != 'none'){		
$text_count = strlen($text);
			$text_size_const = 3984 + ($text_count * 10);
			$line_height_const = 0.68 + ($text_count / 1300); 
	
			//echo 'b<br/>'.$text_count.'b<br/>'.$text_size_const.'b<br/>';
		//To restrict text size from being too big when texts are short
		 if ($text_count < 50 ){$text_size_const = 2500 - ($text_count * 30); 
			 $line_height_const = 0.68 + ($text_count / 1300);
		 }
		 
		else if ($text_count < 70 ){$text_size_const = 4500 - ($text_count * 30); 
			$line_height_const = 0.68 + ($text_count / 1300);
		}
			
			//To restrict text size from being too big when texts are short
	else	if ($text_count <= 150 && $text_count > 48 ){$text_size_const = 4500 - ($text_count * 10); 
				$line_height_const = 0.68 + ($text_count / 1300);
			 }
			 
			 else if ($text_count <= 48 && $text_count > 98){$text_size_const = 4500 - ($text_count * 35); 
			$line_height_const = 0.68 + ($text_count / 1300);}
			
		
		
			

		$size = $text_size_const / $text_count;
		$line_height = $size/$line_height_const ;
		
		if ($text_size_const >= 8500){$text_size_const = $text_size_const + 1000; 
			$line_height_const = 0.68 + ($text_count / 1000); $size = $text_size_const / $text_count; 
		$line_height = ($size/$line_height_const) * 1.9;}
		
		
		//The below is for debugging
		
	//		echo '<br/>';
//echo $text_count;
//echo '<br/>';
//echo $line_height_const;
//echo '<br/>'.$line_height;

  //This line automatically creates Processing Folder incase it doesn't exist
if (!is_dir($this->qproc)) {
    mkdir($this->qproc);
}

try {
    //
    // WARNING: This will create a lot of images in the /processed folder
    //

    $imagewidth = 600;
 
 if ($upg != 'none'){// Best fit
    $this->imgconv->load($upg)->resize(700, 500)->save($this->qproc.'/'.$this->uniqueHash.'1-fit.jpg');
	}
	else {// Best fit
    $this->imgconv->load($this->qbg.'/'.$BgImage)->resize(700, 500)->save($this->qproc.'/'.$this->uniqueHash.'1-fit.jpg');
	}
	
	// Overlay
   $this->imgconv->load($this->qproc.'/'.$this->uniqueHash.'1-fit.jpg')->overlay($this->qtouse.'/overlay.png', 'top right', .9,-30,18)->save($this->qproc.'/'.$this->uniqueHash.'overlaid.jpg');
	
   // Auto-orient
    //$this->imgconv->load($this->qproc.'/'.$this->uniqueHash.'overlaid.jpg')->auto_orient()->save($this->qproc.'/'.$this->uniqueHash.'auto-orient.jpg');
	
	// Colorize //$this->imgconv->load($this->qproc.'/'.$this->uniqueHash.'auto-orient.jpg')->brightness(0)->save($this->qproc.'/'.$this->uniqueHash.'colorize.jpg');

	
	$angle = 0;
	$left = 40;
	$top = -75;
  
   // Text
   $this->imgconv->load($this->qproc.'/'.$this->uniqueHash.'overlaid.jpg')->imagettftextboxopt($size,$angle,$left,$top,'#fff',$this->sbp.'/assets/fonts/Hallo sans black.otf',$text,$this->sbp.'/'.$ImageName,$imagewidth,500,$line_height);

   //To destroy and delete all processing file
   unlink($this->qproc.'/'.$this->uniqueHash.'1-fit.jpg');
   unlink($this->qproc.'/'.$this->uniqueHash.'overlaid.jpg');
  // unlink($this->qproc.'/'.$this->uniqueHash.'auto-orient.jpg');
   //unlink($this->qproc.'/'.$this->uniqueHash.'colorize.jpg');
   
   //echo $text_count;
} catch (Exception $e) {
    echo '<span style="color: red;">'.$e->getMessage().'</span>';
}

	//To use only for cron jobs
//As it clears headers,post and others
/* 	
	$details = array();
	
	$details['filename'] = $ImageName;
	$details['title'] = $quote_ret['Name'].' quote';
	$details['author'] = $quote_ret['Name'];
	$details['authorsposition'] = $quote_ret['Name'];
	$details['caption'] = $quote_ret['Name'].'-'.$quote_ret['Quote'];
	$details['captionwriter'] = 'Quotehood.com';
	$details['keywords'] = "quotes\nimagequote\image wallpaper\nqoutes\ninspirational quote\ninspirational qoute";
	$details['copyrightstatus'] = 'Copyrighted Work';
	$details['copyrightnotice'] = 'Copyright (c) Quotehood.com 2016';
	$details['ownerurl'] = 'http://quotehood.com';
	$details['category'] = 'quotes';
	$details['headline'] = $quote_ret['Quote'];
	$details['supplementalcategories'] = "sayings\nquotations";
	$details['date'] = '2016-03-29';
	$details['credit'] = $quote_ret['Name'].' Quotehood';
	$details['source'] = 'Quotehood.com';
	$this->imdet->edit($details);
 */
	}	
	
	
	 
if ($id == 0 || ($upg != 'none' && !empty($_GET['c']))){ //that condition for when upg is set and $_GET['c'] is also set(allowing user to custom change via own upload)
	
	//to index the convesion
	//only works when logged as email is needed for index
	if ($this->user->is_logged_in()){
		
	if (empty($_GET['c'])){	$this->logconvert();
	 //to add to concern list
	$this->concn('conv',$this->qid);}
	}
	echo '<div class="col-md-10">';
						
						
	if (!empty($_GET['c'])){echo '<p  class="alert" style="border: 1px solid #DDD;color: #73879C;
    background: #fff;
    font-family: "Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;
    font-weight: 600;text-decoration:none;"><a href="http://'.$this->site_address.'/quotes.html?qid='.$_GET['qid'].'">Image Background has been successfully changed on the timeline</a></p>';}
	else{
    echo '<img title="ARE YOU HAPPY NOW?" alt="Image Quote Backgrounds" data-toggle="tooltip" src="http://'.$this->site_address.'/'.$ImageName.'"/>';
	
	echo '<div>';
	echo '<a class="btn btn-primary" style="margin:2px 1px 4px 1px;" href="convert.html?qid='.$_GET['qid'].'"><i class="glyphicon glyphicon-picture" ></i>&nbsp;Change Background</a><a style="margin:2px 1px 4px 1px;" class="btn btn-primary" href="convert.html?file='.$ImageName.'&a=dl"><i class="glyphicon glyphicon-download-alt" ></i>&nbsp;Download Image</a><a style="margin:2px 1px 4px 1px;" class="btn btn-primary" href="http://'.$this->site_address.'/author/'.$name_c.'-quotes"><i class="glyphicon glyphicon-user" ></i>&nbsp;Other Quotes By '.$quote_ret['Name'].' </a><a class="btn btn-default" style="margin:2px 1px 4px 1px;" href="http://'.$this->site_address.'/quotes.html?qid='.$_GET['qid'].'"><i class="glyphicon glyphicon-eye"></i>&nbsp;View/Comment on Quote</a>&nbsp;';
   echo '</div>'; 
	}
   echo '</div>';
}   

else if ($upg != 'none'){
	  if (empty($_GET['c']) && $c != 'b'){
	 $qrq = str_replace('','',$quote_ret['Quote']); $ttl=$quote_ret['Name'].' quote'; echo  '<img class="lazy" title="'.$ttl.'" alt="'.$ttl.'|'.$qrq.'" data-toggle="tooltip" data-src="http://'.$this->site_address.'/'.$ImageName.'"  src="http://'.$this->site_address.'/'.$ImageName.'"/>';}

if (!empty($_GET['c']) || $c == 'b'){
echo '<a class="btn btn-default" style="margin:2px 1px 4px 1px;" href="http://'.$this->site_address.'/quotes.html?qid='.$id.'">Image Background has been successfully changed on the timeline</a>';
}
else {
echo '<a class="btn btn-default" style="margin:2px 1px 4px 1px;" href="http://'.$this->site_address.'/'.$ImageName.'"><i class="glyphicon glyphicon-eye"></i>Download Image</a>';}
}

else {$qrq = str_replace('','',$quote_ret['Quote']); $ttl=$quote_ret['Name'].' quote'; return  '<img class="lazy" title="'.$ttl.'" alt="'.$ttl.'|'.$qrq.'" data-toggle="tooltip" data-src="http://'.$this->site_address.'/'.$ImageName.'"  src="http://'.$this->site_address.'/'.$ImageName.'"/>';}

	}	
	
Public Function SearchAutocomplete(){
if($_POST){
$q=$_POST['q'];
echo $this->dbRetrieve('searchindex','string',$q);
}
}
		
		
		Public Function dbRetrieve($table,$column,$value){
					   $this->pagination->setSQL( "SELECT * FROM $table WHERE $column like '%$value%'" );
		 		 $results = $this->pagination->getSQL();
			 if( $this->pagination->getTotalOfResults() > 0    ) {
				 foreach( $results as $r ) {
					$ret = trim($r[$column]); 
				 return $ret;}
					}
		}




	Public Function checkIndex($string){
			$this->pagination->setSQL( "SELECT * FROM searchindex where string = '$string'" );
			 $this->pagination->setPaginator( 'page' );
			 $results = $this->pagination->getSQL();
			if ($this->pagination->getTotalOfResults() > 0 ){
				 foreach($results as $r ) {	
					 $times = trim($r['times']);
				if (empty($times)){$times = 0;}
				$new_times = $times + 1;
				$db = $this->pagination->connection('','set');
			$sth = $db->prepare("UPDATE searchindex SET times=:tm WHERE string='$string'");
			$sth->bindValue (":tm", $new_times);
			$sth->execute();
				 }
			 }
			 return $this->pagination->getTotalOfResults();
	}
	
	
	Public function sq(){
		
		$this->pagination->setSQL( "SELECT * FROM quotations" );
			 $this->pagination->setPaginator( 'page' );
			 $results = $this->pagination->getSQL('All');
				 foreach($results as $r ) {
					 $Location = str_replace('<i>','',$r['Location']);
					 $Location = str_replace('</i>','',$Location);
					 
					  $Quotation = str_replace('<br>',' ',$r['QuotationText']);
					 
				$db = $this->pagination->connection('','set');
				 $sth = $db->prepare("UPDATE quotations SET Location=:lc,QuotationText = :q WHERE QuotationID='{$r['QuotationID']}'");
			$sth->bindValue (":lc", $Location);
			$sth->bindValue (":q", $Quotation);
			$sth->execute();
				 }
			 
			 return $this->pagination->getTotalOfResults();
		
		
		
		
	}
	
	
Public function encrypt($string) {
   // you may change these values to your own
    $secret_key = 'my_simple_secret_key';
    $secret_iv = 'my_simple_secret_iv';
 
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
 

    $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    
 
    return $output;
}

Public function decrypt($string) {
    $secret_key = 'my_simple_secret_key';
    $secret_iv = 'my_simple_secret_iv';
 
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
 
  
    $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
   
 
    return $output;
}
	
	Public function setcookie($CookieName,$value){
			  setcookie($CookieName,$value);
	}
	
	Public function getcookie($name){
	if (!empty($_COOKIE[$name])){
	        $mr_cook = $_COOKIE[$name];
        return $mr_cook;
        }
	}
	
	
Public function ntf_sentence($id){
	$ret = array();
						$this->pagination->setSQL( "SELECT * FROM notif WHERE `id` = '$id' ORDER by status DESC,stime DESC" );
		  $results = $this->pagination->getSQL('All');		
			if( $this->pagination->checkRes() > 0    ) {
				foreach( $results as $r ) {
					$user_d= $this->user->get_user($r['nfrom']);
					$from = ucwords(strtolower(trim($user_d['firstname'])));
					$user_tod= $this->user->get_user($r['nto']);
					$ret['to'] = ucwords(strtolower(trim($user_tod['firstname'])));
				$from_url = "<a href=\"http://".$this->site_address."/myquotes/".$user_d['id']."\"><img class=\"round\" src=\"http://".$this->site_address."/".$user_d['image']."\" \>".$from."</a>";
					$time = $this->fDate($r['stime']);
					$urlID = $this->getquoteurl($r['qid'],$this->limit_url_words);
					$quote_ret = $this->getquote($r['qid']);
					$quote_short = $this->shorten_words($quote_ret['Quote'],20)."......"; 
					$quote_short = html_entity_decode($quote_short);
			if(!empty(trim($quote_ret['AddedBy']))) { $abD = $this->user->get_user(trim(strtolower($quote_ret['AddedBy'])));
			  $uUrl = 'http://'.$site_address.'/myquotes/'.$abD['id']; 
			 $abD['image'] =  trim($abD['image']);
			 $user = '<img class="round" src="http://'.$this->site_address.'/'.$abD['image'].'" alt="">'; 
			  //To use User Glyphicon
			}
		     else {$uUrl = 'http://'.$this->site_address.'/author/'.$quote_ret['Name'].'-quotes'; $user = '<i class="glyphicon glyphicon-th blue" style="color: #000;"></i>';   //Change Glyphicon to 'th' to differentiate 
			}
			
			if (trim($quote_ret['Name']) == 'anonymous'){$uUrl = 'http://'.$this->site_address.'/author/'.$quote_ret['Name'].'-quotes'; $user = '<i class="glyphicon glyphicon-th blue" style="color: #000;"></i>';   //Change Glyphicon to 'th' to differentiate 
			}
			
					$quote_preview = '<div class="timeline-item">
                          <h3 class="timeline-header">'.$user.'<a href="'.$uUrl.'">'.$quote_ret['Name'].'</a></h3>
                          <div class="timeline-body">
                          '.$quote_short.'
                          </div>
                          <div class="timeline-footer">
                           <a class="btn btn-primary btn-xs" href="http://'.$site_address.'/'.$urlID.'">READ MORE</a>
                          </div>
                        </div>';
					
						switch ( $r['type'] ) {
							 case "comm":
							 $sentence = "$from_url commented on a <a href=\"http://$site_address/$urlID\">quote</a> you know($time)";
							 $sentence_txt = "$from commented on a quote</a> you know
							 
							 Click the link below to view on Quotehood
							 
							 http://$site_address/$urlID
							 ";
							 $ret['title'] = "$from commented on a quote you know";
							 break;
							 case "conv":
							 $sentence = "$from_url converted a <a href=\"http://$site_address/$urlID\">quote</a> you know($time)";
							 $sentence_txt = "$from converted a quote</a> you know
							 
							 Click the link below to view on Quotehood
							 
							 http://$site_address/$urlID
							 ";
							 $ret['title'] = "$from converted a quote you know";
							 break;
							 case "fav":
							 $sentence = "$from_url favoured a <a href=\"http://$site_address/$urlID\">quote</a> you know($time)";
							 $sentence_txt = "$from favoured  a quote</a> you know
							 
							 Click the link below to view on Quotehood
							 
							 http://$site_address/$urlID
							 ";
							 $ret['title'] = "$from favoured a quote you know";
							 break;
							 case "save":
							 $sentence = "$from_url saved a <a href=\"http://$site_address/$urlID\">quote</a> you know($time)";
							 $sentence_txt = "$from saved a quote</a> you know
							 
							 Click the link below to view on Quotehood
							 
							 http://$site_address/$urlID
							 ";
							 $ret['title'] = "$from saved a quote you know";
							 break;
							 default:
							  $sentence = "$from_url defaulted a <a href=\"http://$site_address/$urlID\">quote</a> you know($time)";
							  $sentence_txt = "$from defaulted a quote</a> you know
							 
							 Click the link below to view on Quotehood
							 
							 http://$site_address/$urlID
							 ";$ret['title'] = "$from defaulted a quote you know";
							}
							
							$ret['full_html'] = '
					<div class=""><div class="panel panel-default shadow" >
					<div class="n-body '.$r['status'].'">
					<p class="n-text" >
				   '.$sentence.'</p>
				   <span class="timeline">'.$quote_preview.' </span>
				   </div></div>';	
				   
				   $ret['full_txt'] = $sentence_txt;
				}
				}
				   
				   return $ret;
}



	Public function mail_notify(){

$this->pagination->setSQL( "SELECT * FROM notif WHERE `mail_status` = 'unsent' AND `status` = 'unread'" );
		  $results = $this->pagination->getSQL('All');		
			if( $this->pagination->checkRes() > 0    ) {
	foreach( $results as $r ) 
	{  $lid = $r['id'];
							$sentence = $this->ntf_sentence($lid);
							$to = $sentence['to'];
						// construct the email
						$Email = new Email();
						$Email->sender = 'Quotehood.Com <no-reply@quotehood.com>';
						$Email->recipient = $to.' <'.$nto.'>';
						$Email->subject = $sentence['title'];
						$Email->message_text = $sentence['full_txt'];
						$Email->message_html = $sentence['full_html'];	
						if ($this->courier->send($Email)){
				$nms = 'sent';
					$st = $db->prepare("UPDATE `notif` SET mail_status=:nms WHERE id='$lid'");
					$st->bindValue (":nms", $nms);
					$st->execute();
						}
						
						
		}
		
			}
		
	 }
		
		
	Public function notify($type,$concn,$id){
		$time = strtotime(date("Y-m-d H:i:s"));
	$db = $this->pagination->connection('','set');
	$type = strtolower($type);
	$status = 'unread';
	$mail_status = 'unsent';
	foreach ($concn as $nto){
 if ($nto != $this->user_email){
		$sth = $db->prepare("INSERT INTO `notif` SET qid = :q,type= :ty,nfrom = :nfrom,stime= :t,nto = :nto,status=:st,mail_status=:mst");
		$sth->bindValue (":q", $id);
		$sth->bindValue (":ty", $type);
		$sth->bindValue (":nfrom", $_SESSION['email']);
		$sth->bindValue (":t", $time);
		$sth->bindValue (":nto", $nto);
		$sth->bindValue (":st", $status);
		$sth->bindValue (":mst", $mail_status);
		$sth->execute();
	
	
		}
	}
	}
			

Public function checkfav($id){
		  $this->pagination->setSQL("SELECT id FROM `fav` where `quote_id` = '$id' AND `email` ='{$_SESSION['email']}'");
			 $this->pagination->setPaginator( 'page' );
			 $results = $this->pagination->getSQL();
			if ($this->pagination->CheckRes() > 0 ){return true;}
			else { return false;}
}

Public function checksave($id){
		  $this->pagination->setSQL("SELECT id FROM `save` where `quote_id` = '$id' AND `email` ='{$_SESSION['email']}'");
			 $this->pagination->setPaginator( 'page' );
			 $results = $this->pagination->getSQL();
			if ($this->pagination->CheckRes() > 0 ){return true;}
			else { return false;}
}


//$type could be liking,comment or convert, to use at the point of notification
//$id is id of quote
Public function concn($type,$id){
	//Concn column represents those that are concerned about a post
		  $this->pagination->setSQL("SELECT concn FROM `quotes` where `ID` = $id");
			 $results = $this->pagination->getSQL();
             $concn = trim($results[0]['concn']);
			
			if (!empty($concn)){$concn = explode(',',$concn);}
			else {$concn = array();
					$ft = 'yes'; //this helps identify that its a first time entry, that the concn column is empty
					}

            if (!in_array($this->user_email,$concn)){
			array_push($concn,$this->user_email); //add new users to concn	
             $concn2 = implode(',',$concn);			
			$sth = $this->db->prepare("UPDATE quotes SET concn=:c WHERE ID='$id'");
			$sth->bindValue (":c", $concn2);
			$sth->execute();
			}
		
			//Now we  notify everybody about the new action except the doer of the action

			if (empty($ft)){$this->notify($type,$concn,$id);}
		
}

		Public function addtofav (){
	$time = strtotime(date("Y-m-d H:i:s"));
	$db = $this->pagination->connection('','set');	
		$sth = $db->prepare("INSERT INTO `fav` SET quote_id = :fv,email = '{$_SESSION['email']}',time='$time'");
	$sth->bindValue (":fv", $this->fav);
	if($sth->execute()){ 
return true;
}
}

	Public function addtosave (){
	$time = strtotime(date("Y-m-d H:i:s"));
	$db = $this->pagination->connection('','set');	
		$sth = $db->prepare("INSERT INTO `save` SET quote_id = :fv,email = '{$_SESSION['email']}',time='$time'");
	$sth->bindValue (":fv", $this->save);
	if($sth->execute()){ 
return true;
}
				}			
				
		Public Function fav(){
if ($this->checkfav($this->fav) == false)
      {			
			$this->pagination->setSQL( "SELECT * FROM quotes where ID = '$this->fav'" );
			 $this->pagination->setPaginator( 'page' );
			 $results = $this->pagination->getSQL();
			if ($this->pagination->getTotalOfResults() > 0 ){
				 foreach($results as $r ) {	
					 $fav = trim($r['fav']);
				$new_fav = $fav + 1;
				$db = $this->pagination->connection('','set');
			$sth = $db->prepare("UPDATE quotes SET fav=:zs WHERE ID='$this->fav'");
			$sth->bindValue (":zs", $new_fav);
			
			if($sth->execute())
	{
		$this->concn('fav',$this->fav); //to add to concern list 
		$this->addtofav(); 
       
	}
				 }
			 }
		}} 
		
		
		Public Function save(){
if ($this->checksave($this->save) == false)
      {			
			$this->pagination->setSQL( "SELECT * FROM quotes where ID = '$this->save'" );
			 $this->pagination->setPaginator( 'page' );
			 $results = $this->pagination->getSQL();
			if ($this->pagination->getTotalOfResults() > 0 ){
				 foreach($results as $r ) {	
					 $save = trim($r['save']);
				$new_save = $save + 1;
				$db = $this->pagination->connection('','set');
			$sth = $db->prepare("UPDATE quotes SET save=:zs WHERE ID='$this->save'");
			$sth->bindValue (":zs", $new_save);
			
			if($sth->execute())
	{
		$this->concn('save',$this->save); //to add to concern list 
		$this->addtosave(); 
       
	}
				 }
			 }
		} 
			 		
					If (empty($_SERVER['HTTP_REFERER'])){ $ReferBack = "http://$this->site_address";}
					else { $ReferBack = $_SERVER['HTTP_REFERER'];}
			 header("Location: $ReferBack"); //redirects if not logged in 
			 exit();
	}

	Public Function index($string){
			//Finally insert to the database
			if ($this->checkIndex($string) == 0){
			$db = $this->pagination->connection('','set');
			$sth = $db->prepare("INSERT INTO searchindex(`string`) VALUES(:st)");
			$sth->bindValue (":st", $string);
			$sth->execute();
			}
	}

		 Public Function rate(){
			
		if ($this->type == 'unset'){ $this->pagination->setSQL( "SELECT * FROM quotes WHERE id = '$id'" );}
		   else { $this->pagination->setSQL( "SELECT * FROM quotes WHERE id = '{$_GET['qid']}'" );}
			
			 $this->pagination->setPaginator( 'page' );
			 $results = $this->pagination->getSQL();
			 if( $this->pagination->getTotalOfResults() > 0    ) {
				 foreach( $results as $r ) { return $r;}
					}
		 }
 
         Public Function code($in,$mth)
		 {
			if ($mth == 'up')
			{
				$in = preg_replace('/[^a-z0-9_]/i','-',$in);
				
				while (preg_match('/--/',$in) == 1)
				{
				$in = str_replace('--','-',$in);
				}				
				
				
				$in = preg_replace('/-$/','',$in);
				
				$in = preg_replace('/^-/','',$in);
			}
			 if ($mth == 'down'){ $in = str_replace('-',' ',$in);}
			 return $in;
		 }
		

		Public function fDate($timestamp) 
	{
	
		/*     $timeDiff = trim($_COOKIE["timeDiff"]);
			$timestamp = $timestamp + $timeDiff;	
			$this->user->setcookie('timeDiff',$timeDiff,1);
		 */$stf = 0;
		$cur_time = time();
		$diff = $cur_time - $timestamp;
		$phrase = array('second','minute','hour','day','week','month','year','decade');
		$length = array(1,60,3600,86400,604800,2630880,31570560,315705600);
		for($i =sizeof($length)-1; ($i >=0)&&(($no =  $diff/$length[$i])<=1); $i--); if($i < 0) $i=0; $_time = $cur_time  -($diff%$length[$i]);
		$no = floor($no); if($no <> 1) $phrase[$i] .='s'; $value=sprintf("%d %s ",$no,$phrase[$i]);
		if(($stf == 1)&&($i >= 1)&&(($cur_tm-$_time) > 0)) $value .= time_ago($_time);
		return $value.' ago ';
	}

	
		Public function checkdb ($table,$column,$column_2,$variable){
			
			$this->pagination->setSQL( "SELECT * FROM $table  WHERE $column = '$variable' OR $column_2 LIKE '%$variable%'");
			$results = $this->pagination->getSQL();
			
			if($this->pagination->getTotalOfResults() > 0 ) {foreach ($results as $r){return $r['tags'];}}
			else { return false;}
		}
		
		//To check if variable is related to a tag
	Public function checktags($table,$column,$column_2,$variable){
		$this->pagination->setSQL( "SELECT * FROM tags");
		$results = $this->pagination->getSQL('All');
	foreach ($results as $r){
		$related_array = explode(",", $r['related']);
if (in_array($variable,$related_array))	{	
			if (empty($res)){$res = $r['tags'];}
						else {$res .= ','.$r['tags'];}	
			}
	}
	if (empty($res)){$res = ''; //To denote emptiness
	}
	else {return $res;}
	}
		 
	Public Function getags($tags)
	{	            
	                 $tags = strtolower(trim($tags));
	                 include('store/array_tags.php');
		foreach( $array_tags as $rt ) {
					$related = strtolower($rt['related']);
					$r_array = explode(",",$related);
				foreach( $r_array as $tf) {
					 
					if ($tf == $tags){$res = strtolower($rt['related']);}
				}
				if (empty($res))$res = $tags; //This is to return the tags sent in back if not in array_tags
			 
	}return str_replace(",", " ",$res); 
}
		 
		 
			Public Function rdr($in,$out)
			{
				
				//The preg_match checks if the url is in the format .....url.html
				if (preg_match('/\/([a-zA-Z0-9_]+)\.html/i',$_SERVER['REQUEST_URI'],$res) == 1)
				{ 
					if ($res[0] == '/'.$in){header("location: $out");}
				}
			}
			
			Public Function quote_rdr()
			{	
				//The preg_match checks if the url is in the format .....url.html
				if (preg_match('/\/[a-zA-Z0-9_-]+-quotes-([0-9]+)\/([a-zA-Z0-9_-]+)*/i',$_SERVER['REQUEST_URI'],$res) == 1)
				{ 
				
				$new_url = '/'.$this->getquoteurl($res[1],$this->limit_url_words);
				if ($res[0] != $new_url){$final_new_url = $this->siteurlpre.$this->site_address.$new_url;  header("location: $final_new_url"); exit;}
				

				}
			}
			
			//To reduce the number of words in a string
			Public function shorten_words($string, $wordsreturned)
			{
				$retval = $string;  //  Just in case of a problem
				$array = explode(" ", $string);
				if (count($array)<=$wordsreturned)
				{
					$retval = $string;
				}
				else
				{
					array_splice($array, $wordsreturned);
					$retval = implode(" ", $array);
				}
				return $retval;
			}
			
			//To reduce the number of words in a string
			//This string majorly helps create new keywords
			Public function chop_words($string, $times)
			{  $string = str_replace('.','',$string); //to remove periods
		 $string = str_replace(',','',$string); //to remove commas
				$array = explode(" ", $string);
				$array_2 = array_chunk($array, $times);
				//var_dump($array_2);
				$n = 0;
				foreach ($array_2 as $a){
				$retval = implode(" ", $a);
				$r_array[$n] = $retval.' quotes';
				$n++;
				}
					return implode(",",$r_array);
			}
			
			
			//To reduce the number of sentence in a string
			Public function shorten_sentence($string, $wordsreturned)
			{
				$retval = $string;  //  Just in case of a problem
				$array = explode("
", $string);
				if (count($array)<=$wordsreturned)
				{
					$retval = $string;
				}
				else
				{
					array_splice($array, $wordsreturned);
					$retval = implode(" ", $array);
				}
				return $retval;
			}
			
	Public function if_word_present($string,$word)
	{ 
		$retval = $string;  //  Just in case of a problem
		$array = explode(" ", $string);
		$n = 0;
		foreach($array as $w)
		{   
		    $w = trim(preg_replace('/(\'s)/','',$w));  //To remove wordéndings like 's in Jehovah's
		    $w = trim(preg_replace('/(\")/','',$w)); //like " in akin"
		    $w = trim(preg_replace('/(\')/','',$w));   //like ' in akin'
		    $w = trim(preg_replace('/(\.)/','',$w));   //like . in akin.
			$array[$n] =trim(preg_replace('/^\PL+|\PL\z/','',$w));
			$n = $n + 1;
		}
		if (in_array($word,$array))
		{
			return true;
		}
		//The below searches for words even if it's just part of another word like "mom" in "moment"
		/* else {
			
			if ((strstr( $string, $word ) ? "Yes" : "No" ) == "Yes"){return true;}
			else return false;
			
		} */
	}
	
		 Public Function getquote($id='unset')
		 {		
		   if ($id != 'unset'){ $this->pagination->setSQL( "SELECT * FROM quotes WHERE ID = '$id'" );}
		   else { $this->pagination->setSQL( "SELECT * FROM quotes WHERE ID = '{$_GET['qid']}'" );}
			 $this->pagination->setPaginator( 'page' );
			 $results = $this->pagination->getSQL('unset','unset','set');
			 if( $this->pagination->checkRes() > 0    ) {
				 foreach( $results as $r ) { 
					$r['Name'] = trim($r['Name']);
					if ($r['AddedBy'] == NULL){$r['AddedBy'] = '';};
				 if (empty($r['Name'])){$r['Name'] = $this->without_author;}
				 			   if (empty($r['WithAuthor'])){  $r['WithAuthor'] = html_entity_decode('"'.$r['Quote'].'"

......'.$r['Name']);//	
}
				 return $r;}
					}
		 }
	
//To turn quote into seo url	
	Public Function getquoteurl($id='unset',$limit='unset')
	{		
     if ($id != 'unset'){$r = $this->getquote($id); }
	 else {$r = $this->getquote();}

	 //This line helps to make up up for quotes without author in database
		$r['Name'] = trim($r['Name']);
     	
		if ($r['Name'] == ''){ $r['Name'] = $this->without_author;}
	
	 
		if ($limit != 'unset'){$quote = $this->shorten_words($r['Quote'],$limit); $author = $this->shorten_words($r['Name'],$limit);}
		else {$quote = $r['Quote']; $author = $r['Name'];}
	 
	$quote_url = $this->code($quote,'up');
	$author_url = $this->code($author,'up');
	return $author_url.'-quotes-'.$id.'/'.$quote_url;
	}
	
	Public Function randquoteurl()
	{	
	    $id = $this->rand_id();
		$url = $this->getquoteurl($id,$this->limit_url_words);
		return $url;
		
	}
		 
	Public Function rand_id($id='unset')
	{		
	  if ($id!='unset'){$rando = rand(0,$id);}
	  else {$rando = rand(0,$this->limit_Quote_id);}
		
		return $rando;
	}
	
	
	Public Function emb_id($id='unset')
	{		
	  if ($id!='unset'){$rando = rand(0,$id);}
	  else {
		   
		  $rando = rand(0,$this->limit_Quote_id);
		  $cfq_a = explode(',',$this->cfq);
$A = array_rand($cfq_a,1);
$rando = $cfq_a[$A];
		  }
		return $rando;
	}
	
	//This gets any url path relative to the domain name,or host
	Public Function geturl()
	{		
	if ($this->site_env == 'development'){ $ret = preg_replace('/^\/[a-zA-Z0-9_]+\//i','',$_SERVER['REQUEST_URI']); return $ret;}
	else if ($this->site_env == 'production') { return $_SERVER['REQUEST_URI'];}
	}

	//To check the active url  
	Public function NavUrlA($which){
		$nav = array();
		$nav = $this->NavUrlDetails;
			echo'<li><a ';

		preg_match('/\/([a-zA-Z0-9_]+)\.html*/i',$_SERVER['PHP_SELF'],$res);
		if (str_replace('/','',$res[0]) == $nav[$which.'_URI']){echo 'class="active"';}	
			echo 'href="http://';
			if (strpos($nav[$which],'%quoteurl%')){
				$nav[$which] = str_replace('%quoteurl%','',$nav[$which]);
				$rqu = $this->randquoteurl();echo "$nav[$which]$rqu";
				}
			
			
			else {	echo $nav[$which];}
			if ($nav[$which.'_Name'] == 'Notifications'){
			
				$nno = $this->notif_no();
				if ($nno > 0){$nav[$which.'_Name'] =  $nav[$which.'_Name'].' <span class="badge bg-red">'.$nno.'</span>';}
			echo '"><p class="top-label">'.$nav[$which.'_Name'].'</p><span class="top-label sub-label" >'.$nav[$which.'_Sub'].'</span></a></li>';

			
			}
			
			else{echo '"><p class="top-label">'.$nav[$which.'_Name'].'</p><span class="top-label sub-label" >'.$nav[$which.'_Sub'].'</span></a></li>';}
 }
 
 	//To check get Navigation Url  
	Public function NavUrl(){
		foreach ($this->NavUrlDetails as $key=>$item){
		if (!strpos($key,'_')){ $this->NavUrlA($key);}
		}
		
		
		
		
			//To add loggin Tab
			if ($this->user->is_logged_in() == true){
				@$user = $this->user->get_user($_SESSION['email']);
				@$email_c = $this->encrypt(trim($_SESSION['email']));
			

               $user['image'] =  trim($user['image']);
			  
			  if(!empty($user['image'])){$userIm = '<img src="http://'.$this->site_address.'/'.$user['image'].'" alt="">'; }
			  else {$userIm = '<img  src="http://'.$this->site_address.'/'.'images/men.png" alt="">'; }
			 			
				
				echo'<li class="">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    '.$userIm.$user['name'].'
                                    <span class=" fa fa-angle-down"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu animated fadeInDown pull-right">
                                    <li><a href="http://'.$this->site_address.'/profile.html">  Profile</a></li>
                                    <li><a href="http://'.$this->site_address.'/myquotes/all" >
                                 My Quotes
								</a>
                                    </li>
									<li><a href="http://'.$this->site_address.'/myquotes/liked" >
                                 Liked Quotes
								</a>
                                    </li>
									<li><a href="http://'.$this->site_address.'/myquotes/saved" >
                                 Saved Quotes
								</a>
                                    </li>
                                    <li><a href="http://'.$this->site_address.'/interests.html" >
                                 Add more Interests
								</a>
                                    </li>
                                    <li><a href="http://'.$this->site_address.'/reset_password.html"><i class="fa fa-key pull-right"></i>Reset Password</a>
                                    </li> <li><a href="http://'.$this->site_address.'/?log=out"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
                                    </li>
                                </ul>
                   
			   </li></li>';  
			}
	}
	
	
		//To check get Navigation Url  
	Public function mlogged(){
			//To add loggin Tab
			if ($this->user->is_logged_in() == true){
				$user = $this->user->get_user($_SESSION['email']);
				$userIm = trim($user['image']);
				$email_c = $this->encrypt(trim($_SESSION['email']));
				$nno = $this->notif_no();
				if ($nno > 0){ $ntfStyle = "style='font-size: 20px;text-decoration: underline;'"; $nhtml = '<span class="badge bg-red">'.$nno.'</span>';}
				else {$ntfStyle = '';$nhtml = '';}
				echo'<div class="col-md-12">
                                <a href="http://'.$this->site_address.'/profile.html" class="user-profile" >
                                    <img src="http://'.$this->site_address.'/'.$userIm.'" alt="">'.$user['name'].'
                                </a> &nbsp; | &nbsp;
								<a href="http://'.$this->site_address.'/myquotes/all" class="user-profile" >
                                 My Quotes
								</a>| &nbsp;<a href="http://'.$this->site_address.'/notifications.html"'.$ntfStyle.'class="user-profile" >
                                 Notifications '.$nhtml.'
								</a>| &nbsp;
								<a href="http://'.$this->site_address.'/myquotes/liked" class="user-profile" >
                                 Liked Quotes
								</a>| &nbsp;
								<a href="http://'.$this->site_address.'/myquotes/saved" class="user-profile" >
                                 Saved Quotes
								</a>| &nbsp;
								<a href="http://'.$this->site_address.'/interests.html" class="user-profile" >
                                  Add more Interests
								</a>
								<a class="label label-info" href="http://'.$this->site_address.'/reset_password.html"><i class="fa fa-key pull-right"></i>Reset Password</a>
                                 <a class="label label-info" href="http://'.$this->site_address.'/?log=out"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
                   </div>'; 
			include('store/add.html');				   
			}
			else {echo '<div >
			<a  style="margin-top: 10px;" href="http://'.$this->site_address.'/reg.html" class="btn btn-success">Register to <u>Add your Own Quotes</u></a> 
			<a style="margin-top: 10px;" href="http://'.$this->site_address.'/login.html" class="btn btn-success">Login</a>
		</div>';}
	}
	
	

	
		//Frame displayed in Embedded Code
	 Public Function embedquote($type='text')
	 {		
		 if ($type == 'text')
		 {
		        $id = $this->emb_id();
				$r = $this->getquote($id);
				echo '<style>';
				include('assets/css/embed.css');
				echo '</style>';
				echo "<div class=\"quotehood\"><p class=\"quote\">\"{$r['Quote']}\"</p><span class=\"author\">.....{$r['Name']}</span><p>More Quotes at <a href=\"http://quotehood.com\">Quotehood.com</a></p></div>";
		 }
		else if ($type == 'image')
		 {
		      $id = $this->emb_id();
				$r = $this->getquote($id);
				$quotef =$r['Quote'] ;
				$namef = $r['Name']; 
				echo '<style>';
				include('assets/css/embed.css');
				echo '</style>';
				echo "<div class=\"quotehood\">";
				 echo $this->texttoimage($r['ID'],$quotef,$namef);
				 //$namef_enc =  urlencode($namef); 
				 $namef_enc =  str_replace(' ','-',$namef); 
				 echo "<p><a href=\"http://$this->site_address/author/$namef-quotes\">Get More of <u>$namef</u> Quotes</a></p></div>";
				 echo "<p>More Quotes at <a href=\"http://$this->site_address\">Quotehood.com</a></p></div>";

		}
		else if ($type == 'slider')
		      {
			  include('store/embedslider.html');
			  }
	
		}
	
	 Public Function formatquote()
		 {		
		   $this->pagination->setSQL( "SELECT * FROM quotes1");
			 $results = $this->pagination->getSQL('All');
			 //var_dump($results);
				 foreach( $results as $r ) {
					$rq = addslashes($r['Quote']);
					$rts = strtolower($rq);
				
				$this->pagination->setSQL( "SELECT * FROM quotes WHERE lower(Quote) = '$rts' ORDER BY ID DESC");
			 $results2 = $this->pagination->getSQL('All');
				
	              if ($this->pagination->getTotalOfResults() > 0){ 
				  //similar_text($r['Quote'],);
				  echo 'NEW:'.$r['Quote_ID'].'('.$r['Quote'].')'.'<br/>'.'OLD:'.$results2[0]['ID'].'('.$results2[0]['Quote'].')';
				  
				  	 $db = $this->pagination->connection('','set');
			 $sth = $db->prepare("DELETE FROM quotes WHERE `ID` = '{$results2[0]['ID']}'");
			  $n = 0;
			  if ($sth->execute()){ echo 'DELETED'.'<br/><br/>'; $n++; echo $n;} 	
				 
				  }
				 }
				 echo $n;
		 }
	
	Public Function retname($bookNumber){
	$this->pagination->setSQL( "SELECT * FROM `bible_books_en` WHERE  number= '$bookNumber'");
			 $res = $this->pagination->getSQL('All');
			 return $res[0]['fullname'];
	}
	
	
	Public Function notif_no(){
	$this->pagination->setSQL( "SELECT id FROM `notif` WHERE  nto= '$this->user_email' AND status= 'unread'");
			 $results = $this->pagination->getSQL('All');
             $n = 0;			 
			 foreach( $results as $r ) {$n++;}
			 return $n;
	}
	
	Public Function comment_no($qid){
	$this->pagination->setSQL( "SELECT id FROM `comments` WHERE  quote_id= '$qid'");
			 $results = $this->pagination->getSQL('All');
             $n = 0;			 
			 foreach( $results as $r ) {$n++;}
			 return $n;
	}
	
			 Public Function addquran()
		 {		
		   $this->pagination->setSQL( "SELECT * FROM quran");
			 $results = $this->pagination->getSQL('All');
				 foreach( $results as $r ) 
				 {
								$rq = addslashes($r['AyahText']);
								$name = 'Koran'.' '.$r['SuraID'].':'.$r['VerseID'];
                                $tag = 'Koran (English Literal)';
							  echo $rq.'<br/>'; 
							  echo $name.'<br/>';
							  echo $tag.'<br/><br/><br/>';
							  
								 $db = $this->pagination->connection('','set');
								 
						 $sth = $db->prepare("INSERT INTO quotes SET Quote='$rq',Name='$name',Tags='$tag'");
						  $n = 0;
						  if ($sth->execute()){ echo 'INSERTED'.'<br/><br/>'; $n++;}
				  }
				 echo $n;
		 }
	

	
		 Public Function addbible()
		 {		
		   $this->pagination->setSQL( "SELECT * FROM bible_kjv");
			 $results = $this->pagination->getSQL('All');
				 foreach( $results as $r ) 
				 {
								$rq = addslashes($r['text']);
								$name = $this->retname($r['book']).' '.$r['chapter'].':'.$r['verse'].' (KJV)';
                                $tag = 'Bible (King James Version)';
							  echo $rq.'<br/>'; 
							  echo $name.'<br/>';
							  echo $tag.'<br/><br/><br/>';
							  
								 $db = $this->pagination->connection('','set');
								 
						 $sth = $db->prepare("INSERT INTO quotes SET Quote='$rq',Name='$name',Tags='$tag'");
						  $n = 0;
						  if ($sth->execute()){ echo 'INSERTED'.'<br/><br/>'; $n++;} 	
							 
				  }
				 echo $n;
		 }
	
	Public Function cache()
		{
			$cachefile = $this->cache_filename();
			
			// open the cache file "cache/home.html" for writing
			$fp = fopen($cachefile, 'w'); 
			// save the contents of output buffer to the file
			fwrite($fp, ob_get_contents()); 
			// close the file
			fclose($fp); 
			// Send the output to the browser
			ob_end_flush(); 				
		}
		
		//This function generates the cach filename
		//Messing up with this function could break the caching process
	Public function cache_filename()
	{
			$url = $this->geturl();
			//Hash the cache filename(THIS IS IMPORTANT TO REMOVE SEO SLASHES)
			$url = md5($url);
			$filename = "$this->cache_folder/$url.html";
			return $filename; 
		
	}
		
		//This is usually run at the top of the script to see if file has already been cached
		Public function cache_check()
		{
			$cachefile = $this->cache_filename();
			if (file_exists($cachefile)) {return true;}
			else { return false;}
			
		}
		
		//This runs the whole cachefile retrieval or include process
		Public Function cache_in($stime='unset')
		{
			if ($this->cache_check())
			{
				// the page has been cached from an earlier request
				
				// output the contents of the cache file
				//echo 'memem'; exit;
				include($this->cache_filename()); 
				// exit the script, so that the rest isnt executed
				$this->getRuntime($stime);
				exit;
			}
		}
		
		
		Public function getRuntime($stime){
			    $etime =   microtime(true);
				$runtime = $etime - $_SERVER["REQUEST_TIME_FLOAT"];
					echo "<span style=\"color:#fff;\">$runtime</span>";}
		
		Public function listout($type){
		
		     if ($type=='authors'){
		     
		     $this->pagination->setSQL( "SELECT * FROM `author` Where NOT (`author` = '') GROUP BY author ORDER BY author ASC","SELECT * FROM `author` Where NOT (`author` = '') GROUP BY Tags ORDER BY author ASC {numspace} " );
			
		     }
		     else if($type == 'categories'){
		
			$this->pagination->setSQL( "SELECT * FROM `tags` Where NOT (`Tags` = '') GROUP BY Tags ORDER BY Tags ASC","SELECT * FROM `tags` Where NOT (`Tags` = '') GROUP BY Tags ORDER BY Tags ASC {numspace} " );
			
			$this->pagination->setPaginator( 'page' );
}			
			$results = $this->pagination->getSQL('All','num');
			if( $this->pagination->checkRes() > 0    ) {
			
				foreach( $results as $r ) {
					$col = array('panel-red','panel-yellow','panel-green','panel-primary');
					$luck = rand(0,3);
					$panel_col = $col[$luck];
					if ($type == 'authors'){
					$r['author'] = trim($r['author']);
				      //$tags_c = $this->code($r['author'],'up');
				      $tags_c = $r['author'];
					if ($r['author'] != ''){
					echo '
					<a href="http://'.$this->site_address.'/author/'.$tags_c.'-quotes" ><div class="cg hover">
					'.ucfirst($r['author']).' Quotes
					</div></a>';
				}}
					else if ($type == 'categories'){
					$r['Tags'] = trim($r['tags']);
						$tags_c = $this->code($r['tags'],'up');
					if ($r['Tags'] != ''){
					echo '<a href="http://'.$this->site_address.'/categories/quotes-about-'.$tags_c.'" ><div  class="cg hover">'.ucfirst($r['tags']).' Quote
					</div></a>';}}
					
				}
	    }
		
	}
	
	
		
			Public function display_notification(){
		  $this->pagination->setSQL( "SELECT * FROM notif WHERE `nto` = '$this->user_email' ORDER by status DESC,stime DESC" );
		  $site_address = $this->site_address;	
		  $deviceType = $this->deviceType; 
		  $this->pagination->setPaginator( 'page' );
		  $results = $this->pagination->getSQL('5');
		$b_d = 'Notifications'; $bdw = 'Notifications'; 
				$fln = 48;
				$s_bdw = strlen($bdw);
				$div = $s_bdw/$fln;
				$div_round = sprintf( "%d", $div );
				$dif = $div - $div_round;
				if ($dif != 0){$margin_add = $div * 4.5;}
				else {$margin_add = 0;}
				if ($s_bdw <= $fln){$margin_add = 0;}
				$margin = $margin_add + 14.4;
			
			if (!empty($_GET['page'])){$getnext = $_GET['page'] + 1;}
			else {$getnext = 2;}
			
			@$aNav = $this->pagination->printNavigationBar();  //It's better to serve pagination here		
		if  ($deviceType == 'computer' ){echo '<div class="col-md-2" >kjjkjk</div>';
			echo '<div class="col-md-6" style="margin: 20px 0 0 0;">';echo '<div class="ht col-md-6"><i class="glyphicon glyphicon-search"></i>&nbsp;'.$b_d.'</div>'; 
		}
		else {echo '<div style="margin: 0 0 0 0;">';echo '<div class="ht"><i class="glyphicon glyphicon-th-large"></i>&nbsp;'.$b_d.'</div>';	}
		if  ($deviceType == 'computer' ){echo '<div style="margin-top:'.$margin.'%;"\'>';}
			
			if( $this->pagination->checkRes() > 0    ) {
				foreach( $results as $r ) {
					$user_d= $this->user->get_user($r['nfrom']);
					$from = ucwords(strtolower(trim($user_d['firstname'])));
				$from_url = "<a href=\"http://".$this->site_address."/myquotes/".$user_d['id']."\"><img class=\"round\" src=\"http://".$this->site_address."/".$user_d['image']."\" \>".$from."</a>";
					$time = $this->fDate($r['stime']);
					$urlID = $this->getquoteurl($r['qid'],$this->limit_url_words);
					$quote_ret = $this->getquote($r['qid']);
					$quote_short = $this->shorten_words($quote_ret['Quote'],20)."......"; 
					$quote_short = html_entity_decode($quote_short);
			if(!empty(trim($quote_ret['AddedBy']))) { $abD = $this->user->get_user(trim(strtolower($quote_ret['AddedBy'])));
			  $uUrl = 'http://'.$site_address.'/myquotes/'.$abD['id']; 
			 $abD['image'] =  trim($abD['image']);
			 $user = '<img class="round" src="http://'.$site_address.'/'.$abD['image'].'" alt="">'; 
			  //To use User Glyphicon
			}
		     else {$uUrl = 'http://'.$this->site_address.'/author/'.$quote_ret['Name'].'-quotes'; $user = '<i class="glyphicon glyphicon-th blue" style="color: #000;"></i>';   //Change Glyphicon to 'th' to differentiate 
			}
			
			if (trim($quote_ret['Name']) == 'anonymous'){$uUrl = 'http://'.$this->site_address.'/author/'.$quote_ret['Name'].'-quotes'; $user = '<i class="glyphicon glyphicon-th blue" style="color: #000;"></i>';   //Change Glyphicon to 'th' to differentiate 
			}
			
					$quote_preview = '<div class="timeline-item">
                          <h3 class="timeline-header">'.$user.'<a href="'.$uUrl.'">'.$quote_ret['Name'].'</a></h3>
                          <div class="timeline-body">
                          '.$quote_short.'
                          </div>
                          <div class="timeline-footer">
                           <a class="btn btn-primary btn-xs" href="http://'.$site_address.'/'.$urlID.'">READ MORE</a>
                          </div>
                        </div>';
					
						switch ( $r['type'] ) {
							 case "comm":
							 $sentence = "$from_url commented on a <a href=\"http://$site_address/$urlID\">quote</a> you know($time)";
							 break;
							 case "conv":
							 $sentence = "$from_url converted a <a href=\"http://$site_address/$urlID\">quote</a> you know($time)";
							 break;
							 case "fav":
							 $sentence = "$from_url favoured a <a href=\"http://$site_address/$urlID\">quote</a> you know($time)";
							 break;
							 case "save":
							 $sentence = "$from_url saved a <a href=\"http://$site_address/$urlID\">quote</a> you know($time)";
							 break;
							 default:
							  $sentence = "$from_url defaulted a <a href=\"http://$site_address/$urlID\">quote</a> you know($time)";
							}
					echo '
					<div class=""><div class="panel panel-default shadow" >
					<div class="n-body '.$r['status'].'">
					<p class="n-text" >
				   '.$sentence.'</p>
				   <span class="timeline">'.$quote_preview.' </span>
				   </div></div>';
				   
				   //Confirm as read
				   $sth = $this->db->prepare("UPDATE notif SET `status` = 'read' WHERE id='{$r['id']}'");
				   $sth->execute();
				}
				print '</div>';
			}
			else {
			//Incase there is no notifications
			$sentence = 'You have no notifications at the moment';
			echo '
					<div class=""><div class="panel panel-default shadow">
					<div class="panel-body">
					<p class="quote-text">
				   '.$sentence.'
				   </div>';
			}
			//echo $aNav;	
				print '</div>';
				print '</div>';
			  		
						if  ($deviceType != 'computer' ){
				
				print '<div class="col-md-5">ADS SECTION</div>';
			
				}
				else {
				//$this->adlist();
				//$this->lists();
				if (!$this->user->is_logged_in()){include('store/reg.html');}
				else {	
					if (!empty($_GET['m']) && !empty($mProf)){include('store/p&b.html');}
			        
					include('store/prof_edit.html');   //To edit uncompleted profile
					
					include('store/add.html');
					
					}
					}				
		print '</div>';
		}
		
			Public function display_profile(){
		$b_d = 'Profile'; $bdw = 'Notifications'; 
				$fln = 48;
				$s_bdw = strlen($bdw);
				$div = $s_bdw/$fln;
				$div_round = sprintf( "%d", $div );
				$dif = $div - $div_round;
				if ($dif != 0){$margin_add = $div * 4.5;}
				else {$margin_add = 0;}
				if ($s_bdw <= $fln){$margin_add = 0;}
				$margin = $margin_add + 14.4;
			
		if  ($deviceType == 'computer' ){echo '<div class="col-md-2" >kjjkjk</div>';
			echo '<div class="col-md-6" style="margin: 20px 0 0 0;">';echo '<div class="ht col-md-6"><i class="glyphicon glyphicon-search"></i>&nbsp;'.$b_d.'</div>'; 
		}
	else {echo '<div style="margin: 0 0 0 0;">';echo '<div class="ht"><i class="glyphicon glyphicon-th-large"></i>&nbsp;'.$b_d.'</div>';	}
		if  ($deviceType == 'computer' ){echo '<div style="margin-top:'.$margin.'%;"\'>';}
			
					echo '
					<div class=""><div class="panel panel-default shadow" >
					<div class="panel-body '.$r['status'].'">
					<p class="n-text" >
				   '.$sentence.'
				   </div></div>';
				print '</div>';
				
				print '</div>';
				print '</div>';
			  		
						if  ($deviceType != 'computer' ){
				
				print '<div class="col-md-5">ADS SECTION</div>';
			
				}
				else {
				$this->adlist();
				$this->lists();
				if (!$this->user->is_logged_in()){include('store/reg.html');}
				else {include('store/add.html'); }
					}				
		print '</div>';
		}
		

		
		Public function display_infinite(){
 $start = $_POST['start'];
 $limit = $_POST['limit'];
 $type = $_POST['type'];
 if (!empty($_POST['subtype'])){$subtype = $_POST['subtype'];}
 if (!empty($_POST['subtype_value'])){$subtype_value = $_POST['subtype_value'];}
			$site_address = $this->site_address;	
				$deviceType = $this->deviceType; 
 
		if ($type == 'search'){
			$slashq = addslashes($this->getags($subtype_value)); //adslashes to prepare db entry and getags for related tags	
			$q = $slashq;
			$this->index($q);
			$sql = "SELECT * FROM quotes WHERE MATCH(Quote,Name,Tags) AGAINST ('$slashq')";
			if (strlen($slashq) <= 5){$sql = "SELECT * FROM quotes WHERE Quote LIKE '%$slashq%' OR Name LIKE '%$slashq' OR Tags LIKE '%$slashq' LIMIT $start,$limit" ;
			}
}

else if ($type == 'index'){ //Index handles all author-related tag-related and category-related quotes
//Responsible for retrieving quotes
			if ($subtype == 'names')
			{
		 $g_n_s = addslashes($subtype_value);
		 $g_n_s = str_replace('--',' ',$g_n_s);
		 $sql = "SELECT * FROM quotes WHERE `name` = '$g_n_s' ORDER BY date DESC LIMIT $start,$limit";
			}
			
			else if ($subtype == 'tags')
			{
			  $getDecode = $this->code($subtype_value,'down');
				$g_t_s = addslashes($this->getags($getDecode)); //adslashes to prepare db entry and getags for related tags
				
				 $sql = "SELECT * FROM quotes WHERE MATCH(Tags,Quote) AGAINST('$g_t_s') ORDER BY MATCH(Tags,Quote) AGAINST('$g_t_s') DESC LIMIT $start,$limit";
				 //To recreate 
				 if (strlen($g_t_s) <= 5){$sql = "SELECT * FROM quotes WHERE (CONCAT (Tags,Quote) LIKE '%$g_t_s%') ORDER BY (CONCAT (Tags,Quote) LIKE '%$g_t_s%') DESC LIMIT $start,$limit";}
			}
			
			else if ($subtype == 'm')
			{
		   if($subtype_value == 'all') { $sql = "SELECT *  FROM quotes WHERE `AddedBy` = '{$_SESSION['email']}' ORDER BY date DESC LIMIT $start,$limit";}
			else if ($subtype_value ==  'liked') {$sql = "SELECT * FROM quotes INNER JOIN fav on quotes.ID = fav.quote_id AND fav.email ='{$_SESSION['email']}' ORDER BY fav.time DESC LIMIT $start,$limit";}
			else if ($subtype_value ==  'saved') {$sql = "SELECT * FROM quotes INNER JOIN save on quotes.ID = save.quote_id AND save.email ='{$_SESSION['email']}' ORDER BY save.time DESC LIMIT $start,$limit";}
			else {$mUser= $this->user->get_user($subtype_value,'id');
                
				
				$mEmail = $mUser['email'];
				$mProf = 'set';
			$sql = "SELECT *  FROM quotes WHERE `AddedBy` = '$mEmail' AND `Name` != 'anonymous' ORDER BY date DESC LIMIT $start,$limit";}	
}
			
			else {
			//THis retrieves from cookies
			if($this->user->is_logged_in() == false){
				//Retrieve capsule cookie
				if (!empty($_COOKIE["capsule"])){$int_1 = explode(',',$this->user->getcookie('capsule'));}
				else {$int_1 = explode(',',$this->defcap);}

				} //$_COOKIE["capsule"] should always be set
			
			//Below retrieves from database if logged in
			else {
			$int_1 = explode(',',$this->user_d['interests']);
			}
			$count = -1;
			$int_2 = array();
			foreach ($int_1 as $a){$count++; $int_2["$count"] = addslashes($this->getags($a));}
			$int= implode(' ',$int_2); 
			
		    //adslashes to prepare db entry and getags for related tags
			//$sql = "SELECT *,FROM_UNIXTIME(date,'%Y%c%e') as timer FROM quotes ORDER BY timer DESC,comments DESC,converts DESC,fav DESC limit $start, $limit";
			$sql = "SELECT * FROM quotes ORDER BY date DESC limit $start, $limit";
			
			//if (strlen($int) <= 3){$sql = "SELECT *,FROM_UNIXTIME(date,'%Y%c%e') as timer FROM quotes ORDER BY timer DESC,comments DESC,converts DESC,fav DESC limit $start, $limit";}
			
			
			
			
}			
}
			//echo $sql;
				$results = $this->pagination->connection($sql);
			      //var_dump($results);
 		$n = 0;
				foreach( $results as $r ) {
			$n++;
				 if ($this->user->is_logged_in()){	$ckf = $this->checkfav($r['ID']);
				 $cks = $this->checksave($r['ID']);}
				    $namef = $r['Name']; //for fresh use
				    $quotef = $r['Quote']; //for fresh use
					$r['Quote'] = nl2br($r['Quote']);	
					$r['Quote'] = $this->hashTag($r['Quote']);
					
				if (trim($r['Name']) == ''){$r['Name'] = "Unknown";}
				$tag_c = $this->code($r['ntags'],'up'); 
					//$name_c = $this->code($r['Name'],'up'); 	
					$name_c = $r['Name']; 
					
					$encrEmail = $this->encrypt(trim(strtolower($r['AddedBy'])));		
			if(!empty(trim($r['AddedBy']))) { $abD = $this->user->get_user(trim(strtolower($r['AddedBy'])));
			  $uUrl = 'http://'.$site_address.'/myquotes/'.$abD['id']; 
			 $abD['image'] =  trim($abD['image']);
			  
			  if(!empty($abD['image'])){$user = '<img class="round" src="http://'.$site_address.'/'.$abD['image'].'" alt="">'; }
			  else {$user = '<img class="round" src="http://'.$site_address.'/'.'images/men.png" alt="">'; }
			  //To use User Glyphicon
			}
		     else {$uUrl = 'http://'.$site_address.'/author/'.$name_c.'-quotes'; $user = '<i class="glyphicon glyphicon-th blue" style="color: #000;"></i>';   //Change Glyphicon to 'th' to differentiate 
			}
			
			if (trim($r['Name']) == 'anonymous'){$uUrl = 'http://'.$site_address.'/author/'.$name_c.'-quotes'; $user = '<i class="glyphicon glyphicon-th blue" style="color: #000;"></i>';   //Change Glyphicon to 'th' to differentiate 
			}
			
			
					
					$r_c = substr($r['Quote'],0,80);
					
					$r_ca = '#quotes '.$r_c.'....';
					
					if  ($deviceType != 'computer' )
					{@$ret .= '	<div>';}
					else {@$ret .= '<div class="">';}
		              
        $time = $this->fDate($r['date']);
					$ret .= '<div class="panel panel-default shadow">
					<div class="panel-body">
				<a class="quote-title"  href="'.$uUrl.'">'.$user.'
					'.ucfirst($r['Name']).'</a>
						<b style="float:right;margin-top: 6px;font-size: 18px;">'.$time.'</b>
			
					<p class="quote-text">
				   '.$r['Quote'] .'</p>';
				   
				   
				   if ($r['img'] == 'TEXT'){
					  if($this->user->is_logged_in() == TRUE){
					  if (trim($r['AddedBy']) == $this->user_email){$ret .= '<a href="http://'.$site_address.'/convert.html?qid='.$r['ID'].'&c=b" style="font-size: 15px;">++Change Background</a>';}}//$ret .= $this->texttoimage($r['ID'],$quotef,$namef);
					  }
				 else {$qrq = $r['Quote'] = $this->removehtml(str_replace('','',$r['Quote'])); $ttl=$r['Name'].' quote'; $ret .= '<img class="lazy" title="'.$ttl.'" alt="'.$qrq.'" data-toggle="tooltip" data-src="http://'.$this->site_address.'/'.$r['img'].'"  src="http://'.$this->site_address.'/'.$r['img'].'"/>';}
				   
				$ret .= '</div>
					<div class="panel-footer box-footer"><div>';
					
					if (!empty(trim($r['ntags'])))
					{
						$lower_rt = strtolower($r['ntags']);
						$t_array = explode(",",$lower_rt);
						$t_count = 0;
						foreach( $t_array as $tf )
						{
							$t_count++;
						$tf_c = $this->code($tf,'up');
						if ($t_count >= 1 ){$ret .= '&nbsp;';}
						$ret .= '<a style="font-size: 12px;color: #fff;white-space: normal;" class="label label-default" href="http://'.$site_address.'/categories/quotes-about-'.$tf_c.'">'.$tf.'</a>';
						}
					}
						
						$ret .= '</div>';
					
					
					
					$ret .= '<div class="action_div">';
					 $c_num = $r['comments'];			
					 $cv_num = $r['converts'];			
					
				
				if ($this->user->is_logged_in() == true)
				{
					
					  //for liking
				     	if ($ckf == true){$likes = 1; $str_like = 'unlike'; }else {$likes = 0; $str_like = 'like';}
					
						if ($ckf == false)
						{
						$ret .= '<span id="likediv-'.$r['ID'].'">
						<input type="hidden" id="likes-'.$r['ID'].'" value="'.$likes.'">';
						$ret .= '<span class="btn-likes "><a title="'.ucwords($str_like).'" class="'.$str_like.'" onClick="addLikes('.$r['ID'].',\''. $site_address.'\')">LIKE('.$r['fav'].')</a></span>';
						$ret .= '</span>';
						}
						else if ($ckf == true)
						{
						$ret .= '<span data-toggle="" style="font-size: 10px;color: #000;">LIKED('.$r['fav'].')</span>';
						}
					
					  //for saving
				     	if ($cks == true){$saves = 1; $str_save = 'delete'; }else {$saves = 0; $str_save = 'save';}
					
						if ($cks == false)
						{


					if ($this->user->is_logged_in()){
						$ret .='<span id="savediv-'.$r['ID'].'">
						<input type="hidden" id="saves-'.$r['ID'].'" value="'.$saves.'">';
						$ret .= '<span class="btn-saves "><a title="'.ucwords($str_save).'" class="'.$str_save.'" onClick="addSaves('.$r['ID'].',\''. $site_address.'\')">&nbsp;Save('.$r['save'].')</a></span>';
					$ret .= '</span>';
						}
						}
						else if ($cks == true)
						{
						$ret .= '<span data-toggle="" style="font-size: 10px;color: #000;">&nbsp;SAVED('.$r['save'].')</span>';
						}
						
						
						
						
				}
						 $ret .= '<a href="http://'.$site_address.'/convert.html?qid='.$r['ID'].'" rel="nofollow" data-toggle=""  class="cpic" >Convert To Image('.$cv_num.')</a>';
						
					
						 
						 if ($this->user->is_logged_in()){
							 $urlID = $this->getquoteurl($r['ID'],$this->limit_url_words);
						 $ret .= '<a href="http://'.$site_address.'/'.$urlID.'" data-toggle=""  class="cpic" >Comment&nbsp;('.$c_num.')&nbsp;</a>';}
						 else { $ret .= '<a href="http://'.$site_address.'/'.$this->getquoteurl($r['ID'],$this->limit_url_words).'" data-toggle=""  class="cpic" >Comment&nbsp;('.$c_num.')&nbsp;</a>';}
						 
					$ret .= '</div></div></div>
                    </div>';
				
				}
		
if (!empty($ret)){$ret .= '</div>'; echo $ret;}

				

				
		}
	
	Public function hashTag($message)
{
	$parsedMessage = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])#+([a-z0-9_\s]+)#/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a style="font-size: 18px;" href="http://'.$this->site_address.'/search.html?q=%23$2%23">#$2#</a>'), $message);
	return $parsedMessage;
}
		
		Public function display($type,$no=1){
		
if ($type == 'search'){
			$slashq = addslashes($this->getags($_GET['q'])); //adslashes to prepare db entry and getags for related tags	
			$q = $slashq;
			$this->index($q);
			$this->pagination->setSQL("SELECT * FROM quotes WHERE MATCH(Quote,Name,Tags) AGAINST ('$slashq')" );
			if ($this->pagination->checkRes() == 0){$this->pagination->setSQL( "SELECT * FROM quotes WHERE Quote LIKE '%$slashq%' OR Name LIKE '%$slashq' OR Tags LIKE '%$slashq'" );
			}
}

else if($type == 'one'){
			$this->pagination->setSQL( "SELECT * FROM quotes WHERE id = '{$_GET['qid']}'" );
}


else if ($type == 'index'){ //Index handles all author-related tag-related and category-related quotes
//Responsible for retrieving quotes
			if (!empty($_GET['names']))
			{
			//$g_n_s = $this->code($_GET['names'],'down');
			
			
		/* $this->pagination->setSQL( "SELECT * FROM quotes WHERE MATCH(name,Tags,Quote) AGAINST('$g_n_s') ORDER BY MATCH(name) AGAINST('$g_n_s') DESC" );
		if ($this->pagination->checkRes() == 0){$this->pagination->setSQL( "SELECT * FROM quotes WHERE (CONCAT (name,Tags,Quote) LIKE '%$g_n_s%') ORDER BY name LIKE '%$g_n_s%' DESC");}
		 */
		 
		 $g_n_s = addslashes($_GET['names']);
		 $g_n_s = str_replace('--',' ',$g_n_s);
		  $this->pagination->setSQL( "SELECT * FROM quotes WHERE name  = '$g_n_s' ORDER BY date DESC" );
			}
			else if (!empty($_GET['tags']))
			{
	      $getDecode = $this->code($_GET['tags'],'down');
			$g_t_s = addslashes($this->getags($getDecode)); //adslashes to prepare db entry and getags for related tags
			
			 $this->pagination->setSQL( "SELECT * FROM quotes WHERE MATCH(Tags,Quote) AGAINST('$g_t_s') ORDER BY MATCH(Tags,Quote) AGAINST('$g_t_s') DESC" );
			 //To recreate 
			 if ($this->pagination->checkRes() == 0){$this->pagination->setSQL( "SELECT * FROM quotes WHERE (CONCAT (Tags,Quote) LIKE '%$g_t_s%') ORDER BY (CONCAT (Tags,Quote) LIKE '%$g_t_s%') DESC");}
			}
			
			else if (!empty($_GET['m']))
			{
		   if($_GET['m'] == 'all') { $this->pagination->setSQL( "SELECT *  FROM quotes WHERE `AddedBy` = '{$_SESSION['email']}' ORDER BY date DESC" );}
			else if ($_GET['m'] == 'liked') {$this->pagination->setSQL( "SELECT * FROM quotes INNER JOIN fav on quotes.ID = fav.quote_id AND fav.email ='{$_SESSION['email']}' ORDER BY fav.time DESC" );}
			else if ($_GET['m'] == 'saved') {$this->pagination->setSQL( "SELECT * FROM quotes INNER JOIN save on quotes.ID = save.quote_id AND save.email ='{$_SESSION['email']}' ORDER BY save.time DESC" );}
			else {$mUser= $this->user->get_user($_GET['m'],'id');
                
				//THis line uses confirmation to screen out non existing and unconfirmed users
				 if (empty($mUser['confirmation']) || $mUser['confirmation'] == 0 ){header("Location: http://$this->site_address");exit();}		
				$mEmail = $mUser['email'];
				$mProf = 'set';
			$this->pagination->setSQL("SELECT *  FROM quotes WHERE `AddedBy` = '$mEmail' AND `Name` != 'anonymous' ORDER BY date DESC");}
			}
			
			else  {
			//THis retrieves from cookies
			if($this->user->is_logged_in() == false){
				//Retrieve capsule cookie
				if (!empty($_COOKIE["capsule"])){$int_1 = explode(',',$this->user->getcookie('capsule'));}
				else {$int_1 = explode(',',$this->defcap);}

				} //$_COOKIE["capsule"] should always be set
			
			//Below retrieves from database if logged in
			else {
			$int_1 = explode(',',$this->user_d['interests']);
			}
			$count = -1;
			$int_2 = array();
			foreach ($int_1 as $a){$count++; $int_2["$count"] = addslashes($this->getags($a));}
			$int= implode(' ',$int_2); 
			
			//formally by interest
			/**
		    //adslashes to prepare db entry and getags for related tags
			$this->pagination->setSQL("SELECT *,FROM_UNIXTIME(date,'%Y%c%e') as timer, MATCH(Quote,Name,Tags) AGAINST ('$int') as Relevance FROM quotes  WHERE MATCH(Quote,Name,Tags) AGAINST ('$int') ORDER BY timer DESC ,Relevance DESC,comments DESC,converts DESC,fav DESC" );
			$checkR = $this->pagination->CheckRes("SELECT id FROM quotes  WHERE MATCH(Quote,Name,Tags) AGAINST ('$int') LIMIT 1");
			$checkNextsql = "SELECT id FROM quotes  WHERE MATCH(Quote,Name,Tags) AGAINST ('$int')";
			
			
			
			if (strlen($int) <= 3){$this->pagination->setSQL( "SELECT *,FROM_UNIXTIME(date,'%Y%c%e') as timer FROM quotes WHERE (CONCAT (Quote,Name,Tags) LIKE '%$int%') ORDER BY timer DESC , (CONCAT (Quote,Name,Tags) LIKE '%$int%') DESC,comments DESC,converts DESC,fav DESC");
			$checkR = $this->pagination->CheckRes("SELECT id FROM quotes WHERE (CONCAT (Quote,Name,Tags) LIKE '%$int%')  LIMIT 1");
			$checkNextsql = "SELECT id FROM quotes WHERE (CONCAT (Quote,Name,Tags) LIKE '%$int%')";
			}
			
			
			****/
			
		 //adslashes to prepare db entry and getags for related tags
			$this->pagination->setSQL("SELECT * FROM quotes ORDER BY date DESC" );
			//$this->pagination->setSQL("SELECT *,FROM_UNIXTIME(date,'%Y%c%e') as timer FROM quotes ORDER BY timer DESC,comments DESC,converts DESC,fav DESC" );
			$checkR = $this->pagination->CheckRes("SELECT id FROM quotes LIMIT 1");
			$checkNextsql = "SELECT id FROM quotes";
			}
			
}
		
		
			
		//PRofile Edit for Phones And Tabs
//HAd to be ontop because front end is diffrent from that of PC		
if ($this->user->is_logged_in() && $this->deviceType != 'computer')
{
if (!empty($mUser))	{$abD = $mUser;}
else {$abD = $this->user->get_user($_SESSION['email']);}
include('store/mprof_edit.html');   //To edit uncompleted profile
if (!empty($_GET['m'])){
	include('store/mp&b.html');   //To edit uncompleted profile
	}
		}
				$site_address = $this->site_address;	
				$deviceType = $this->deviceType; 
				$results = $this->pagination->getSQL();
			
			//echo $this->pagination->bull; exit;
			/**
			 * The result bar will inform you if no results were found
			 * so you should keep it outside the controll structure below 
			 */
			 
			 if (!empty($_GET['names'])){
			$_GET['names'] = $this->code($_GET['names'],'down'); 
			$b_d = 'Top Quotes of <span class="border-under">'.$_GET['names'].'</span>'; $bdw =  'Top Quotes of '.$_GET['names'];}
			 else if (!empty($_GET['tags'])){
			$_GET['tags'] =  $this->code($_GET['tags'],'down');   $b_d = 'Quotes About <span class="border-under">'.ucfirst($_GET['tags']).'</span>'; $bdw = 'Quotes About '.$_GET['tags']; } 
			 else if (!empty($_GET['q'])){
			$b_d = 'Search Quotes About <span class="border-under">'.ucfirst($_GET['q']).'</span>'; $bdw = 'Search Quotes About'. $_GET['q'];} 
			 else if (!empty($_GET['qid'])){
			 $b_d = ucfirst($this->getquote()['Name']).' Quote' ; $bdw =  ucfirst($this->getquote()['Name']).' Quote';}
			 else if (!empty($_GET['m'])){	
              if ($_GET['m'] == 'all'){$b_d = 'My Quotes' ; $bdw = 'My Quotes';}			 
              else if ($_GET['m'] == 'liked'){$b_d = 'Liked Quotes' ; $bdw = 'Liked Quotes';}			 
              else if ($_GET['m'] == 'saved'){$b_d = 'Saved Quotes' ; $bdw = 'Saved Quotes';}			 
              else { 
			  
		  
			  $uDet= $this->user->get_user($_GET['m'],'id');
			
			  $b_d = $uDet['firstname'].' Quotes' ; $bdw = $uDet['firstname'].' Saved Quotes';
			  
			  
			  }			 
        }	 
			 else {  
			 
			 //For reporting of interest update
			
			 
		$b_d = 'Recent Quotes'; $bdw = 'Recent Quotes'; 
		
		if (!empty($_SESSION['update_error'])){$b_d = 'Suggested Quotes &nbsp;&nbsp;'.$_SESSION['update_error']; $bdw = 'Suggested Quotes &nbsp;&nbsp;'.$_SESSION['update_error']; }} 
	
				$fln = 48;
				$s_bdw = strlen($bdw);
				$div = $s_bdw/$fln;
				$div_round = sprintf( "%d", $div );
				$dif = $div - $div_round;
				if ($dif != 0){$margin_add = $div * 4.5;}
				else {$margin_add = 0;}
				if ($s_bdw <= $fln){$margin_add = 0;}
				$margin = $margin_add + 14.4;
			
		
		
		if  ($deviceType == 'computer' ){echo '<div class="col-md-2" >kjjkjk</div>';
			echo '<div class="col-md-6" style="margin: 20px 0 0 0;">';echo '<div class="ht col-md-6"><i class="glyphicon glyphicon-search"></i>&nbsp;'.$b_d.'</div>'; 
		}
		else {
			echo '<div style="margin: 0 0 0 0;">';echo '<div class="ht"><i class="glyphicon glyphicon-search"></i>&nbsp;'.$b_d.'</div>';
		}
		if  ($deviceType == 'computer' ){echo '<div'; if ($type != 'one'){echo ' id="timeline-container"';} echo ' style="margin-top:'.$margin.'%;"\'>';}
			
			if (!empty($checkR)){$checkResP = $checkR;}
		else {$checkResP = $this->pagination->checkRes();}
		
			if( $checkResP > 0 ) {
				

				if ($deviceType != 'computer'){		
					if ($type != 'one'){
							if (!empty($checkNextsql)){	$aNav = $this->pagination->printNavigationBar($checkNextsql,$checkR);  //It's better to serve pagination here
							}
							else {$aNav = $this->pagination->printNavigationBar();
							}
					}


					}
				
				foreach( $results as $r ) {
				 if ($this->user->is_logged_in()){	$ckf = $this->checkfav($r['ID']);
				 $cks = $this->checksave($r['ID']);}
				 
				    $namef = $r['Name']; //for fresh use
				    $quotef = $r['Quote']; //for fresh use
					$r['Quote'] = nl2br($r['Quote']);
					$r['Quote'] = $this->hashTag($r['Quote']);
					
				if (trim($r['Name']) == ''){$r['Name'] = "Unknown";}
				$tag_c = $this->code($r['ntags'],'up'); 
					//$name_c = $this->code($r['Name'],'up'); 	
					$name_c = $r['Name']; 
					
					$encrEmail = $this->encrypt(trim(strtolower($r['AddedBy'])));		
			if(!empty(trim($r['AddedBy']))) { $abD = $this->user->get_user(trim(strtolower($r['AddedBy'])));
			  $uUrl = 'http://'.$site_address.'/myquotes/'.$abD['id']; 
			 $abD['image'] =  trim($abD['image']);
			  
			  if(!empty($abD['image'])){$user = '<img class="round" src="http://'.$site_address.'/'.$abD['image'].'" alt="">'; }
			  else {$user = '<img class="round" src="http://'.$site_address.'/'.'images/men.png" alt="">'; }
			  //To use User Glyphicon
			}
		     else {$uUrl = 'http://'.$site_address.'/author/'.$name_c.'-quotes'; $user = '<i class="glyphicon glyphicon-th blue" style="color: #000;"></i>';   //Change Glyphicon to 'th' to differentiate 
			}
			
			if (trim($r['Name']) == 'anonymous'){$uUrl = 'http://'.$site_address.'/author/'.$name_c.'-quotes'; $user = '<i class="glyphicon glyphicon-th blue" style="color: #000;"></i>';   //Change Glyphicon to 'th' to differentiate 
			}
			
			
					
					$r_c = substr($r['Quote'],0,80);
					
					$r_ca = '#quotes '.$r_c.'....';
					
					if  ($deviceType != 'computer' )
					{echo '	<div>';}
					else {echo '<div class="">';}
		              
                     //TO fix tags
				 //if (!empty($r['tags']))
					//{ 
				//if (empty(trim($r['ntags']))){$r['ntags']= $this->fixTags($r['ID']);} //Fixes the tags and returns the made tags
	//				 } 
	$time = $this->fDate($r['date']);
					echo'<div class="panel panel-default shadow">
					<div class="panel-body">
				<a class="quote-title"  href="'.$uUrl.'">'.$user.'
					'.ucfirst($r['Name']).'</a>
					<b style="float:right;margin-top: 6px;font-size: 18px;">'.$time.'</b>
					
					<p class="quote-text">
				   '.$r['Quote'] .'</p>';
				   
				 if ($r['img'] == 'TEXT'){
					 
					 if($this->user->is_logged_in() == TRUE){if (trim($r['AddedBy']) == $this->user_email){echo '<a href="http://'.$site_address.'/convert.html?qid='.$r['ID'].'&c=b" style="font-size: 15px;">++Change Background</a>';}}
					// echo $this->texttoimage($r['ID'],$quotef,$namef);
					 }
				 else {$qrq = $r['Quote'] = $this->removehtml(str_replace('','',$r['Quote'])); $ttl=$r['Name'].' quote'; echo '<img class="lazy" title="'.$ttl.'" alt="'.$qrq.'" data-toggle="tooltip" data-src="http://'.$this->site_address.'/'.$r['img'].'"  src="http://'.$this->site_address.'/'.$r['img'].'"/>';}
				   
				   echo '
					</div>
					<div class="panel-footer box-footer"><div>';
					
					if (!empty(trim($r['ntags'])))
					{
						$lower_rt = strtolower($r['ntags']);
						$t_array = explode(",",$lower_rt);
						$t_count = 0;
						foreach( $t_array as $tf )
						{
							$t_count++;
						$tf_c = $this->code($tf,'up');
						if ($t_count >= 1 ){echo '&nbsp;';}
						echo '<a style="font-size: 12px;color: #fff;white-space: normal;" class="label label-default" href="http://'.$site_address.'/categories/quotes-about-'.$tf_c.'">'.$tf.'</a>';
						}
					}
						
						echo '</div>';
					
					
					
					echo '<div class="action_div">';
					 $c_num = $r['comments'];			
					 $cv_num = $r['converts'];			
					if ($deviceType != 'computer')
					{
			
				if ($this->user->is_logged_in() == true)
				{
					
					//for liking
					if ($ckf == false)
					{
					echo'<a href="http://'.$site_address.'?fav='.$r['ID'].'" data-toggle="" class="cpic" >Like('.$r['fav'].')</a>';
					}
					else if ($ckf == true)
					{
					echo'<span data-toggle="" style="font-size: 10px;color: #000;">LIKED('.$r['fav'].')</span>';
					}
					
					//for saving
					if ($cks == false)
					{
					echo'<a href="http://'.$site_address.'?save='.$r['ID'].'" data-toggle="" class="cpic" >&nbsp;Save('.$r['save'].')</a>';
					}
					else if ($cks == true)
					{
					echo'<span data-toggle="" style="font-size: 10px;color: #000;">&nbsp;Saved('.$r['save'].')</span>';
					}
                }					
					  echo'&nbsp;<a href="http://'.$site_address.'/copytext.html?qid='.$r['ID'].'" data-toggle="" class="cpic" >Copy as Text</a>';
						 echo'<a href="http://'.$site_address.'/convert.html?qid='.$r['ID'].'" rel="nofollow" data-toggle=""  class="cpic" >Convert To Image('.$cv_num.')</a>';
						
						
					}
					else 
					{

				
				if ($this->user->is_logged_in() == true)
				{
					
					  //for liking
				     	if ($ckf == true){$likes = 1; $str_like = 'unlike'; }else {$likes = 0; $str_like = 'like';}
					
						if ($ckf == false)
						{
						echo '<span id="likediv-'.$r['ID'].'">
						<input type="hidden" id="likes-'.$r['ID'].'" value="'.$likes.'">';
						echo '<span class="btn-likes "><a title="'.ucwords($str_like).'" class="'.$str_like.'" onClick="addLikes('.$r['ID'].',\''. $site_address.'\')">LIKE('.$r['fav'].')</a></span>';
						echo '</span>';
						}
						else if ($ckf == true)
						{
						echo'<span data-toggle="" style="font-size: 10px;color: #000;">LIKED('.$r['fav'].')</span>';
						}
					
					  //for saving
				     	if ($cks == true){$saves = 1; $str_save = 'delete'; }else {$saves = 0; $str_save = 'save';}
					
						if ($cks == false)
						{


					if ($this->user->is_logged_in()){
						echo '<span id="savediv-'.$r['ID'].'">
						<input type="hidden" id="saves-'.$r['ID'].'" value="'.$saves.'">';
						echo '<span class="btn-saves "><a title="'.ucwords($str_save).'" class="'.$str_save.'" onClick="addSaves('.$r['ID'].',\''. $site_address.'\')">&nbsp;Save('.$r['save'].')</a></span>';
						echo '</span>';
						}
						}
						else if ($cks == true)
						{
						echo'<span data-toggle="" style="font-size: 10px;color: #000;">&nbsp;SAVED('.$r['save'].')</span>';
						}
						
						
						
						
				}
						 echo'<a href="http://'.$site_address.'/convert.html?qid='.$r['ID'].'" data-toggle="" rel="nofollow" class="cpic" >Convert To Image('.$cv_num.')</a>';
						} 
					 if ($type != 'one'){
						 
						 if ($this->user->is_logged_in()){
							 $urlID = $this->getquoteurl($r['ID'],$this->limit_url_words);
						 echo'<a href="http://'.$site_address.'/'.$urlID.'" data-toggle=""  class="cpic" >Comment&nbsp;('.$c_num.')&nbsp;</a>';}
						 else { echo'<a href="http://'.$site_address.'/'.$this->getquoteurl($r['ID'],$this->limit_url_words).'" data-toggle=""  class="cpic" >Comment&nbsp;('.$c_num.')&nbsp;</a>';}
						 
						 
						 }
					 
					echo '</div></div>';
					
					if ($type == 'one'){
   if ($this->deviceType == 'computer' && $this->user->is_logged_in() == false ){echo '<div class="error">You have to be logged in/Registered to comment here</div>';}
   else if ($this->deviceType != 'computer' && $this->user->is_logged_in() == false )  {echo '<div class="error">You have to be <a href="http://'.$this->site_address.'/'.'login.html">logged in</a>/<a href="http://'.$this->site_address.'/'.'reg.html">Registered</a> to comment here</div>';}
				echo '<div class="commentload">';
				$this->comments();
				echo '</div>';
if ($this->user->is_logged_in()){include('store/add_comment.html');}
		
		}
					echo'</div>
                    </div>';
				
				}
				
				print '</div>';
				if ($deviceType != 'computer'){
				if ($type != 'one'){echo $aNav;}
				else {print '<p><a href="http://'.$this->site_address.'/'.$this->randquoteurl().'" class="btn btn-success btn-lg" role="button">RANDOM QUOTE</a></p>';
				}
}
				print '</div>';
				print '</div>';
			  		}
					
					else {echo '<div class="panel panel-default shadow" style=\'font-size: 20px;\'>Nothing Found.Try and Search again with a different keyword</div>';}
					
						if  ($deviceType != 'computer' ){
				
				print '<div class="col-md-5">ADS SECTION</div>';
			
				}
				else {
			
				if (!empty($_GET['m']) && !empty($mProf)){include('store/p&b.html');}
			       
				if (!$this->user->is_logged_in()){include('store/reg.html');}
				else {	
				
					 include('store/prof_edit.html');   //To edit uncompleted profile
					
					 include('store/add.html');
					 
					 
					}
					
				
					}				
		print '</div>';
		}
		
		
	
		

Public function runTags(){
					$a = $this->pagination->setSQL( "SELECT * FROM tags" );
					$array = $this->pagination->getSQL('All');
					$tagcount = 0;
					echo 'array('; echo '<br/>';
					foreach ($array as $a){echo 'array('; echo '"id" => "'.$a['id'].'",';echo '"tags" => "'.$a['tags'].'",';echo '"related" => "'.$a['related'].'"'; echo '),'; echo '<br/>';}
					echo ');';
					
}	


Public function uploadFile ($file_field = null, $check_image = false, $random_name = false) {
       
  //Config Section    
  //Set file upload path
  $path = '/images/prof_images'; //with trailing slash
  //Set max file size in bytes
  $max_size = 1000000;
  //Set default file extension whitelist
  $whitelist_ext = array('jpg','png','gif');
  //Set default file type whitelist
  $whitelist_type = array('image/jpeg', 'image/png','image/gif');

  //The Validation
  // Create an array to hold any output
  $out = array('error'=>null);
               
  if (!$file_field) {
    $out['error'][] = "Please specify a valid form field name";           
  }

  if (!$path) {
    $out['error'][] = "Please specify a valid upload path";               
  }
       
  if (count($out['error'])>0) {
    return $out;
  }

  //Make sure that there is a file
  if((!empty($_FILES[$file_field])) && ($_FILES[$file_field]['error'] == 0)) {
         
    // Get filename
    $file_info = pathinfo($_FILES[$file_field]['name']);
    $name = $file_info['filename'];
    $ext = $file_info['extension'];
               
    //Check file has the right extension           
    if (!in_array($ext, $whitelist_ext)) {
      $out['error'][] = "Invalid file Extension";
    }
               
    //Check that the file is of the right type
    if (!in_array($_FILES[$file_field]["type"], $whitelist_type)) {
      $out['error'][] = "Invalid file Type";
    }
               
    //Check that the file is not too big
    if ($_FILES[$file_field]["size"] > $max_size) {
      $out['error'][] = "File is too big";
    }
               
    //If $check image is set as true
    if ($check_image) {
      if (!getimagesize($_FILES[$file_field]['tmp_name'])) {
        $out['error'][] = "Uploaded file is not a valid image";
      }
    }

    //Create full filename including path
    if ($random_name) {
      // Generate random filename
      $tmp = str_replace(array('.',' '), array('',''), microtime());
                       
      if (!$tmp || $tmp == '') {
        $out['error'][] = "File must have a name";
      }     
      $newname = $tmp.'.'.$ext;                                
    } else {
        $newname = $name.'.'.$ext;
    }
               
    //Check if file already exists on server
    if (file_exists($path.$newname)) {
      $out['error'][] = "A file with this name already exists";
    }

    if (count($out['error'])>0) {
      //The file has not correctly validated
      return $out;
    } 

    if (move_uploaded_file($_FILES[$file_field]['tmp_name'], $path.$newname)) {
      //Success
      $out['filepath'] = $path;
      $out['filename'] = $newname;
      return $out;
    } else {
      $out['error'][] = "Server Error!";
    }
         
  } else {
    $out['error'][] = "No file uploaded";
    return $out;
  }      
}


Public function fixcomments(){
					$a = $this->pagination->setSQL( "SELECT * FROM comments" );
					$array = $this->pagination->getSQL('All');
					
					$tagcount = 0;
					foreach ($array as $a){
				$db = $this->pagination->connection('','set'); $sth = $db->prepare("UPDATE quotes SET `comments` = comments + 1 WHERE ID='{$a['quote_id']}'");
			if($sth->execute()){echo '<br>Done for '.$a['quote_id'];}
					}	
}	



Public function fixNames(){
					$a = $this->pagination->setSQL( "SELECT * FROM quotes where `Name` = 'unknown' OR `Name` = 'Unknown' " );
					$array = $this->pagination->getSQL('All');
					
					$tagcount = 0;
					foreach ($array as $a){
				$db = $this->pagination->connection('','set'); $sth = $db->prepare("UPDATE quotes SET `Name` = 'anonymous' WHERE ID='{$a['ID']}'");
			if($sth->execute()){echo '<br>Done for '.$a['ID'];}
					}	
}	



Public function fixTags($id='unset'){
	if ($id == 'unset'){$a = $this->pagination->setSQL( "SELECT * FROM quotes"); $quote_array = $this->pagination->getSQL('All');}
	else {	$quote_array = $this->pagination->connection("SELECT * FROM quotes WHERE ID=$id");}
foreach ($quote_array as $a){ 
					if (strlen(trim($a['Tags'])) != 0){
						$tags = $a['Tags'];
						$gathered = strtolower($tags); //as commaed
					    $gathered = array();
						$gathered =  explode(',',$tags);
						$count = count($gathered); 
					}
					else { $gathered = array(); $count = 0;}
					include('store/array_tags.php');
					foreach( $array_tags as $rt ) {
					$related = strtolower($rt['related']);
					$pquote = strtolower($a['Quote']);
					$r_array = explode(",",$related);
					
				foreach( $r_array as $tf ) {
						if ($this->if_word_present($pquote,$tf) && !in_array(strtolower($rt['tags']),$gathered)){ 
						$count++;
						$gathered["$count"]= strtolower($rt['tags']); //entering the lower case form of the tags
						}
					}
					
					}
					
		if (count($gathered)!= 0 ){$tags = implode(',',$gathered);}
		else {$tags = '';}
		
	
	$db = $this->pagination->connection('','set');	
		$sth = $db->prepare("UPDATE quotes SET nTags=:nt WHERE ID='{$a['ID']}'");
			$sth->bindValue (":nt", $tags);
			if($sth->execute()){if ($id == 'unset'){echo 'done('.$a['ID'].')<br/>';} else {return $tags;}}
}
		
}
						

	
Public Function comments(){
$site_address = $this->site_address;
			$this->pagination->setSQL( "SELECT * FROM `comments` WHERE  quote_id= '{$_GET['qid']}' ORDER BY time DESC");
			$comm = $this->pagination->getSQL('All');
			if( $this->pagination->getTotalOfResults() > 0    ) {
				echo '<div class="comment_list" ><div class="box-comments">';
				
				foreach( $comm as $c ) {
			 $user= $this->user->get_user($c['user']);
			 $fTime = $this->fDate($c['time']);
			 
		$c['comment'] = nl2br($c['comment']);
      		
		if (!empty(trim($user['image']))){$userIm = trim($user['image']);}
				else {$userIm = 'images/men.png';}
					echo '
                  <div class="box-comment"><img class="img-circle img-sm" src="http://'.$this->site_address.'/'.$userIm.'" alt="img">
                               <div class="comment-text">
                      <span class="username">
                         <a href="http://'.$this->site_address.'/myquotes/'.$user['id'].'">'.$user['firstname'].'</a>
                        <span class="text-muted pull-right">';
						echo $fTime;
							echo '</span>
                      </span>';
                          echo  $c['comment'] ; 
                            echo    '</div><!-- /.comment-text -->
                  </div><!-- /.box-comment -->';
				}
				echo '</div>';
				echo '</div>';
			}
}


Public Function adlist(){
print '<div class="adsec"></div>';
				}		
		
	
Public function lists(){
	echo '<div class="lt" role="tabpanel" data-example-id="togglable-tabs" style="position: fixed;left: 2.1%;top: 9%;width: 16.5%;font-size: 15px;"><ul class="nav nav-tabs bar_tabs cg" id="myTab" role="tablist">
                    <li role="presentation" class="active"><a href="#tab_content1" role="tab" data-toggle="tab" aria-expanded="true">Categories</a> </li>
		    <li role="presentation" ><a href="#tab_content2" role="tab" data-toggle="tab" aria-expanded="false">Authors</a></li>
                </ul>';
		echo '<div id="myTabContent" class="tab-content">';
                  echo '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1">';
echo $this->listout('categories');
echo '</div>';
      echo '<div role="tabpanel" class="tab-pane fade" id="tab_content2" >';
	  echo $this->listout('authors');
	  echo '</div>';
	    echo '</div>';
	echo '</div>';
}	


Public function addcomment (){
    
	$time = strtotime(date("Y-m-d H:i:s"));
	$db = $this->pagination->connection('','set');
	
$data 			= array(); 		// array to pass back data

// validate the variables ======================================================
	// if any of these variables don't exist, add an error to our $errors array

	if (empty($_POST['comment'])){
$errors         = array();  	// array to hold validation errors
		$errors['comment'] = 'Please Input Some Text ';
}
else 	if (@$_SESSION['comment_check'] == $_POST['comment']){
	$errors         = array();
	$errors['comment'] = 'You have already submitted that comment';
}
else if (!empty($_POST['urladdc'])){$errors['comment'] = 'Not So fast,Spammer';}
	
	// if there are any errors in our errors array, return a success boolean of false
	if (empty($errors)) {
		
		$comment = htmlspecialchars(trim($_POST['comment']));
		
    $sth = $db->prepare("INSERT INTO `comments` SET comment = :q,user = '{$_SESSION['email']}',time='$time',quote_id='{$_GET['qid']}'");
	$sth->bindValue (":q", $comment);
	
	if($sth->execute()){
		
			$d = $this->pagination->connection('','set'); $s = $db->prepare("UPDATE quotes SET `comments` = comments + 1 WHERE ID='{$_GET['qid']}'");
		     if($s->execute()) {         $this->concn('comm',$_GET['qid']); //to add to concern list
	$data['success'] = true;  $_SESSION['comment_check'] = $_POST['comment'];	$data['message'] = 'Your Comment has been Successfully Added';}
	
	
	}
	else {$data['success'] = false; $data['message'] = 'An error occured,Please try again';}
	}
	else {
     $data['success'] = false;
		$data['errors']  = $errors;
	}

	// return all our data to an AJAX call
	echo json_encode($data);
				}


Public function logconvert (){
if ($this->checkconvert($this->qid) == false){
		$time = strtotime(date("Y-m-d H:i:s"));
	$db = $this->pagination->connection('','set');
		$sth = $db->prepare("INSERT INTO `converts` SET quote_id = :q,bg = :bg,user = '{$_SESSION['email']}',time='$time'");
	$sth->bindValue (":q", $this->qid);
	$sth->bindValue (":bg", $this->bgi);
	if($sth->execute()){
              $s1 = $db->prepare("UPDATE `bg` SET `times` = times + 1 WHERE bg=:bgi");
			$s1->bindValue (":bgi", $this->bgi.'.jpg');
			$s1->execute();
             $s2 = $db->prepare("UPDATE quotes SET `converts` = converts + 1 WHERE ID='$this->qid'"); //load quote table with counts
		     $s2->execute();
			 return true;
			}
}
				}



Public function popbg(){
$dirPath = "images/use";
if ( !( $handle = opendir( $dirPath ) ) ) die( "Cannot open the directory." );
while ( $file = readdir( $handle ) ) {
if ( $file != "." && $file != ".." ){
$this->pagination->setSQL( "SELECT id FROM `bg` where `bg` = '$file'" );
if ($this->pagination->CheckRes() == 0 ){
$sth = $this->db->prepare("INSERT INTO `bg` SET bg = '$file',times='0'");
if ($sth->execute()){echo '<b>updated '.$file.'</b><br/>';}
}
}

}
closedir( $handle );
				}

Public function checkconvert($id){
		  $this->pagination->setSQL("SELECT id FROM `converts` where `quote_id` = '$id' AND `user` ='{$_SESSION['email']}'");
			 $this->pagination->setPaginator( 'page' );
			 $results = $this->pagination->getSQL();
			if ($this->pagination->CheckRes() > 0 ){return true;}
			else { return false;}
}


Public function takeutc(){
	if (empty($_SESSION['timeDiff'])){
		    $_SESSION['user_utc'] = trim($_POST['x']);
			$ServerTime = strtotime(date("Y-m-d H:i:s"));
			$_SESSION['timeDiff'] =  $_SESSION['user_utc'] - $ServerTime;
	}
	}

Public function addquote (){
    
	$time = strtotime(date("Y-m-d H:i:s"));
	$db = $this->pagination->connection('','set');
	
$errors         = array();  	// array to hold validation errors
$data 			= array(); 		// array to pass back data

// validate the variables ======================================================
	// if any of these variables don't exist, add an error to our $errors array

	if (empty($_POST['quote']))
	{$errors['quote'] = 'Please Input Some Text ';}

	if (!empty($_POST['quote']))
	{    

$_POST['quote'] = trim($_POST['quote']);
		if (strlen($_POST['quote']) < 20){
		$errors['quote'] = 'Text too small';}
	 else if (@$_SESSION['quote_check'] == $_POST['quote']){
	$errors         = array();
	$errors['quote'] = 'You have already quoted that';
}else if (!empty($_POST['urladd'])){$errors['quote'] = 'Not so fast,spammers';}


}
	// if there are any errors in our errors array, return a success boolean of false
	if (empty($errors)) {
		
	//Getting Tags	
/*** To loop through TAGS ****/
					include('store/array_tags.php');
					foreach( $array_tags as $rt ) {
					$related = strtolower($rt['related']);
					$pquote = strtolower($_POST['quote']);
					$r_array = explode(",",$related);
					$count = 0;
				$gathered = array();
				foreach( $r_array as $tf ) {
						if ($this->if_word_present($pquote,$tf) && !in_array(strtolower($rt['tags']),$gathered)){ 
						if (empty($in)){$tags = $rt['tags'].','; $in= 'set';}
					    else {$tags .= $rt['tags'].','; }
						$count++;
						$gathered["$count"]= $rt['tags'];
						}
					}
					
					}
		if (!empty($tags)){$tags = substr_replace($tags,'',-1,1);}
		else {$tags = '';}

if (@$_POST['a'] == 'yes'){$n = $this->without_author;}
else {$n = $this->user_d['firstname'];}


    $sth = $db->prepare("INSERT INTO `quotes` SET Quote = :q,AddedBy = '{$_SESSION['email']}',name=:n,Date='$time',Tags=:t,concn='{$_SESSION['email']}'");
	$sth->bindValue (":q", $_POST['quote']);
	$sth->bindValue (":n", $n);
	$sth->bindValue (":t", $tags);
	if($sth->execute()){$lid = $db->lastInsertId();


	$qurl = $this->siteurlpre.$this->site_address.'/'.$this->getquoteurl($lid);
	$data['success'] = true; $_SESSION['quote_check'] = $_POST['quote'];	$data['message'] = 'Your Quote has been Successfully Added<br/> You can view it <a style="color: #fff;background: #13779D;" href="'.$qurl.'">HERE</a>';
	}
	
	else {$data['success'] = false; $data['message'] = 'An error occured,Please try again';}
	}
	else {
     $data['success'] = false;
		$data['errors']  = $errors;
	}

	// return all our data to an AJAX call
	echo json_encode($data);
				}			

Public function addbio (){
    
	$db = $this->pagination->connection('','set');
	
$errors         = array();  	// array to hold validation errors
$data 			= array(); 		// array to pass back data

// validate the variables ======================================================
	// if any of these variables don't exist, add an error to our $errors array

	if (empty(trim($_POST['bio'])))
	{$errors['bio'] = 'Please Input Some Text ';}

	if (!empty($_POST['bio']))
	{  $_POST['bio'] = htmlspecialchars(trim($_POST['bio']));
		if (strlen($_POST['bio']) > 400){
		$errors['bio'] = 'Only 400 characters are allowed';}
else if (!empty($_POST['urlbio'])){$errors['bio'] = 'Not so fast,spammers';}

}
	// if there are any errors in our errors array, return a success boolean of false
	if (empty($errors)) {
$sth = $db->prepare("UPDATE members SET bio=:bio WHERE email='{$_SESSION['email']}'");
			$sth->bindValue (":bio", $_POST['bio']);
	if($sth->execute()){


	$data['success'] = true; $data['message'] = 'Your Biography has been succesfully updated';
	}
	
	else {$data['success'] = false; $data['message'] = 'An error occured,Please try again';}
	}
	else {
     $data['success'] = false;
		$data['errors']  = $errors;
	}

	// return all our data to an AJAX call
	echo json_encode($data);
				}			

Public function validateEmail($email) {
		$regex = '/([a-z0-9_]+|[a-z0-9_]+\.[a-z0-9_]+)@(([a-z0-9]|[a-z0-9]+\.[a-z0-9]+)+\.([a-z]{2,4}))/i';
		return preg_match($regex, $email);
	}

	//This generates sitemap 
	Public Function sitemap($sitehost='unset')
	{
		$url_pre = $this->siteurlpre;
		if ($sitehost == 'unset'){$sitehost = $this->site_address;}
		$head = <<<EOD
		<?xml version="1.0" encoding="UTF-8"?>
		<urlset
		xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
		http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
		<!-- created from www.quotehood.com-->
EOD;

$freq = 'Monthly';

$sitehomepage = "quotehood.com";

$foot = '</urlset>';




				//for different names
$pagination_2 = new pdo_pagination;
$pagination_2->setsql( "select * from tags" );
	//input numbers or string 'all' to denote all results		
		//input numbers or string 'all' to denote all results
		$results_4 = $pagination_2->getsql('All'); 
		if( $pagination_2->gettotalofresults() > 0    ) {
			foreach( $results_4 as $r_4 ) {
			
			$r_4['related'] = trim($r_4['related']);
			$tagArray = explode(',',$r_4['related']);
			  foreach ($tagArray as $tagEach)
			  {
				  
				  
				  				$tags_c = $this->code($tagEach,'up');
					if ($r_4['related'] != ''){
$send1 .= <<<eod
<url>
	<loc>$this->siteurlpre$sitehomepage/categories/quotes-about-$tags_c</loc>
	<changefreq>$freq</changefreq>
</url>

eod;
				  
			  }


		

					}
					
					}}		
					
$handle1 = fopen( "sitemap4.xml", "w" );
fwrite( $handle1, $send1 );


		//for different tags
$pagination = new pdo_pagination;
$pagination->setsql( "select * from quotes GROUP BY Tags ORDER BY Tags ASC" );
	//input numbers or string 'all' to denote all results
$results = $pagination->getsql('All'); 
		if( $pagination->gettotalofresults() > 0    ) {
			foreach( $results as $r ) {
				$r['Tags'] = trim($r['Tags']);
				$tags_c = $this->code($r['Tags'],'up');
				if ($r['Tags'] != '')
				{
						$send2 .= <<<eod
<url>
	<loc>$this->siteurlpre$sitehomepage/categories/quotes-about-$tags_c</loc>
	<changefreq>$freq</changefreq>
</url>

eod;
				}
				
				}}
		

				$handle2 = fopen( "sitemap1.xml", "w" );
fwrite( $handle2, $send2 );
			
				

				//for different names
$pagination_2 = new pdo_pagination;
$pagination_2->setsql( "select * from quotes GROUP BY Name ORDER BY Name ASC" );
	//input numbers or string 'all' to denote all results		
		//input numbers or string 'all' to denote all results
		$results_2 = $pagination_2->getsql('All'); 
		if( $pagination_2->gettotalofresults() > 0    ) {
			foreach( $results_2 as $r_2 ) {
			
			$r_2['Name'] = trim($r_2['Name']);
					$name_c = $this->code($r_2['Name'],'up');
					if ($r_2['Name'] != ''){
$send3 .= <<<eod
<url>
	<loc>$this->siteurlpre$sitehomepage/author/$name_c-quotes</loc>
	<changefreq>$freq</changefreq>
</url>

eod;

		

					}
					
					}}					

				$handle3 = fopen( "sitemap2.xml", "w" );
fwrite( $handle3, $send3 );
	
		$pagination_3 = new pdo_pagination;
		$pagination_3->setsql( "select * from quotes" );
		//input numbers or string 'all' to denote all results
		$results = $pagination_3->getsql('All'); 
		foreach( $results as $r ) { 
		
		//this line helps to make up up for quotes without author in database
		$r['Name'] = trim($r['Name']); 
		if ($r['Name'] == ''){ $r['Name'] = $this->without_author;}
	
$quote = $this->shorten_words($r['Quote'],$this->limit_url_words); 
$author = $this->shorten_words($r['Name'],$this->limit_url_words);
	 
	$quote_url = $this->code($quote,'up');
	$author_url = $this->code($author,'up');
	$qid_url = $author_url.'-quotes-'.$r['ID'].'/'.$quote_url;

			$send4 .= <<<eod
			<url>
	<loc>$this->siteurlpre$sitehomepage/$qid_url</loc>
	<changefreq>$freq</changefreq>
</url>
			
eod;

}
	
	$handle4 = fopen( "sitemap3.xml", "w" );
fwrite( $handle4, $send4 );	
}

Public function checkEmail($email){
$this->pagination->setSQL( "SELECT * FROM members where email = '$email'" );
			 $results = $this->pagination->getSQL();
			if ($this->pagination->getTotalOfResults() > 0 ){
			return false;}
			else {return true;}
}

Public function confirmReg(){
	if ($this->user->is_logged_in() == true){ header("Location: http://$this->site_address");
	exit();}
	$email = trim($this->decrypt(trim($_GET['c'])));
$this->pagination->setSQL( "SELECT * FROM members WHERE email = '$email'" );
			 $results = $this->pagination->getSQL();
			 echo '<b style="background: #000;color: #fff;">'.$this->pagination->getTotalOfResults().'</b>';
			if ($this->pagination->getTotalOfResults() > 0 )
			{
			$db = $this->pagination->connection('','set');
			$sth = $db->prepare("UPDATE members SET confirmation=:c WHERE email='$email'");
			$sth->bindValue (":c", '1');
			if($sth->execute()){return true;}
			else {return false;}
            }
}



Public function reg($type='json'){	
$errors         = array();  	// array to hold validation errors
$data 			= array(); 		// array to pass back data
	
	if (empty($_POST["url"])) {
	
    foreach($_POST as $a){
	trim($a);}
		
			
			
			
		// Check if password has been entered
		if (empty($_POST['pw'])){
			$errors['pw'] =  'Please enter your password';
		}
		
		
	
		// Check if email has been entered and is valid
		if (empty($_POST['em']) || !$this->validateEmail($_POST['em'])) {
			$errors['em'] =  'Please enter a valid email address';
		}
		// Check if email has been entered and is valid
		else {$_POST['em'] = strtolower($_POST['em']); 	 if ($this->checkEmail($_POST['em']) == false) {
			$errors['em'] =  'Email Address Already In Use<br/><br/> <b><u>Please Log In or Use another Email Address</u></b>';
		}}
	
		
		// Check if name has been entered
		if (empty($_POST['fn'])) {
			$errors['fn'] = 'Please enter your first name';
		}
		
		
	
	}
	else {$errors['fn'] = 'Cmon that\'s bad';}
  
		
	
		
		// If there are no errors, send the email
if (empty($errors)) {	

$db = $this->pagination->connection('','set');

$c_hash = $this->encrypt(trim($_POST['em']));

// construct the email
$Email = new Email();
$Email->sender = 'Quotehood.Com <no-reply@quotehood.com>';
$Email->recipient = $_POST['fn'].' <'.$_POST['em'].'>';
$Email->subject = "Confirm Your Registration-Quotehood";
$Email->message_text = "Hello!

Thanks for registering on QuoteHood 

Please Kindly click on the link below to confirm your registration

http://quotehood.com/confirm.php?c=$c_hash
";

$Email->message_html = "<h1>Hello!</h1>
<p>
Thanks for registering on <a href=\"http://quotehood.com\">QuoteHood</a>
</p>
<p>
Please Kindly click on <a href=\"http://quotehood.com/confirm.php?c=$c_hash\">HERE</a>  to confirm your registration
</p><br/>";
 
 $pw = $_POST['pw'];
 
	$sth = $db->prepare("INSERT INTO `members` SET firstname = :fn,email = :em,password = :pw,date= :time");
	if ($sth->execute(array(
					':fn' => $_POST['fn'],
					':em' => $_POST['em'],
					':pw' => $pw,
					':time' => date('Y-m-d H:i:s')
				)) //&& $this->courier->send($Email) == true
				)
				{
							 $data['success'] = true;
		$data['message'] ='<b>Registration successful</b><br/>Please confirm your registration via the email we just sent to your inbox @ <u>'.$_POST['em'].'</u>'.'<br/>'.$c_hash.'<br/>'.$this->decrypt($c_hash);
		
		
		 unset ($_SESSION['error']); unset ($_SESSION['refer_back']);
		 
		 
	} else {
		
		$data['message'] = '<div class="alert alert-danger">Sorry there was an error sending your message. Please try again.</div>'.$c_hash.'<br/>'.$this->decrypt($c_hash); unset ($_SESSION['error']); unset ($_SESSION['refer_back']);
		
		 $data['success'] = false;
		$data['errors']  = $errors;
		}
}

else {$data['success'] = false;	$data['errors']  = $errors;
	}


	// return all our data to an AJAX call
if ($type=='json'){echo json_encode($data);}

	}
	
	
	Public function restrict(){
		
		if ($this->user->is_logged_in() == false){ $_SESSION['error'] = 'You have to be logged in to view this page,<br/>Please <u>log in</u> or <u>register</u> below'; header("Location: login.html"); exit;}
		
	}
	
	Public function login(){
	
	if (isset($_POST["login"]) && empty($_POST["url2"])) {
	
	$_POST['em'] = strtolower($_POST['em']);
    
	foreach($_POST as $a){trim($a);}

	
        //Initialise $err
		$err = array();
	
		// Check if email has been entered and is valid
		if (!$_POST['em'] || !$this->validateEmail($_POST['em'])) {
			$err['em'] = 'Please enter a valid email address';
		}	
		
			
		// Check if password has been entered
		if (!$_POST['pw']) {
			$err['pw'] = 'Please enter your password';
		}
		
$res = array();
		// If there are no errors, send the email
if (count($err) == 0) 
{	
if ($this->user->in_database($_POST['em'],$_POST['pw']) == true && $this->user->confirmed($_POST['em'],$_POST['pw']) == true){$this->user->login($_POST['em'],$_POST['pw']);} 
else if ($this->user->in_database($_POST['em'],$_POST['pw']) == true && $this->user->confirmed($_POST['em'],$_POST['pw']) == false){$result = '<div class="alert alert-danger">Please Confirm Your Account first<br/><i>Check your email inbox or spam/junk folder to click the confirmation link</i></div>';}
else {$result='<div class="alert alert-danger"> Incorrect Email or Address, please Try again</div>';}
}

   if (!empty($err)){$res['err'] = $err;}
   if (!empty($result)){$res['result'] = $result;}
	
	return $res;
	if (!empty($_POST['pw'])){unset($_POST['pw'] );} //To avoid unusual and insecure reoccurence of the password input
	
}
}


	Public function interests_form(){
	if (empty($_POST["urli"])) {
			
        //Initialise $err
		$err = array();	

	foreach($_POST as $a){trim($a);}
	// Check if password has been entered
		if (empty($_POST['int'])) {
			$err['int'] = 'Please update your Interests,It\'s mandatory';
		}
		else {$_POST['int'] = strtolower($_POST['int']);}
    
		
$res = array();
		// If there are no errors, send the email
if (count($err) == 0) 
{
$db = $this->pagination->connection('','set');

$user = $this->user->get_user($_SESSION['email']);

$sth = $db->prepare("UPDATE members SET interests=:int WHERE email='{$user['email']}'");
			$sth->bindValue (":int", $_POST['int']);
			
			if ($sth->execute()){
				
				
				$result = '<div class="alert alert-success alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <strong>Goodluck!</strong> Your Interest has been updated
                                </div>';
								
								$_SESSION['update_error'] = '<span class="alert alert-success">'.$_SESSION['update_error'].'</span>';
								
								header("Location: index.html");  exit;
								
								
								}
								
								
			
			}

   if (!empty($err)){$res['err'] = $err;}
   if (!empty($result)){$res['result'] = $result;}
	
	return $res;

}
}






	Public function reset_password(){
	if (isset($_POST["rpass"]) && empty($_POST["url4"])) {
	
	$_POST['pass'] = strtolower($_POST['pass']);
	$_POST['vpass'] = strtolower($_POST['vpass']);
    
	foreach($_POST as $a){trim($a);}

	
        //Initialise $err
		$err = array();	
			
		// Check if password has been entered
		if (!$_POST['pass']) {
			$err['pass'] = 'Please enter your password';
		}
		else if (!$_POST['vpass']) {
			$err['vpass'] = 'Please verify your password';
		}
		else if (!empty($_POST['pass']) && !empty($_POST['vpass'])){
			if ($_POST['pass'] != $_POST['vpass']){ $err['notsame'] = 'Your passwords are not the same,try and input it again';  }
		}
		
		
$res = array();
		// If there are no errors, send the email
if (count($err) == 0) 
{	
$db = $this->pagination->connection('','set');

$user = $this->user->get_user($_SESSION['email']);

$sth = $db->prepare("UPDATE members SET password=:pw WHERE email='{$user['email']}'");
			$sth->bindValue (":pw", $_POST['vpass']);
			
			if ($sth->execute()){$result = '<div class="alert alert-success alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <strong>Goodluck!</strong> Your password has been successfully changed
                                </div>';}
			
			}

   if (!empty($err)){$res['err'] = $err;}
   if (!empty($result)){$res['result'] = $result;}
	
	return $res;

	if (!empty($_POST['pass'])){unset($_POST['pass']);} //To avoid unusual and insecure reoccurence of the password input
	if (!empty($_POST['vpass'])){unset($_POST['vpass']);} //To avoid unusual and insecure reoccurence of the password input
}
}

	Public function forgot_password(){
	
	if (isset($_POST["fpass"]) && empty($_POST["url3"])) {

	$_POST['em'] = strtolower($_POST['em']);
    
	foreach($_POST as $a){trim($a);}

	
        //Initialise $err
		$err = array();
	
		// Check if email has been entered and is valid
		if (!$_POST['em'] || !$this->validateEmail($_POST['em'])) {
			$err['em'] = 'Please enter a valid email address';
		}	
	else if (!empty($_POST['em']))
		{
		if  ($this->user->present($_POST['em']) == False){$err['conf']= 'This email is not present in our database,Please try and register again';}
		}
		
	
$res = array();
		// If there are no errors, send the email
if (count($err) == 0) {
	
	$user = $this->user->get_user($_POST['em']);
	$pass = $user['password'];
	
// construct the email
$Email = new Email();
$Email->sender = 'Quotehood.Com <no-reply@quotehood.com>';
$Email->recipient = $user['firstname'].' <'.$_POST['em'].'>';
$Email->subject = "Quotehood: Password Sent";
$Email->message_text = "This is your password: $pass
You can now head back and login by clicking the link below
http://quotehood.com/login.html
";

$Email->message_html = "<h1>Hello!</h1>
<p>This is your password: $pass </p>
<p>You can now head back and login <a href=\"http://quotehood.com/login.html\">HERE</a>
</p><br/>";
 
 if ($this->courier->send($Email) == true) {
 $result='<div class="alert alert-success"><b>Password successfully sent to your email address</b> @ '.$_POST['em'].'<br/>Check your email address and login.</div>'; }
}
   if (!empty($err)){$res['err'] = $err;}
   if (!empty($result)){$res['result'] = $result;}
	
	return $res;
}
}

}