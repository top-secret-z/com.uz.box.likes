<?php
namespace wcf\system\condition;
use wcf\data\user\UserProfileList;
use wcf\data\DatabaseObjectList;
use wcf\system\condition\AbstractSelectCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\util\StringUtil;

/**
 * Condition implementation for the period.
 * 
 * @author		2018-2022 Zaydowicz.de
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.box.likes
 */
class UzBoxLikesPeriodCondition extends AbstractSelectCondition implements IObjectListCondition {
	/**
	 * @inheritDoc
	 */
	protected $description = 'wcf.acp.box.uzlikes.condition.period.description';
	
	/**
	 * @inheritDoc
	 */
	protected $fieldName = 'uzboxLikesPeriod';
	
	/**
	 * @inheritDoc
	 */
	protected $label = 'wcf.acp.box.uzlikes.condition.period';
	
	/**
	 * @inheritDoc
	 */
	protected $fieldValue = 'month';
	
	/**
	 * @inheritDoc
	 */
	public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData) {
		if (!($objectList instanceof UserProfileList)) {
			throw new \InvalidArgumentException("Object list is no instance of '".UserProfileList::class."', instance of '".get_class($objectList)."' given.");
		}
		
		// do nothing
	}
	
	/**
	 * @inheritDoc
	 */
	protected function getOptions() {
		return [
				'alltime' => 'wcf.acp.box.uzlikes.condition.period.alltime',
				'curday' => 'wcf.acp.box.uzlikes.condition.period.curday',
				'curweek' => 'wcf.acp.box.uzlikes.condition.period.curweek',
				'curmonth' => 'wcf.acp.box.uzlikes.condition.period.curmonth',
				'curyear' => 'wcf.acp.box.uzlikes.condition.period.curyear',
				'day' => 'wcf.acp.box.uzlikes.condition.period.day',
				'week' => 'wcf.acp.box.uzlikes.condition.period.week',
				'month' => 'wcf.acp.box.uzlikes.condition.period.month',
				'year' => 'wcf.acp.box.uzlikes.condition.period.year'
		];
	}
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		if (isset($_POST[$this->fieldName])) $this->fieldValue = StringUtil::trim($_POST[$this->fieldName]);
	}
}
