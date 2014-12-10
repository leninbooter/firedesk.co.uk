function get_code_details(cur_code)
{
var cur_table_type=$('.menu_item.active_menu_item').text();
//alert(cur_page_main_command);

if (cur_page_main_command=='hire_stock')
{
if (cur_table_type=='') 
	{
	if (cur_code.indexOf('FAM')!=-1) cur_table_type='Family List';
		else cur_table_type='Unit List';
	}
//console.log(cur_table_type);
	//console.log($('.menu_item.active_menu_item').text());
	var cur_menu_ob=
	{
	'Unit List':'View Unit',
	'Family List':'View Family'
	};
}
if (cur_page_main_command=='sales_stock'	)
{
if (cur_table_type=='') 
	{
	cur_table_type='Stock List';
	}
var cur_menu_ob=
	{
	'Stock List':'View Stock'
	};
}
if (cur_page_main_command=='purchase_point')
{
if (cur_table_type=='') 
	{
	cur_table_type='Supplier List';
	}
var cur_menu_ob=
	{
	'Supplier List':'View Supplier'
	};
}
if (cur_page_main_command=='sales_point')
{
if (cur_table_type=='') 
	{
	cur_table_type='Customer List';
	}
var cur_menu_ob=
	{
	'Customer List':'View Customer'
	};
}
if (cur_page_main_command=='admin_point')
{
if (cur_table_type=='') 
	{
	cur_table_type='Contact List';
	}
var cur_menu_ob=
	{
	'Contact List':'View Contact',
	'VAT List':'View VAT',
	'Holiday List':'View Holiday'
	};
}
if (cur_page_main_command=='accounts')
{
if (cur_table_type=='') 
	{
	cur_table_type='Nominal Ledger List';
	}
var cur_menu_ob=
	{
	'Nominal Ledger List':'View Nominal Ledger',
	'Audit Trail List':'View Audit Trail',
	'Bank Trail List':'View Bank Trail',
	'Bank Ledger List':'View Bank Ledger'
	};
}
	//open details page:
	m_item_code=cur_code;
	//console.log(m_item_code);
	$.each(cur_menu_ob, function (k, val)
	   {
	   if (k==cur_table_type || val==cur_table_type)
	       {
		   $('.menu_item').each(function ()
		        {
				if ($.trim($(this).text())==val) $(this).trigger('click');
				});
		   return true;
		   }
	   });
}
function go_back()
{
var cur_table_type=$('.menu_item.active_menu_item').text();
	//console.log($('.menu_item.active_menu_item').text());
if (cur_page_main_command=='hire_stock')
{
	var cur_menu_ob=
	{
	'Unit List':'Unit',
	'Family List':'Family'
	};
}
if (cur_page_main_command=='sales_stock')
{
	var cur_menu_ob=
	{
	'Stock List':'Stock'
	};
}
if (cur_page_main_command=='purchase_point')
{
	var cur_menu_ob=
	{
	'Supplier List':'Supplier'
	};
}
if (cur_page_main_command=='sales_point')
{
	var cur_menu_ob=
	{
	'Customer List':'Customer'
	};
}
if (cur_page_main_command=='admin_point')
{
	var cur_menu_ob=
	{
	'Contact List':'Contact',
	'VAT List':'VAT',
	'Holiday List':'Holiday'
	};
}
if (cur_page_main_command=='accounts')
{
	var cur_menu_ob=
	{
	'Nominal Ledger List':'Nominal Ledger',
	'Audit Trail List':'Audit Trail',
	'Bank Trail List':'Bank Trail',
	'Bank Ledger List':'Bank Ledger'
	};
}
	
	$.each(cur_menu_ob, function (k, val)
	   {
	   if (cur_table_type.indexOf(val)!=-1)
	       {
		   $('.menu_item').each(function ()
		        {
				if ($.trim($(this).text())==k) $(this).trigger('click');
				});
		   return true;
		   }
	   });
}
function get_code_edit(cur_code)
{
var cur_table_type=$('.menu_item.active_menu_item').text();
	//console.log($('.menu_item.active_menu_item').text());
if (cur_page_main_command=='hire_stock')
{
	var cur_menu_ob=
	{
	'View Unit':'Edit Unit',
	'View Family':'Edit Family'
	};
}	
if (cur_page_main_command=='sales_stock')
{
	var cur_menu_ob=
	{
	'View Stock':'Edit Stock'
	};
}	
if (cur_page_main_command=='purchase_point')
{
	var cur_menu_ob=
	{
	'View Supplier':'Edit Supplier'
	};
}	
if (cur_page_main_command=='sales_point')
{
	var cur_menu_ob=
	{
	'View Customer':'Edit Customer'
	};
}	
if (cur_page_main_command=='admin_point')
{
	var cur_menu_ob=
	{
	'View Contact':'Edit Contact',
	'View VAT':'Edit VAT',
	'View Holiday':'Edit Holiday'
	};
}	
if (cur_page_main_command=='accounts')
{
	var cur_menu_ob=
	{
	'View Nominal Ledger':'Edit Nominal Ledger',
	'View Audit Trail':'Edit Audit Trail',
	'View Bank Trail':'Edit Bank Trail',
	'View Bank Ledger':'Edit Bank Ledger'
	};
}	
	//open details page:
	m_item_code=cur_code;
	$.each(cur_menu_ob, function (k, val)
	   {
	   if (k==cur_table_type || val==cur_table_type)
	       {
		   $('.menu_item').each(function ()
		        {
				if ($.trim($(this).text())==val) $(this).trigger('click');
				});
		   return true;
		   }
	   });
}

//search function:
function proceed_search()
{
var cur_hash=location.hash;
if (cur_hash.indexOf('/')==-1) location.hash=cur_hash+'/search';
     else location.hash=cur_hash.split('/')[0]+'/search';
$('.menu_item').removeClass('active_menu_item');
  
var cur_tb_selector=$(this).parent('td').parent('tr').parent('tbody').children('tr').children('td');
  var cur_search_code=cur_tb_selector.children('.search_code').val();
  var cur_search_keyword=cur_tb_selector.children('.search_keyword').val();
  var cur_search_date_from=cur_tb_selector.children('.date_from').val();
  var cur_search_date_to=cur_tb_selector.children('.date_to').val();
  if (cur_search_code=='undefined' || $.trim(cur_search_code)=='') 
    {
	//console.log(cur_search_keyword);
	if (cur_search_keyword=='undefined' || $.trim(cur_search_keyword)=='') 
	   {
	   if ((cur_search_date_from=='undefined' || $.trim(cur_search_date_from)=='') || (cur_search_date_to=='undefined' || $.trim(cur_search_date_to)=='')) 
	      {
		  alert('Nothing to search!');
		  return false;
		  }
	   }
	}
  var search_level=cur_tb_selector.children('select[name="level_sl"]').val();
  if (search_level=='undefined' || $.trim(search_level)=='') return false;
  var cur_type=$(this).attr('class')=='search_all_act' ? 'sr_all':'sr_onsite';
//  console.log(cur_type);
  //search by code:
  $.post('./server_scripts/ajax_worker.php',{search_content:true,search_type:cur_type,find_code:cur_search_code,search_level:search_level,find_word:cur_search_keyword,find_from_date:cur_search_date_from,find_to_date:cur_search_date_to}, function (data)
		{
		cur_tb_selector.parent('tr').parent('tbody').parent('.top_table').parent('.top_panel').parent('.tab_container').children('.main_content').stop().fadeOut(250, function ()
		      {
			  $(this).html(data);
			  }).fadeIn(300);
		});
  //alert(cur_search_code+' - '+search_level);
}
/*----------------------------------------------------------------------------------------*/
var tab_menu_array={
								'sales_point':
										[
										'customer_list','view_customer','new_customer',
										'edit_customer'
										],
								'purchase_point':
										[
										'supplier_list', 'view_supplier', 'new_supplier',
										'edit_supplier'
										],
								'hire_stock':
										[
										'fleet_records','group_changes','net_rates','global',
										'fleet_status','price_list','reports','transfer_stock',
										'charged_since','additional_detail'
										],
								'sales_stock':
										[
										'stock_list','view_stock','new_stock','edit_stock'
										],
								'admin_point':
										[
										'contact_list','view_contact','new_contact','edit_contact',
										'vat_list','view_vat','new_vat','edit_vat',
										'holiday_list','view_holiday','new_holiday','edit_holiday'
										],
								'accounts':
										[
										'audit_trail_list','view_audit_trail','new_audit_trail','edit_audit_trail',
										'nominal_ledger_list','view_nominal_ledger','new_nominal_ledger','edit_nominal_ledger',
										'bank_trail_list','view_bank_trail','new_bank_trail','edit_bank_trail',
										'bank_ledger_list','view_bank_ledger','new_bank_ledger','edit_bank_ledger'
										]
								};
$(document).ready(function ()
{

$( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
$( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
var comnd=location.href.split('/#')[1];
if (typeof comnd!='undefined')
{
var got_com=false;
if (comnd.indexOf('/')!=-1) 
   {
   got_com=true;
   comnd0=comnd.split('/')[0];
   comnd=comnd.split('/')[1];
   //console.log('comnd0: '+comnd0+'comnd: '+comnd);
   $('.menu_item').each(function ()
        {
		var cur_val=$(this).text();
        cur_val=cur_val.toLowerCase().replace(/ /g,'_');
		if (cur_val==comnd)
		     {
			 that500=this;
			 setTimeout(function ()
			    {
				$('.'+comnd0).trigger('click');
				$(that500).trigger('click');
				got_com=false;
				},500);
			 return true;
			 }
		});
   $('.'+comnd0).trigger('click');
   }
   else $('.'+comnd).trigger('click');
}   
if (typeof comnd0=='undefined') cur_page_main_command=comnd;
	else cur_page_main_command=comnd0;
$(window).bind('hashchange', function(e) 
								{
								if (!got_com) delete comnd0;
								var comnd=location.href.split('/#')[1];
								if (comnd.indexOf('/')!=-1) 
								   {
									comnd0=comnd.split('/')[0];
									comnd=comnd.split('/')[1];
									}
								if (typeof comnd0=='undefined') cur_page_main_command=comnd;
									else cur_page_main_command=comnd0;
								if (typeof comnd0=='undefined') 
									{
									setTimeout(function()
									{
									if (typeof tab_menu_array[cur_page_main_command]=='undefined') return false;
									var cur_needed_link=tab_menu_array[cur_page_main_command][0];
									$('.menu_item').each(function ()
										{
										var cur_menu_item=$(this).text();
										cur_menu_item=cur_menu_item.toLowerCase().replace(/ /g,'_');
										/*console.log(cur_menu_item);
										console.log(cur_needed_link);
										console.log('-----------------');*/
										if (cur_menu_item==cur_needed_link) 
											{
											$(this).trigger('click');
											return true;
											}
										});
									},100);
									}
								
								});	
//console.log(cur_page_main_command);
$.ajaxSetup(
                 {
				 timeout:60000,
				 dataType:'html',
				 error: function (x,m,t)
				          {
						  alert('Can`t connect to server!');
						  },
				 beforeSend: function ()
                          {
						  $('body').css('cursor','wait');
                          },
                 complete: function ()
                          {
						  $('body').css('cursor','default');
                          }						  
                 });	
	
if ($('#login_logo').length>0)
{				 
$('#login_logo').css('left',($('#login_wrapper').position().left+$('#login_logo').width()/2)+'px')
.css('top',($('#login_wrapper').position().top-$('#login_logo').height())+'px');				 
}
if ($('.side_menu').parent('.tab_container').height()>$('.side_menu').height()) $('.side_menu').css('height',($('.side_menu').parent('.tab_container').height()+15));
//menu items:
m_item_code=0;
$('.menu_item').livequery('click', function ()
  {
  if ($(this).hasClass('active_menu_item')) return true;
  var cur_val=$(this).text();
  cur_val=cur_val.toLowerCase().replace(/ /g,'_');
  var cur_hash=location.hash;
  if (cur_hash.indexOf('/')==-1) location.hash=cur_hash+'/'+cur_val;
     else location.hash=cur_hash.split('/')[0]+'/'+cur_val;
  $('.menu_item').removeClass('active_menu_item');
  $(this).addClass('active_menu_item');
  $('.sub_menus').stop().hide(100);
  $('.sub_menu_item').removeClass('active_menu_item');
  $(this).parent('div').children('.sub_menus').show(200);
  //sends request to get content:
  var that_item=this;
  console.log('CODE: '+m_item_code);
  if (cur_val=='group_changes') 
	{
	$(this).parent('div').parent('.side_menu')
	.parent('.tab_container ').children('.main_content').html('');
	return true;
	}
  $.post('./server_scripts/ajax_worker.php',{get_content:true,content_name:cur_val,id_code:m_item_code}, function (data)
		{
		if ($(that_item).parent('.side_menu').length>0)
			{
			$(that_item).parent('.side_menu').parent('.tab_container ').children('.main_content').stop().fadeOut(250, function ()
				{
				$(this).html(data);
				}).fadeIn(300);
			}
		else 	$(that_item).parent('div').parent('.side_menu').parent('.tab_container ').children('.main_content').stop().fadeOut(250, function ()
					{
					$(this).html(data);
					}).fadeIn(300);
		m_item_code=0;
		});
  });
$('.sub_menu_item').livequery('click', function ()
  {
  if ($(this).hasClass('active_menu_item')) return true;
  var cur_val=$(this).text();
  cur_val=cur_val.toLowerCase().replace(/ /g,'_');
  var cur_hash=location.hash;
  console.log(cur_hash.split('/'));
  if (cur_hash.split('/').length<=2) location.hash=cur_hash+'/'+cur_val;
     else location.hash=cur_hash.split('/')[0]+'/'+cur_hash.split('/')[1]+'/'+cur_val;
  $('.sub_menu_item').removeClass('active_menu_item');
  $(this).addClass('active_menu_item');
  if ($(this).parent('.complex_item').children('.sub_menus').length>0)
	{
	var cur_sel=$(this).parent('.complex_item').children('.sub_menus');
	$('.sub_sub_menu_item').parent('.sub_menus').stop().hide(100);
	cur_sel.children('.sub_sub_menu_item').removeClass('active_menu_item');
	$(this).parent('.complex_item').children('.sub_menus').show(200);
	}
	else
	{
	$('.sub_sub_menu_item').parent('.sub_menus').stop().hide(100);
	}
  
  var that_item=this;
  $.post('./server_scripts/ajax_worker.php',{get_content:true,content_name:cur_val,id_code:m_item_code}, function (data)
		{
		if ($(that_item).parent('.sub_menus').parent('div').parent('.side_menu').length>0)
		{
		$(that_item).parent('.sub_menus').parent('div').parent('.side_menu').parent('.tab_container ').children('.main_content').stop().fadeOut(250, function ()
				{
				$(this).html(data);
				}).fadeIn(300);
		}
		else
			{
			$(that_item).parent('.complex_item').parent('.sub_menus').parent('div').parent('.side_menu').parent('.tab_container ').children('.main_content').stop().fadeOut(250, function ()
				{
				$(this).html(data);
				}).fadeIn(300);
			}
		m_item_code=0;
		});
  });  
  
$('.detail_but').livequery('click', function ()
    {
	var cur_code=$(this).attr('attr-row_code');
	get_code_details(cur_code);
	});
$('input[name="det_code"]').livequery('change', function ()
  {
  var cur_code=$(this).val();
  get_code_details(cur_code);
  });  
$('.det_back_but').livequery('click', function ()
    {
	go_back();
    });	
$('.det_edit_but').livequery('click', function ()
    {
	var cur_code=$(this).attr('attr-cur_code');
	get_code_edit(cur_code);
	});	
$('input[name="edit_code"]').livequery('change', function ()
  {
  var cur_code=$(this).val();
  get_code_edit(cur_code);
  });  

//searchbar script:
$('.search_all_act').livequery(function ()
{
$(this).bind('click', proceed_search);
});  
$('.search_onsite_act').livequery(function ()
{
$(this).bind('click', proceed_search);
});  

$('.date_from').livequery(function ()
{
$(this).datepicker({
                    showOtherMonths: true,
                    selectOtherMonths: true,
					dateFormat:'dd-mm-yy',
					defaultDate:'+0',
					firstDay:1});
});
$('.date_to').livequery(function ()
{
$(this).datepicker({
                    showOtherMonths: true,
                    selectOtherMonths: true,
					dateFormat:'dd-mm-yy',
					defaultDate:'+0',
					firstDay:1});
});
$('.date_field').livequery(function ()
{
$(this).datepicker({
                    showOtherMonths: true,
                    selectOtherMonths: true,
					dateFormat:'dd-mm-yy',
					defaultDate:'+0',
					firstDay:1});
});

$('input[type="checkbox"][name="Min_1_Week"]').livequery('click', function ()
	{
	var cur_selector=$(this).parent('td').parent('tr').parent('tbody').children('tr').children('td');
	if (this.checked)
		{
		cur_selector.children('input[name="Price_1_day"]').add('input[name="Price_2_days"]').attr('disabled',true);
		//cur_selector.children('input[name="Price_1_week"]').attr('disabled',false);
		}
		else
		{
		cur_selector.children('input[name="Price_1_day"]').add('input[name="Price_2_days"]').attr('disabled',false);
		//cur_selector.children('input[name="Price_1_week"]').attr('disabled',true);
		}
	});
$('select[name="Active"]').livequery('change', function ()
	{
	var cur_selector=$(this).parent('td').parent('tr').parent('tbody').children('tr').children('td');
	var cur_val=$.trim($(this).val());
	switch (cur_val)
	{
	case 'Returned':	cur_selector.children('input[name="Returned_date"]').attr('disabled',false);
								cur_selector.children('input[name="Sold_date"]').attr('disabled',true);
								cur_selector.children('input[name="Disposal"]').attr('disabled',true);
								break;
	case 'Disposed':		cur_selector.children('input[name="Returned_date"]').attr('disabled',true);
								cur_selector.children('input[name="Sold_date"]').attr('disabled',true);
								cur_selector.children('input[name="Disposal"]').attr('disabled',false);	
								break;
	case 'Sold':			cur_selector.children('input[name="Returned_date"]').attr('disabled',true);
								cur_selector.children('input[name="Sold_date"]').attr('disabled',false);
								cur_selector.children('input[name="Disposal"]').attr('disabled',true);
								break;
	default: 				cur_selector.children('input[name="Returned_date"]').attr('disabled',true);
								cur_selector.children('input[name="Sold_date"]').attr('disabled',true);
								cur_selector.children('input[name="Disposal"]').attr('disabled',true);
								break;			
	}
	
	});
$('input[type="checkbox"][name="Reconciled"]').livequery('click', function ()
	{
	var cur_selector=$(this).parent('td').parent('tr').parent('tbody').children('tr').children('td');
	if (this.checked)
		{
		cur_selector.children('input[name="Reconciliation_Date"]').attr('disabled',false);
		}
		else
		{
		cur_selector.children('input[name="Reconciliation_Date"]').attr('disabled',true);
		}
	});	


});