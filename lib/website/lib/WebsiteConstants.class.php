<?php
/**
 * @deprecated
 */
abstract class WebsiteConstants
{
	const VISIBILITY_HIDDEN              = 0;
	const VISIBILITY_VISIBLE             = 1;
	const VISIBILITY_HIDDEN_IN_MENU_ONLY = 2;
	const VISIBILITY_HIDDEN_IN_SITEMAP_ONLY = 4;

	const WEBSITE_MODULE_NAME         = 'website';

	const DOCUMENTTYPE_TOPIC          = 'modules_website/topic';
	const DOCUMENTTYPE_WEBSITE        = 'modules_website/website';
	const DOCUMENTTYPE_MENU           = 'modules_website/menu';
	const DOCUMENTTYPE_PAGE           = 'modules_website/page';
	const DOCUMENTTYPE_REFERENCE      = 'modules_generic/reference';

	const TAG_DEFAULT_WEBSITE         = 'default_modules_website_default-website';

	const TAG_ERROR_404_PAGE          = 'contextual_website_website_error404';
	const TAG_ERROR_401_PAGE          = 'contextual_website_website_error401-1';
	const TAG_ERROR_PAGE              = 'contextual_website_website_server-error';

	const TAG_PRINT_PAGE              = 'contextual_website_website_print';
	const TAG_HELP_PAGE               = 'contextual_website_website_help';
	const TAG_LEGAL_NOTICE_PAGE       = 'contextual_website_website_legal';
	const TAG_ADD_TO_FAVORITES_PAGE   = 'contextual_website_website_favorite';

}