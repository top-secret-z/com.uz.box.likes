<?php
namespace wcf\system\condition;
use wcf\data\user\UserList;
use wcf\data\DatabaseObjectList;
use wcf\system\condition\AbstractSelectCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Condition implementation for reaction action.
 * 
 * @author		2018-2022 Zaydowicz.de
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.box.likes
 */
class UzBoxLikesActionCondition extends AbstractSelectCondition implements IObjectListCondition {
	/**
	 * @inheritDoc
	 */
	protected $description = '';
	
	/**
	 * @inheritDoc
	 */
	protected $fieldName = 'uzboxLikesAction';
	
	/**
	 * @inheritDoc
	 */
	protected $label = 'wcf.acp.box.uzlikes.condition.action';
	
	/**
	 * @inheritDoc
	 */
	protected $fieldValue = 'receive';
	
	/**
	 * @inheritDoc
	 */
	public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData) {
		if (!($objectList instanceof UserList)) {
			throw new \InvalidArgumentException("Object list is no instance of '".UserList::class."', instance of '".get_class($objectList)."' given.");
		}
		
		// does nothing
	}
	
	/**
	 * @inheritDoc
	 */
	protected function getOptions() {
		return [
			'receive' => 'wcf.acp.box.uzlikes.condition.action.receive',
			'give' => 'wcf.acp.box.uzlikes.condition.action.give'
		];
	}
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		if (isset($_POST[$this->fieldName])) $this->fieldValue = StringUtil::trim($_POST[$this->fieldName]);
	}
}
