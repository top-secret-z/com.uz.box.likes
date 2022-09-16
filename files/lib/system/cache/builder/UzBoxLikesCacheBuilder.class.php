<?php
namespace wcf\system\cache\builder;
use wcf\data\like\Like;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caches the users with most likes iaw conditions.
 * 
 * @author		2018-2022 Zaydowicz.de
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.box.likes
 */
class UzBoxLikesCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected $maxLifetime = 180;
	
	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		/**
		 * preset data
		 */
		$sqlLimit = $value = $uzboxLikesLast = 0;
		$action = $period = '';
		
		$conditionBuilder = new PreparedStatementConditionBuilder();
		
		foreach ($parameters as $condition) {
			if (isset($condition['limit'])) {
				$sqlLimit = $condition['limit'];
			}
			
			if (isset($condition['uzboxLikesAction'])) {
				if ($condition['uzboxLikesAction'] == 'give') $action = 'give';
				if ($condition['uzboxLikesAction'] == 'receive') $action = 'receive';
			}
			
			if (isset($condition['uzboxLikesPeriod'])) {
				$period = $condition['uzboxLikesPeriod'];
			}
			
			if (isset($condition['uzboxLikesPeriodValue'])) {
				$value = intval($condition['uzboxLikesPeriodValue']);
			}
			
			if (isset($condition['uzboxLikesLast'])) {
				$uzboxLikesLast = $condition['uzboxLikesLast'];
			}
			
			if (isset($condition['lastActivity'])) {
				$conditionBuilder->add('user_table.lastActivityTime > ?', [TIME_NOW - $condition['lastActivity'] * 86400]);
			}
			
			if (isset($condition['userIsBanned'])) {
				$conditionBuilder->add('user_table.banned = ?', [$condition['userIsBanned']]);
			}
			
			if (isset($condition['userIsEnabled'])) {
				if ($condition['userIsEnabled'] == 0) $conditionBuilder->add('user_table.activationCode > ?', [0]);
				if ($condition['userIsEnabled'] == 1) $conditionBuilder->add('user_table.activationCode = ?', [0]);
			}
			
			if (isset($condition['groupIDs'])) {
				if ($action== 'give') {
					$conditionBuilder->add('like_table.userID IN (SELECT userID FROM wcf'.WCF_N.'_user_to_group WHERE groupID IN (?))', [$condition['groupIDs']]);
				}
				else {
					$conditionBuilder->add('like_table.objectUserID IN (SELECT userID FROM wcf'.WCF_N.'_user_to_group WHERE groupID IN (?))', [$condition['groupIDs']]);
				}
			}
			
			if (isset($condition['notGroupIDs'])) {
				if ($action== 'give') {
					$conditionBuilder->add('like_table.userID NOT IN (SELECT userID FROM wcf'.WCF_N.'_user_to_group WHERE groupID IN (?))', [$condition['notGroupIDs']]);
				}
				else {
					$conditionBuilder->add('like_table.objectUserID NOT IN (SELECT userID FROM wcf'.WCF_N.'_user_to_group WHERE groupID IN (?))', [$condition['notGroupIDs']]);
				}
			}
		}
		
		// period
		$start = $start2 = -1;
		$end = TIME_NOW;
		switch ($period) {
			case 'curday':
				$start = strtotime("midnight", TIME_NOW) - ($value - 1) * 86400;
				if ($start < 0) $start = 0;
				
				$start2 = $start - $value * 86400;
				if ($start2 < 0) $start2 = 0;
				break;
				
			case 'curweek':
				$start = gmmktime(0, 0, 0, date("n"), date("j") - date("N") + 1); // Monday
				$start -= ($value - 1) * 86400 * 7;
				if ($start < 0) $start = 0;
				
				$start2 = $start - $value * 86400 * 7;
				if ($start2 < 0) $start2 = 0;
				break;
				
			case 'curmonth':
				$month = date('n');
				$year = date('Y');
				$savedValue = $value;
				while ($value > 1) {
					$month--;
					if ($month == 0) {
						$month = 12;
						$year--;
					}
					$value--;
				}
				if ($year < 1970) $year = 1970;
				$start = gmmktime(0, 0, 1, $month, 1, $year);
				
				while ($savedValue > 0) {
					$month--;
					if ($month == 0) {
						$month = 12;
						$year--;
					}
					$savedValue--;
				}
				if ($year < 1970) $year = 1970;
				$start2 = gmmktime(0, 0, 1, $month, 1, $year);
				break;
				
			case 'curyear':
				$year = date('Y') - ($value - 1);
				if ($year < 1970) $year = 1970;
				$start = gmmktime(0, 0, 1, 1, 1, $year);
				
				$year -= $value;
				if ($year < 1970) $year = 1970;
				$start2 = gmmktime(0, 0, 1, 1, 1, $year);
				break;
				
			case 'day':
				$start = TIME_NOW - $value * 86400;
				if ($start < 0) $start = 0;
				
				$start2 = $start - $value * 86400;
				if ($start2 < 0) $start2 = 0;
				break;
				
			case 'week':
				$start = TIME_NOW - $value * 86400 * 7;
				if ($start < 0) $start = 0;
				
				$start2 = $start - $value * 86400 * 7;
				if ($start2 < 0) $start2 = 0;
				break;
				
			case 'month':
				if ($value > 500) $value = 500;
				$string = '-'.$value.' month';
				$start = strtotime($string, $end);
				
				$value = 2 * $value;
				if ($value > 500) $value = 500;
				$string = '-'.$value.' month';
				$start2 = strtotime($string, $start);
				break;
				
			case 'year':
				if ($value > 47) $value = 47;
				$string = '-'.$value.' year';
				$start = strtotime($string, $end);
				
				$value = 2 * $value;
				if ($value > 47) $value = 47;
				$string = '-'.$value.' month';
				$start2 = strtotime($string, $start);
				break;
		}
		
		// clone conditionbuilder
		$conditionBuilderClone = clone $conditionBuilder;
		
		if ($start > -1) {
			$conditionBuilder->add('like_table.time BETWEEN ? AND ?', [$start, $end]);
		}
		
		// get userIDs and likes
		$userIDs = $userToLike = [];
		if ($action == 'receive') {
			$sql = "SELECT		like_table.objectUserID as likeUserID, COUNT(like_table.likeValue) as uzboxLikes
					FROM		wcf".WCF_N."_like like_table
					LEFT JOIN	wcf".WCF_N."_user AS user_table ON (like_table.objectUserID = user_table.userID)
					".$conditionBuilder."
					GROUP BY likeUserID
					ORDER BY uzboxLikes DESC";
		}
		else {
			$sql = "SELECT		like_table.userID as likeUserID, COUNT(like_table.likeValue) as uzboxLikes
					FROM		wcf".WCF_N."_like like_table
					LEFT JOIN	wcf".WCF_N."_user AS user_table ON (like_table.userID = user_table.userID)
					".$conditionBuilder."
					GROUP BY likeUserID
					ORDER BY uzboxLikes DESC";
		}
		
		$statement = WCF::getDB()->prepareStatement($sql, $sqlLimit);
		$statement->execute($conditionBuilder->getParameters());
		while ($row = $statement->fetchArray()) {
			$userIDs[] = $row['likeUserID'];
			$userToLike[$row['likeUserID']] = $row['uzboxLikes'];
		}
		
		$users = [];
		if (!empty($userIDs)) {
			foreach ($userIDs as $userID) {
				$user = UserProfileRuntimeCache::getInstance()->getObject($userID);
				$user->uzboxLikes = $userToLike[$user->userID];
				$users[] = $user;
			}
		}
		
		// last
		$lasts = [];
		if ($uzboxLikesLast) {
			$conditionBuilder = $conditionBuilderClone;
			
			if ($start2 > -1) {
				$conditionBuilder->add('like_table.time BETWEEN ? AND ?', [$start2, $start]);
			}
			
			$userIDs = $userToLike = [];
			if ($action == 'receive') {
				$sql = "SELECT		like_table.objectUserID as likeUserID, COUNT(like_table.likeValue) as uzboxLikes
						FROM		wcf".WCF_N."_like like_table
						LEFT JOIN	wcf".WCF_N."_user AS user_table ON (like_table.objectUserID = user_table.userID)
						".$conditionBuilder."
						GROUP BY likeUserID
						ORDER BY uzboxLikes DESC";
			}
			else {
				$sql = "SELECT		like_table.userID as likeUserID, COUNT(like_table.likeValue) as uzboxLikes
						FROM		wcf".WCF_N."_like like_table
						LEFT JOIN	wcf".WCF_N."_user AS user_table ON (like_table.userID = user_table.userID)
						".$conditionBuilder."
						GROUP BY likeUserID
						ORDER BY uzboxLikes DESC";
			}
			
			$statement = WCF::getDB()->prepareStatement($sql, $uzboxLikesLast);
			$statement->execute($conditionBuilder->getParameters());
			while ($row = $statement->fetchArray()) {
				$userIDs[] = $row['likeUserID'];
				$userToLike[$row['likeUserID']] = $row['uzboxLikes'];
			}
			
			if (!empty($userIDs)) {
				foreach ($userIDs as $userID) {
					$user = new UserProfile(new User($userID));
					$user->uzboxLikes = $userToLike[$user->userID];
					$lasts[] = $user;
				}
			}
		}
		
		return [
				'users' => $users, 
				'lasts' => $lasts
		];
	}
}
