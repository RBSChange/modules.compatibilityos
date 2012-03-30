<?php
/**
 * @deprecated
 */
class website_LoadMarkersPropertiesAction extends f_action_BaseJSONAction
{
	/**
	 * @deprecated
	 */
	public function _execute($context, $request)
	{
		$folder = $this->getDocumentInstanceFromRequest($request);
		return $this->sendJSON(array ('documents' => $folder->getMarkersInfos()));
	}
}