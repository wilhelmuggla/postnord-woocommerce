<?php

/**
 * 
 *
 * 
 *
 * @since      1.0.0
 * @package    Postnord_Woocommerce
 * @subpackage Postnord_Woocommerce/includes
 * @author     OwlHub AB <wilhelm@owlhub.se>
 */
class Postnord_Woocommerce_Labels
{

    /**
     *
     *
     * @since    1.0.0
     */

    function __construct()
    {

        //add_action('init', [$this, 'generate_label'], 10, 0);
        $this->auto_generate_labels();
    }


    function auto_generate_labels()
    {
        if (get_option('postnord_autogenerate_labels')) {
            switch (get_option('postnord_wc_status_name')) {
                case "wc-pending":
                    add_action('woocommerce_order_status_pending', [$this, 'generate_label'], 1, 1);
                    break;
                case "wc-processing":
                    add_action('woocommerce_order_status_processing', [$this, 'generate_label'], 1, 1);
                    break;
                case "wc-on-hold":
                    add_action('woocommerce_order_status_on-hold', [$this, 'generate_label'], 1, 1);
                    break;
                case "wc-completed":
                    add_action('woocommerce_order_status_completed', [$this, 'generate_label'], 1, 1);
                    break;
                case "wc-cancelled":
                    add_action('woocommerce_order_status_cancelled', [$this, 'generate_label'], 1, 1);
                    break;
                case "wc-refunded":
                    add_action('woocommerce_order_status_efunded', [$this, 'generate_label'], 1, 1);
                    break;
                case "wc-failed":
                    add_action('woocommerce_order_status_failed"', [$this, 'generate_label'], 1, 1);
                    break;
            }
        }
    }


    function get_shipping_address($order_id)
    {
        $order = wc_get_order($order_id);

        if (function_exists('wc_get_order') && $order_id > 0) {

            return array(
                "party" => [
                    "nameIdentification" => [
                        "name" => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name()
                    ],
                    "address" => [
                        "streets" => [
                            $order->get_shipping_address_1()
                        ],
                        "postalCode" => $order->get_shipping_postcode(),
                        "city" => $order->get_shipping_city(),
                        "countryCode" => "SE"
                    ],
                    "contact" => [
                        "contactName" => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                        "emailAddress" => $order->get_billing_email(),
                        "smsNo" => $order->get_billing_phone()
                    ]
                ]
            );
        } 
    }

    function generate_label($order_id)
    {

        $package_weight = $this->my_get_total_order_weight($order_id);

        //only create label if its not created yet
        if (!metadata_exists('post', $order_id, 'postnord_tracking_id')) :

            if (get_option('postnord_sandbox'))
                $base_url = 'atapi2.postnord.com';
            else
                $base_url = 'api2.postnord.com';

            $url = 'https://' . $base_url . '/rest/shipment/v3/edi/labels/pdf?apikey=' . get_option('postnord_api_key') . '&paperSize='.get_option('postnord_wc_printer_size').'&rotate=0&multiPDF=false&labelType=standard&pnInfoText=false&labelsPerPage=100&page=1&processOffline=false&storeLabel=false';
            $date = date(DateTime::RFC3339, time());

            //values from postnord
            $applicationId = 11438;
            $partyId = get_option('postnord_customer_number');

            $company_name = get_option('postnord_company_name');
            $address_street = get_option('postnord_address_street');
            $address_zip = get_option('postnord_address_zipcode');
            $address_city = get_option('postnord_address_city');


            $jayParsedAry = [
                "messageDate" => $date,
                "messageId" => "id:",
                "application" => [
                    "applicationId" => $applicationId,
                    "name" => "PostNord",
                    "version" => "1.0"
                ],
                "updateIndicator" => "Original",
                "shipment" => [
                    [
                        "shipmentIdentification" => [
                            "shipmentId" => "0"
                        ],
                        "dateAndTimes" => [
                            "loadingDate" => $date
                        ],
                        "service" => [
                            "basicServiceCode" => "86",
                            "additionalServiceCode" => [
                                "A3"
                            ]
                        ],
                        "numberOfPackages" => [
                            "value" => 1
                        ],
                        "totalGrossWeight" => [
                            "value" => $package_weight,
                            "unit" => "KGM"
                        ],
                        "parties" => [
                            "consignor" => [
                                "issuerCode" => "Z12",
                                "partyIdentification" => [
                                    "partyId" => $partyId,
                                    "partyIdType" => "160"
                                ],
                                "party" => [
                                    "nameIdentification" => [
                                        "name" => $company_name
                                    ],
                                    "address" => [
                                        "streets" => [
                                            $address_street
                                        ],
                                        "postalCode" => $address_zip,
                                        "city" => $address_city,
                                        "countryCode" => "SE"
                                    ],
                                    "contact" => [
                                        "emailAddress" => 'info@powear.se',
                                    ]
                                ]
                            ],
                            "consignee" => $this->get_shipping_address($order_id),
                        ],
                        "goodsItem" => [
                            [
                                "packageTypeCode" => "PC",
                                "items" => [
                                    [
                                        "itemIdentification" => [
                                            "itemId" => "0",
                                            "itemIdType" => "SSCC"
                                        ],
                                        "grossWeight" => [
                                            "value" => $package_weight,
                                            "unit" => "KGM"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            $response = wp_remote_post(
                $url,
                array(
                    'method'      => 'POST',
                    'timeout'     => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array('Content-Type' => 'application/json'),
                    'body'        => json_encode($jayParsedAry),
                    'cookies'     => array()
                )
            );


            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                echo "Something went wrong: $error_message";
            } else {
                if ($response['response']['code'] == '200') {
                    $body = json_decode($response['body']);
                    $tracking_id = $body->bookingResponse->idInformation[0]->ids[0]->value;
                    $base64PDF = $body->labelPrintout[0]->printout->data;
                    $data = base64_decode($base64PDF);

                    if (!file_exists(wp_upload_dir()['basedir'] . '/labels')) {
                        mkdir(wp_upload_dir()['basedir'] . '/labels', 0777, true);
                    }

                    if (file_put_contents(wp_upload_dir()['basedir'] . '/labels/' . $tracking_id . '.pdf', $data))
                        update_post_meta($order_id, 'postnord_tracking_id', $tracking_id);
                    else {
                        echo 'error saving label';
                        update_post_meta($order_id, 'postnord_tracking_id', $tracking_id);
                    }
                } else {
                    print_r($response);
                    echo 'Something went wrong when generating the label.';
                }
            }

        else :
            echo 'tracking label already generated';
        endif;
    }

    function my_get_total_order_weight($order_id)
    {
        $order        = wc_get_order($order_id);
        $order_items  = $order->get_items();
        $total_qty    = 0;
        $total_weight = 0;

        //add extra weight
        $total_weight += get_option('postnord_wc_extra_weight') * 0.001;

        foreach ($order_items as $item_id => $product_item) {
            $product = $product_item->get_product();
            if (!$product) continue;
            $product_weight  = $product->get_weight();
            $quantity        = $product_item->get_quantity();
            $total_qty      += $quantity;
            $total_weight   += floatval($product_weight * $quantity);
        }
        return $total_weight;
    }
}
