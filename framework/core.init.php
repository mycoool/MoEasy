<?php

if( !defined('AROOT') ) die('NO AROOT!');
if( !defined('DS') ) define( 'DS' , DIRECTORY_SEPARATOR );

// define constant
define( 'IN' , true );

define( 'ROOT' , dirname( __FILE__ ) . DS );
define( 'CROOT' , ROOT . 'core' . DS  );

// define 
error_reporting(E_ALL^E_NOTICE);
ini_set( 'display_errors' , true );

include_once( CROOT . 'lib' . DS . 'core.function.php' );
@include_once( AROOT . 'lib' . DS . 'app.function.php' );

include_once( CROOT . 'config' .  DS . 'core.config.php' );
include_once( AROOT . 'config' . DS . 'app.config.php' );



$c = $GLOBALS['c'] = v('c') ? v('c') : c('default_controller');
$a = $GLOBALS['a'] = v('a') ? v('a') : c('default_action');

$c = basename(strtolower( z($c) ));
$a =  basename(strtolower( z($a) ));



if(!empty($_GET[C('VAR_PATHINFO')])) { // 判断URL里面是否有兼容模式参数
    $_SERVER['PATH_INFO']   = $_GET[C('VAR_PATHINFO')];
    unset($_GET[C('VAR_PATHINFO')]);
}


if(empty($_SERVER['PATH_INFO'])) {
    $types   =  explode(',',C('URL_PATHINFO_FETCH'));
    foreach ($types as $type){
        if(0===strpos($type,':')) {// 支持函数判断
            $_SERVER['PATH_INFO'] =   call_user_func(substr($type,1));
            break;
        }elseif(!empty($_SERVER[$type])) {
            $_SERVER['PATH_INFO'] = (0 === strpos($_SERVER[$type],$_SERVER['SCRIPT_NAME']))?
                substr($_SERVER[$type], strlen($_SERVER['SCRIPT_NAME']))   :  $_SERVER[$type];
            break;
        }
    }
}

$depr = C('URL_PATHINFO_DEPR');
if(!empty($_SERVER['PATH_INFO'])) {
    //tag('path_info');
    if(C('URL_HTML_SUFFIX')) {
        $_SERVER['PATH_INFO'] = preg_replace('/\.'.trim(C('URL_HTML_SUFFIX'),'.').'$/i', '', $_SERVER['PATH_INFO']);
    }
    if(1){   // 检测路由规则 如果没有则按默认规则调度URL
        $paths = explode($depr,trim($_SERVER['PATH_INFO'],'/'));
        if(C('VAR_URL_PARAMS')) {
            // 直接通过$_GET['_URL_'][1] $_GET['_URL_'][2] 获取URL参数 方便不用路由时参数获取
            $_GET[C('VAR_URL_PARAMS')]   =  $paths;
        }
        $var  =  array();
        if (C('APP_GROUP_LIST') && !isset($_GET[C('VAR_GROUP')])){
            $var[C('VAR_GROUP')] = in_array(strtolower($paths[0]),explode(',',strtolower(C('APP_GROUP_LIST'))))? array_shift($paths) : '';
            if(C('APP_GROUP_DENY') && in_array(strtolower($var[C('VAR_GROUP')]),explode(',',strtolower(C('APP_GROUP_DENY'))))) {
                // 禁止直接访问分组
                exit;
            }
        }
        if(!isset($_GET[C('VAR_MODULE')])) {// 还没有定义模块名称
            $var[C('VAR_MODULE')]  =   array_shift($paths);
        }
        $var[C('VAR_ACTION')]  =   array_shift($paths);
        // 解析剩余的URL参数
        $res = preg_replace('@(\w+)'.$depr.'([^'.$depr.'\/]+)@e', '$var[\'\\1\']=strip_tags(\'\\2\');', implode($depr,$paths));
        $_GET   =  array_merge($var,$_GET);
    }
    define('__INFO__',$_SERVER['PATH_INFO']);
}

define('MODULE_NAME',getModule(C('VAR_MODULE')));
define('ACTION_NAME',getAction(C('VAR_ACTION')));
// URL常量
define('__SELF__',strip_tags($_SERVER['REQUEST_URI']));
// 当前项目地址
define('__APP__',strip_tags(PHP_FILE));
// 当前模块和分组地址
$module = defined('P_MODULE_NAME')?P_MODULE_NAME:MODULE_NAME;
if(defined('GROUP_NAME')) {
    define('__GROUP__',(!empty($domainGroup) || strtolower(GROUP_NAME) == strtolower(C('DEFAULT_GROUP')) )?__APP__ : __APP__.'/'.GROUP_NAME);
    define('__URL__',!empty($domainModule)?__GROUP__.$depr : __GROUP__.$depr.$module);
}else{
    define('__URL__',!empty($domainModule)?__APP__.'/' : __APP__.'/'.$module);
}
// 当前操作地址
define('__ACTION__',__URL__.$depr.ACTION_NAME);
//保证$_REQUEST正常取值
$_REQUEST = array_merge($_POST,$_GET);

$c = $GLOBALS['c'] = MODULE_NAME ? MODULE_NAME : c('default_controller');
$a = $GLOBALS['a'] = ACTION_NAME ? ACTION_NAME : c('default_action');

$post_fix = '.class.php';

$cont_file = AROOT . 'controller'  . DS . $c . $post_fix;
$class_name = $c .'Controller' ; 
if( !file_exists( $cont_file ) )
{
	$cont_file = CROOT . 'controller' . DS . $c . $post_fix;
	if( !file_exists( $cont_file ) ) die('Can\'t find controller file - ' . $c . $post_fix );
} 

require_once( $cont_file );
if( !class_exists( $class_name ) ) die('Can\'t find class - '   .  $class_name );


$o = new $class_name;

if( !method_exists( $o , $a ) ) die('Can\'t find method - '   . $a . ' ');


if(strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)  ob_start("ob_gzhandler");


call_user_func( array( $o , $a ) );

