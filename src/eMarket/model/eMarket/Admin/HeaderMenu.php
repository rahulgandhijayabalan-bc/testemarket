<?php

/* =-=-=-= Copyright © 2018 eMarket =-=-=-=  
  |    GNU GENERAL PUBLIC LICENSE v.3.0    |
  |  https://github.com/musicman3/eMarket  |
  =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= */

declare(strict_types=1);

namespace eMarket\Admin;

use eMarket\Core\{
    Settings,
    Valid
};
use Cruder\Db;

/**
 * Header Menu
 *
 * @package Admin
 * @author eMarket Team
 * @copyright © 2018 eMarket
 * @license GNU GPL v.3.0
 * 
 */
class HeaderMenu {

    public static $menu_market = 'market';
    public static $menu_sales = 'sales';
    public static $menu_marketing = 'marketing';
    //public static $menu_production = 'production';
    public static $menu_tools = 'tools';
    public static $menu_settings = 'settings';
    public static $level = [];
    public static $menu = [];
    public static $submenu = [];
    public static $param_1 = [];
    public static $param_2 = [];
    public static $staff_data = false;

    /**
     * Constructor
     *
     */
    function __construct() {
        $this->init();
        $this->initModules();
        $this->levelOne();
        $this->staffInit();
        $this->staticLevels();
        $this->exit();
    }

    /**
     * Init
     *
     */
    private function init(): void {
        $files = glob(ROOT . '/model/eMarket/Admin/*');
        foreach ($files as $filename) {
            $namespace = '\eMarket\Admin\\' . pathinfo($filename, PATHINFO_FILENAME);
            if (method_exists($namespace, 'menu')) {
                $namespace::menu();
            }
        }
    }

    /**
     * Init Modules
     *
     */
    private function initModules(): void {
        $group = glob(ROOT . '/modules/*');
        $files = [];
        foreach ($group as $group_name) {
            $path = glob($group_name . '/*');
            foreach ($path as $value) {
                array_push($files, $value);
            }
        }
        foreach ($files as $filename) {
            $namespace = '\eMarket\Core\Modules\\' . ucfirst(pathinfo(dirname($filename, 1), PATHINFO_FILENAME)) . '\\' . ucfirst(pathinfo($filename, PATHINFO_FILENAME));
            if (method_exists($namespace, 'menu') && $namespace::status() != false) {
                $namespace::menu();
            }
        }
    }

    /**
     * Level One
     *
     */
    private function levelOne(): void {
        self::$level['0'][self::$menu_market] = ['#', lang('menu_market'), 'true', 'bi-cart4'];
        self::$level['1'][self::$menu_settings] = ['#', lang('menu_settings'), 'true', 'bi-sliders2-vertical'];
        self::$level['2'][self::$menu_sales] = ['#', lang('menu_sales'), 'true', 'bi-calculator'];
        self::$level['3'][self::$menu_marketing] = ['#', lang('menu_marketing'), 'true', 'bi-graph-up'];
        //self::$level['0.01'][self::$menu_production] = ['#', lang('menu_production'), 'true', 'bi-nut'];
        self::$level['4'][self::$menu_tools] = ['#', lang('menu_tools'), 'true', 'bi-tools'];

        $sort_level = [];
        ksort(self::$level);

        foreach (self::$level as $val) {
            $sort_level[key($val)] = $val[key($val)];
        }

        self::$level = $sort_level;
    }

    /**
     * Set parameters
     *
     * @param string $param_1 Parameter 1
     * @param string $param_2 Parameter 2
     */
    public static function setParameters(string $param_1, string $param_2): void {
        self::$param_1 = $param_1;
        self::$param_2 = $param_2;
    }

    /**
     * Get parameters
     *
     * @return array Parameters
     */
    public static function getParameters(): array {
        return [self::$param_1, self::$param_2];
    }

    /**
     * Get parameters
     *
     * @param string $flag Flag
     */
    public static function clearParameters(string $flag): void {
        if ($flag == 'false') {
            self::$param_1 = '';
            self::$param_2 = '';
        }
    }

    /**
     * Static levels
     *
     */
    private function staticLevels(): void {

        //LANGUAGES
        self::$level['languages'] = ['#', lang('menu_languages'), 'true', 'bi-translate'];
        for ($lng = 0; $lng < count(lang('#lang_all')); $lng++) {
            self::$menu['languages'][$lng] = [Settings::langCurrencyPath() . '&language=' . lang('#lang_all')[$lng], 'bi-caret-right-fill', lang('language_name', lang('#lang_all')[$lng]), '', 'false'];
        }

        //HELP
        self::$level['help'] = ['#', lang('menu_extra'), 'true', 'bi-lightbulb-fill'];
        self::$menu['help'][0] = ['http://emarketforum.com', 'bi-chat-quote', lang('menu_support'), 'target="_blank"', 'false'];
        self::$menu['help'][1] = ['/', 'bi-bag', lang('menu_catalog'), 'target="_blank"', 'false'];
    }

    /**
     * Exit
     *
     */
    private function exit(): void {
        //EXIT
        self::$level['exit'] = ['?route=login&logout=ok', lang('menu_exit'), 'false', 'bi-box-arrow-right'];
    }

    /**
     * Staff init
     *
     */
    private function staffInit(): void {
        if (isset($_SESSION['login'])) {

            $staff_permission = Db::connect()
                    ->read(TABLE_ADMINISTRATORS)
                    ->selectValue('permission')
                    ->where('login=', $_SESSION['login'])
                    ->save();

            if ($staff_permission != 'admin') {

                $staff_permissions = Db::connect()
                        ->read(TABLE_STAFF_MANAGER)
                        ->selectAssoc('permissions')
                        ->where('id=', $staff_permission)
                        ->save();

                self::$staff_data = json_decode($staff_permissions[0]['permissions'], true);

                $menu_array = [];
                foreach (self::$menu as $menu_key => $menu_val) {
                    foreach ($menu_val as $menu_item) {
                        if (in_array($menu_item[0], self::$staff_data)) {
                            $menu_array[$menu_key][] = $menu_item;
                        }

                        if (strpos($menu_item[0], '&language=')) {
                            $lang_string = strstr($menu_item[0], '&language=');
                            foreach (self::$staff_data as $staff_string) {
                                if (strpos($staff_string, $lang_string)) {
                                    $menu_array[$menu_key][] = $menu_item;
                                }
                            }
                        }
                    }
                }

                $level_array = [];
                foreach (self::$level as $key => $val) {
                    if (array_key_exists($key, $menu_array)) {
                        $level_array[$key] = $val;
                    }
                }
                self::$level = $level_array;
                self::$menu = $menu_array;

                $this->permissions();
            }
        }
    }

    /**
     * Permissions
     *
     */
    private function permissions(): void {
        $count = 0;
        foreach (self::$staff_data as $page) {
            if (Valid::inGET('route') == 'page_not_found') {
                $count++;
            }
            if (strpos('/?route=' . Valid::inGET('route'), $page)) {
                $count++;
            }
            if (!Valid::inGET('route')) {
                $count++;
            }
            if (Valid::inGET('route') == 'modules/edit' && strpos(Valid::inSERVER('REQUEST_URI'), $page)) {
                $count++;
            }
        }

        if ($count == 0) {
            header('Location: ?route=page_not_found');
            exit;
        }
    }

}
