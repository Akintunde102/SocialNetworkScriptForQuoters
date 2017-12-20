<?php
class user{

    private $db;
	
	
	function __construct($db){
		$this->_db = $db;
		global $site_address,$defcap;
	    $this->site_address = $site_address;
		$this->defcap  = $defcap;
	}

	public function is_logged_in(){
		if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
			return true;
		}		
		
		else false;
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
	
	
	Public function crypt($string,$action='en') {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = 'This is my secret key';
    $secret_iv = 'This is my secret iv';

    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if( $action == 'en' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'de' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}
	
	Public function setcookie($CookieName,$value,$days,$crypt='no'){
		      
			  if ($crypt == 'no'){$value = trim($value);}
	else if ($crypt == 'yes'){$value = $this->encrypt(trim($value));}
			  setcookie($CookieName,$value,time() + 60 * 60 * 24 * $days, "/", "");
	}
	
	
	Public function getcookie($name){
	if (!empty($_COOKIE[$name])){
	        $mr_cook = trim($_COOKIE[$name]);
	     return $this->decrypt($mr_cook);
        }
		else {return false;}
	}

	Public function unsetcookie($CookieName){
			  setcookie($CookieName,'',time() - 60 * 60 * 24, "/", "");
	}
	
	Public function capsule($defcap){
		
		if (!empty($_COOKIE["capsule"])){$value = $this->getcookie('capsule'); }//retrieve cookie 
		else {$value  = $defcap;}
		
		if (!empty($_GET['q'])){$value .= ','.$_GET['q'];}
			else if (!empty($_GET['names'])){$value .= ','.$_GET['names'];}
			else if (!empty($_GET['tags'])){$value .= ','.$_GET['tags'];}
			
			
		//if (!empty($value)){$value = substr_replace($value,'',-1,1);}
		//echo $value; exit;
			$enValue = $this->encrypt($value);
		    $this->setcookie('capsule',$enValue,365);
	}
	
	Public function autoLogin(){
		if (!empty($_COOKIE["remain1"]) && !empty($_COOKIE["remain2"]) && $this->is_logged_in() == false && empty($_GET['log']))
		{ 
			$email = $this->getcookie("remain1");  
			$password = $this->getcookie("remain2");
			/* 
			echo '<span style="background:black;">'.$email.'</span>';
			echo '<span style="background:black;">'.$password.'</span>';
			exit; */
			
			$this->login($email,$password);
			}
			else return false;
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
			
	public function get_user($where,$type='unset'){	
		try {
			if ($type == 'unset'){
			$stmt = $this->_db->prepare('SELECT * FROM members WHERE email = :email');
			$stmt->execute(array('email' => $where));}
			else if (strtolower($type) == 'id') {
			$stmt = $this->_db->prepare('SELECT * FROM members WHERE id = :id');
			$stmt->execute(array('id' => $where));	
			}
			
			$row = $stmt->fetch();
			
			
			$row['name'] = $this->shorten_words($row['firstname'],1);
			if (empty(trim($row['image']))){$row['image'] = 'images/men.png';}
			return $row;
			
			

		} catch(PDOException $e) {
		    echo '<p class="error">'.$e->getMessage().'</p>';
		}
	}

	public function checkUser($email){
		$stmt = $this->_db->prepare('SELECT firstname FROM members WHERE email = :email');
			$stmt->execute(array('email' => $email));
			$row = $stmt->fetch();
			if (!empty($row['firstname'])){return true;}
			else {return false;}
	}
	
	
	public function in_database($email,$password){
		$stmt = $this->_db->prepare('SELECT password FROM members WHERE email = :email');
			$stmt->execute(array('email' => $email));
			$row = $stmt->fetch();
			if ($row['password'] == $password){return true;}
			else {return false;}
	}
	
	
	public function confirmed($email,$password){
		$stmt = $this->_db->prepare('SELECT confirmation FROM members WHERE email = :email AND password = :pass');
			$stmt->execute(array('email' => $email,'pass' => $password));
			$row = $stmt->fetch();
			if ($row['confirmation'] == 1){return true;}
			else {return false;}
	}
	
	public function present($email){
		$stmt = $this->_db->prepare('SELECT * FROM members WHERE email = :email');
			$stmt->execute(array('email' => $email));
			$row = $stmt->fetch();
			if ($row >= 1){return true;}
			else {return false;}
	}
	
	public function login($email,$password){
		    $_SESSION['loggedin'] = true;
			$user = $this->get_user($email);
			
			 if ($_SESSION['loggedin'] == true) {
				 
				 $cookieValue1 = $this->encrypt($email);
				 $this->setcookie('remain1',$cookieValue1,365);
				 
				 $cookieValue2 = $this->encrypt($password);
				 $this->setcookie('remain2',$cookieValue2,365);
				 
				 $_SESSION['email'] = $user['email'];    
			
			If (empty($_SESSION['refer_back'])){$ReferBack = "http://$this->site_address";}
					else { $ReferBack = $_SESSION['refer_back'];}
					
					 
			 unset($_SESSION['refer_back']); //redirects if not logged in
			 
			  
			 unset($_SESSION['error']); //cleans error
			 header("Location: $ReferBack");  
			 
			 exit();
			 }
	}
	
	Public function restrict($a='none'){
		
		if ($this->is_logged_in() == false){
		if (!empty($_COOKIE['remain1'])){$_SESSION['error'] = 'You have to be logged in to view this page,<br/>Please <u>log in</u> or <u>register</u> below'; header("Location: login.html"); exit;}
		else {$_SESSION['error'] = 'You have to be logged in to view this page,<br/>Please <u>register</u> below'; header("Location: signup.html"); exit;}
		
		}
		
	}
		
	public function logout(){
		//Send interests  into capsule
		$capsule =  $this->getcookie('capsule');
		
			$user = $this->get_user($_SESSION['email']);
			$enValue = $this->encrypt($capsule.','.$user['interests']);
		    $this->setcookie('capsule',$enValue,365);
			
			
			unset( $_SESSION['loggedin']);
		unset( $_SESSION['email']);
			
		$this->unsetcookie('remain2'); //remove password cookie
		session_destroy();
	    header("Location: http://$this->site_address"); //redirects if not logged in 
	}
}
?>