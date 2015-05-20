<?php 
    set_time_limit ( 3600 );
    date_default_timezone_set( 'Europe/London' );
    
    include 'dbconn.php';
    
    $currentDate  = new DateTime("2015-05-19 00:00:00");
    
    // Prod
    //$firedeskDB = new mysqli( 'localhost', 'cl52-firedesk', 'Rfkt7Jkm.', 'cl52-firedesk' );
    
    // Dev
    $firedeskDB = new mysqli( 'localhost', 'root', '7H3nMvBaWcAHDr8K', 'firedesk' );
    
    // Check connection
    if ($firedeskDB->connect_error) {
        die("Connection failed: " . $firedeskDB->connect_error);
    }
    
    // Get connections to every branch database
    $result = $firedeskDB->query('SELECT 
                                    pk_id, 
                                    db_host, 
                                    db, 
                                    db_user, 
                                    db_pwd,
                                    db_warehouse_host,
                                    db_warehouse_name,
                                    db_warehouse_user,
                                    db_warehouse_pwd
                                FROM branches');
    
    echo $result->num_rows ." branches to process\r\n";
    $result->data_seek(0);    
    while ($row = $result->fetch_assoc() ) {
        
        $branchesDbConnects[] = array(
                                    'server' => $row['db_host'],
                                    'database' => $row['db'],
                                    'username' => $row['db_user'],
                                    'password' => $row['db_pwd'],
                                    'dw_host' => $row['db_warehouse_host'],
                                    'dw_name' => $row['db_warehouse_name'],
                                    'dw_user' => $row['db_warehouse_user'],
                                    'dw_pwd' => $row['db_warehouse_pwd'],
                                    'branchID' => $row['pk_id']
                                );
    }

    // Free the resources associated with the result set
    // This is done automatically at the end of the script
    $result->free();
    $firedeskDB->close();
    
    foreach ( $branchesDbConnects as $b ) {
                
        $endDate      = clone $currentDate->sub(new DateInterval('P1D'));
        $endDate->setTime(23,59,59);
        $startDate    = clone $endDate;
        $startDate->setTime(0,0,0);
        
        $branchDB     = new mysqli( 'localhost', $b['username'], $b['password'], $b['database'] );
        
        // Check connection
        if ($branchDB->connect_error) {
            die("Connection failed: " . $branchDB->connect_error);
        }

        $result     = $branchDB->query('SELECT
                                            fk_customer_id,
                                            fk_invoice_id,
                                            i.creation_date,
                                            sum( ifnull(value,0)) as total_sales,
                                            sum( ifnull(unit_cost,0)*qty) as total_cost
                                        FROM invoices_details as id
                                            inner join invoices as i on i.pk_id = id.fk_invoice_id
                                            inner join contracts as c on c.pk_id = i.fk_contract_id
                                        WHERE i.creation_date BETWEEN \''.$startDate->format('Y-m-d H:i:s').'\' AND \''.$endDate->format('Y-m-d H:i:s').'\'
                                        GROUP BY fk_invoice_id');

        echo $result->num_rows ." invoices to process of the current branch\r\n";
        $result->data_seek(0); 
        while ( $row = $result->fetch_assoc() ) {
            
            // Totalize every invoice of the branch
            if (!$branchDB->query('INSERT INTO sales_ledger(customer_ID, invoice_ID, datetime,
                                                        total_sales, total_cost)
                                VALUES( '.$row['fk_customer_id'].',
                                        '.$row['fk_invoice_id'].',
                                        \''.$row['creation_date'].'\',
                                        '.$row['total_sales'].',
                                        '.$row['total_cost'].')') ) {
                                          
                die("Saving failed: " . $branchDB->connect_error);
                                            
            }else {
            
                echo "Invoices by branch totalized \r\n";
            }
            
            // Calculate profit of every invoice
            if ( !$branchDB->query('UPDATE sales_ledger 
                                        SET total_gross_profit = total_sales - total_cost,
                                            gross_margin = (total_gross_profit/total_sales)*100
                                        WHERE 
                                            datetime BETWEEN \''.$startDate->format('Y-m-d H:i:s').'\' AND \''.$endDate->format('Y-m-d H:i:s').'\'
                                ') ) {
                
                die("Saving failed: " . $branchDB->connect_error); 
                
            }else {
                
                echo "Gross profit of invoices by branch calculated \r\n";
            }
            // Retrieve invoice items  and calculate profit of every one of it
            if ( !$branchDB->query('
                            INSERT INTO sales_ledger_details (item_number,description, qty, sales_price,cost,gross_profit,gross_margin,invoice_id)
                                SELECT
                                     item_no,	
                                    description,
                                    qty,
                                    ifnull(value,0) as total_sales,
                                    ifnull(unit_cost,0)*qty as total_cost,
                                    ifnull(value,0) - (ifnull(unit_cost,0)*qty) as gross_profit,
                                    ( (ifnull(value,0) - (ifnull(unit_cost,0)*qty)) / (ifnull(value,0)) )*100 as gross_margin,
                                    fk_invoice_id                        
                                FROM invoices_details as id                                    
                                WHERE fk_invoice_id = '.$row['fk_invoice_id'] ) ) {
                   
                die("Saving failed: " . $branchDB->error);
                                    
            }else {            
                echo "Gross profit of every item of the invoices calculated \r\n";
            }
        }
        
        $result = $branchDB->query('
                        SELECT
                            '.$b['branchID'].'                          as branchID,
                            date_format(datetime, \'%Y-%m-%d\')         as datetime,
                            SUM(total_sales)                            as total_sales,
                            SUM(total_cost)                             as total_cost,
                            SUM(total_gross_profit) as total_gross_profit
                        FROM 
                            sales_ledger
                        WHERE datetime  BETWEEN \''.$startDate->format('Y-m-d H:i:s').'\' AND \''.$endDate->format('Y-m-d H:i:s').'\'
                        GROUP BY date_format(datetime, \'%Y-%m-%d\')
                        ');        

        $branchDataWH = new mysqli( 'localhost', $b['dw_user'], $b['dw_pwd'], $b['dw_name'] );
        
        // Check connection
        if ($branchDataWH->connect_error) {
            die("Connection failed: " . $branchDataWH->connect_error);
        }
        
        $result->data_seek(0); 
        while ( $row = $result->fetch_assoc() ) {
            
            if ( !$branchDataWH->query('INSERT INTO sales(
                                    branch_id,
                                    datetime,
                                    total_sales,
                                    total_cost,
                                    total_gross_profit
                                    )
                                VALUES(
                                  '.$row['branchID'].',
                                  \''.$row['datetime'].'\',
                                  '.$row['total_sales'].',
                                  '.$row['total_cost'].',
                                  '.$row['total_gross_profit'].'
                                )') ) {
                                    
                die("Saving failed: " . $branchDB->error);
                
            }else { 
                echo "Sales of branch {$row['branchID']} sent to the warehouse \r\n";
            }
            
            if ( !$branchDataWH->query('UPDATE sales 
                                            SET total_gross_profit = total_sales - total_cost,
                                                gross_margin = (total_gross_profit/total_sales)*100
                                            WHERE date_format(datetime, \'%Y-%m-%d\' ) = \''.$row['datetime'].'\'') ) {
                                    
                die("Saving failed: " . $branchDB->error);
                
            }else { 
                echo "Gross profit calculated of the branch {$row['branchID']} in the warehouse \r\n";
            }
        }
        
    }

?>