<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2026
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
$build = function (array $attr_items, $id) use ($fields) {
    $result = [];
    $type = 'criteria/attribute';
    foreach ($attr_items as $attr_item) {
        $attributes = $attr_item->to_array(true);
        if (isset($fields[$type])) {
            $attributes = array_intersect_key($attributes, $fields[$type]);
        }
        $result[] = ['type' => $type, 'id' => $attr_item->get_code(), 'attributes' => $attributes];
    }
    return $result;
};
$config_items = $this->get('configItems', []);
?>
{
	"meta": {
		"total": <?php 
echo count($config_items);
?>

	}

	<?php 
if (isset($this->errors)) {
    ?>
		, "errors": <?php 
    echo $this->partial($this->config($this->get('partial-errors', 'admin/jsonadm/partials/template-errors'), 'partials/errors'), ['errors' => $this->errors]);
    ?>

	<?php 
} else {
    ?>
		, "data": <?php 
    echo json_encode($build($config_items, $this->param('id')), $options);
    ?>

	<?php 
}
?>
}
