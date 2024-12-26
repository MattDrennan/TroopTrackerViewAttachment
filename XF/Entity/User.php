<?php

namespace TroopTrackerViewAttachment\XF\Entity;

class User extends XFCP_User
{
	public function hasPermission($group, $permission)
	{
		// Allow guests to view attachment
		$visitor = \XF::visitor();

		if (strpos($_SERVER['REQUEST_URI'], 'index.php?attachments') !== false && $visitor->user_id == 0) {
			return true;
		}
		
		return $this->PermissionSet->hasGlobalPermission($group, $permission);
	}
}

?>