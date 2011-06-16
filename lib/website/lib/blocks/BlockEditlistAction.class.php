<?php
/**
 * website_BlockEditAction
 * @package modules.generic.lib.blocks
 */
class website_BlockEditlistAction extends website_TaggerBlockAction
{
	/**
	 * @see website_BlockAction::execute()
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
	{
		//$this->getHttpRequest()->removeCookie("website_EditListIds");
		if (!$this->isLogged())
		{
			return website_BlockView::NONE;
		}
		$actions = $this->getStoredDocumentIds();
		if (f_util_ArrayUtils::isNotEmpty($actions))
		{
			$ids = array();
			foreach ($actions as $action)
			{
				$ids[] = $action["i"];
			}
			$query = f_persistentdocument_PersistentProvider::getInstance()->createQuery()->add(Restrictions::in("document_id", $ids));
			$documents = $query->find();
			$usableActions = array();
			foreach (array_reverse($actions) as $action)
			{
				$document = null;
				foreach ($documents as $doc)
				{
					if ($doc->getId() == $action["i"])
					{
						$document = $doc;
						break; 
					}
				}
				if ($document !== null)
				{
					$usableActions[] = array("document" => $document, "name" => $action["a"], "time" => date("d/m/y G:i:s", $action["t"]));	
				}
			}
			$request->setAttribute("actions", $usableActions);
		}
		return website_BlockView::SUCCESS;
	}
	
	// private methods
	
	/**
	 * @return boolean
	 */
	private function isLogged()
	{
		return users_UserService::getInstance()->getCurrentBackEndUser() !== null;
	}

	/**
	 * @param boolean $create
	 * @return array or null
	 */
	static function getStoredDocumentIds($create = false)
	{
		$httpRequest = f_mvc_HTTPRequest::getInstance();
		if ($httpRequest->hasCookie("website_EditListIds"))
		{
			$ids = JsonService::getInstance()->decode($httpRequest->getCookie("website_EditListIds"));
			if (is_array($ids))
			{
				return $ids;
			}
			else
			{
				Framework::warn("Error while decoding website_EditListIds cookie");
				$httpRequest->removeCookie("website_EditListIds");
			}
		}
		return (($create) ? array() : null);
	}
	
	/**
	 * @param integer[] $ids
	 */
	static function storeDocumentIds($ids)
	{
		f_mvc_HTTPRequest::getInstance()->setCookie("website_EditListIds", JsonService::getInstance()->encode($ids));
	}
}