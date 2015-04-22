<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class MY_Controller extends CI_Controller
{

    var $queryStrArr = array();
   
   function __construct() {
    
        parent::__construct();
        
        $queryString = $_SERVER['QUERY_STRING'];
        
        if( !empty($queryString) ) {
            
            parse_str($queryString, $this->queryStrArr);
            array_walk($this->queryStrArr, function(&$item, $key) {
                $item = trim($this->security->xss_clean($item));
            });
        }
        
    }
    
}