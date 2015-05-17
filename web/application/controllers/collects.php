<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Respect\Validation\Validator as v;

class Collects extends MY_Controller
{
    function __construct() {
        
        parent::__construct();
        $this->load->model('collects_m');
    }
    
    public function collect() {
        
        if ( isset($_POST['collectDate'])) {
            // Got a form. Save collect 

            $dateTime   =  DateTime::createFromFormat('d/m/Y H:i', $this->input->post('collectDate', true).' '.$this->input->post('collectTime', true))->format('Y-m-d H:i.s');
            $contractID = $this->input->post('contractID', true);
            $notes = $this->input->post('collectNotes', true);
            
            if ( v::date('Y-m-d H:i.s')->validate($dateTime)) {
                                               
                if ( isset($_POST['contractItemID'])) {
                    $items = array();
                    for($i = 0; $i < count( $_POST['contractItemID']) ; $i++) {
                                            
                        if ( v::int()->validate($_POST['contractItemID'][$i]) 
                            && v::int()->validate($_POST['collectQty'][$i]) ) {
                            
                            if ( $_POST['collectQty'][$i] > 0 ) {
                                array_push($items, array(
                                                        'contractItemID'=> $_POST['contractItemID'][$i],
                                                        'collectQty'    => $_POST['collectQty'][$i]
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
                                
                if ( $this->collects_m->saveCollect($param_arr) ) {
                    echo "ok";
                }else {
                    
                    echo "Please, reload the page and try again";
                }   
                
            }else {
                
                http_response_code(400);
                echo "Date and/or time wrong format";
            }                        
            
        }else {
            // No form. Echo html of new collect form
            
            $contractID = intval($this->input->post('contractID', true));
        
            if ( v::int()->min(0)->validate($contractID)) {
        
                $this->load->model('contracts_m');
                    
                $data = array(
                                'contractID'    => $contractID,
                                'date'          => date('d/m/Y'),
                                'time'          => date('H:i'),
                                'items'         => $this->contracts_m->selectHiredSoldItemsWithBalance($contractID)
                            );
                
                echo $this->load->view('collects_new', $data);
            
            }else {
                
                http_response_code(400);
                echo "Bad request";
            }   
        }
    }
    
    public function past() {  
        
        $contractID = $this->input->get('contractID', true);
        
        if ( v::int()->min(0)->validate($contractID)) {
            $notes = $this->collects_m->selectPastNotes( $contractID );
            $data = array(
                            'notes' => $notes,
                            'notesCount' => count($notes)
                        );
            echo $this->load->view('collects_past', $data, true);
        }else {
                
                http_response_code(400);
                echo "Bad request";
        } 
    }

    public function view() {
        
        $collectID = $this->queryStrArr['id'];
        
        if ( v::int()->min(0)->validate($collectID) ) {
            
            $this->load->model('contracts_m');
            
            $collectDetails = $this->collects_m->selectCollectDetails($collectID);
            $data = array(
                            'collect_id'     => $collectID,
                            'collectDetails' => $collectDetails,
                            'contractDetails'=> $this->contracts_m->getContractDetails($collectDetails->fk_contract_id),
                            'items'          => $this->collects_m->selectNotesItems($collectID)
                        );
            
            $this->load->view('header_nav');
            $this->load->view('collects_view', $data);
            $this->load->view('footer_common');
            $this->load->view('footer_copyright');
            $this->load->view('footer');
        }
    }
    
}