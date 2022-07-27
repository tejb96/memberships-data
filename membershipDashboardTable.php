<?php
    require_once(dirname(__FILE__)."/queriesHSB.php");
    function display_membership_summary_table_hsb($membership_ids,$membership_names,$wc_product_ids_new,$case_statement_new,$wc_product_ids_renewal,$case_statement_renewal){
        global $wpdb;
        $the_prefix=$wpdb->base_prefix;

        $results=[];
        //membership totals from memberpress tables.
        foreach($membership_ids as $key=>$membership_id){
            $mp_query = get_mp_dashsboard_query_hsb($the_prefix, $membership_id);
            $result = $wpdb->get_results($mp_query, ARRAY_A)[0];
            $result=array('Membership Name'=> $membership_names[$key]) + $result;
            array_push($results, $result);
        }

        //new membership purchases from woocomm
        $wc_query = get_wc_dashsboard_query_hsb($the_prefix, $wc_product_ids_new, $case_statement_new);
        $results2 =$wpdb->get_results($wc_query, ARRAY_A);
        $results=merge_mp_with_wp_results_hsb($membership_ids, $results, $results2, "New"); 

        //renew membership purchases from woocomm
        $wc_query = get_wc_dashsboard_query_hsb($the_prefix, $wc_product_ids_renewal, $case_statement_renewal);
        $results2 =$wpdb->get_results($wc_query, ARRAY_A);
        $results=merge_mp_with_wp_results_hsb($membership_ids, $results, $results2, "Renewal");    

        //total all the columns
        array_push($results, total_the_columns_hsb($results));

        //get organizational member parent account info and sub account info
        $key=array_search('Organizational', $membership_names);
        if($key !== false){
            $wc_query = get_mp_dashsboard_query_hsb($the_prefix, $membership_ids[$key], false);
            $sub_accs = $wpdb->get_results($wc_query, ARRAY_A)[0];
            if(!empty($sub_accs)){
                $size=sizeof($results);
                foreach($results[$key] as $col_name=>$col){
                    if($col_name!='product_id' && array_key_exists($col_name, $sub_accs)){
                        $results[$size-1][$col_name]+=$sub_accs[$col_name];
                        $results[$key][$col_name].='('.$sub_accs[$col_name].')';
                    }
                }
            }
        }
?>
        <?php if (!empty($results)): ?>
            <hr/>
            <div class="membership-dashboard-table-hsb">
                <h1>Membership Summary Table (Work in Progress):</h1>
                <br/>
                <table>
                    <thead>
                        <tr>
                        <th><?php echo implode('</th><th>', array_keys(current($results))); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $key=>$row): array_map('htmlentities', $row); ?>
                        <tr <?php if($key === count($results)-1){echo 'id="table-memberships-last-row"';} ?>>
                                <td id="table-memberships-first-col"><?php echo implode('</td><td>', $row);?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <br/>
            <hr/>
            <div>
                <h3>Table Info:</h3>
                <ul>
                    <li>Col: Total = Active + Renewal Overdue + Lapsed.</li>
                    <li>Col: Active = All memberships that have not yet expired.</li>
                    <li>Col: Renewal Overdue = Memberships that are expired according to memberpress. But have not been for more than 60 days.</li>
                    <li>Col: Lapsed = Memberships that have been expired for longer than 60 days.</li>
                    <li>Col: Rest of the columns are from the WooCommerce database.</li>
                    <li>Row: Organizational row has just the parent accounts and then in the brackets just the sub accounts alone.</li>
                    <li>Row: Total row has all the totals for each column, and it also includes the sub-accounts.</li>
                </ul>
            </div>
        <?php endif; ?>
<?php 
    }

    function merge_mp_with_wp_results_hsb($membership_ids, $results, $results2, $name){
        foreach($membership_ids as $key=>$membership_id){
            $col1 = 'Completed last month: '.$name;
            $col2 = 'Pending: '.$name;
            $set1 = false;
            $set2 = false;
            foreach($results2 as $result){
                if($result['product_id'] == $membership_id){
                    if($result['post_status']==='wc-completed'){
                        $results[$key][$col1] = $result['totals'];
                        $set1 = true;
                    }else{
                        $results[$key][$col2] = $result['totals'];
                        $set2 = true;
                    }
                }
                if($set1 === true && $set2 === true){
                    break;
                }
            }
            if($set1 === false ){
                $results[$key][$col1] = 0;
            }
            if($set2 === false ){
                        $results[$key][$col2] = 0;
            }
        }
        return $results;
    }

    function total_the_columns_hsb($results){
        $totals = array('Membership Name'=>'Totals', 'product_id'=>'-', 'Total'=>0, 'Active'=>0, 'Renewal Overdue'=>0, 'Lapsed'=>0, 'Completed last month: New'=>0, 'Pending: New'=>0, 'Completed last month: Renewal'=>0, 'Pending: Renewal'=>0);
        foreach($results as $result){
            $totals['Total']+=$result['Total'];
            $totals['Active']+=$result['Active'];
            $totals['Renewal Overdue']+=$result['Renewal Overdue'];
            $totals['Lapsed']+=$result['Lapsed'];
            $totals['Completed last month: New']+=$result['Completed last month: New'];
            $totals['Pending: New']+=$result['Pending: New'];
            $totals['Completed last month: Renewal']+=$result['Completed last month: Renewal'];
            $totals['Pending: Renewal']+=$result['Pending: Renewal'];
        }
        return $totals;
    }
?>