<?php
if( !defined('IN') ) die('bad request');
include_once( CROOT . 'controller' . DS . 'core.class.php' );

class appController extends coreController
{
	function __construct()
	{
		// 载入默认的
		parent::__construct();
	}
    
   	function index()
	{
		$data['title'] = $data['top_title'] = '首页';
		render( $data );
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