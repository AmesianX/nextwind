<?php
Wind::import('WIND:viewer.AbstractWindTemplateCompiler');
Wind::import('SRV:design.srv.PwPortalCompile');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwTemplateCompilerPortal.php 21897 2012-12-14 12:45:12Z gao.wanggao $ 
 * @package 
 */
class PwTemplateCompilerPortal extends AbstractWindTemplateCompiler {
	
	protected $srv;
	private $_url;
	private $_router;
	
	public function compile($key, $content) {
		$this->_router();
		list($pageName, $unique) = $this->_pageName();
		if (!$pageName && !$unique) return false;
		$this->srv = Wekit::load('design.srv.PwDesignCompile');
		$this->srv->setIsDesign($this->getRequest()->getPost('design'));
		$_pk = $unique ? $this->getRequest()->getGet($unique) : '';
		$this->srv->beforeDesign($this->_router, $pageName, $_pk);
		$pageid = $this->srv->getPageid();
		
		//对模版进行编译
		$portalSrv = new PwPortalCompile($pageid);
		$portalSrv->compilePortal();
		
		//对新模版进行编译
		$content = $portalSrv->compileDesign($content);
		
		//转换标签
		$content = $this->compileStart($content, $_pk, $this->_url);
		$content = $this->compileSign($content);
		$content = $this->compileTitle($content);
		$content = $this->compileList($content);
		$content = $this->compileEnd($content);
		$this->srv->refreshPage();
		return $content;
	}
	
	protected function compileStart($content, $pk, $url) {
		$viewTemplate = Wind::getComponent('template');
		$start = $this->srv->startDesign($pk, $url);
		$content =  str_replace('<pw-start/>', $start, $content);
		return $viewTemplate->compileStream($content, $this->windViewerResolver);
	}
	
	protected function compileSign($content) {
		$in = array(
			'<pw-head/>',
			'<pw-navigate/>',
			'<pw-footer/>',
		);
		$out = array(
			'<!--# if($portal[\'header\']): #--><template source=\'TPL:common.header\' load=\'true\' /><!--# endif; #-->',
			'<!--# if($portal[\'navigate\']): #--><div class="bread_crumb">{@$headguide|html}</div><!--# endif; #-->',
			'<!--# if($portal[\'footer\']): #--><template source=\'TPL:common.footer\' load=\'true\' /><!--# endif; #-->',
		);
		$content = str_replace($in, $out, $content);
		$viewTemplate = Wind::getComponent('template');
		return $viewTemplate->compileStream($content, $this->windViewerResolver);
	}
	
	protected function compileTitle($content) {
		if (preg_match_all('/\<pw-title\s*id=\"(\w+)\"\s*[>|\/>](.+)<\/pw-title>/isU',$content, $matches)) {
			$viewTemplate = Wind::getComponent('template');
			foreach ($matches[1] AS $k=>$v) {
				if (!$v) continue;
    			$title = $this->srv->compileTitle($v);
	    		$content = str_replace($matches[0][$k], $title, $content);
    		}
    		$content = $viewTemplate->compileStream($content, $this->windViewerResolver);
		}
		return $content;
	}
	
	protected function compileList($content) {
		if (preg_match_all('/\<pw-list\s*id=\"(\d+)\"\s*[>|\/>](.+)<\/pw-list>/isU',$content, $matches)) {
			$viewTemplate = Wind::getComponent('template');
			foreach ($matches[1] AS $k=>$v) {
				if (!$v) continue;
    			$list = $this->srv->compileList($v);
	    		$content = str_replace($matches[0][$k], $list, $content);
    		}
    		$content = $viewTemplate->compileStream($content, $this->windViewerResolver);
		}
		return $content;
	}
	
	
	
	/**
	 * 必须放在转换的最后一步
	 */
	protected function compileEnd($content) {
		$viewTemplate = Wind::getComponent('template');
		$end = $this->srv->afterDesign();
		$content =  str_replace('<pw-end/>', $end, $content);
		return $viewTemplate->compileStream($content, $this->windViewerResolver);
	}
	
	private function _router() {
		$router = Wind::getComponent('router');
    	$m = $router->getModule(); 
    	$c = $router->getController(); 
    	$a = $router->getAction();
    	$this->_router = $m.'/'.$c.'/'.$a;
    	$this->_url = urlencode($router->request->getHostInfo() .$router->request->getRequestUri());
	}
	
	private function _pageName() {
		$path = Wind::getRealPath('SRV:design.srv.router.router');
		if (!is_file($path)) return false;
		$sysPage = @include $path;
		
		if ($this->_router && isset($sysPage[$this->_router])){ 
			return $sysPage[$this->_router];
		}
		return array();
	}
}
?>