<?php

/* =-=-=-= Copyright © 2018 eMarket =-=-=-=  
  |    GNU GENERAL PUBLIC LICENSE v.3.0    |
  |  https://github.com/musicman3/eMarket  |
  =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= */

declare(strict_types=1);

namespace eMarket\Constructor;

use eMarket\Core\{
    Interfaces\ConstructorInterface,
    Settings
};

/**
 * Install class
 *
 * @package Constructor
 * @author eMarket Team
 * @copyright © 2018 eMarket
 * @license GNU GPL v.3.0
 * 
 */
class Install implements ConstructorInterface {

    /**
     * Init
     *
     */
    public static function init(): string|bool {

        if (Settings::path() == 'install') {
            return getenv('DOCUMENT_ROOT') . '/view/' . Settings::template() . '/install/constructor.php';
        }
        return false;
    }

}
