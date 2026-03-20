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
$build = function (\Aimeos\M_Shop\Order\Item\Iface $item, \Aimeos\Map $child_items) use ($fields) {
    $id = $item->get_id();
    $type = $item->get_resource_type();
    $params = ['resource' => $item->get_resource_type(), 'id' => $id];
    $attributes = $item->to_array(true);
    $target = $this->config('admin/jsonadm/url/target');
    $cntl = $this->config('admin/jsonadm/url/controller', 'jsonadm');
    $action = $this->config('admin/jsonadm/url/action', 'get');
    $config = $this->config('admin/jsonadm/url/config', []);
    if (isset($fields[$type])) {
        $attributes = array_intersect_key($attributes, $fields[$type]);
    }
    $result = ['id' => $id, 'type' => $type, 'attributes' => $attributes, 'links' => ['self' => $this->url($target, $cntl, $action, $params, [], $config)], 'relationships' => []];
    foreach ($child_items as $child_item) {
        $type = $child_item->get_resource_type();
        $params = ['resource' => $child_item->get_resource_type(), 'id' => $child_item->get_id()];
        $result['relationships'][$type]['data'][] = ['id' => $child_item->get_id(), 'type' => $type, 'links' => ['self' => $this->url($target, $cntl, $action, $params, [], $config)]];
    }
    return $result;
};
$data = $this->get('data', map());
$child_items = $this->get('childItems', map());
if (is_map($data)) {
    $response = [];
    foreach ($data as $item) {
        $response[] = $build($item, $child_items);
    }
} elseif ($data !== null) {
    $response = $build($data, $child_items);
} else {
    $response = null;
}
echo json_encode($response, $options);