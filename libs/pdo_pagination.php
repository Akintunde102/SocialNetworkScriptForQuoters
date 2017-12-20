<?php
/**
 * This package can be used to display query results split in pages using PDO
 * 
 * It is based on my primary classe available on 
 * http://www.phpclasses.org/browse/file/40496.html
 * and since the most usage is been from countries outside Brazil
 * I've ported the class methods and properties names to English
 * 
 * It can have multiple connections by Depedency Injection 
 * and takes a SQL query and executes it once to retrieve 
 * the total number of rows that it would return.
 * It can generate HTML with links to go other pages of the the results listing, 
 * given the current listing page number and the limit of results to display per page.
 *
 * @category   Database Pagination
 * @package    PDO_Pagination
 * @author     Gilberto Albino <www@gilbertoalbino.com>
 * @copyright  2010-2012 Gilberto Albino
 * @license    Not applied
 * @version    Release: 2.0
 * @since      Class available since Release 1.2.0
 * @deprecated Class deprecated in Release 2.0.0
 */
class PDO_Pagination {

    /**
     * The PDO object to be used on every connection requested
     * @var PDO
     */
    private $_connection;
    
    /**
     * The string for identifying the pager value
     * Has to be from GET or POST
     * @var string 
     */
    private $_paginator = 'page';
      
    /**
     * The SQL string itself
     * @var string 
     */
    private $_sql;
	
	private $_numsql;
	
	
	private $_getTotalOfResults;
    
    /**
     * The total of results per age
     * @var type 
     */
    public $_limit_per_page = 10;
    
    /**
     * The total of pages in the menu 
     * @var type 
     */
    private $_range = 5;

    /**
     * The constructor gets the PDO conection object
     * 
     * @param PDO $connection 
     */
    public function __construct() 
    {
        global $site_address,$site_db_type,$site_db_host,$site_db_name,$site_db_user ,$site_db_password;
		$this->site_db_type = $site_db_type; //the database type
		$this->site_db_host = $site_db_host; //Database Host
		$this->site_db_name = $site_db_name;	
		$this->site_db_user  = $site_db_user ; //Database user
		$this->site_db_password = $site_db_password; //Database Password
		$this->site_address = $site_address;
		



//THis part is for author pagination formatting as it contains spaces

//for myquotes string only
if (preg_match('/\/myquotes\/([a-zA-Z0-9\-\_\+]{1,10})/i',$_SERVER['REQUEST_URI'],$res) == 1)
		{	
	if (preg_match('/\/myquotes\/([a-zA-Z0-9\-\_\+]{1,10})\/([a-zA-Z0-9\-\_\+]{1,10})/i',$_SERVER['REQUEST_URI'],$r) == 1){$res[1] = $r[1];}
	
				$this->site_address= $site_address.'/myquotes';
				 $this->page_address = $res[1]; $this->qtype = 'myquotes';
				 
					
		}
		
		
		if (preg_match('/\/([a-zA-Z]{5,30})\/([0-9]{1,10}\/)*([^|]+)/i',$_SERVER['REQUEST_URI'],$res) == 1)
		{
 if ($res[1] == 'author')
		{ 
		if (preg_match('/([^|]+)-quotes-([0-9]+)/',$res[3],$r)){$res[3] = "$r[1]-quotes";}
			$this->site_address= $site_address.'/author';
		    $this->page_address = $res[3]; $this->qtype = 'author';
}
		}

		if (preg_match('/\/([a-zA-Z]{5,30})\/([0-9]{1,10}\/)*([a-zA-Z0-9\-\_\+]+)/i',$_SERVER['REQUEST_URI'],$res) == 1)
		{  
		if ($res[1] == 'categories')
		{ 
			if (preg_match('/([0-9]+)-quotes-about-([a-zA-Z0-9_-]+)/',$res[3],$r)){$res[3] = "quotes-about-$r[2]";}
			$this->site_address= $site_address.'/categories'; 
			$this->page_address = $res[3]; 
			$this->qtype = 'categories';
			} 
		else if ($res[1] == 'search')
			{ 
				$this->SPS = $res[3];
				//$this->site_address= $site_address.'/search/';
				$this->site_address= $site_address;
				
				 $this->qtype = 'search';
			}		
		
		}
		
		else if (preg_match('/\/([a-zA-Z0-9_]+)\.html(\?q\=([^\?||\&]+))*/i',$_SERVER['REQUEST_URI'],$res) == 1)
		{
				if  (preg_match('/categories(_[0-9])*+/i',$_SERVER['REQUEST_URI'],$res[1]) == 1){ $this->page_address = 'categories'; $this->qtype = 'cat_list';}
				
				if ($res[0] == '/index.html') {$this->page_address = ''; $this->qtype = 'unset';}
			   
             //The code below catches the search url when without the seo or htacesss rewriting
			if (substr($res[0],0,12) == '/search.html' && !empty($_GET['q'])) 
			{
				$this->SPS = $res[3];
				$this->site_address= $site_address.'/search/';
				$this->site_address= $site_address;
				$this->qtype = 'search';
			}	
			
			 //The code below catches the search url when without the seo or htacesss rewriting
			if (substr($res[0],0,19) == '/notifications.html') 
			{
				$this->site_address= $site_address;
				$this->qtype = 'notifications';
			}
		}
		else if (preg_match('/\/author-list-([0-9]+)/i',$_SERVER['REQUEST_URI'],$res) == 1)
		{
			 $this->page_address = $res[1];
				$this->site_address= $site_address.'/author-list-';
				
			$this->qtype = 'author-list';
			
			
		}
		else if (preg_match('/\/category-list-([0-9]+)/i',$_SERVER['REQUEST_URI'],$res) == 1)
		{
			$this->page_address = $res[1];
			$this->site_address= $site_address.'/category-list-';
			
			$this->qtype = 'category-list';
			
			
		}
		else {$this->page_address = ''; $this->qtype = 'unset';}
    }
    
	public function connection($query,$justask='unset') 
    {
		
		/**
		 * The reason why I kept this conection variable outside the class
		 * is because you may need to inject multiple connections in the PDO_Pagination
		 * So it gets pretty easier for you 
		 * Feel free to use any connection logic you may wish!
		 */
		 
		try {
			/**
			 * The PDO String connection is for mysql
			 * Check the documentation for your database
			 // */
			$options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => FALSE,
			);
			$connection = new PDO(
			"$this->site_db_type:host=$this->site_db_host;dbname=$this->site_db_name", 
			$this->site_db_user, 
			$this->site_db_password,$options
			);  
			
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$connection->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
			$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			
			
			} catch ( PDOException $e ) {
			
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
			
		}
        $this->_connection = $connection;
        $this->setConnection($connection);
        $this->getPager();
		
		if (!empty($query) && $justask == 'unset')
		{ 
		$sth = $connection->query($query);
		$sth->execute();
		$row = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $row;}				
		
		else if ($justask != 'unset'){ return $connection;}
    }
	
	
	
   
/* This method gets the total of results found by querying the database
     * It can be accessed outside to check if no records were found
     * so that you can prevent some html code to be rendered
     * Note that we don't use getSQL but _sql property
     * It's because the getSEL returns the LIMIT and OFFSET
     * 
     * @return int
     */
    public function checkRes($query_num='unset') 
    {
	if ($query_num == 'unset'){
	if (substr_count( $this->_sql, "DESC" ) < 1){$query_num =  $this->_sql. ' LIMIT 1';}
	else {$query_num = str_replace('DESC', 'LIMIT 1', $this->_sql ); }
	if (substr_count( $this->_sql, "ASC" ) < 1){$query_num =  $this->_sql. ' LIMIT 1';}
	else {$query_num = str_replace('ASC', ' LIMIT 1', $this->_sql ); }
	}
			$array = $this->connection($query_num);
			$n = 0;
					foreach( $array as $rt ) {$n++;}
				$res= $n;	
      return $res;
    }
	
	
	
	
  public function getTotalOfResults() 
    {
		
		if ($this->_numsql != 'unset')
		{   $query_num = $this->_sql;
			$conn = $this->connection('','justask');
			$sth_num = $conn->query($query_num);
			$sth_num->execute();
			$res_all = $sth_num->fetchAll();			
			$res = count($res_all); 
		}
		else 
		{
			$query_num = str_replace( '*', 'COUNT(*)', $this->_sql );
			$conn = $this->connection('','justask');
			$sth_num = $conn->query($query_num);
			$sth_num->execute();
			$res = $sth_num->fetchColumn(); 
		}
      return (int) $res;
    }

	
	
    public function checkNext($query_num = 'unset') 
    {
		if (empty($_GET['page'])){$page = 1;}
		else {$page = $_GET['page'];}
		$offset = $page * $this->getLimitPerPage();
		
			if ($query_num == 'unset'){
	if (substr_count( $this->_sql, "DESC" ) < 1){$query_num =  $this->_sql. ' LIMIT 1 offset '.$offset;}
	 else {$query_num = str_replace('DESC', 'LIMIT  1 offset '.$offset, $this->_sql ); }
			
			  if (substr_count( $this->_sql, "ASC" ) < 1){$query_num =  $this->_sql. ' LIMIT 1 offset '.$offset;}
			 else {$query_num = str_replace('ASC', ' LIMIT 1 offset '.$offset, $this->_sql ); }
			}
			
			else {$query_num =  $query_num.' LIMIT 1 offset '.$offset;}
			$array = $this->connection($query_num);
			$n = 0;
					foreach( $array as $rt ) {$n++;}
				if ($n >=1){return true;}
				else {return false;}
    }
	
    /**
     * This method is a helper method for
     * validating the connection checking
     * if the passed variable is an object
     * instance of native PDO class
     * 
     * @see __contruct()
     * @param PDO $connection
     * @throws Exception 
     */
    private function setConnection( $connection ) 
    {

        if ( $connection instanceof PDO ) {
            $this->connection = $connection;
        } else {
            throw new Exception('<<THIS DEPENDENCY NEEDS A PDO OBJECT>>');
        }
        
    }
	
	
    
    /**
     * This method prints the result bar 
     * containg all the available information.
     * You can change the HTML inside the 
     * printf function to fit your needs 
     */
    public function printResultBar() 
    {         
        
        if( $this->getTotalOfResults() > 0 ) { 
           printf("
               <div id=\"result-bar\">
                Showing page <span>%s</span> of <span>
                %s</span> available pages for
                <span>%s</span> 
                results.         
               </div>
                "
               , $this->getCurrentPage()
               , $this->getTotalOfPages()
               , $this->getTotalOfResults()
           );
           
        } else { 
            print "<div id=\"result-bar-not-found\">
               No records were found to your search.</div>"; 
        }     
        
    } 
	
	public function printsearchresultbar() 
    {         
        
        if( $this->getTotalOfResults() > 0 ) { 
			printf("
			%s results in %s pages found
			"
			, $this->getTotalOfResults()
			, $this->getTotalOfPages()
			);
			
        } else { 
            print "<div id=\"result-bar-not-found\">
			No records were found to your search.</div>"; 
        }     
        
    } 
    
    public function printNavigationBar($checkNextsql = 'unset',$checkR = 'unset')  
    {   
	     $cNx = $this->checkNext($checkNextsql);
		 if ($checkR == 'unset'){$checkR = $this->checkRes();}
		
        if (empty($_GET['page'])){$current_page = 1;}
		else {$current_page = $_GET['page'];}
		
        if($checkR > 0) { 
		
           $a = "<div><ul class=\"pagination\">"; 
		   	$previous = $current_page - 1;
			$next = $current_page + 1;
	
            if ($this->qtype == 'categories')
			{ 
if ($current_page != 1){$a .= " <li><a href=http://". $this->site_address ."/". $previous ."-".$this->page_address.">←Previous</a> </li>"; }
				if ($this->checkNext($checkNextsql) == true) 			   
                {
					$a .= " <li><a href=http://". $this->site_address ."/" . $next  ."-". $this->page_address.">Next→</a></li> "; 
				}
			}
			
			
				else if ($this->qtype == 'author')
				{ 
					
					if ($current_page != 1){	$a .= " <li><a href=http://". $this->site_address ."/".$this->page_address."-".$previous. ">←Previous</a> </li>"; }
					
					if ( $cNx == true) 			   
                {
					$a .= " <li><a href=http://". $this->site_address."/".$this->page_address."-".$next. ">Next→</a></li> "; 
				}
				}
				else if ($this->qtype == 'search')
				{  
if ($current_page != 1){$a .= " <li><a href=http://". $this->site_address."/". "search.html?q=".$this->SPS."&page=".$previous.">←Previous</a></li> "; }
				
					if ( $cNx == true) 			   
                {
			$a .= " <li><a href=http://". $this->site_address."/"."search.html?q=".$this->SPS."&page=".$next.">Next→</a></li> "; 				
				}
}	
else if ($this->qtype == 'notifications')
				{  
if ($current_page != 1){$a .= " <li><a href=http://". $this->site_address."/". "notifications.html?page=".$previous.">←Previous</a></li> "; }
				
					if ( $cNx == true) 			   
                {
			$a .= " <li><a href=http://". $this->site_address."/"."notifications.html?page=".$next.">Next→</a></li> "; 				
				}
}
			else if ($this->qtype == 'myquotes')
				{  
                     
			
				if ($current_page != 1){	$a .= " <li><a href=http://". $this->site_address ."/".$this->page_address."/".$previous. ">←Previous</a> </li>"; }
					
					if ( $cNx == true) 			   
                {  
				$a .= " <li><a href=http://". $this->site_address."/".$this->page_address."/".$next. ">Next→</a></li> "; 
				}	
}

				else if ($this->qtype == 'cat_list')
				{ 
					if ($current_page != 1){$a .= " <li><a href=http://". $this->site_address."_".$this->page_address."-".$previous.">←Previous</a></li>"; }
							if ( $cNx == true) 			   
						{
							
							$a .= " <li><a href=http://". $this->site_address."_".$this->page_address."-".$next.">Next→</a> </li>"; 
						}	
							
				}
				
				
				else if ($this->qtype == 'author-list')
				{ 
				if ($current_page != 1){$a .= " <li><a href=http://". $this->site_address.$previous.">←Previous</a> </li>"; }
					
					if ( $cNx == true) 			   
					{
						
						$a .= " <li><a href=http://". $this->site_address.$next.">Next→</a> </li>"; 
					}	
					
				}
				
				else if ($this->qtype == 'category-list')
				{ 
					
					$a .= " <li><a href=http://". $this->site_address.$previous.">←Previous</a> </li>"; 
					
					if ( $cNx == true) 			   
					{
						
						$a .= " <li><a href=http://". $this->site_address.$next.">Next→</a> </li>"; 
					}	
					
				}
				else
				{if ($current_page != 1){
					$a .= " <li><a href=http://". $this->site_address ."/".$this->page_address.'page_'.$previous. ">←Previous</a> </li>"; 
				}
					if ( $cNx == true) 			   
					{				
					$a .= " <li><a href=http://". $this->site_address."/".$this->page_address.'page_'.$next. ">Next→</a></li> "; 
					}
				}
			
           
               
}
             $a .= '</ul>';
            $a .= '</div>';             
           
     
		
		return $a;
    }     
    
    /**
     * This method returns the total number of pages
     * @return integer 
     */
    public function getTotalOfPages() 
    {        
        
        return ceil( $this->getTotalOfResults() / $this->getLimitPerPage() );
        
    } 

    /**
     * This method returns the number of the current page
     * 
     * @return int 
     */
    public function getCurrentPage() 
    { 
	/*
        $total_of_pages = $this->getTotalOfPages();
        $pager = $this->getPager();
        
        if ( isset( $pager ) && is_numeric( $pager ) ) {          
            $currentPage = $pager; 
        } else { 
            $currentPage = 1; 
        } 

        if ( $currentPage > $total_of_pages ) { 
            $currentPage = $total_of_pages; 
        } 

        if ($currentPage < 1) { 
            $currentPage = 1; 
        } 
		*/
        
		if (empty($_GET['page'])){$currentPage = 1;}
			else {$currentPage = $_GET['page'];}
        return (int) $currentPage; 
         
    } 
    
    /**
     * This method prepares the offset value for the sql() method
     * 
     * @return int
     */
    private function getOffset($limit='unset') 
    {      
       if ($limit != 'unset'){return  ($this->getCurrentPage() - 1 ) * $this->getLimitPerPage($limit);}
	   else {return  ( $this->getCurrentPage() - 1 ) * $this->getLimitPerPage(); } 
        
    } 
    
    /**
     * This method just validates if a string is passed 
     * 
     * @param string $string
     * @throws Exception 
     */
    public function setSQL( $string,$string_num='unset') 
    {
        if ( strlen( $string ) < 0 ) {
            throw new Exception( "<<THE QUERY NEEDS A SQL STRING>>" );
        } 
        
        $this->_sql = $string;
		
		
		$this->_numsql = $string_num;
       
    }

    /**
     * This method returns the SQL string
     * 
     * @return string
     */
    public function getSQL($limit='unset',$num='unset',$pageless='unset') 
    {
		if ($limit != 'unset'){$limit_per_page = $limit;}
		else {$limit_per_page = $this->getLimitPerPage();}
        
        $offset = $this->getOffset($limit); 
		
		if ($num != 'unset')
	    {
		 if ($limit == 'All' || $limit == 'all' || $limit == 'ALL'){ $ret = $this->_sql;}
		 
		else 
		{
	$ret = str_replace( '{numspace}',  " LIMIT {$limit_per_page} OFFSET {$offset} ", $this->_numsql );			
		}
		}		
		else
		{
			if ($limit == 'All'){ $ret = $this->_sql;}
			else $ret = $this->_sql .  " LIMIT {$limit_per_page} OFFSET {$offset} ";
		}	
		if ($pageless != 'unset'){$ret = $this->_sql;}
		$results = $this->connection($ret);
		$this->bull = $ret; 
		
        return $results;
        
    }
    
    /**
     * This method sets the pager other
     * than the one passed in the class declaration 
     */
    public function setPaginator( $paginator ) 
    {
        
        if( !is_string( $paginator ) ) {
            throw new Exception("<<PAGINATOR MUST BE OF TYPE STRING>>");
        } 
        
        $this->_paginator = $paginator;
        
    }
    
    /**
     * This method returns the paginator used to get the pager
     * 
     * @return string 
     */
    private function getPaginator()
    {
        return $this->_paginator;
    }

    /**
     * This method returns the value to paginate
     * 
     * @return type 
     */
    public function getPager() 
    {
        
         return ( isset ( $_REQUEST["{$this->_paginator}"] ) )  
                ? (int) $_REQUEST["{$this->_paginator}"]  
                : 0 
        ;  
        
    }


    /**
     * This method sets the limit of pagination available on the page
     * 
     * @param int $limit
     * @return boolean
     * @throws Execption 
     */
    public function setLimitPerPage( $limit ) 
    {
        
        if( !is_int( $limit ) ) {
            throw new Execption( "<<THE LIMIT MUST BE AN INTEGER>>" );
        }
        
        $this->_limit_per_page = $limit;
        
        
    }

    /**
     * This method returns the availabe pagination limit per page
     * 
     * @return type 
     */
    public function getLimitPerPage() 
    {
        
        return $this->_limit_per_page;
        
    }

    /**
     * This method sets the range of pages to be selected
     * 
     * @param int $range
     * @throws Execption 
     */
    public function setRange( $range ) 
    {
        
        if( !is_int( $range ) ) {
            throw new Execption( "<<THE RANGE MUST BE AN INTEGER>>" );
        }
        
        $this->_range = $range;
        
    }

    /**
     * This method returns the range of pages to be selected
     * from start to end in the pagination menu bar
     * 
     * @return int
     */
    public function getRange() 
    {
        
        return $this->_range;
        
    }
    
    /**
     * This method rebuilds the query string.
     * It's refactored from some code I found on the internet
     * 
     * @param string $query_string
     * @return boolean|string 
     */
    public function rebuildQueryString ( $query_string ) 
    { 
        $old_query_string = $_SERVER['QUERY_STRING'];
        
        if ( strlen( $old_query_string ) > 0 ) { 
            
            $parts = explode("&", $old_query_string ); 
            $new_array = array();
            
            foreach ($parts as $val) { 
                if ( stristr( $val, $query_string ) == false)  { 
                    array_push( $new_array , $val ); 
                } 
            } 
            
            if ( count( $new_array ) != 0 ) { 
                $new_query_string = "&".implode( "&", $new_array ); 
            } else { 
                return false; 
            }
            
            return $new_query_string;
            
        } else { 
            return false; 
        } 
        
    }     

}