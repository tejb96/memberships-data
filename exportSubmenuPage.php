<?php 
    require_once(dirname(__FILE__)."/queriesHSB.php");
    
    function membership_data_download_csv_hsb(){
      
        if (isset($_POST['download_quarterly_report_hsb'])) {
            $from_date_hsb=$_POST['qreport_from_date_hsb'];
            $to_date_hsb=$_POST['qreport_to_date_hsb'];
            if(empty($from_date_hsb) || empty($to_date_hsb)){
                $from_date_hsb=date('Y-m-d', strtotime('-1 months'));
                $to_date_hsb= date('Y-m-d');
            }

            $membership=explode(',', $_POST['membership_type_wc']);  
            if(empty($membership)){
                $membership = array(0=>2574);
            }

            $filename=$_POST['file_name_hsb'];
            if(empty($filename)){
                $filename = $membership[0].'-Report_'.$from_date_hsb.'_to_'.$to_date_hsb;
            }

            global $wpdb;

            $prefix_hsb = $wpdb->prefix;
        
            $header_membership_hsb=array('Product Id:', $membership[0], NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
            $header_new_hsb=array('NEW/EXISTING', 'PURCHASES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
            $header_hsb=array('ID', 'Last Name', 'First Name', 'Display Name', 'Email', 'Address 1', 'Address 2', 'City', 'State', 'Zip', 'Country', 'Phone', 'Paid Date', 'Payment Method', 'Order Total');

            $fp = fopen("php://output", "w");
            header("Content-type: text/csv");
            header("Content-disposition: csv" . date("Y-m-d") . ".csv");
            header( "Content-disposition: filename=".$filename.".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $query_new = get_wc_export_query_hsb($prefix_hsb, $membership[0], $from_date_hsb, $to_date_hsb);            
            $result_new = $wpdb->get_results($query_new, ARRAY_A);    
                              
            fputcsv( $fp, $header_membership_hsb);
            fputcsv( $fp, $header_new_hsb);
            fputcsv( $fp, $header_hsb);
            if(!empty($result_new)){
                foreach ( $result_new as $row ) {
                    fputcsv( $fp, $row );
                }
            }

         
            exit;
        }
    }

    function render_export_submenu_page_html_hsb($memberships_mepr, $memberships_wc){
        
?>
    <div class="align-center-hsb">
    <hr>
        
          
        <hr>
        <form method="post" id="download_quarterly_report_form_hsb" action="">
            <h3>Export membership related transactional data for specific time periods:</h3>
            <table class="form-table-hsb" id="date-range-table-hsb">
                <tr>
                    <td><label>Select Membership Type:</label></td>
                    <td>
                        <select name="membership_type_wc" id="membership-type-wc">
                            <?php 
                            foreach($memberships_wc as $membership_id=>$membership){
                                $values = implode(',',$membership);
                                echo '<option value="'.$values.'">'.$membership_id.'</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label>Inclusive Date Range:</label></td>
                    <td><label for="year">From:</label> <input type="date" name="qreport_from_date_hsb" id="from-date-hsb"> <label for="year">To:</label> <input type="date" name="qreport_to_date_hsb" id="to-date-hsb"></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Auto Fill Date:</td>
                </tr>
                <tr>
                    <td><label>Select Year:</label></td>
                    <td><label for="last-year-hsb">Last Year</label> <input type="radio" name="year_hsb" id="last-year-hsb" value="<?php echo date('Y') - 1 ?>"> <label for="current-year-hsb">Current Year</label> <input type="radio" name="year_hsb" id="current-year-hsb" checked="checked" value="<?php echo date('Y')?>"></td>
                </tr>
                <tr>
                    <td><label>Select Quarter:</label></td>
                    <td><input type="button" id='q1-hsb' value="Quarter 1"><input type="button" id='q2-hsb' value="Quarter 2"><input type="button" id='q3-hsb' value="Quarter 3"><input type="button" id='q4-hsb' value="Quarter 4"><input type="button" id='clear-dates' value="clear dates"></td>
                </tr>
                <tr>
                    <td><label for="file-name-hsb">Custom File Name:</label></td>
                    <td><input type="text" name="file_name_hsb" id="file-name-hsb"></td>
                </tr>
            </table>
            <input type="submit" name="download_quarterly_report_hsb" class="button-primary" value="Export Report" />
        </form>
    </div>
<?php
    }
?>