<?php
/**
 * @deprecated
 */
class generic_LoadPermissionsAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */

	private $moduleName = null;
	private $docId = null;

	public function _execute($context, $request)
	{
		$this->docId = $this->getDocumentIdFromRequest($request);
		$this->moduleName = $this->getModuleName($request);
		$writer = new XMLWriter();
		$writer->openMemory();
		$writer->startDocument('1.0','UTF-8');
		// -> response
		$writer->startElement('response');
		// Header
		$writer->writeElement('action', 'LoadPermissions');
		$writer->writeElement('module', $this->moduleName);
		$writer->writeElement('status', 'OK');
		$writer->startElement('message');
		$writer->writeAttribute('alert', '');
		$writer->endElement();
		$writer->writeElement('id', $this->docId);
		$writer->writeElement('lang', 'fr');
		// -> document
		$writer->startElement('document');

		$writer->startElement('component');
		$writer->writeAttribute('name', 'id');
		$writer->text($this->docId);
		$writer->endElement();

		$writer->startElement('component');
		$writer->writeAttribute('name', 'lang');
		$writer->text('fr');
		$writer->endElement();

		$comps = $this->generateMappedRoleArray();
		foreach ( $comps as $role => $aIds  )
		{
			$writer->startElement('component');
			$writer->writeAttribute('name', $role);
			foreach ($aIds as $accessorId)
			{
				$accessorDoc = DocumentHelper::getDocumentInstance($accessorId);
				$writer->startElement('document');
				$writer->startElement('component');
				$writer->writeAttribute('name', 'label');
				$writer->text($accessorDoc->getLabel());
				$writer->endElement();
				$writer->startElement('component');
				$writer->writeAttribute('name', 'id');
				$writer->text($accessorId);
				$writer->endElement();
				$writer->endElement();
			}
			$writer->endElement();
		}
		// <- document
		$writer->endElement();
		// <- response
		$writer->endElement();
		header('Content-type: text/xml');
		die ($writer->outputMemory());
	}

	private function generateMappedRoleArray()
	{
		$result = array();
		$ps = f_permission_PermissionService::getInstance();
		$defId = $ps->getDefinitionPointForPackage($this->docId, 'modules_'.$this->moduleName);
		if ($defId === null)
		{
			$defId = ModuleService::getInstance()->getRootFolderId($this->moduleName);
		}

		$ACLs = $ps->getACLForNode($defId);

		foreach ($ACLs as $acl)
		{
			$roleName = $acl->getRole();
			$elems = preg_split('/[_.]/', $roleName);
			if ($elems[1] == $this->moduleName)
			{
				$roleName = $elems[2];
				if (array_key_exists($roleName, $result))
				{
					array_push($result[$roleName], $acl->getAccessorId());
				}
				else
				{
					$result[$roleName] = array($acl->getAccessorId());
				}
			}
		}
		return $result;
	}

	/**
	 * @return Boolean
	 */
	protected function suffixSecureActionByDocument()
	{
		return true;
	}

}