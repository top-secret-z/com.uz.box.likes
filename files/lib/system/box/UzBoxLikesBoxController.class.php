<?php
namespace wcf\system\box;
use wcf\system\cache\builder\UzBoxLikesCacheBuilder;
use wcf\system\box\AbstractDatabaseObjectListBoxController;
use wcf\system\event\EventHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Most likes box controller.
 *
 * @author		2018-2022 Zaydowicz.de
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.box.likes
 */
class UzBoxLikesBoxController extends AbstractDatabaseObjectListBoxController {
	/**
	 * @inheritDoc
	 */
	protected $conditionDefinition = 'com.uz.box.likes.condition';
	
	/**
	 * @inheritDoc
	 */
	public $defaultLimit = 5;
	public $maximumLimit = 100;
	
	/**
	 * @inheritDoc
	 */
	protected $sortFieldLanguageItemPrefix = 'com.uz.box.likes';
	
	/**
	 * data
	 */
	protected $lasts = [];
	
	/**
	 * @inheritDoc
	 */
	public function getLink() {
		if (MODULE_MEMBERS_LIST) {
			$parameters = 'sortField=likesReceived&sortOrder=DESC';
			
			return LinkHandler::getInstance()->getLink('MembersList', [], $parameters);
		}
		
		return '';
	}
	
	/**
	 * @inheritDoc
	 */
	protected function getObjectList() {
		// get conditions as parameters for cache builder
		$parameters = [];
		foreach ($this->box->getConditions() as $condition) {
			$parameters[] = $condition->conditionData;
		}
		$parameters[] = ['limit' => $this->limit];
		
		$temp = UzBoxLikesCacheBuilder::getInstance()->getData($parameters);
		$userList = $temp['users'];
		$this->lasts = $temp['lasts'];
		
		return $userList;
	
	}
	
	/**
	 * @inheritDoc
	 */
	protected function getTemplate() {
		return WCF::getTPL()->fetch('boxUzLikes', 'wcf', [
				'boxUserList' => $this->objectList,
				'lasts' => $this->lasts
		], true);
	}
	
	/**
	 * @inheritDoc
	 */
	public function hasContent() {
		// module
		if (!MODULE_LIKE) return false;
		
		// object list
		if ($this->objectList === null) {
			$this->objectList = $this->getObjectList();
		}
		
		EventHandler::getInstance()->fireAction($this, 'hasContent');
		
		return ($this->objectList !== null && count($this->objectList) > 0);
	}
	
	/**
	 * @inheritDoc
	 */
	protected function loadContent() {
		$this->content = $this->getTemplate();
	}
	
	/**
	 * @inheritDoc
	 */
	public function hasLink() {
		return MODULE_MEMBERS_LIST == 1;
	}
}
