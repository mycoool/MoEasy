<?php
if( !defined('IN') ) die('bad request');
include_once( CROOT . 'controller' . DS . 'core.class.php' );

class userController extends coreController
{
	function __construct()
	{
		// 载入默认的
		parent::__construct();
	}
    
   	function index()
	{
		$data['title'] = $data['top_title'] = '用户中心';
		render( $data );
	}
    function login()
    {
       $data['title'] = $data['top_title'] = '登录';
	   render( $data ); 
    }
    
	// login check or something
	
	
}


?>