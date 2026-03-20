<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2026
 * @package Admin
 * @subpackage Jsonadm
 */
$target = $this->config('admin/jsonadm/url/target');
$cntl = $this->config('admin/jsonadm/url/controller', 'jsonadm');
$action = $this->config('admin/jsonadm/url/action', 'get');
$config = $this->config('admin/jsonadm/url/config', []);
$resources = $attributes = [];
$site = $this->param('site', 'default');
foreach ($this->get('resources', []) as $resource) {
    $resources[$resource] = $this->url($target, $cntl, $action, ['site' => $site, 'resource' => $resource], [], $config);
}
foreach ($this->get('attributes', []) as $attr) {
    $attributes[$attr->get_code()] = $attr->to_array(true);
}
?>
{
	"meta": {
		"prefix": <?php 
echo json_encode($this->get('prefix'));
?>,
		"resources": <?php 
echo json_encode($resources);
?>,
		"attributes": <?php 
echo json_encode($attributes);
?>

		<?php 
if ($this->csrf()->name() != '') {
    ?>
			, "csrf": {
				"name": "<?php 
    echo $this->csrf()->name();
    ?>",
				"value": "<?php 
    echo $this->csrf()->value();
    ?>"
			}
		<?php 
}
?>
	}

	<?php 
if (isset($this->errors)) {
    ?>
		,"errors": <?php 
    echo $this->partial($this->config('admin/jsonadm/partials/template-errors', 'partials/errors'), ['errors' => $this->errors]);
    ?>

	<?php 
}
?>

}
