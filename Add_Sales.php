<?php
/**
 * Script to add sales data.
 * This script sends a POST request to the specified URL to add sales data.
 */

// The URL you want to request
$url = "https://cloudpos.bigbcomputer.com.my/connector/api/sell";

// The parameters you want to send in the POST request
$post_data = array(
    "data" => [
        [
            "id" => 2,
            "business_id" => 1,
            "location_id" => 1,
            "is_kitchen_order" => 1,
            "res_table_id" => null,
            "res_waiter_id" => null,
            "res_order_status" => null,
            "type" => "sell",
            "sub_type" => null,
            "status" => "final",
            "sub_status" => null,
            "is_quotation" => 0,
            "payment_status" => "paid",
            "adjustment_type" => null,
            "contact_id" => 1,
            "customer_group_id" => null,
            "invoice_no" => "0001",
            "ref_no" => "",
            "source" => null,
            "subscription_no" => null,
            "subscription_repeat_on" => null,
            "transaction_date" => "2025-01-15 22:05:29",
            "total_before_tax" => "5.0000",
            "tax_id" => null,
            "tax_amount" => "0.0000",
            "discount_type" => "percentage",
            "discount_amount" => "0.0000",
            "rp_redeemed" => 0,
            "rp_redeemed_amount" => "0.0000",
            "shipping_details" => null,
            "shipping_address" => null,
            "delivery_date" => null,
            "shipping_status" => null,
            "delivered_to" => null,
            "delivery_person" => null,
            "shipping_charges" => "0.0000",
            "shipping_custom_field_1" => null,
            "shipping_custom_field_2" => null,
            "shipping_custom_field_3" => null,
            "shipping_custom_field_4" => null,
            "shipping_custom_field_5" => null,
            "additional_notes" => null,
            "staff_note" => null,
            "is_export" => 0,
            "export_custom_fields_info" => null,
            "round_off_amount" => "0.0000",
            "additional_expense_key_1" => null,
            "additional_expense_value_1" => "0.0000",
            "additional_expense_key_2" => null,
            "additional_expense_value_2" => "0.0000",
            "additional_expense_key_3" => null,
            "additional_expense_value_3" => "0.0000",
            "additional_expense_key_4" => null,
            "additional_expense_value_4" => "0.0000",
            "final_total" => "5.0000",
            "expense_category_id" => null,
            "expense_sub_category_id" => null,
            "expense_for" => null,
            "commission_agent" => null,
            "document" => null,
            "is_direct_sale" => 0,
            "is_suspend" => 0,
            "exchange_rate" => "1.000",
            "total_amount_recovered" => null,
            "transfer_parent_id" => null,
            "return_parent_id" => null,
            "opening_stock_product_id" => null,
            "created_by" => 1,
            "purchase_requisition_ids" => null,
            "prefer_payment_method" => null,
            "prefer_payment_account" => null,
            "sales_order_ids" => null,
            "purchase_order_ids" => null,
            "custom_field_1" => null,
            "custom_field_2" => null,
            "custom_field_3" => null,
            "custom_field_4" => null,
            "import_batch" => null,
            "import_time" => null,
            "types_of_service_id" => null,
            "packing_charge" => "0.0000",
            "packing_charge_type" => null,
            "service_custom_field_1" => null,
            "service_custom_field_2" => null,
            "service_custom_field_3" => null,
            "service_custom_field_4" => null,
            "service_custom_field_5" => null,
            "service_custom_field_6" => null,
            "is_created_from_api" => 0,
            "rp_earned" => 0,
            "order_addresses" => null,
            "is_recurring" => 0,
            "recur_interval" => 1,
            "recur_interval_type" => "days",
            "recur_repetitions" => 0,
            "recur_stopped_on" => null,
            "recur_parent_id" => null,
            "invoice_token" => null,
            "pay_term_number" => null,
            "pay_term_type" => null,
            "selling_price_group_id" => 0,
            "created_at" => "2025-01-15T14:05:29.000000Z",
            "updated_at" => "2025-01-15T14:05:29.000000Z",
            "sell_lines" => [
                [
                    "id" => 1,
                    "transaction_id" => 2,
                    "product_id" => 1,
                    "variation_id" => 1,
                    "quantity" => 1,
                    "secondary_unit_quantity" => "0.0000",
                    "quantity_returned" => "0.0000",
                    "unit_price_before_discount" => "5.0000",
                    "unit_price" => "5.0000",
                    "line_discount_type" => "fixed",
                    "line_discount_amount" => "0.0000",
                    "unit_price_inc_tax" => "5.0000",
                    "item_tax" => "0.0000",
                    "tax_id" => null,
                    "discount_id" => null,
                    "lot_no_line_id" => null,
                    "sell_line_note" => "",
                    "so_line_id" => null,
                    "so_quantity_invoiced" => "0.0000",
                    "res_service_staff_id" => null,
                    "res_line_order_status" => "cooked",
                    "parent_sell_line_id" => null,
                    "children_type" => "",
                    "sub_unit_id" => null,
                    "created_at" => "2025-01-15T14:05:29.000000Z",
                    "updated_at" => "2025-01-15T14:05:53.000000Z"
                ]
            ],
            "payment_lines" => [
                [
                    "id" => 1,
                    "transaction_id" => 2,
                    "business_id" => 1,
                    "is_return" => 0,
                    "amount" => "5.0000",
                    "method" => "cash",
                    "payment_type" => null,
                    "transaction_no" => null,
                    "card_transaction_number" => null,
                    "card_number" => null,
                    "card_type" => "credit",
                    "card_holder_name" => null,
                    "card_month" => null,
                    "card_year" => null,
                    "card_security" => null,
                    "cheque_number" => null,
                    "bank_account_number" => null,
                    "paid_on" => "2025-01-15 22:05:29",
                    "created_by" => 1,
                    "paid_through_link" => 0,
                    "gateway" => null,
                    "is_advance" => 0,
                    "payment_for" => 1,
                    "parent_id" => null,
                    "note" => null,
                    "document" => null,
                    "payment_ref_no" => "SP2025/0001",
                    "account_id" => null,
                    "created_at" => "2025-01-15T14:05:29.000000Z",
                    "updated_at" => "2025-01-15T14:05:29.000000Z"
                ]
            ],
            "contact" => [
                "id" => 1,
                "business_id" => 1,
                "type" => "customer",
                "contact_type" => null,
                "supplier_business_name" => null,
                "name" => "Walk-In Customer",
                "prefix" => null,
                "first_name" => null,
                "middle_name" => null,
                "last_name" => null,
                "email" => null,
                "contact_id" => "CO0001",
                "contact_status" => "active",
                "tax_number" => null,
                "city" => null,
                "state" => null,
                "country" => null,
                "address_line_1" => null,
                "address_line_2" => null,
                "zip_code" => null,
                "dob" => null,
                "mobile" => "",
                "landline" => null,
                "alternate_number" => null,
                "pay_term_number" => null,
                "pay_term_type" => null,
                "credit_limit" => "0.0000",
                "created_by" => 1,
                "balance" => "0.0000",
                "total_rp" => 0,
                "total_rp_used" => 0,
                "total_rp_expired" => 0,
                "is_default" => 1,
                "shipping_address" => null,
                "shipping_custom_field_details" => null,
                "is_export" => 0,
                "export_custom_field_1" => null,
                "export_custom_field_2" => null,
                "export_custom_field_3" => null,
                "export_custom_field_4" => null,
                "export_custom_field_5" => null,
                "export_custom_field_6" => null,
                "position" => null,
                "customer_group_id" => null,
                "custom_field1" => null,
                "custom_field2" => null,
                "custom_field3" => null,
                "custom_field4" => null,
                "custom_field5" => null,
                "custom_field6" => null,
                "custom_field7" => null,
                "custom_field8" => null,
                "custom_field9" => null,
                "custom_field10" => null,
                "deleted_at" => null,
                "created_at" => "2025-01-07T12:26:59.000000Z",
                "updated_at" => "2025-01-07T12:26:59.000000Z"
            ],
            "invoice_url" => "https://cloudpos.bigbcomputer.com.my/invoice/8708717a82b0f20bb3e7327995fe20c9",
            "payment_link" => ""
        ],
        [
            "id" => 3,
            "business_id" => 1,
            "location_id" => 1,
            "is_kitchen_order" => 0,
            "res_table_id" => null,
            "res_waiter_id" => null,
            "res_order_status" => null,
            "type" => "sell",
            "sub_type" => null,
            "status" => "final",
            "sub_status" => null,
            "is_quotation" => 0,
            "payment_status" => "paid",
            "adjustment_type" => null,
            "contact_id" => 1,
            "customer_group_id" => null,
            "invoice_no" => "0002",
            "ref_no" => "",
            "source" => null,
            "subscription_no" => null,
            "subscription_repeat_on" => null,
            "transaction_date" => "2025-05-20 12:34:00",
            "total_before_tax" => "10.0000",
            "tax_id" => null,
            "tax_amount" => "0.0000",
            "discount_type" => "percentage",
            "discount_amount" => "0.0000",
            "rp_redeemed" => 0,
            "rp_redeemed_amount" => "0.0000",
            "shipping_details" => null,
            "shipping_address" => null,
            "delivery_date" => null,
            "shipping_status" => null,
            "delivered_to" => null,
            "delivery_person" => null,
            "shipping_charges" => "0.0000",
            "shipping_custom_field_1" => null,
            "shipping_custom_field_2" => null,
            "shipping_custom_field_3" => null,
            "shipping_custom_field_4" => null,
            "shipping_custom_field_5" => null,
            "additional_notes" => null,
            "staff_note" => null,
            "is_export" => 0,
            "export_custom_fields_info" => null,
            "round_off_amount" => "0.0000",
            "additional_expense_key_1" => null,
            "additional_expense_value_1" => "0.0000",
            "additional_expense_key_2" => null,
            "additional_expense_value_2" => "0.0000",
            "additional_expense_key_3" => null,
            "additional_expense_value_3" => "0.0000",
            "additional_expense_key_4" => null,
            "additional_expense_value_4" => "10.0000",
            "final_total" => "10.0000",
            "expense_category_id" => null,
            "expense_sub_category_id" => null,
            "expense_for" => null,
            "commission_agent" => null,
            "document" => null,
            "is_direct_sale" => 1,
            "is_suspend" => 0,
            "exchange_rate" => "1.000",
            "total_amount_recovered" => null,
            "transfer_parent_id" => null,
            "return_parent_id" => null,
            "opening_stock_product_id" => null,
            "created_by" => 2,
            "purchase_requisition_ids" => null,
            "prefer_payment_method" => null,
            "prefer_payment_account" => null,
            "sales_order_ids" => null,
            "purchase_order_ids" => null,
            "custom_field_1" => null,
            "custom_field_2" => null,
            "custom_field_3" => null,
            "custom_field_4" => null,
            "import_batch" => null,
            "import_time" => null,
            "types_of_service_id" => null,
            "packing_charge" => "0.0000",
            "packing_charge_type" => null,
            "service_custom_field_1" => null,
            "service_custom_field_2" => null,
            "service_custom_field_3" => null,
            "service_custom_field_4" => null,
            "service_custom_field_5" => null,
            "service_custom_field_6" => null,
            "is_created_from_api" => 0,
            "rp_earned" => 0,
            "order_addresses" => null,
            "is_recurring" => 0,
            "recur_interval" => 1,
            "recur_interval_type" => "days",
            "recur_repetitions" => 0,
            "recur_stopped_on" => null,
            "recur_parent_id" => null,
            "invoice_token" => null,
            "pay_term_number" => null,
            "pay_term_type" => null,
            "selling_price_group_id" => null,
            "created_at" => "2025-05-20T04:35:00.000000Z",
            "updated_at" => "2025-05-20T04:35:00.000000Z",
            "sell_lines" => [
                [
                    "id" => 2,
                    "transaction_id" => 3,
                    "product_id" => 1,
                    "variation_id" => 1,
                    "quantity" => 2,
                    "secondary_unit_quantity" => "0.0000",
                    "quantity_returned" => "0.0000",
                    "unit_price_before_discount" => "5.0000",
                    "unit_price" => "5.0000",
                    "line_discount_type" => "fixed",
                    "line_discount_amount" => "0.0000",
                    "unit_price_inc_tax" => "5.0000",
                    "item_tax" => "0.0000",
                    "tax_id" => null,
                    "discount_id" => null,
                    "lot_no_line_id" => null,
                    "sell_line_note" => "",
                    "so_line_id" => null,
                    "so_quantity_invoiced" => "0.0000",
                    "res_service_staff_id" => null,
                    "res_line_order_status" => null,
                    "parent_sell_line_id" => null,
                    "children_type" => "",
                    "sub_unit_id" => null,
                    "created_at" => "2025-05-20T04:35:00.000000Z",
                    "updated_at" => "2025-05-20T04:35:00.000000Z"
                ]
            ],
            "payment_lines" => [
                [
                    "id" => 2,
                    "transaction_id" => 3,
                    "business_id" => 1,
                    "is_return" => 0,
                    "amount" => "10.0000",
                    "method" => "cash",
                    "payment_type" => null,
                    "transaction_no" => null,
                    "card_transaction_number" => null,
                    "card_number" => null,
                    "card_type" => "credit",
                    "card_holder_name" => null,
                    "card_month" => null,
                    "card_year" => null,
                    "card_security" => null,
                    "cheque_number" => null,
                    "bank_account_number" => null,
                    "paid_on" => "2025-05-20 12:34:00",
                    "created_by" => 2,
                    "paid_through_link" => 0,
                    "gateway" => null,
                    "is_advance" => 0,
                    "payment_for" => 1,
                    "parent_id" => null,
                    "note" => null,
                    "document" => null,
                    "payment_ref_no" => "SP2025/0002",
                    "account_id" => null,
                    "created_at" => "2025-05-20T04:35:00.000000Z",
                    "updated_at" => "2025-05-20T04:35:00.000000Z"
                ]
            ],
            "contact" => [
                "id" => 1,
                "business_id" => 1,
                "type" => "customer",
                "contact_type" => null,
                "supplier_business_name" => null,
                "name" => "Walk-In Customer",
                "prefix" => null,
                "first_name" => null,
                "middle_name" => null,
                "last_name" => null,
                "email" => null,
                "contact_id" => "CO0001",
                "contact_status" => "active",
                "tax_number" => null,
                "city" => null,
                "state" => null,
                "country" => null,
                "address_line_1" => null,
                "address_line_2" => null,
                "zip_code" => null,
                "dob" => null,
                "mobile" => "",
                "landline" => null,
                "alternate_number" => null,
                "pay_term_number" => null,
                "pay_term_type" => null,
                "credit_limit" => "0.0000",
                "created_by" => 1,
                "balance" => "0.0000",
                "total_rp" => 0,
                "total_rp_used" => 0,
                "total_rp_expired" => 0,
                "is_default" => 1,
                "shipping_address" => null,
                "shipping_custom_field_details" => null,
                "is_export" => 0,
                "export_custom_field_1" => null,
                "export_custom_field_2" => null,
                "export_custom_field_3" => null,
                "export_custom_field_4" => null,
                "export_custom_field_5" => null,
                "export_custom_field_6" => null,
                "position" => null,
                "customer_group_id" => null,
                "custom_field1" => null,
                "custom_field2" => null,
                "custom_field3" => null,
                "custom_field4" => null,
                "custom_field5" => null,
                "custom_field6" => null,
                "custom_field7" => null,
                "custom_field8" => null,
                "custom_field9" => null,
                "custom_field10" => null,
                "deleted_at" => null,
                "created_at" => "2025-01-07T12:26:59.000000Z",
                "updated_at" => "2025-01-07T12:26:59.000000Z"
            ],
            "invoice_url" => "https://cloudpos.bigbcomputer.com.my/invoice/29af5a19ef7e97bad0ce0bac7f79add3",
            "payment_link" => ""
        ]
    ],
    "links" => [
        "first" => "https://cloudpos.bigbcomputer.com.my/connector/api/sell?page=1",
        "last" => "https://cloudpos.bigbcomputer.com.my/connector/api/sell?page=1",
        "prev" => null,
        "next" => null
    ],
    "meta" => [
        "current_page" => 1,
        "from" => 1,
        "last_page" => 1,
        "path" => "https://cloudpos.bigbcomputer.com.my/connector/api/sell",
        "per_page" => 30,
        "to" => 2,
        "total" => 2
    ]
);

// Convert the data to JSON
$json_data = json_encode($post_data);

// Check for encoding errors
if ($json_data === false) {
    die('Error encoding JSON: ' . json_last_error_msg());
}

// Initialize cURL session
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI0IiwianRpIjoiMWM1NjdiNDNkN2ZmZDkwYzdiMzA2YzY2NjI2NDZiMmNhYjMwODk5YTlkOTY3MjE2ZmZkYzBhODBiZTExNzcyNGE5ZmFiNmJkNGMyMWU4MGEiLCJpYXQiOjE3NDc2NTE1MjYuMDE2NjMxLCJuYmYiOjE3NDc2NTE1MjYuMDE2NjM1LCJleHAiOjE3NzkxODc1MjUuOTkxNzksInN1YiI6IjIiLCJzY29wZXMiOltdfQ.CP5HRq9691sd733cFZaBaTb3xPbrlAyaYilal01nZw-LjocE5BbkDzzFgYjpJhNgpfca_Syxokm3MGzzcnqfY_I-hH2FBZuy_nBiGZujLQl1-01Ss75iJdT0PNSqv-rivAmBzfk1cNbZwR_9cOFKL_BdfwAwa7b8JgaqRtbNnMltJjJ2nxzCdaqW120DybP7nmQDmscVGj4x1vuSetl9rgcUtuJSbb8lLs1R-E3aybeQMx4mBiVLFWSOFAeGVwDCPcN7zOKILIV3GQt7K1dpiG_ft1xywvPogMVubXUf2cpE5cUZ2weOUsxeDnjYZF-Aq0WpVPts-fe663XZyeQ3Prv_H8Wd46Aaw0fTajyU3BBLuyENYfrwO3MUwH9rDmc3C_ZTwKhvjyPYK1QZ3oaMx8804c_XKu4q31jdDE15MkL9JdtUOIHRRcTnDDyAERb5tPW_PrF-IaaM8Qxjt0r6aKbAjfPts6tZnDB9hgfbM4zFIB-PfNijb1Gy7gdghAVd89Z6A_u8mh2b7fW2QcKZwgW33DVFakv_vq_tWN0Z208tTb2P5SypoSrE8wlRby2B6dNDkPm1HjbSndwc0XiKzke8zgqgz8XDfYoSLTCvA4Z_v7VJmsssyw0n6cUVO1Q96-NXcOdI-nMTPANyihfM4_YNKv9PJ4CufleWa9twoHI', // Replace {access_token} with your actual token
    'Content-Type: application/json',
    'Accept: application/json'
));

// Execute the request
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    die('cURL Error: ' . curl_error($ch));
}

// Get HTTP status code
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Close cURL session
curl_close($ch);

// Handle the response
if ($http_status == 200) {
    echo "Data successfully added.\n";
    echo "Response:\n";
    echo $response . "\n"; // Print the response for debugging
} else {
    echo "Error: Unable to add data. HTTP Status Code: $http_status\n";
    echo "Response:\n";
    echo $response . "\n"; // Print the response for debugging
}
?>
