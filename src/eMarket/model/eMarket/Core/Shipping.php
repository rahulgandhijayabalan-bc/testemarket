<?php

/* =-=-=-= Copyright © 2018 eMarket =-=-=-=  
  |    GNU GENERAL PUBLIC LICENSE v.3.0    |
  |  https://github.com/musicman3/eMarket  |
  =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= */

declare(strict_types=1);

namespace eMarket\Core;

use Cruder\Db;

/**
 * Shipping
 *
 * @package Core
 * @author eMarket Team
 * @copyright © 2018 eMarket
 * @license GNU GPL v.3.0
 * 
 */
final class Shipping {

    /**
     * List of zones for which delivery to the buyer is available
     * @param string $region Regions numbers
     * @return array
     */
    private function shippingZonesAvailable(string $region): array {

        $data = Db::connect()
                ->read(TABLE_MODULES)
                ->selectAssoc('*')
                ->where('active=', 1)
                ->and('type=', 'shipping')
                ->save();

        $modules_data = [];
        foreach ($data as $module) {

            $mod_array = Db::connect()
                    ->read(DB_PREFIX . 'modules_shipping_' . $module['name'])
                    ->selectAssoc('*')
                    ->save();

            array_push($modules_data, $mod_array);
        }

        $output = [];

        $zones_id = Db::connect()
                ->read(TABLE_ZONES_VALUE)
                ->selectValue('zones_id')
                ->where('regions_id=', $region)
                ->save();

        if ($zones_id != FALSE) {
            foreach ($modules_data as $mod_data_ext) {
                foreach ($mod_data_ext as $mod_data) {
                    if ($mod_data['shipping_zone'] == $zones_id) {
                        array_push($output, $zones_id);
                    }
                }
            }
        }

        return $output;
    }

    /**
     * List of shipping modules for which delivery to the buyer is available
     * @param array $shipping_zones_id_available Id of zones in which the region is located
     * @return array
     */
    private function shippingModulesAvailable(array $shipping_zones_id_available): array {

        $data = Db::connect()
                ->read(TABLE_MODULES)
                ->selectAssoc('*')
                ->where('active=', 1)
                ->and('type=', 'shipping')
                ->save();

        $modules_data = [];
        foreach ($data as $module) {

            $mod_array = Db::connect()
                    ->read(DB_PREFIX . 'modules_shipping_' . $module['name'])
                    ->selectAssoc('*')
                    ->save();

            array_push($modules_data, [$module['name'] => $mod_array]);
        }
        $output = [];

        foreach ($data as $val) {
            foreach ($modules_data as $data_arr) {
                if (isset($data_arr[$val['name']])) {
                    foreach ($data_arr[$val['name']] as $data_name) {
                        if (in_array($data_name['shipping_zone'], $shipping_zones_id_available)) {
                            if (!in_array($val['name'], $output)) {
                                array_push($output, $val['name']);
                            }
                        }
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Loading data from shipping modules
     * 
     * @param string $region Data on available shipping zones for region
     */
    public function loadData(string $region): void {

        $zones_id = $this->shippingZonesAvailable($region);
        $modules_names = $this->shippingModulesAvailable($zones_id);

        foreach ($modules_names as $name) {
            $namespace = '\eMarket\Core\Modules\Shipping\\' . ucfirst($name);
            $namespace::load($zones_id);
        }
    }

    /**
     * Filtering and sorting data
     * 
     * @param array $interface_data_all Input data
     * @return array|FALSE
     */
    public static function filterData(array $interface_data_all): array|bool {

        if (count($interface_data_all) > 0) {
            $chanel_minimum_price = array_column($interface_data_all, 'chanel_minimum_price');
            array_multisort($chanel_minimum_price, SORT_ASC, $interface_data_all);

            $interface_minimum_price = [];
            foreach ($interface_data_all as $val) {
                if ($val['chanel_minimum_price'] == $interface_data_all[0]['chanel_minimum_price']) {
                    array_push($interface_minimum_price, $val);
                }
            }

            $chanel_minimum_shipping_price = array_column($interface_minimum_price, 'chanel_shipping_price');
            array_multisort($chanel_minimum_shipping_price, SORT_ASC, $interface_minimum_price);

            $interface = $interface_minimum_price[0];

            return $interface;
        } else {
            return FALSE;
        }
    }

}
