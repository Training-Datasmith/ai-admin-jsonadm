<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2026
 * @package Admin
 * @subpackage JsonAdm
 */
namespace Aimeos\Admin\Json_Adm\Index;

use Psr\Http\Message\Response_Interface;
use Psr\Http\Message\Server_Request_Interface;
/**
 * JSON API index client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Standard extends \Aimeos\Admin\Json_Adm\Standard implements \Aimeos\Admin\Json_Adm\Common\Iface
{
    /** admin/jsonadm/index/decorators/excludes
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
     * @since 2020.10
     * @category Developer
     * @see admin/jsonadm/common/decorators/default
     * @see admin/jsonadm/index/decorators/global
     * @see admin/jsonadm/index/decorators/local
     */
    /** admin/jsonadm/index/decorators/global
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
     *  admin/jsonadm/index/decorators/global = array( 'decorator1' )
     *
     * This would add the decorator named "decorator1" defined by
     * "\Aimeos\Admin\Jsonadm\Common\Decorator\Decorator1" only to the
     * "index" Jsonadm client.
     *
     * @param array List of decorator names
     * @since 2020.10
     * @category Developer
     * @see admin/jsonadm/common/decorators/default
     * @see admin/jsonadm/index/decorators/excludes
     * @see admin/jsonadm/index/decorators/local
     */
    /** admin/jsonadm/index/decorators/local
     * Adds a list of local decorators only to the Jsonadm client
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to wrap local decorators
     * ("\Aimeos\Admin\Jsonadm\Index\Decorator\*") around the Jsonadm
     * client.
     *
     *  admin/jsonadm/index/decorators/local = array( 'decorator2' )
     *
     * This would add the decorator named "decorator2" defined by
     * "\Aimeos\Admin\Jsonadm\Index\Decorator\Decorator2" only to the
     * "index" Jsonadm client.
     *
     * @param array List of decorator names
     * @since 2020.10
     * @category Developer
     * @see admin/jsonadm/common/decorators/default
     * @see admin/jsonadm/index/decorators/excludes
     * @see admin/jsonadm/index/decorators/global
     */
    /**
     * Deletes the resource or the resource list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function delete(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        $view = $this->view();
        try {
            $manager = \Aimeos\M_Shop::create($this->context(), 'index');
            if (($id = $view->param('id')) == null) {
                $body = (string) $request->get_body();
                if (($payload = json_decode($body)) === null || !isset($payload->data) || !is_array($payload->data)) {
                    throw new \Aimeos\Admin\Json_Adm\Exception('Invalid JSON in body', 400);
                }
                $view->total = count($payload->data);
                $view->data = $payload->data;
                $id = $payload->data;
            } else {
                $view->total = 1;
                $view->data = $id;
            }
            $manager->remove($id);
            $status = 200;
        } catch (\Aimeos\Admin\Json_Adm\Exception $e) {
            $status = $e->get_code();
            $view->errors = [['title' => $this->context()->translate('admin/jsonadm', $e->get_message()), 'detail' => $e->get_trace_as_string()]];
        } catch (\Aimeos\M_Shop\Exception $e) {
            $status = 404;
            $view->errors = [['title' => $this->context()->translate('mshop', $e->get_message()), 'detail' => $e->get_trace_as_string()]];
        } catch (\Exception $e) {
            $status = 500;
            $view->errors = [['title' => $e->get_message(), 'detail' => $e->get_trace_as_string()]];
        }
        /** admin/jsonadm/template-delete
         * Relative path to the JSON API template for DELETE requests
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
         * @param string Relative path to the template creating the body for the DELETE method of the JSON API
         * @since 2020.10
         * @category Developer
         * @see admin/jsonadm/template-aggregate
         * @see admin/jsonadm/template-get
         * @see admin/jsonadm/template-patch
         * @see admin/jsonadm/template-post
         * @see admin/jsonadm/template-put
         * @see admin/jsonadm/template-options
         */
        $tplconf = 'admin/jsonadm/template-delete';
        $default = 'delete';
        $body = $view->render($view->config($tplconf, $default));
        return $response->with_header('Content-Type', 'application/vnd.api+json; supported-ext="bulk"')->with_body($view->response()->create_stream_from_string($body))->with_status($status);
    }
    /**
     * Creates or updates the resource or the resource list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function post(Server_Request_Interface $request, Response_Interface $response): \Psr\Http\Message\Response_Interface
    {
        $view = $this->view();
        try {
            $body = (string) $request->get_body();
            if (($payload = json_decode($body)) === null || !isset($payload->data)) {
                throw new \Aimeos\Admin\Json_Adm\Exception('Invalid JSON in body', 400);
            }
            if (is_array($payload->data)) {
                $response = $response->with_header('Content-Type', 'application/vnd.api+json; ext="bulk"; supported-ext="bulk"');
                $ids = $payload->data;
            } else {
                $ids = [$payload->data];
            }
            $context = $this->context();
            $domains = $context->config()->get('mshop/index/manager/domains', []);
            $manager = \Aimeos\M_Shop::create($context, 'product');
            $items = $manager->search($manager->filter()->add('product.id', '==', $ids), $domains);
            \Aimeos\M_Shop::create($context, 'index')->rebuild($items->to_array());
            $status = 201;
            $view->total = count($ids);
        } catch (\Aimeos\Admin\Json_Adm\Exception $e) {
            $status = $e->get_code();
            $view->errors = [['title' => $this->context()->translate('admin/jsonadm', $e->get_message()), 'detail' => $e->get_trace_as_string()]];
        } catch (\Aimeos\M_Shop\Exception $e) {
            $status = 404;
            $view->errors = [['title' => $this->context()->translate('mshop', $e->get_message()), 'detail' => $e->get_trace_as_string()]];
        } catch (\Exception $e) {
            $status = 500;
            $view->errors = [['title' => $e->get_message(), 'detail' => $e->get_trace_as_string()]];
        }
        /** admin/jsonadm/template-post
         * Relative path to the JSON API template for POST requests
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
         * @param string Relative path to the template creating the body for the POST method of the JSON API
         * @since 2020.10
         * @category Developer
         * @see admin/jsonadm/template-aggregate
         * @see admin/jsonadm/template-get
         * @see admin/jsonadm/template-patch
         * @see admin/jsonadm/template-delete
         * @see admin/jsonadm/template-put
         * @see admin/jsonadm/template-options
         */
        $tplconf = 'admin/jsonadm/template-post';
        $default = 'post';
        $body = $view->render($view->config($tplconf, $default));
        return $response->with_header('Content-Type', 'application/vnd.api+json; supported-ext="bulk"')->with_body($view->response()->create_stream_from_string($body))->with_status($status);
    }
}