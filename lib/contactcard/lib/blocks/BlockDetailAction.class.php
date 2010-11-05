<?php
class contactcard_BlockDetailAction extends block_BlockAction
{

	/**
	 * Récupère l'id de la note a affiché de la QS. Si on visualise la page dans une nouvelle
	 * fenêtre à partir du back office.
	 *
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     * @return String view name
	 */
	public function execute($context, $request)
	{
		$contact = $this->getDocumentParameter();

		if ($contact == null)
		{
			return block_BlockView::DUMMY;
		}

		if(($context->getGlobalRequest()->getParameter('action') == 'PreviewPage' || $context->inBackofficeMode()) || $contact->isPublicated())
		{
			$this->setParameter('contact', $contact);
			if(!$contact->isPublicated())
			{
				$this->setParameter('contactNotPublished', true);
			}

			$ws = website_WebsiteModuleService::getInstance();
			try
			{
				$page = $ws->getDocumentByContextualTag('contextual_website_website_modules_contactcard_page-contact', $ws->getCurrentWebsite());
				if (!$page->isPublicated())
				{
					$this->setParameter('contactformPageNotPublished',true);
					if($context->getGlobalRequest()->getParameter('action') == 'PreviewPage' || $context->inBackofficeMode())
					{
						$this->setParameter('contactform', $page);
					}
				}
				else
				{
					$this->setParameter('contactform', $page);
				}
			}
			catch (Exception $e)
			{
				Framework::exception($e);
			}

			return block_BlockView::ITEM ;
		}
		return block_BlockView::NONE ;
	}
}
