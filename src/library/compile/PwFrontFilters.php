<?php
Wind::import('WIND:base.AbstractWindBootstrap');
/**
 * P9中的一些全局挂载
 * 
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwFrontFilters.php 21881 2012-12-14 10:18:32Z yishuo $
 * @package wind
 */
class PwFrontFilters extends AbstractWindBootstrap {
	/*
	 * (non-PHPdoc) @see WindHandlerInterceptor::preHandle()
	 */
	public function onCreate() {
		//先注释掉，看一下实现机制，考虑会不会影响效率
		/* if (Wind::getAppName() == 'phpwind') {
			//云应用监听sql执行
			WindFactory::_getInstance()->loadClassDefinitions(
				array(
					'sqlStatement' => array(
						'proxy' => 'WIND:filter.proxy.WindEnhancedClassProxy', 
						'listeners' => array('LIB:compile.acloud.PwAcloudDbListener'))));
		} */
		
		Wekit::createapp(Wind::getAppName());
		if ('phpwind' == Wind::getAppName()) {
			error_reporting(
				Wekit::C('site', 'debug') ? E_ALL ^ E_NOTICE ^ E_DEPRECATED : E_ERROR | E_PARSE);
			set_error_handler(array($this->front, '_errorHandle'), error_reporting());
		}
		$this->_convertCharsetForAjax();
		if ($components = Wekit::C('components')) {
			Wind::getApp()->getFactory()->loadClassDefinitions($components);
		}
	}
	
	/*
	 * (non-PHPdoc) @see AbstractWindBootstrap::onStart()
	 */
	public function onStart() {
		Wekit::app()->init();
	}
	
	/*
	 * (non-PHPdoc) @see AbstractWindBootstrap::onResponse()
	 */
	public function onResponse() {}

	/**
	 * ajax递交编码转换
	 */
	private function _convertCharsetForAjax() {
		if (!Wind::getApp()->getRequest()->getIsAjaxRequest()) return;
		$toCharset = Wind::getApp()->getResponse()->getCharset();
		if (strtoupper(substr($toCharset, 0, 2)) != 'UT') {
			$_tmp = array();
			foreach ($_POST as $key => $value) {
				$key = WindConvert::convert($key, $toCharset, 'UTF-8');
				$_tmp[$key] = WindConvert::convert($value, $toCharset, 'UTF-8');
			}
			$_POST = $_tmp;
		}
	}
}