<?php

function get_wc_export_query_hsb($the_prefix, $prod_id, $from_date, $to_date){
    return "SELECT
    u1.id,
    COALESCE(
        TRIM(
            SUBSTRING_index(
                SUBSTRING_index(u1.display_name, ',', 1),
                ' ', 
                -1
            )
        ),
        pm3._billing_last_name
    )AS 'a',
    COALESCE(
        TRIM(
            SUBSTRING_INDEX(u1.display_name, ' ', 1)
            ),
        pm2._billing_first_name
    ) AS 'b',
    u1.display_name,
    COALESCE(
        u1.user_email,
        pm4._billing_email
    ) AS 'c',
    COALESCE(
        meta2.meta_value,
        pm5._billing_address_1
    ) AS 'd',
    meta7.meta_value,
    COALESCE(
        meta3.meta_value,
        pm6._billing_city
    ) AS 'e',
    COALESCE(
        meta4.meta_value,
        pm7._billing_state
    ) AS 'f',
    COALESCE(
        meta5.meta_value,
        pm8._billing_postcode
    ) AS 'g',
    COALESCE(
        meta6.meta_value,
        pm9._billing_country
    ) AS 'h',
    COALESCE(
        meta8.meta_value,
        pm10._billing_phone
    ) AS 'i',
    pm2_fil.paid_date AS 'j',
    pm11._payment_method AS 'k',
    pm12._order_total AS 't'
FROM
    {$the_prefix}postmeta AS pm
LEFT JOIN {$the_prefix}posts AS p
ON
    pm.post_id = p.ID
LEFT JOIN {$the_prefix}woocommerce_order_items oi ON
    oi.order_id = pm.post_id
LEFT JOIN {$the_prefix}woocommerce_order_itemmeta oim ON
    oim.order_item_id = oi.order_item_id
LEFT JOIN {$the_prefix}users u1 ON
    pm.meta_value = u1.ID
LEFT JOIN(
    SELECT
        post_id,
        DATE(meta_value) AS paid_date
    FROM
        {$the_prefix}postmeta
    WHERE
        meta_key = '_paid_date'
) AS pm2_fil
ON
    pm.post_id = pm2_fil.post_id
LEFT JOIN(
    SELECT
        post_id,
        meta_value AS _billing_first_name
    FROM
        {$the_prefix}postmeta
    WHERE
        meta_key = '_billing_first_name'
) AS pm2
ON
    pm.post_id = pm2.post_id
LEFT JOIN(
    SELECT
        post_id,
        meta_value AS _billing_last_name
    FROM
        {$the_prefix}postmeta
    WHERE
        meta_key = '_billing_last_name'
) AS pm3
ON
    pm.post_id = pm3.post_id
LEFT JOIN(
    SELECT
        post_id,
        meta_value AS _billing_email
    FROM
        {$the_prefix}postmeta
    WHERE
        meta_key = '_billing_email'
) AS pm4
ON
    pm.post_id = pm4.post_id
LEFT JOIN(
    SELECT
        post_id,
        meta_value AS _billing_address_1
    FROM
        {$the_prefix}postmeta
    WHERE
        meta_key = '_billing_address_1'
) AS pm5
ON
    pm.post_id = pm5.post_id
LEFT JOIN(
    SELECT
        post_id,
        meta_value AS _billing_city
    FROM
        {$the_prefix}postmeta
    WHERE
        meta_key = '_billing_city'
) AS pm6
ON
    pm.post_id = pm6.post_id
LEFT JOIN(
    SELECT
        post_id,
        meta_value AS _billing_state
    FROM
        {$the_prefix}postmeta
    WHERE
        meta_key = '_billing_state'
) AS pm7
ON
    pm.post_id = pm7.post_id
LEFT JOIN(
    SELECT
        post_id,
        meta_value AS _billing_postcode
    FROM
        {$the_prefix}postmeta
    WHERE
        meta_key = '_billing_postcode'
) AS pm8
ON
    pm.post_id = pm8.post_id
LEFT JOIN(
    SELECT
        post_id,
        meta_value AS _billing_country
    FROM
        {$the_prefix}postmeta
    WHERE
        meta_key = '_billing_country'
) AS pm9
ON
    pm.post_id = pm9.post_id
LEFT JOIN(
    SELECT
        post_id,
        meta_value AS _billing_phone
    FROM
        {$the_prefix}postmeta
    WHERE
        meta_key = '_billing_phone'
) AS pm10
ON
    pm.post_id = pm10.post_id
LEFT JOIN(
    SELECT
        post_id,
        meta_value AS _payment_method
    FROM
        {$the_prefix}postmeta
    WHERE
        meta_key = '_payment_method_title'
) AS pm11
ON
    pm.post_id = pm11.post_id
LEFT JOIN(
    SELECT
        post_id,
        meta_value AS _order_total
    FROM
        {$the_prefix}postmeta
    WHERE
        meta_key='_order_total'
    ) AS pm12
    ON
        pm.post_id=pm12.post_id
LEFT JOIN(
    SELECT
        *
    FROM
        {$the_prefix}usermeta
    WHERE
        meta_key = 'mepr-address-one'
) AS meta2
ON
    u1.ID = meta2.user_id
LEFT JOIN(
    SELECT
        *
    FROM
        {$the_prefix}usermeta
    WHERE
        meta_key = 'mepr-address-city'
) AS meta3
ON
    u1.ID = meta3.user_id
LEFT JOIN(
    SELECT
        *
    FROM
        {$the_prefix}usermeta
    WHERE
        meta_key = 'mepr-address-state'
) AS meta4
ON
    u1.ID = meta4.user_id
LEFT JOIN(
    SELECT
        *
    FROM
        {$the_prefix}usermeta
    WHERE
        meta_key = 'mepr-address-zip'
) AS meta5
ON
    u1.ID = meta5.user_id
LEFT JOIN(
    SELECT
        *
    FROM
        {$the_prefix}usermeta
    WHERE
        meta_key = 'mepr-address-country'
) AS meta6
ON
    u1.ID = meta6.user_id
LEFT JOIN(
    SELECT
        *
    FROM
        {$the_prefix}usermeta
    WHERE
        meta_key = 'mepr-address-two'
) AS meta7
ON
    u1.ID = meta7.user_id
LEFT JOIN(
    SELECT
        *
    FROM
        {$the_prefix}usermeta
    WHERE
        meta_key = 'billing_phone'
) AS meta8
ON
    u1.ID = meta8.user_id
WHERE
    p.post_type = 'shop_order' 
    AND pm.meta_key = '_customer_user' 
    AND p.post_status = 'wc-completed' 
    AND oi.order_item_type = 'line_item' 
    AND oim.meta_key = '_product_id' 
    AND oim.meta_value = {$prod_id}
    AND DATE(pm2_fil.paid_date) >= STR_TO_DATE('{$from_date}', '%Y-%m-%d')
    AND DATE(pm2_fil.paid_date) <= STR_TO_DATE('{$to_date}', '%Y-%m-%d')
ORDER BY
    pm2_fil.paid_date ASC";
}