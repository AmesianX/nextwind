<?php
Wind::import('APPS:.profile.controller.BaseProfileController');

/**
 * 隐私设置
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: SecretController.php 21452 2012-12-07 10:18:33Z gao.wanggao $
 * @package src.products.u.controller.profile
 */
class SecretController extends BaseProfileController {

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$this->setCurrentLeft();
		$model = Wekit::load('APPS:profile.service.PwUserProfileMenu')->getTabs('profile');
		unset($model['profile'], $model['contact'], $model['tag']);
		$userInfo = Wekit::load('user.PwUser')->getUserByUid($this->loginUser->uid, PwUser::FETCH_INFO);
		$secret = $userInfo['secret'] ? unserialize($userInfo['secret']) : array();
		//手机号码默认仅自己可见
		!isset($secret['mobile']) && $secret['mobile'] = 1;
		$this->setOutput($model, 'model');
		$this->setOutput($secret, 'secret');
		$this->setOutput($this->getSecretOption(), 'option');
		$this->appendBread('空间隐私', WindUrlHelper::createUrl('profile/secret/run'));
		$this->setTemplate('profile_secret');
		
		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$lang = Wind::getComponent('i18n');
		PwSeoBo::setCustomSeo($lang->getMessage('SEO:profile.secret.run.title'), '', '');
	}
	
	public function dorunAction() {
		$_array = array();
		$model = Wekit::load('APPS:profile.service.PwUserProfileMenuService')->getProfileTabMenu();
		unset($model['profile'], $model['contact'], $model['tag']);
		if (count($model) > 1){
			$post = array_keys($model);
		}
		$array = array('space', 'constellation', 'local', 'nation', 'aliwangwang', 'qq','msn', 'mobile');
		$array = array_merge($array,$post);
		foreach ($array AS $value) {
			$_array[$value] = (int)$this->getInput($value,'post');
		}
		Wind::import('SRV:user.dm.PwUserInfoDm');
		$dm = new PwUserInfoDm($this->loginUser->uid);
		$dm->setSecret($_array);
		$resource = Wekit::load('user.PwUser')->editUser($dm, PwUser::FETCH_INFO);
		if ($resource instanceof PwError) $this->showError($resource->getError());
		$this->showMessage("MEDAL:success");
	}
	
	/**
	 * 设置黑名单
	 */
	public function blackAction() {
		$this->setCurrentLeft();
		$blacklist = Wekit::load('user.PwUserBlack')->getBlacklist($this->loginUser->uid);
		$blacks = array();
		if ($blacklist) {
			$users = Wekit::load('user.PwUser')->fetchUserByUid($blacklist);
			foreach ($users as $v) {
				$blacks[] = $v['username'];
			}
		}
		$this->setOutput($blacks,'blacklist');
		$this->appendBread('黑名单', WindUrlHelper::createUrl('profile/secret/black'));
		$this->setTemplate('profile_black');
	}
	
	/**
	 * do设置黑名单
	 */
	public function doblackAction() {
		$blacklist = $this->getInput('blacklist');
		$userids = array();
		if ($blacklist) {
			$users = Wekit::load('user.PwUser')->fetchUserByName($blacklist);
			$userids = array_keys($users);
		}
		($blacklist && !$userids) && $this->showError('USER:profile.secret.username.error');
		//只能一个一个存
		$ds = Wekit::load('user.PwUserBlack');
		foreach ($userids AS $uid) {
			$ds->setBlacklist($this->loginUser->uid, $uid);
		}
		
		// 设置完黑名单取消互相关注  有点纠结 限制个50个
		$attentionService = Wekit::load('attention.srv.PwAttentionService');
		$userids = array_slice($userids, 0, 50);
		foreach ($userids as $uid) {
			$attentionService->deleteFollow($this->loginUser->uid, $uid);
			$attentionService->deleteFollow($uid, $this->loginUser->uid);
		}
		$this->showMessage('success');
	}
	
	protected function getSecretOption() {
		$lang = Wind::getComponent('i18n');
		return array(
			0 => $lang->getMessage('USER:secret.option.open'),
			1 => $lang->getMessage('USER:secret.option.myself'),
			2 => $lang->getMessage('USER:secret.option.attention')
		);
	}
	
}