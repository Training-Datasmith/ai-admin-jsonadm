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
        $list[] = ['id' => $id, 'type' => $type, 'attributes' => $attributes, 'links' => ['self' => $this->url($target, $cntl, $action, ['resource' => $type, 'id' => $id], [], $config), 'related' => ['href' => $this->url($target, $cntl, $action, ['resource' => $type, 'id' => null], [], $config)]]];
    }
    return $list;
};
$response = $build($this->get('childItems', map()));
$response = array_merge($response, $build($this->get('refItems', map())));
echo json_encode($response, $options);