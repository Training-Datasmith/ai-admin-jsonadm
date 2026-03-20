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
$build = function (\Aimeos\M_Shop\Catalog\Item\Iface $item, \Aimeos\Map $list_items) use ($fields) {
    $id = $item->get_id();
    $type = $item->get_resource_type();
    $params = ['resource' => $type, 'id' => $id];
    $attributes = $item->to_array(true);
    $target = $this->config('admin/jsonadm/url/target');
    $cntl = $this->config('admin/jsonadm/url/controller', 'jsonadm');
    $action = $this->config('admin/jsonadm/url/action', 'get');
    $config = $this->config('admin/jsonadm/url/config', []);
    if (isset($fields[$type])) {
        $attributes = array_intersect_key($attributes, $fields[$type]);
    }
    $result = ['id' => $id, 'type' => $type, 'attributes' => $attributes, 'links' => ['self' => $this->url($target, $cntl, $action, $params, [], $config)], 'relationships' => []];
    foreach ($item->get_children() as $child_item) {
        $type = $child_item->get_resource_type();
        $result['relationships'][$type]['data'][] = ['id' => $child_item->get_id(), 'type' => $type];
    }
    foreach ($list_items as $list_id => $list_item) {
        if ($list_item->get_parent_id() == $id) {
            $type = $list_item->get_domain();
            $params = ['resource' => $list_item->get_resource_type(), 'id' => $list_id];
            $result['relationships'][$type]['data'][] = ['id' => $list_item->get_ref_id(), 'type' => $type, 'attributes' => $list_item->to_array(true), 'links' => ['self' => $this->url($target, $cntl, $action, $params, [], $config)]];
        }
    }
    return $result;
};
$data = $this->get('data', []);
$list_items = $this->get('listItems', map());
if (is_map($data)) {
    $response = [];
    foreach ($data as $item) {
        $response[] = $build($item, $list_items);
    }
} elseif ($data !== null) {
    $response = $build($data, $list_items);
} else {
    $response = null;
}
echo json_encode($response, $options);