<?php
if( !defined('IN') ) die('bad request');
include_once( CROOT . 'controller' . DS . 'core.class.php' );

class caiController extends coreController
{
	function __construct()
	{
		// 载入默认的
		parent::__construct();
	}
    
   	function index()
	{   
        $url= 'http://weibo.com/jx/aj_morepics.php?class=3&ts=1342524607&page=2&_t=0&__rnd=1342524876359';
        $curl = curl_init($url);
        curl_setopt($curl,CURLOPT_HEADER,0);
        curl_setopt($curl,CURLOPOT_RETURNTRANSFER,1);
        curl_setopt($curl,CURLOPT_REFERER,"http://weibo.com/jx/pic.php?class=3");
        curl_setopt($curl,CURLOPT_COOKIEFILE,'coo');
        curl_setopt($curl,CURLOPT_PROXY,'weibo.com');
        curl_setopt($curl,CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Requested-With' => 'XMLHttpRequest')); 
        //echo file_get_contents('coo');
        $contents =  curl_exec($curl);
        echo $contents;
        //$mode= "#<title>(.*)</title>#";
        //preg_match($mode,$contents,$arr);
        //echo "arr[1]:".$arr[1];
        curl_close($curl);
        //render( $data );
	}
    function show()
    {
       $data['title'] = $data['top_title'] = '首页2';
       echo $_GET['_URL_'][2];
		render( $data ); 
    }
    
	// login check or something
	
	
}


?>