<?php
/**
 * @deprecated
 */
class generic_DeleteAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$docIds = $this->getDocumentIdArrayFromRequest($request);

		try
		{
    		foreach ($docIds as $docId)
    		{
    			$doc = DocumentHelper::getDocumentInstance($docId);

    			switch ($doc->getDocumentModelName())
    			{
    			    case 'modules_generic/rootfolder':
    			        Framework::warn("Tried to delete a rootfolder: ID=".$doc->getId());
    			        break;

    			    case 'modules_generic/systemfolder':
    			        Framework::warn("Tried to delete a systemfolder: ID=".$doc->getId());
    			        break;

    			    default:
    			    	$info = array('documentlabel' => $doc->getLabel());
    			        $doc->delete();
    			        $this->logAction($doc, $info);
    			        break;
    			}
    		}
    		$request->setAttribute('ids', $docIds);
		}
		catch (Exception $e)
		{
		    Framework::exception($e);
		    $this->setException($request, $e, true);
			return self::getErrorView();
		}
		
		return View::SUCCESS ;
	}

	/**
	 * @return Boolean
	 */
	protected function suffixSecureActionByDocument()
	{
		return true;
	}
}