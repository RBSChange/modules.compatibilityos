<?php
/**
 * @deprecated
 */
class generic_SavePermissionsAction extends f_action_BaseAction
{
	public function _execute($context, $request)
	{
		$moduleName = $this->getModuleName($request);
		$document = $this->getDocumentInstanceFromRequest($request);
		$id = $document->getId();
		$rs = f_permission_PermissionService::getRoleServiceByModuleName($moduleName);

		if (!is_null($rs))
		{
			
			$ps = f_permission_PermissionService::getInstance();
			// Permissions are redefined on this node...
			$modifiedRoles = $ps->clearNodePermissions($id, 'modules_'.$moduleName);
			$roles = $rs->getRoles();

			foreach ($roles as $roleName)
			{
				$elems = preg_split('/[_.]/', $roleName);
				$shortName = $elems[2];
				if ($request->hasParameter($shortName))
				{
					foreach( $request->getParameter($shortName) as $accessor )
					{
						if( intval($accessor) > 0)
						{
							$doc = $ps->getDocumentInstance(intval($accessor));
							if ( $doc instanceof users_persistentdocument_user )
							{
								$modifiedRoles[] = $roleName;
								$ps->addRoleToUser($doc, $roleName, array($id));
							}
							else if ( $doc instanceof users_persistentdocument_group )
							{
								$modifiedRoles[] = $roleName;
								$ps->addRoleToGroup($doc, $roleName, array($id));
							}
						}
					}
				}
			}
			$this->logAction($document);
			// The event is dispatched at the end of the loop
			$eventParam = array('nodeId' => $id, 'updatedRoles' => array_unique($modifiedRoles), 'module' => $moduleName);
			$ps->dispatchPermissionsUpdatedEvent($eventParam);
			$request->setAttribute('message', "Permissions saved");
		}
		return self::getSuccessView();
	}

	/**
	 * @return Boolean
	 */
	protected function suffixSecureActionByDocument()
	{
		return true;
	}
}