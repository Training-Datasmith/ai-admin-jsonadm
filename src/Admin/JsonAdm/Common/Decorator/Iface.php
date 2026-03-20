<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Admin
 * @subpackage JsonAdm
 */
namespace Aimeos\Admin\Json_Adm\Common\Decorator;

/**
 * Decorator interface for JSON API client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
interface Iface extends \Aimeos\Admin\Json_Adm\Iface
{
    /**
     * Initializes a new client decorator object
     *
     * @param \Aimeos\Admin\JsonAdm\Iface $client Client object
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param string $path Name of the client separated by slashes, e.g "product/stock"
     */
    public function __construct(\Aimeos\Admin\Json_Adm\Iface $client, \Aimeos\M_Shop\Context_Iface $context, string $path);
}