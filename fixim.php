<?php include('inc.php');
$dirPath = "images/quoteimages";
if ( !( $handle = opendir( $dirPath ) ) ) die( "Cannot open the directory." );
while ( $file = readdir( $handle ) ) {
if ( $file != "." && $file != "..") {
$ImageName = $dirPath.'/'.$file;
$reta = $imdet->retrieve("$dirPath/$file");
if ($reta['source'] != 'Quotehood.com')
{ 
echo "<li>$file</li>"; 
			if (preg_match('/\_([0-9]+)\.jpg/i',$file,$res) == 1){
			echo $res[1];	
			}
					
			$quote_ret = $wp->getquote($res[1]);
			$details = array();
				
				$details['filename'] = $ImageName;
				$details['title'] = $quote_ret['Name'].' quote';
				$details['author'] = $quote_ret['Name'];
				$details['authorsposition'] = $quote_ret['Name'];
				$details['caption'] = $quote_ret['Name'].'-'.$quote_ret['Quote'];
				$details['captionwriter'] = 'Quotehood.com';
				$details['keywords'] = "quotes\nimagequote\nimage wallpaper\nqoutes\ninspirational quote\ninspirational qoute";
				$details['copyrightstatus'] = 'Copyrighted Work';
				$details['copyrightnotice'] = 'Copyright (c) Quotehood.com 2016';
				$details['ownerurl'] = 'http://quotehood.com';
				$details['category'] = 'quotes';
				$details['headline'] = $quote_ret['Quote'];
				$details['supplementalcategories'] = "sayings\nquotations";
				$details['date'] = '2016-03-29';
				$details['credit'] = $quote_ret['Name'].' Quotehood';
				$details['source'] = 'Quotehood.com';
				$imdet->edit($details);
				$akin = $imdet->retrieve($ImageName);
				var_dump($akin);
}
}
}
closedir( $handle );




$dirPath = "images/up_quotes";
if ( !( $handle = opendir( $dirPath ) ) ) die( "Cannot open the directory." );
while ( $file = readdir( $handle ) ) {
if ( $file != "." && $file != "..") {
$ImageName = $dirPath.'/'.$file;
$reta = $imdet->retrieve("$dirPath/$file");
if ($reta['source'] != 'Quotehood.com')
{ 
echo "<li>$file</li>"; 
			if (preg_match('/\_([0-9]+)\.jpg/i',$file,$res) == 1){
			echo $res[1];	
			
					
			$quote_ret = $wp->getquote($res[1]);
			$details = array();
				
				$details['filename'] = $ImageName;
				$details['title'] = $quote_ret['Name'].' quote';
				$details['author'] = $quote_ret['Name'];
				$details['authorsposition'] = $quote_ret['Name'];
				$details['caption'] = $quote_ret['Name'].'-'.$quote_ret['Quote'];
				$details['captionwriter'] = 'Quotehood.com';
				$details['keywords'] = "quotes\nimagequote\nimage wallpaper\nqoutes\ninspirational quote\ninspirational qoute";
				$details['copyrightstatus'] = 'Copyrighted Work';
				$details['copyrightnotice'] = 'Copyright (c) Quotehood.com 2016';
				$details['ownerurl'] = 'http://quotehood.com';
				$details['category'] = 'quotes';
				$details['headline'] = $quote_ret['Quote'];
				$details['supplementalcategories'] = "sayings\nquotations";
				$details['date'] = '2016-03-29';
				$details['credit'] = $quote_ret['Name'].' Quotehood';
				$details['source'] = 'Quotehood.com';
				$imdet->edit($details);
				$akin = $imdet->retrieve($ImageName);
				var_dump($akin);
}
}
}
}
closedir( $handle );