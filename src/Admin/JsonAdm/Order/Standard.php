<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Admin
 * @subpackage JsonAdm
 */
namespace Aimeos\Admin\Json_Adm\Order;

use Psr\Http\Message\Response_Interface;
use Psr\Http\Message\Server_Request_Interface;
/**
 * JSON API order client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Standard extends \Aimeos\Admin\Json_Adm\Standard implements \Aimeos\Admin\Json_Adm\Common\Iface
{
    /** admin/jsonadm/order/decorators/excludes
     * Excludes decorators added by the "common" option from the JSON API clients
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to remove a decorator added via
     * "admin/jsonadm/common/decorators/default" before they are wrapped
     * around the Jsonadm client.
     *
     *  admin/jsonadm/decorators/excludes = array( 'decorator1' )
     *
     * This would remove the decorator named "decorator1" from the list of
     * common decorators ("\Aimeos\Admin\JsonAdm\Common\Decorator\*") added via
     * "admin/jsonadm/common/decorators/default" for the JSON API client.
     *
     * @param array List of decorator names
     * @since 2016.01
     * @category Developer
     * @see admin/jsonadm/common/decorators/default
     * @see admin/jsonadm/order/decorators/global
     * @see admin/jsonadm/order/decorators/local
     */
    /** admin/jsonadm/order/decorators/global
     * Adds a list of globally available decorators only to the Jsonadm client
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to wrap global decorators
     * ("\Aimeos\Admin\Jsonadm\Common\Decorator\*") around the Jsonadm
     * client.
     *
     *  admin/jsonadm/order/decorators/global = array( 'decorator1' )
     *
     * This would add the decorator named "decorator1" defined by
     * "\Aimeos\Admin\Jsonadm\Common\Decorator\Decorator1" only to the
     * "order" Jsonadm client.
     *
     * @param array List of decorator names
     * @since 2016.01
     * @category Developer
     * @see admin/jsonadm/common/decorators/default
     * @see admin/jsonadm/order/decorators/excludes
     * @see admin/jsonadm/order/decorators/local
     */
    /** admin/jsonadm/order/decorators/local
     * Adds a list of local decorators only to the Jsonadm client
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to wrap local decorators
     * ("\Aimeos\Admin\Jsonadm\Order\Decorator\*") around the Jsonadm
     * client.
     *
     *  admin/jsonadm/order/decorators/local = array( 'decorator2' )
     *
     * This would add the decorator named "decorator2" defined by
     * "\Aimeos\Admin\Jsonadm\Order\Decorator\Decorator2" only to the
     * "order" Jsonadm client.
     *
     * @param array List of decorator names
     * @since 2016.01
     * @category Developer
     * @see admin/jsonadm/common/decorators/default
     * @see admin/jsonadm/order/decorators/excludes
     * @see admin/jsonadm/order/decorators/global
     */
    /**
     * Returns the requested resource or the resource list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function get(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        /** admin/jsonadm/partials/order/template-data
         * Relative path to the data partial template file for the order client
         *
         * Partials are templates which are reused in other templates and generate
         * reoccuring blocks filled with data from the assigned values. The data
         * partial creates the "data" part for the JSON API response.
         *
         * The partial template files are usually stored in the templates/partials/ folder
         * of the core or the extensions. The configured path to the partial file must
         * be relative to the templates/ folder, e.g. "partials/data".
         *
         * @param string Relative path to the template file
         * @since 2016.01
         * @category Developer
         */
        $this->view()->assign(['partial-data' => 'admin/jsonadm/partials/order/template-data', 'partial-included' => 'admin/jsonadm/partials/order/template-included']);
        return parent::get($request, $response);
    }
    /**
     * Retrieves the item or items and adds the data to the view
     *
     * @param \Aimeos\Base\View\Iface $view View instance
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    protected function get_items(\Aimeos\Base\View\Iface $view, Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, $this->get_path());
        $search = $manager->filter(false, true);
        if (($key = $view->param('aggregate')) !== null) {
            $search = $this->init_criteria($search, $view->param());
            $view->data = $manager->aggregate($search, explode(',', $key), $view->param('value'), $view->param('type'));
            return $response;
        }
        $total = 1;
        $include = ($include = $view->param('include')) !== null ? explode(',', $include) : [];
        if (($id = $view->param('id')) == null) {
            $search = $this->init_criteria($search, $view->param());
        } else {
            $search->set_conditions($search->compare('==', 'order.id', $id));
        }
        $view->data = $manager->search($search, [], $total);
        $view->child_items = $this->get_child_items($view->data, $include);
        $view->list_items = $this->get_list_items($view->data, $include);
        $view->ref_items = $this->get_ref_items($view->list_items);
        $view->total = $total;
        return $response;
    }
    /**
     * Returns the items with parent/child relationships
     *
     * @param \Aimeos\Map $items List of items implementing \Aimeos\MShop\Common\Item\Iface
     * @param array $include List of resource types that should be fetched
     * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface
     */
    protected function get_child_items(\Aimeos\Map $items, array $include): \Aimeos\Map
    {
        $list = map();
        $ids = map($items)->get_id()->to_array();
        $manager = \Aimeos\M_Shop::create($this->context(), 'order');
        $search = $manager->filter(false, true);
        $search->set_conditions($search->and([$search->compare('==', 'order.id', $ids), $search->get_conditions()]));
        $orders = $manager->search($search, $include);
        $list = $list->merge($orders)->merge($orders->get_addresses()->flat())->merge($orders->get_coupons()->flat())->merge($orders->get_products()->flat())->merge($orders->get_services()->flat());
        foreach ($orders->get_products()->flat() as $row) {
            $list->merge($row->get_attribute_items());
        }
        foreach ($orders->get_services()->flat() as $row) {
            $list->merge($row->get_attribute_items());
        }
        if (in_array('order/status', $include)) {
            $ids = $items->keys()->to_array();
            $manager = \Aimeos\M_Shop::create($this->context(), 'order/status');
            $search = $manager->filter();
            $search->set_conditions($search->compare('==', 'order.status.parentid', $ids));
            $list = $list->merge($manager->search($search));
        }
        return $list;
    }
}