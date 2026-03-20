<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Admin
 * @subpackage JsonAdm
 */
namespace Aimeos\Admin\Json_Adm;

/**
 * JSON API common client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
abstract class Base implements \Aimeos\Admin\Json_Adm\Iface, \Aimeos\Macro\Iface
{
    use \Aimeos\Macro\Macroable;
    private \Aimeos\M_Shop\Context_Iface $context;
    private ?\Aimeos\Base\View\Iface $view = null;
    private ?\Aimeos\Bootstrap $aimeos = null;
    /**
     * Initializes the client
     *
     * @param \Aimeos\MShop\ContextIface $context MShop context object
     * @param string $path Name of the client separated by slashes, e.g "product/property"
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context, private string $path)
    {
        $this->context = $context;
    }
    /**
     * Returns the Aimeos bootstrap object
     *
     * @return \Aimeos\Bootstrap The Aimeos bootstrap object
     */
    protected function get_aimeos(): \Aimeos\Bootstrap
    {
        if (!isset($this->aimeos)) {
            throw new \Aimeos\Admin\Json_Adm\Exception('Aimeos object not available', 500);
        }
        return $this->aimeos;
    }
    /**
     * Sets the Aimeos bootstrap object
     *
     * @param \Aimeos\Bootstrap $aimeos The Aimeos bootstrap object
     * @return \Aimeos\Admin\JsonAdm\Iface Reference to this object for fluent calls
     */
    public function set_aimeos(\Aimeos\Bootstrap $aimeos): \Aimeos\Admin\Json_Adm\Iface
    {
        $this->aimeos = $aimeos;
        return $this;
    }
    /**
     * Returns the view object that will generate the admin output.
     *
     * @return \Aimeos\Base\View\Iface The view object which generates the admin output
     */
    protected function view(): \Aimeos\Base\View\Iface
    {
        if (!isset($this->view)) {
            throw new \Aimeos\Admin\Json_Adm\Exception('No view available', 500);
        }
        return $this->view;
    }
    /**
     * Sets the view object that will generate the admin output.
     *
     * @param \Aimeos\Base\View\Iface $view The view object which generates the admin output
     * @return \Aimeos\Admin\JsonAdm\Iface Reference to this object for fluent calls
     */
    public function set_view(\Aimeos\Base\View\Iface $view): \Aimeos\Admin\Json_Adm\Iface
    {
        $this->view = $view;
        return $this;
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
        return map();
    }
    /**
     * Returns the context item object
     *
     * @return \Aimeos\MShop\ContextIface Context object
     */
    protected function context(): \Aimeos\M_Shop\Context_Iface
    {
        return $this->context;
    }
    /**
     * Returns the list of domains that are available as resources
     *
     * @param \Aimeos\Base\View\Iface $view View object with "resource" parameter
     * @return array List of domain names
     */
    protected function get_domains(\Aimeos\Base\View\Iface $view): array
    {
        if (($domains = $view->param('resource')) == '') {
            /** admin/jsonadm/domains
             * A list of domain names whose clients are available for the JSON API
             *
             * The HTTP OPTIONS method returns a list of resources known by the
             * JSON API including their URLs. The list of available resources
             * can be exteded dynamically be implementing a new Jsonadm client
             * class handling request for this new domain.
             *
             * To add the new domain client to the list of resources returned
             * by the HTTP OPTIONS method, you have to add its name in lower case
             * to the existing configuration.
             *
             * @param array List of domain names
             * @since 2016.01
             * @category Developer
             * @see admin/jsonadm/resources
             */
            $domains = $this->context()->config()->get('admin/jsonadm/domains', []);
        }
        return (array) $domains;
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
        return $items->filter(fn($item) => $item instanceof \Aimeos\M_Shop\Common\Item\Lists\Iface);
    }
    /**
     * Returns the path to the client
     *
     * @return string Client path, e.g. "product/property"
     */
    protected function get_path(): string
    {
        return $this->path;
    }
    /**
     * Returns the items associated via a lists table
     *
     * @param \Aimeos\Map $listItems List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
     * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface
     */
    protected function get_ref_items(\Aimeos\Map $list_items): \Aimeos\Map
    {
        $list = [];
        foreach ($list_items as $list_item) {
            if ($ref_item = $list_item->get_ref_item()) {
                $list[$ref_item->get_resource_type() . '-' . $ref_item->get_id()] = $ref_item;
            }
        }
        return map($list);
    }
    /**
     * Returns the list of allowed resources
     *
     * @param \Aimeos\Base\View\Iface $view View object with "access" helper
     * @param array $resources List of all available resources
     * @return array List of allowed resources
     */
    protected function get_allowed_resources(\Aimeos\Base\View\Iface $view, array $resources): array
    {
        $config = $this->context()->config();
        $allowed = [];
        foreach ($resources as $resource) {
            if ($view->access($config->get('admin/jsonadm/resource/' . $resource . '/groups', [])) === true) {
                $allowed[] = $resource;
            }
        }
        return $allowed;
    }
    /**
     * Returns the list of additional resources
     *
     * @param \Aimeos\Base\View\Iface $view View object with "resource" parameter
     * @return array List of domain names
     */
    protected function get_resources(\Aimeos\Base\View\Iface $view): array
    {
        /** admin/jsonadm/resources
         * A list of additional resources name whose clients are available for the JSON API
         *
         * The HTTP OPTIONS method returns a list of resources known by the
         * JSON API including their URLs. The list of available resources
         * can be exteded dynamically be implementing a new Jsonadm client
         * class handling request for this new domain.
         *
         * The resource config lists the resources that are not automatically
         * derived from the admin/jsonadm/domains configuration.
         *
         * @param array List of domain names
         * @since 2017.07
         * @category Developer
         * @see admin/jsonadm/domains
         */
        return (array) $view->config('admin/jsonadm/resources', []);
    }
    /**
     * Initializes the criteria object based on the given parameter
     *
     * @param \Aimeos\Base\Criteria\Iface $criteria Criteria object
     * @param array $params List of criteria data with condition, sorting and paging
     * @return \Aimeos\Base\Criteria\Iface Initialized criteria object
     */
    protected function init_criteria(\Aimeos\Base\Criteria\Iface $criteria, array $params): \Aimeos\Base\Criteria\Iface
    {
        return $criteria->order($params['sort'] ?? [])->add($criteria->parse($params['filter'] ?? []))->slice($params['page']['offset'] ?? 0, $params['page']['limit'] ?? 25);
    }
    /**
     * Creates of updates several items at once
     *
     * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager responsible for the items
     * @param \stdClass $request Object with request body data
     * @return \Aimeos\Map List of items
     */
    protected function save_data(\Aimeos\M_Shop\Common\Manager\Iface $manager, \stdClass $request): \Aimeos\Map
    {
        $data = [];
        if (isset($request->data)) {
            foreach ((array) $request->data as $entry) {
                $data[] = $this->save_entry($manager, $entry);
            }
        }
        return map($data);
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
        if (isset($entry->id)) {
            $item = $manager->get($entry->id);
        } else {
            $item = $manager->create();
        }
        if (isset($entry->attributes) && $attr = (array) $entry->attributes) {
            if ($item instanceof \Aimeos\M_Shop\Common\Item\Config\Iface) {
                $key = str_replace('/', '.', $this->path) . '.config';
                $attr[$key] = (array) ($attr[$key] ?? []);
            }
            $item = $item->from_array($attr, true);
        }
        $item = $manager->save($item);
        if (isset($entry->relationships)) {
            $this->save_relationships($manager, $item, $entry->relationships);
        }
        return $manager->get($item->get_id());
    }
    /**
     * Saves the item references associated via the list
     *
     * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager responsible for the items
     * @param \Aimeos\MShop\Common\Item\Iface $item Domain item with an unique ID set
     * @param \stdClass $relationships Object including the <domain>/data/attributes structure
     */
    protected function save_relationships(\Aimeos\M_Shop\Common\Manager\Iface $manager, \Aimeos\M_Shop\Common\Item\Iface $item, \stdClass $relationships)
    {
        $id = $item->get_id();
        $list_manager = $manager->get_sub_manager('lists');
        foreach ((array) $relationships as $domain => $list) {
            if (isset($list->data)) {
                foreach ((array) $list->data as $data) {
                    $list_item = $list_manager->create()->set_type('default');
                    if (isset($data->attributes) && $attr = (array) $data->attributes) {
                        $key = str_replace('/', '.', $this->path) . '.config';
                        $attr[$key] = (array) ($attr[$key] ?? []);
                        $list_item = $list_item->from_array($attr, true);
                    }
                    if (isset($data->id)) {
                        $list_item->set_ref_id($data->id);
                    }
                    $list_item->set_parent_id($id)->set_domain($domain);
                    $list_manager->save($list_item, false);
                }
            }
        }
    }
}