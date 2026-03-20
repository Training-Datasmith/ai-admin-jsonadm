<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Admin
 * @subpackage JsonAdm
 */
namespace Aimeos\Admin\Json_Adm\Catalog;

use Psr\Http\Message\Response_Interface;
use Psr\Http\Message\Server_Request_Interface;
/**
 * JSON API catalog client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Standard extends \Aimeos\Admin\Json_Adm\Standard implements \Aimeos\Admin\Json_Adm\Common\Iface
{
    /** admin/jsonadm/catalog/decorators/excludes
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
     * @see admin/jsonadm/catalog/decorators/global
     * @see admin/jsonadm/catalog/decorators/local
     */
    /** admin/jsonadm/catalog/decorators/global
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
     *  admin/jsonadm/catalog/decorators/global = array( 'decorator1' )
     *
     * This would add the decorator named "decorator1" defined by
     * "\Aimeos\Admin\Jsonadm\Common\Decorator\Decorator1" only to the
     * "catalog" Jsonadm client.
     *
     * @param array List of decorator names
     * @since 2016.01
     * @category Developer
     * @see admin/jsonadm/common/decorators/default
     * @see admin/jsonadm/catalog/decorators/excludes
     * @see admin/jsonadm/catalog/decorators/local
     */
    /** admin/jsonadm/catalog/decorators/local
     * Adds a list of local decorators only to the Jsonadm client
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to wrap local decorators
     * ("\Aimeos\Admin\Jsonadm\Catalog\Decorator\*") around the Jsonadm
     * client.
     *
     *  admin/jsonadm/catalog/decorators/local = array( 'decorator2' )
     *
     * This would add the decorator named "decorator2" defined by
     * "\Aimeos\Admin\Jsonadm\Catalog\Decorator\Decorator2" only to the
     * "catalog" Jsonadm client.
     *
     * @param array List of decorator names
     * @since 2016.01
     * @category Developer
     * @see admin/jsonadm/common/decorators/default
     * @see admin/jsonadm/catalog/decorators/excludes
     * @see admin/jsonadm/catalog/decorators/global
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
        /** admin/jsonadm/partials/catalog/template-data
         * Relative path to the data partial template file for the catalog client
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
         * @since 2016.07
         * @category Developer
         */
        $this->view()->assign(['partial-data' => 'admin/jsonadm/partials/catalog/template-data']);
        return parent::get($request, $response);
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
        if (in_array('catalog', $include)) {
            foreach ($items as $item) {
                $list = $list->push($item)->merge($this->get_child_items(map($item->get_children()), $include));
            }
        }
        return $list;
    }
    /**
     * Retrieves the item or items and adds the data to the view
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    protected function get_items(\Aimeos\Base\View\Iface $view, Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'catalog');
        if (($key = $view->param('aggregate')) !== null) {
            $search = $this->init_criteria($manager->filter(), $view->param());
            $view->data = $manager->aggregate($search, explode(',', $key));
            return $response;
        }
        $include = ($include = $view->param('include')) !== null ? explode(',', $include) : [];
        $search = $this->init_criteria($manager->filter(), $view->param());
        $total = 1;
        if (($id = $view->param('id')) == null) {
            $view->data = $manager->search($search, $include, $total);
            $view->list_items = $this->get_list_items($view->data, $include);
            $view->child_items = map();
        } else {
            $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE;
            if (in_array('catalog', $include)) {
                $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_LIST;
            }
            $view->data = $manager->get_tree($id, $include, $level, $search);
            $view->list_items = $this->get_list_items(map([$id => $view->data]), $include);
            $view->child_items = $this->get_child_items(map($view->data->get_children()), $include);
        }
        $view->ref_items = $this->get_ref_items($view->list_items);
        $view->total = $total;
        return $response;
    }
    /**
     * Returns the list items for association relationships
     *
     * @param \Aimeos\Map $items List of items implementing \Aimeos\MShop\Common\Item\Iface
     * @param array $include List of resource types that should be fetched
     * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
     */
    protected function get_list_items(\Aimeos\Map $items, array $include): \Aimeos\Map
    {
        return $items->get_list_items(null, null, null, false)->collapse();
    }
    /**
     * Saves and returns the new or updated item
     *
     * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager responsible for the items
     * @param \stdClass $entry Object including "id" and "attributes" elements
     * @return \Aimeos\MShop\Common\Item\Iface New or updated item
     */
    protected function save_entry(\Aimeos\M_Shop\Common\Manager\Iface $manager, \stdClass $entry): \Aimeos\M_Shop\Common\Item\Iface
    {
        $target_id = $entry->targetid ?? null;
        $ref_id = $entry->refid ?? null;
        if (isset($entry->id)) {
            $item = $manager->get($entry->id);
            if (isset($entry->attributes) && $attr = (array) $entry->attributes) {
                $item = $item->from_array($attr, true);
            }
            $item = $manager->save($item);
            if (isset($entry->parentid) && $target_id !== null) {
                $manager->move($item->get_id(), $entry->parentid, $target_id, $ref_id);
            }
        } else {
            $item = $manager->create();
            if (isset($entry->attributes) && $attr = (array) $entry->attributes) {
                $item = $item->from_array($attr, true);
            }
            $item = $manager->insert($item, $target_id, $ref_id);
        }
        if (isset($entry->relationships)) {
            $this->save_relationships($manager, $item, $entry->relationships);
        }
        return $manager->get($item->get_id());
    }
}