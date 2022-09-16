<?php
namespace wcf\system\condition;
use wcf\data\user\UserList;
use wcf\data\DatabaseObjectList;
use wcf\system\condition\AbstractTextCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\WCF;

/**
 * Condition implementation for a period value.
 * 
 * @author		2018-2022 Zaydowicz.de
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.box.likes
 */
class UzBoxLikesPeriodValueCondition extends AbstractTextCondition implements IObjectListCondition {
	/**
	 * @inheritDoc
	 */
	protected $description = 'wcf.acp.box.uzlikes.condition.period.value.description';
	
	/**
	 * @inheritDoc
	 */
	protected $fieldName = 'uzboxLikesPeriodValue';
	
	/**
	 * @inheritDoc
	 */
	protected $fieldValue = '1';
	/**
	 * @inheritDoc
	 */
	protected $label = 'wcf.acp.box.uzlikes.condition.period.value';
	
	/**
	 * @inheritDoc
	 */
	protected function getFieldElement() {
		return '<input type="number" name="'.$this->fieldName.'" value="'.$this->fieldValue.'" class="tiny" min="1">';
	}
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		if (isset($_POST[$this->fieldName])) $this->fieldValue = intval($_POST[$this->fieldName]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData) {
		if (!($objectList instanceof UserList)) {
			throw new \InvalidArgumentException("Object list is no instance of '".UserList::class."', instance of '".get_class($objectList)."' given.");
		}
		
		// do nothing
	}
}
