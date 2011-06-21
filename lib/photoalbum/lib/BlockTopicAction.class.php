<?php
/**
 * @deprecated
 */
class photoalbum_BlockTopicAction extends website_BlockAction
{
	/**
	 * @deprecated
	 */
    public function execute($request, $response)
    {   
        $container = $this->getRequiredDocumentParameter();
		$request->setAttribute('container', $container);

		$items = photoalbum_AlbumService::getInstance()->getPublishedByTopicId($container->getId());
		$itemsPerPage = 10;
		$page = $request->getParameter('page');
		if (!is_numeric($page) || $page < 1 || $page > ceil(count($items) / $itemsPerPage))
		{
			$page = 1;
		}
		$paginator = new paginator_Paginator('photoalbum', $page, $items, $itemsPerPage);
		$request->setAttribute('paginator', $paginator);

		return website_BlockView::SUCCESS;
    }
}