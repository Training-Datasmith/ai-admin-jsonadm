<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2026
 * @package Admin
 * @subpackage JsonAdm
 */
namespace Aimeos\Admin\Json_Adm\Plugin\Config;

use Psr\Http\Message\Response_Interface;
use Psr\Http\Message\Server_Request_Interface;
/**
 * JSON API plugin config client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Standard extends \Aimeos\Admin\Json_Adm\Standard implements \Aimeos\Admin\Json_Adm\Common\Iface
{
    /** admin/jsonadm/plugin/config/decorators/excludes
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
     * @since 2017.07
     * @category Developer
     * @see admin/jsonadm/common/decorators/default
     * @see admin/jsonadm/plugin/config/decorators/global
     * @see admin/jsonadm/plugin/config/decorators/local
     */
    /** admin/jsonadm/plugin/config/decorators/global
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
     *  admin/jsonadm/plugin/config/decorators/global = array( 'decorator1' )
     *
     * This would add the decorator named "decorator1" defined by
     * "\Aimeos\Admin\Jsonadm\Common\Decorator\Decorator1" only to the
     * "plugin/config" Jsonadm client.
     *
     * @param array List of decorator names
     * @since 2017.07
     * @category Developer
     * @see admin/jsonadm/common/decorators/default
     * @see admin/jsonadm/plugin/config/decorators/excludes
     * @see admin/jsonadm/plugin/config/decorators/local
     */
    /** admin/jsonadm/plugin/config/decorators/local
     * Adds a list of local decorators only to the Jsonadm client
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to wrap local decorators
     * ("\Aimeos\Admin\Jsonadm\Plugin\Config\Decorator\*") around the Jsonadm
     * client.
     *
     *  admin/jsonadm/plugin/config/decorators/local = array( 'decorator2' )
     *
     * This would add the decorator named "decorator2" defined by
     * "\Aimeos\Admin\Jsonadm\Plugin\Config\Decorator\Decorator2" only to the
     * "plugin/config" Jsonadm client.
     *
     * @param array List of decorator names
     * @since 2017.07
     * @category Developer
     * @see admin/jsonadm/common/decorators/default
     * @see admin/jsonadm/plugin/config/decorators/excludes
     * @see admin/jsonadm/plugin/config/decorators/global
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
        $view = $this->view();
        try {
            $response = $this->get_items($view, $request, $response);
            $status = 200;
        } catch (\Aimeos\M_Shop\Exception $e) {
            $status = 404;
            $view->errors = [['title' => $this->context()->translate('mshop', $e->get_message()), 'detail' => $e->get_trace_as_string()]];
        } catch (\Exception $e) {
            $status = 500;
            $view->errors = [['title' => $e->get_message(), 'detail' => $e->get_trace_as_string()]];
        }
        /** admin/jsonadm/plugin/config/template-get
         * Relative path to the JSON API template for GET requests
         *
         * The template file contains the code and processing instructions
         * to generate the result shown in the JSON API body. The
         * configuration string is the path to the template file relative
         * to the templates directory (usually in templates/admin/jsonadm).
         *
         * You can overwrite the template file configuration in extensions and
         * provide alternative templates. These alternative templates should be
         * named like the default one but with the string "standard" replaced by
         * an unique name. You may use the name of your project for this. If
         * you've implemented an alternative client class as well, "standard"
         * should be replaced by the name of the new class.
         *
         * @param string Relative path to the template creating the body for the GET method of the JSON API
         * @since 2017.07
         * @category Developer
         */
        $tplconf = 'admin/jsonadm/plugin/config/template-get';
        $default = 'config';
        $body = $view->render($view->config($tplconf, $default));
        return $response->with_header('Content-Type', 'application/vnd.api+json; supported-ext="bulk"')->with_body($view->response()->create_stream_from_string($body))->with_status($status);
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
        if (($id = $view->param('id')) == null) {
            throw new \Aimeos\Admin\Json_Adm\Exception('No ID given', 400);
        }
        $manager = \Aimeos\M_Shop::create($this->context(), 'plugin');
        $item = $manager->create()->set_provider($id);
        $view->config_items = $manager->get_provider($item, 'order')->get_config_be();
        return $response;
    }
}