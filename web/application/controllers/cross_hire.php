<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cross_hire extends CI_Controller
{
	public function abandon()
	{
		$this->load->model('cross_hire_m');
		$pk_id = trim($this->uri->segment(3));
		
		if( $pk_id != false && is_numeric($pk_id) )
		{
			if(!$this->cross_hire_m->abandon_order( $pk_id ))
			{
				echo "ko-db";
				return;
			}else
			{
				echo "ok";
			}
		}		
	}
	
	public function complete()
	{
		$this->load->model('cross_hire_m');
		$pk_id = trim($this->uri->segment(3));
		
		if( $pk_id != false && is_numeric($pk_id) )
		{
			if(!$this->cross_hire_m->activate_order( $pk_id ))
			{
				echo "ko-db";
				return;
			}else
			{
				echo "ok";
			}
		}		
	}
	
	public function edit_order()
	{
		$this->load->model('cross_hire_m');
		
		$pk_id = trim($this->uri->segment(3));
		if( $pk_id != false && is_numeric($pk_id) )
		{
			$data['order_id'] = $pk_id;
			$data['order_details'] = $this->cross_hire_m->get_order_details( $pk_id );			
			$data['items'] = $this->cross_hire_m->get_order_items( $pk_id );
			if($data['order_details'] != false) {
				$data['supplier_id'] = $data['order_details']->fk_supplier_id;			
						
			
			$this->load->view('header_nav');		
			$this->load->view('cross_hire_order_edit', $data);		
			$this->load->view('footer_common');
			$this->output->append_output("<script src=\"".base_url('assets/jquery-ui-1.11.4.custom/jquery-ui.js')."\"></script>");
			$this->output->append_output("<script src=\"".base_url('assets/js/cross_hire.js')."\"></script>");					
			$this->load->view('footer_copyright');
			$this->load->view('footer');
			}
		}			
	}
	
	public function existing_orders()
	{
		$this->load->model('cross_hire_m');
		
		$data['orders'] = $this->cross_hire_m->get_all_purchase_orders();
		
		$this->load->view('header_nav');		
		$this->load->view('footer_common');
		$this->load->view('cross_hire_orders_list_all', $data);
		//$this->output->append_output("<script src=\"".base_url('assets/js/purchase_orders_edit.js')."\"></script>");		
		$this->output->append_output("<script src=\"".base_url('assets/dhtmlx-4.13/codebase/dhtmlxgrid.js')."\"></script>");	
		$this->output->append_output("<script src=\"".base_url('assets/dhtmlx-4.13/sources/dhtmlxGrid/codebase/ext/dhtmlxgrid_filter.js')."\"></script>");	

		$this->output->append_output("<script>
		var grid = new dhtmlXGridObject('gridbox');
		grid.setHeader(\"On hire, Order No.,Description, Hired from, Status\");
		grid.setInitWidths(\"200,100,300,300,100\");
		grid.setColAlign(\"left,left, left,left,left\"); 
		grid.setColTypes(\"ro,ron,ro,ro,ro\"); 
		grid.setColSorting(\"str,str,str,str,str\");
		grid.attachHeader(\"&nbsp;,&nbsp;,#select_filter,#select_filter,#select_filter\");	 
		grid.init();
		grid.load(\"get_all_orders_json\",\"json\");		
		
		
		grid.getFilterElement(4)._filter = function(){
			var input = this.value; // gets the text of the filter input
			return function(value, id){
				var val=grid.cells(id,5).getValue();
				if (val == input){ 
						return true;
				}
				if (input == \"\")
					return true;
				
				return false;
			}
		};
		
		grid.attachEvent(\"onXLE\", function(grid_obj,count){
			$('#gridbox tr').dblclick(function(){
				location.href=\"".base_url('index.php/cross_hire/edit_order')."\/\"+$('td:first', $(this)).next().html();
			}).on('keyup',function(e){
				if(e.which() ==  13)
				{
					location.href=\"".base_url('index.php/cross_hire/cross_hire')."\/\"+$('td:first', $(this)).html();
				}
			});
		});
				
		</script>");		
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}
	
	public function new_order()
	{	
		// Views
		$this->load->view('header_nav');		
		$this->load->view('cross_hire_order_new');		
		$this->load->view('footer_common');
		$this->output->append_output("<script src=\"".base_url('assets/jquery-ui-1.11.4.custom/jquery-ui.js')."\"></script>");
		$this->output->append_output("<script src=\"".base_url('assets/js/cross_hire.js')."\"></script>");		
		$this->output->append_output("<script src=\"".base_url('assets/js/purchases.js')."\"></script>");		
		$this->load->view('footer_copyright');
		$this->load->view('footer');
	}	
	
	public function generate_order()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->helper('custom_validations');
		
		$config = array(
               array(
					'field'		=> 'supplier_pk_id',
					'label'		=> 'Supplier ID',
					'rules'		=> 'trim|required|xss_clean|integer'
			   ),
			   array(
					'field'		=> 'contact_email',
					'label'		=> 'Contact e-mail',
					'rules'		=> 'trim|xss_clean|valid_email|min_length[5]|max_length[200]'
			   ));

		$this->form_validation->set_rules($config);
		$this->form_validation->set_rules('delivery_address', 'Delivery Address', 'callback_shorttext_valid');
		$this->form_validation->set_rules('contact_name', 'Contact Name', 'callback_name_valid');
		$this->form_validation->set_rules('contact_telephone', 'Contact Telephone', 'callback_telephone_valid');

		$this->form_validation->set_message('required', 'The %s field is mandatory.');

		if ( !$this->form_validation->run() )
		{
			echo validation_errors();
		}
		else
		{ 
			$supplier_id		= $this->input->post('supplier_pk_id');
			$delivery_address	= trim($this->input->post('delivery_address', true));
			$contact_name		= trim($this->input->post('contact_name', true));
			$contact_phone		= trim($this->input->post('contact_telephone', true));
			$contact_email		= $this->input->post('contact_email');			
            $creation_date		= date('Y-m-d H:i:s');
			
			
			$vars_array = compact(
							"supplier_id",
							"delivery_address",
							"contact_name",
							"contact_phone",
							"contact_email",
							"creation_date"
								);
								
			$this->load->model('cross_hire_m');
			$result = $this->cross_hire_m->generate_order( $vars_array );
			if( $result != false )
			{
				redirect(base_url('index.php/cross_hire/edit_order/'.$result),'refresh');
				echo $result;						
				
			}else
			{
				
			}
		}
	}
	
	public function get_all_orders_json()
	{	
		$this->load->model('cross_hire_m');
		
		header('Content-type: application/json');
		if( ($results = $this->cross_hire_m->get_all_purchase_orders() ) != false )
		{	
			$rows = array();
			$data = array();
			$id = 1;
			foreach($results as $order)
			{	
				array_push( $data, array( 'id' => $id, "data" => array(date('d/m/Y - H:i', strtotime( $order->creation_date )), intval($order->pk_id), $order->description, $order->name, $order->status)));
				$id++;
			}		
			
			echo json_encode(array( "rows" => $data));
		}else
		{
			echo "[]";
		}
	}	
	
	public function print_outstanding_orders_pdf()
	{
		$this->load->library('tcpdf');
		$this->load->model('purchases_orders_m');
		
		$html = "";
		
		$data['orders'] = $this->purchases_orders_m->get_outstanding_orders();
		
		$html = $html.$this->load->view('reports/purchase_orders_outstanding_items_all_orders_header', $data, true);
		
		foreach( $data['orders'] as $o )
		{
			$data['order'] = $o;
			$data['items'] = $this->purchases_orders_m->get_order_items( $o->pk_id );			
			$html = $html.$this->load->view('reports/purchase_orders_outstanding_items_all_orders', $data, true);
		}
		// create new PDF document
		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		/*$pdf->SetAuthor('Nicola Asuni');
		$pdf->SetTitle('TCPDF Example 061');
		$pdf->SetSubject('TCPDF Tutorial');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

		// set default header data
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 061', PDF_HEADER_STRING);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('helvetica', '', 10);

		// add a page
		$pdf->AddPage();					

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');
		
		//Close and output PDF document
		$pdf->Output('outstanding_orders.pdf', 'I');
	}
	
	public function print_order_pdf()
	{
		$this->load->model('purchases_orders_m');
		$this->load->library('tcpdf');
		
		$pk_id = trim($this->uri->segment(3));
		if( $pk_id != false && is_numeric($pk_id) )
		{
			$data['order_id'] = $pk_id;
			$data['order_details'] = $this->purchases_orders_m->get_order_details( $pk_id );			
			$data['items'] = $this->purchases_orders_m->get_order_items( $pk_id );
			if($data['order_details'] != false) {
				$data['supplier_id'] = $data['order_details']->fk_supplier_id;									
			
			// create new PDF document
			$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			// set document information
			$pdf->SetCreator(PDF_CREATOR);
			/*$pdf->SetAuthor('Nicola Asuni');
			$pdf->SetTitle('TCPDF Example 061');
			$pdf->SetSubject('TCPDF Tutorial');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

			// set default header data
			//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 061', PDF_HEADER_STRING);

			// set header and footer fonts
			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

			// set default monospaced font
			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

			// set margins
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

			// set auto page breaks
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

			// set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			// set some language-dependent strings (optional)
			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
				require_once(dirname(__FILE__).'/lang/eng.php');
				$pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			// set font
			$pdf->SetFont('helvetica', '', 10);

			// add a page
			$pdf->AddPage();		
			
			//$out = $this->load->view('header_reports', '',true);	
			$out = $this->load->view('reports/purchase_order', $data, true);		
			//$out = $out.$this->load->view('footer_common', '',true);
			//$out = $out.$this->load->view('footer','', true);

			// output the HTML content
			$pdf->writeHTML($out, true, false, true, false, '');
			
			//Close and output PDF document
			$pdf->Output('example_061.pdf', 'I');
			
			//echo $out;
			
			//$this->output->append_output("<script src=\"".base_url('assets/jquery-ui-1.11.4.custom/jquery-ui.js')."\"></script>");
			//$this->output->append_output("<script src=\"".base_url('assets/js/purchase_orders_edit.js')."\"></script>");		
			//$this->load->view('footer_copyright');
			//$this->load->view('footer');
			}
		}			
	}
	
	public function receive_items()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$order_id = trim($this->input->post('order_id', true));
		$date = date('Y-m-d H:i:s');
		
		$all_items = array();
		for($i = 0; $i < count($_POST['qty_in_now']); $i++)
		{
			$purchase_order_item_id = trim($this->security->xss_clean($_POST["purchase_order_item_id"][$i]));			
			$qty = trim($this->security->xss_clean($_POST["qty_in_now"][$i]));
			$cost = trim($this->security->xss_clean($_POST["cost"][$i]));			
			
			if( intval($qty) > 0 ) {
				$item = compact('order_id', 'purchase_order_item_id', 'qty', 'cost', 'date');
				array_push($all_items, $item);							
			}
		}
		
		$this->load->model('stock_m');
		
		foreach( $all_items as $i )
		{
			if( !$this->stock_m->ins_items_receipts($i) )
			{
				echo "ko-db";
				return;
			}
		}
		echo "ok";
	}
	
	public function save_order()
	{
		if( isset($_POST['qty']))
		{
			$this->load->helper(array('form', 'url'));
			$this->load->library('form_validation');
			
			$order_id = trim($this->input->post('order_id', true));
			
			$all_items = array();
			for($i = 0; $i < count($_POST['qty']); $i++)
			{
				$item_id = trim($this->security->xss_clean($_POST["item_id"][$i]));			
				$qty = trim($this->security->xss_clean($_POST["qty"][$i]));
				$description = trim($this->security->xss_clean($_POST["description"][$i]));
				$suppliers_code = trim($this->security->xss_clean($_POST["suppliers_code"][$i]));
				$rate = trim($this->security->xss_clean($_POST["rate"][$i]));
				$disc = trim($this->security->xss_clean($_POST["disc"][$i]));				
				$min = trim($this->security->xss_clean($_POST["min"][$i]));
				$total = trim($this->security->xss_clean($_POST["total"][$i]));
				$delete = trim($this->security->xss_clean($_POST["delete"][$i]));					
				if($delete == "yes")
					$qty*=-1;
				
				if( is_numeric($item_id) &&
					( ( is_numeric($qty) )) && 
					( ( is_numeric($rate) && $rate > 0 )) &&
					( ( is_numeric($total) && $total > 0 )) &&
					( ($qty > 0 && ($total == $rate * $qty || $total == $rate * $qty - (($rate * $qty)*$disc)/100) )|| $qty < 0 )				
				)
				{
					$item = compact('order_id', 'item_id', 'qty', 'description', 'suppliers_code', 'rate', 'disc' ,'total', 'min' );
					array_push($all_items, $item);							
				}else
				{
					echo "ko-validation";
					return;
				}
							
			}
			
			$this->load->model('cross_hire_m');
			
			foreach( $all_items as $i )
			{
				if( !$this->cross_hire_m->save_item($i) )
				{
					echo "ko-db";
					return;
				}
			}
			//redirect(base_url('index.php/purchases_orders/edit/'.$order_id), 'refresh');
			echo "ok";
		}else{
			echo "Nothing to save";
		}
	}
	

}