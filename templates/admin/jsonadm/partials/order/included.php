<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2026
 * @package Admin
 * @subpackage Jsonadm
 */
$options = 0;
if (defined('JSON_PRETTY_PRINT')) {
    $options = JSON_PRETTY_PRINT;
}
$fields = $this->param('fields', []);
foreach ((array) $fields as $resource => $list) {
    $fields[$resource] = array_flip(explode(',', $list));
}
$build = function (\Aimeos\Map $items) use ($fields) {
    $list = [];
    $target = $this->config('admin/jsonadm/url/target');
    $cntl = $this->config('admin/jsonadm/url/controller', 'jsonadm');
    $action = $this->config('admin/jsonadm/url/action', 'get');
    $config = $this->config('admin/jsonadm/url/config', []);
    foreach ($items as $item) {
        $id = $item->get_id();
        $type = $item->get_resource_type();
        $attributes = $item->to_array(true);
        if (isset($fields[$type])) {
            $attributes = array_intersect_key($attributes, $fields[$type]);
        }
        $entry = ['id' => $id, 'type' => $type, 'attributes' => $attributes, 'links' => ['self' => $this->url($target, $cntl, $action, ['resource' => $type, 'id' => $id], [], $config), 'related' => ['href' => $this->url($target, $cntl, $action, ['resource' => $type, 'id' => null], [], $config)]]];
        if ($item instanceof \Aimeos\M_Shop\Order\Item\Iface) {
            foreach ($item->get_addresses()->flat() as $addr) {
                $entry['relationships']['order/address']['data'][] = ['id' => $addr->get_id(), 'type' => 'order/address'];
            }
            foreach ($item->get_coupons() as $code => $products) {
                $entry['relationships']['order/coupon']['data'][] = ['id' => $code, 'type' => 'order/coupon'];
            }
            foreach ($item->get_products() as $prod) {
                $entry['relationships']['order/product']['data'][] = ['id' => $prod->get_id(), 'type' => 'order/product'];
            }
            foreach ($item->get_services()->flat() as $serv) {
                $entry['relationships']['order/service']['data'][] = ['id' => $serv->get_id(), 'type' => 'order/service'];
            }
        }
        if ($item instanceof \Aimeos\M_Shop\Order\Item\Service\Iface) {
            foreach ($item->get_attribute_items()->flat() as $serv) {
                $entry['relationships']['order/service/attribute']['data'][] = ['id' => $serv->get_id(), 'type' => 'order/service/attribute'];
            }
        }
        if ($item instanceof \Aimeos\M_Shop\Order\Item\Product\Iface) {
            foreach ($item->get_attribute_items()->flat() as $serv) {
                $entry['relationships']['order/product/attribute']['data'][] = ['id' => $serv->get_id(), 'type' => 'order/service/attribute'];
            }
        }
        $list[] = $entry;
    }
    return $list;
};
$response = $build($this->get('childItems', map()));
echo json_encode($response, $options);