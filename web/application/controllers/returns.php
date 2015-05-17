<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Respect\Validation\Validator as v;

class Returns extends MY_Controller
{
    function __construct() {
        
        parent::__construct();
        $this->load->model('returns_m');
    }
    
    public function save() {
        
        if ( isset($_POST['returnDate'])) {
            // Got a form. Save collect 

            $dateTime   = DateTime::createFromFormat('d/m/Y H:i', $this->input->post('returnDate', true).' '.$this->input->post('returnTime', true))->format('Y-m-d H:i.s');
            $contractID = $this->input->post('contractID', true);
            $notes      = $this->input->post('returnNotes', true);
            $type       = $this->input->post('type', true);
            
            if ( v::date('Y-m-d H:i.s')->validate($dateTime)) {
                                               
                if ( isset($_POST['collectItemId'])) {
                    
                    $items = array();
                    for($i = 0; $i < count( $_POST['collectItemId']) ; $i++) {
                                            
                        if ( v::int()->min(0)->validate($_POST['collectItemId'][$i]) 
                            && v::int()->validate($_POST['returnedQty'][$i]) ) {
                            
                            if ( $_POST['returnedQty'][$i] > 0 ) {
                                array_push($items, array(
                                                        'collectItemId'=> $_POST['collectItemId'][$i],
                                                        'returnedQty'    => $_POST['returnedQty'][$i]
                                                        ));          
                            }                                                
                        }else {
                            
                            http_response_code(400);
                            echo "Please, verify you enter only numbers in the quantities fields";
                            return;
                        }
                    }
                }else {
       
                    echo "Nothing to save";
                    return;
                }
                              
                
                $param_arr = array(
                                    'contractID' => $contractID,
                                    'datetime'   => $dateTime,
                                    'notes'      => $notes,
                                    'items'      => $items
                                );
                
                if ( $type == 'hired') {
                    
                    $return =  $this->returns_m->saveHiredReturn($param_arr);
                }elseif ($type == 'sold') {
                    
                     $return =  $this->returns_m->saveSoldReturn($param_arr);
                }
                
                if ( $return ) {
                    
                    echo "ok";
                }else {
                    
                    echo "Please, reload the page and try again";
                }   
                
            }else {
                
                http_response_code(400);
                echo "Date and/or time wrong format";
            }                        
            
        }else {
                
            http_response_code(400);
            echo "Invalid form";
        } 
    }
    
    public function past() {  
        
    }

    public function view() {        
        
    }
    
}