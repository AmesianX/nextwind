<?php
Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignStructureDao.php 11328 2012-06-06 07:37:29Z gao.wanggao $ 
 * @package 
 */
class PwDesignStructureDao extends PwBaseDao {
	protected $_pk = 'struct_name';
	protected $_table = 'design_structure';
	protected $_dataStruct = array('struct_name', 'struct_title', 'struct_style');
	
	public function get($name) {
		return $this->_get($name);
	}
	
	public function fetch($names) {
		return $this->_fetch($names,'struct_name');
	}
	
	public function getList() {
		$sql = $this->_bindTable('SELECT * FROM %s');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array());
	}
	
	public function replace($data) {
		if (!$data = $this->_filterStruct($data)) return false;
		if (!$data['struct_name']) return false;
		$sql = $this->_bindSql('REPLACE INTO %s SET %s',  $this->getTable(), $this->sqlSingle($data));
		return $this->getConnection()->execute($sql);
	}
	
	public function delete($name) {
		return $this->_delete($name);
	}
	
}
?>