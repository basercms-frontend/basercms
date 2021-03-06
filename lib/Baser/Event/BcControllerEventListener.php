<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Event
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcEventListener', 'Event');

/**
 * コントローラーイベントリスナー
 *
 * Controllerイベントにコールバック処理を登録するための継承用クラス。
 * events プロパティに配列で、イベント名を登録する。
 * イベント名についてレイヤー名は省略できる。
 * コールバック関数はイベント名より .（ドット）をアンダースコアに置き換えた上でキャメルケースに変換したものを
 * 同クラス内のメソッドとして登録する
 * 
 * （例）
 * Controller.Dashboard.beforeRendr に対してコールバック処理を登録
 * 
 * public $events = array('Dashboard.beforeRender');
 * public function dashboardBeforeRender($event) {}
 * 
 */
class BcControllerEventListener extends BcEventListener {

/**
 * レイヤー名
 * 
 * @var string
 */
	public $layer = 'Controller';
	
/**
 * 管理システムの現在のサイトをセットする
 * 
 * @param \Controller $controller
 * @param $siteId
 */
	public function setAdminCurrentSite(Controller $controller, $siteId) {
		if(!BcUtil::isAdminSystem()) {
			return false;
		}
		$controller->passedArgs['site_id'] = $siteId;
		return true;
	}
	
}
