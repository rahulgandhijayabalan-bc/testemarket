<?php

/* =-=-=-= Copyright © 2018 eMarket =-=-=-=  
  |    GNU GENERAL PUBLIC LICENSE v.3.0    |
  |  https://github.com/musicman3/eMarket  |
  =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= */

declare(strict_types=1);

namespace eMarket\Catalog;

use eMarket\Core\{
    Authorize,
    Valid
};
use Cruder\Db;

/**
 * Checkout
 *
 * @package Catalog
 * @author eMarket Team
 * @copyright © 2018 eMarket
 * @license GNU GPL v.3.0
 * 
 */
class Checkout {

    public static $routing_parameter = 'checkout';
    public $title = 'title_checkout_index';
    public static $customer;
    public static $address_data;

    /**
     * Constructor
     *
     */
    function __construct() {
        $this->authorize();
        $this->customerData();
        $this->customerAddress();
    }

    /**
     * Authorize
     *
     */
    private function authorize(): void {
        if (Authorize::$customer == FALSE && !isset($_SESSION['without_registration_data'])) {
            header('Location: ?route=login');
            exit;
        }
    }

    /**
     * Customer Data
     *
     */
    private function customerData(): void {
        if (isset($_SESSION['without_registration_data'])) {
            $without_registration_user = json_decode($_SESSION['without_registration_user'], true)[0];
            self::$customer = [
                'id' => '',
                'address_book' => $_SESSION['without_registration_data'],
                'gender' => '',
                'firstname' => $without_registration_user['firstname'],
                'lastname' => $without_registration_user['lastname'],
                'middle_name' => '',
                'fax' => '',
                'telephone' => $without_registration_user['telephone']
            ];
        } else {

            self::$customer = Db::connect()
                            ->read(TABLE_CUSTOMERS)
                            ->selectAssoc('id, address_book, gender, firstname, lastname, middle_name, fax, telephone')
                            ->where('email=', $_SESSION['customer_email'])
                            ->orderByDesc('id')
                            ->save()[0];
        }
    }

    /**
     * Customer Address
     *
     */
    private function customerAddress(): void {
        if (Valid::inPOST('address')) {
            $address_all = json_decode(self::$customer['address_book'], true);
            if (isset($_SESSION['without_registration_data'])) {
                self::$address_data = $address_all[0];
            } else {
                self::$address_data = $address_all[Valid::inPOST('address') - 1];
            }
        }
    }

}
