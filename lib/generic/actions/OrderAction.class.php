<?php
/**
 * @deprecated
 */
class generic_OrderAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$parentId      = $request->getParameter(K::PARENT_ID_ACCESSOR);
		$childrenOrder = $request->getParameter(K::CHILDREN_ORDER_ACCESSOR);

		if ( empty($childrenOrder) || empty($parentId) ) {
			$errorMessage = f_Locale::translate("&modules.generic.backoffice.OrderChildrenInvalidParametersErrorMessage;");
			$request->setAttribute("message", $errorMessage);
			return self::getErrorView();
		}

		try
		{
			if ($request->getParameter('rt', 't') == 't') // tree
			{
				$this->orderTree($parentId, $childrenOrder);
			}
			else
			{
				$this->orderRelations($parentId, $childrenOrder, $request->getParameter('rn', null));
			}
		}
		catch (Exception $e)
		{
			$request->setAttribute("message", $e->getMessage());
			return self::getErrorView();
		}

		return self::getSuccessView();
	}

	/**
	 * @param integer $parentId
	 * @param array<id=>order> $childrenOrder
	 * @return unknown
	 */
	private function orderRelations($parentId, $childrenOrder, $relationName)
	{
		if ( is_null($relationName) )
		{
			throw new Exception(__METHOD__.": \$relationName is null (parameter 'rn' is missing in the request)");
		}
		$parent = DocumentHelper::getDocumentInstance($parentId);

		// Build an array with the actual children.
		// For each actual child, if a position exists in the desired order array
		// this position overrides the current one.
		$actualChildArray = f_util_ClassUtils::callMethodOn($parent, 'get' . ucfirst($relationName) . 'Array');
		$actualChildrenOrder = array();
		$pos = 0;
		foreach ($actualChildArray as $child)
		{
			$id = $child->getId();
			if (isset($childrenOrder[$id]))
			{
				$actualChildrenOrder[$id] = $childrenOrder[$id];
			}
			else
			{
				$actualChildrenOrder[$id] = strval($pos);
			}
			$pos++;
		}

		// Sort the actual chidren by position (asort preserves the key associations)
		asort($actualChildrenOrder);

		// Remove all the relations
		f_util_ClassUtils::callMethodOn($parent, 'removeAll' . ucfirst($relationName));

		// Recreate relations with the right order
		$pos = 0;
		foreach (array_keys($actualChildrenOrder) as $id)
		{
			$pos++;
			$parent->{'set' . ucfirst($relationName)}($pos, DocumentHelper::getDocumentInstance($id));
		}

		// Save the document
		$parent->save();
	}

	/**
	 * @param integer $parentId
	 * @param array<Integer, Integer> $childrenOrder (id => order)
	 */
	private function orderTree($parentId, $childrenOrder)
	{
		$parentNode = f_persistentdocument_PersistentTreeNode::getInstanceByDocumentId($parentId);
		if ($parentNode === null)
		{
			throw new Exception("Parent node $parentId not found");
		}
		
		TreeService::getInstance()->order($parentNode, $childrenOrder);
	}
}