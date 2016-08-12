<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcContentsController', 'Controller');

/**
 * フォルダ コントローラー
 *
 * @package Baser.Controller
 * @property ContentFolder $ContentFolder
 */
class ContentFoldersController extends AppController {

/**
 * コンポーネント
 * @var array
 */
	public $components = array('Cookie', 'BcAuth', 'BcAuthConfigure', 'BcContents' => array('useForm' => true));

/**
 * モデル
 *
 * @var array
 */
	public $uses = ['ContentFolder', 'Page'];

/**
 * Before Filter
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BcAuth->allow('view');
	}
	
/**
 * コンテンツを登録する
 *
 * @return void
 */
	public function admin_add() {
		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->ContentFolder->save($this->request->data)) {
			$data = array(
				'contentId'	=> $this->Content->id,
				'entityId'	=> $this->ContentFolder->id
			);
			$this->setMessage("フォルダ「{$this->request->data['Content']['title']}」を追加しました。", false, true, false);
			echo json_encode($data);
		} else {
			$this->ajaxError(500, '保存中にエラーが発生しました。');
		}
		exit();
	}

/**
 * コンテンツを更新する
 *
 * @return void
 */
	public function admin_edit($entityId) {
		$this->pageTitle = 'フォルダ編集';
		if(!$this->request->data) {
			$this->request->data = $this->ContentFolder->read(null, $entityId);
		} else {
			if ($this->ContentFolder->save($this->request->data)) {
				$this->setMessage("フォルダ「{$this->request->data['Content']['title']}」を更新しました。", false, true);
				$this->redirect(array(
					'plugin' => '',
					'controller' => 'content_folders',
					'action' => 'edit',
					$entityId
				));
			} else {
				$this->setMessage('保存中にエラーが発生しました。入力内容を確認してください。', true, true);
			}
		}
		$this->set('folderTemplateList', $this->ContentFolder->getFolderTemplateList($this->request->data['Content']['id'], $this->siteConfigs['theme']));
		$this->set('pageTemplateList', $this->Page->getPageTemplateList($this->request->data['Content']['id'], $this->siteConfigs['theme']));
		$this->set('publishLink', $this->request->data['Content']['url']);
	}

/**
 * コンテンツを削除する
 *
 * @param $entityId
 */
	public function admin_delete() {
		if(empty($this->request->data['entityId'])) {
			return false;
		}
		if($this->ContentFolder->delete($this->request->data['entityId'])) {
			return true;
		}
		return false;
	}

/**
 * コンテンツを表示する
 *
 * @param $entityId
 * @return void
 */
	public function view() {
		$entityId = $this->request->params['entityId'];
		$data = $this->ContentFolder->find('first', array('conditions' => array('ContentFolder.id' => $entityId)));
		$this->ContentFolder->Content->Behaviors->Tree->settings['Content']['scope'] = array('Content.site_root' => false) + $this->ContentFolder->Content->getConditionAllowPublish();
		$children = $this->ContentFolder->Content->children($data['Content']['id'], true, array(), 'lft');
		if($this->BcContents->preview && !empty($this->request->data['Content'])) {
			$data['Content'] = $this->request->data['Content'];
		}
		$this->set(compact('data', 'children'));
		$folderTemplate = $data['ContentFolder']['folder_template'];
		if(!$folderTemplate) {
			$folderTemplate = $this->ContentFolder->getParentTemplate($data['Content']['id'], 'folder');
		}
		$this->set('editLink', array('admin' => true, 'plugin' => '', 'controller' => 'content_folders', 'action' => 'edit', $data['ContentFolder']['id'], 'content_id' => $data['Content']['id']));
		$this->render($folderTemplate);
	}

}