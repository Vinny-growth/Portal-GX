<?php

use CodeIgniter\Router\RouteCollection;
use Config\Globals;

/**
 * @var RouteCollection $routes
 */

$languages = Globals::$languages;
$generalSettings = Globals::$generalSettings;
$customRoutes = Globals::$customRoutes;

// ── White-label: rotas de módulos habilitados ──────────────────────────────────
// Carregadas AQUI (topo) para terem PRIORIDADE sobre o catch-all (:any) da CMS mais
// abaixo. ModuleRegistry é defensivo (nunca lança), então isto é seguro em produção.
foreach (service('moduleRegistry')->enabledRouteFiles() as $__moduleRoutesFile) {
    require $__moduleRoutesFile;
}

$routes->get('/', 'HomeController::index');
$routes->get('blog', 'HomeController::blog');
$routes->get('simuladores', 'HomeController::simulatorsHub');
$routes->get('simuladores/cambio', 'HomeController::simulatorsFxHub');
$routes->get('simuladores-cambio', 'HomeController::simulatorsFxLegacyRedirect');
$routes->get('simulador-de-risco-cambial', 'HomeController::simulatorsFxLegacyRedirect');
$routes->get('fx-loan', 'HomeController::simulatorsFxLegacyRedirect');
// Slugs de simulador legados/quebrados -> 301 para o slug canônico (Fase 6 GEO/SEO).
// Precisam ser rotas GET (não addRedirect): getRoutes() casa o verbo GET antes do
// wildcard `*`, então o catch-all `(:any)` -> HomeController::any interceptaria o
// addRedirect e devolveria 404. Mapa: HomeController::LEGACY_SIMULATOR_REDIRECTS.
foreach (array_keys(\App\Controllers\HomeController::LEGACY_SIMULATOR_REDIRECTS) as $legacySlug) {
    $routes->get($legacySlug, 'HomeController::legacyRedirect');
}
// Conteúdo duplicado (site audit Ubersuggest) -> 301 para o canônico. Mesma razão de
// ser rota GET. Mapa: HomeController::DUPLICATE_CONTENT_REDIRECTS.
foreach (array_keys(\App\Controllers\HomeController::DUPLICATE_CONTENT_REDIRECTS) as $dupSlug) {
    $routes->get($dupSlug, 'HomeController::duplicateRedirect');
}
$routes->get('cron/update-feeds', 'CronController::checkFeedPosts');
$routes->get('cron/update-sitemap', 'CronController::updateSitemap');
$routes->get('unsubscribe', 'AuthController::unsubscribe');

// Newsletter tracking (open pixel + click redirect)
$routes->get('nl/pixel/(:any)', 'NewsletterTrackingController::pixel/$1');
$routes->get('r/(:any)', 'NewsletterTrackingController::redirect/$1');

// Newsletter capture (landing + subscribe + confirm + magnet)
$routes->get('newsletter', 'NewsletterController::landing');
$routes->post('newsletter/subscribe', 'NewsletterController::subscribe');
$routes->get('newsletter/confirme-seu-email', 'NewsletterController::pendingConfirmation');
$routes->get('newsletter/confirmar/(:any)', 'NewsletterController::confirm/$1');
$routes->get('newsletter/obrigado', 'NewsletterController::thankYou');
$routes->get('newsletter/magnet/(:any)', 'NewsletterController::magnetDownload/$1');
$routes->get('connect-with-facebook', 'AuthController::connectWithFacebook');
$routes->get('facebook-callback', 'AuthController::facebookCallback');
$routes->get('connect-with-google', 'AuthController::connectWithGoogle');
$routes->get('connect-with-vk', 'AuthController::connectWithVK');
$routes->get('gnews/feed', 'HomeController::googleNewsFeeds');
$routes->get('simulador-aurum', 'HomeController::simuladorAurum');
$routes->get('simulador-seguro-resgatavel', 'HomeController::simuladorSeguroResgatavel');
$routes->get('playbook/importacao-blindada', 'HomeController::playbookImportacaoBlindada');
$routes->get('playbook/exportacao-premium', 'HomeController::playbookExportacaoPremium');
$routes->post('api/save-simulator-lead', 'ApiController::saveSimulatorLead');
$routes->match(['post', 'options'], 'api/quotation/preview', 'ApiController::quotationPreview');
$routes->match(['post', 'options'], 'api/quotation/unlock', 'ApiController::quotationUnlock');

// Bio Links Routes
$routes->get('bio', 'BioLinksController::index');
$routes->get('bio/click/(:num)', 'BioLinksController::click/$1');

// Web Stories Routes
$routes->get('web-stories', 'WebStoriesController::index');
$routes->get('web-stories/view/(:num)', 'WebStoriesController::view/$1');
$routes->get('web-stories/story/(:num)', 'WebStoriesController::story/$1');
$routes->get('web-stories/click/(:num)', 'WebStoriesController::click/$1');
$routes->get('api/web-stories', 'WebStoriesController::apiGetStories');

// Wealth Manager (public)
$routes->get('wealth', 'WealthManagerController::index');
$routes->get('wealth/conversa', 'WealthManagerController::conversa', ['filter' => 'auth']);
$routes->post('wealth/lead', 'WealthManagerController::leadCapture');
$routes->post('WealthManager/sendMessage', 'WealthManagerController::sendMessage', ['filter' => 'auth']);
$routes->post('WealthManager/acceptConsent', 'WealthManagerController::acceptConsent', ['filter' => 'auth']);
$routes->post('WealthManager/saveProfileBasic', 'WealthManagerController::saveProfileBasic', ['filter' => 'auth']);
$routes->post('WealthManager/saveIncomeForm', 'WealthManagerController::saveIncomeForm', ['filter' => 'auth']);
$routes->post('WealthManager/saveExpenseForm', 'WealthManagerController::saveExpenseForm', ['filter' => 'auth']);
$routes->post('WealthManager/saveDependentsForm', 'WealthManagerController::saveDependentsForm', ['filter' => 'auth']);
$routes->post('WealthManager/saveAllocationForm', 'WealthManagerController::saveAllocationForm', ['filter' => 'auth']);
$routes->post('WealthManager/saveRealEstateForm', 'WealthManagerController::saveRealEstateForm', ['filter' => 'auth']);
$routes->post('WealthManager/saveLiabilitiesForm', 'WealthManagerController::saveLiabilitiesForm', ['filter' => 'auth']);
$routes->post('WealthManager/saveGoalsForm', 'WealthManagerController::saveGoalsForm', ['filter' => 'auth']);
$routes->get('wealth/resultado', 'WealthManagerController::resultado', ['filter' => 'auth']);
$routes->get('wealth/resultado/pdf', 'WealthManagerController::resumoPdf', ['filter' => 'auth']);
$routes->get('wealth/agendar', 'WealthManagerController::agendar');
$routes->post('wealth/agendar', 'WealthManagerController::agendarPost');
$routes->post('WealthManager/trackEvent', 'WealthManagerController::trackEvent');

// CMS Pages (public)
$routes->get('p/(:any)', 'CmsController::view/$1');

/*
 * --------------------------------------------------------------------
 * Post Routes
 * --------------------------------------------------------------------
 */
$routes->post('register-post', 'AuthController::registerPost');
$routes->post('forgot-password-post', 'AuthController::forgotPasswordPost');
$routes->post('reset-password-post', 'AuthController::resetPasswordPost');
$routes->post('contact-post', 'HomeController::contactPost');
$routes->post('switch-dark-mode', 'CommonController::switchDarkMode');
$routes->post('follow-user-post', 'ProfileController::followUnfollowUserPost');
$routes->post('edit-profile-post', 'ProfileController::editProfilePost');
$routes->post('social-accounts-post', 'ProfileController::socialAccountsPost');
$routes->post('preferences-post', 'ProfileController::preferencesPost');
$routes->post('change-password-post', 'ProfileController::changePasswordPost');
$routes->post('delete-account-post', 'ProfileController::deleteAccountPost');
$routes->post('download-file', 'CommonController::downloadFile');
$routes->post('add-newsletter-post', 'AjaxController::addNewsletterPost');
$routes->post('close-cookies-warning-post', 'AjaxController::closeCookiesWarningPost');

/*f
 * --------------------------------------------------------------------
 * Admin Routes
 * --------------------------------------------------------------------
 */

$routes->get($customRoutes->admin . '/login', 'CommonController::adminLogin');
$routes->post($customRoutes->admin . '/login-post', 'CommonController::adminLoginPost');

$routes->group($customRoutes->admin, ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'AdminController::index');
    $routes->get('themes', 'AdminController::themes');
    //pages
    $routes->get('pages', 'AdminController::pages');
    $routes->get('add-page', 'AdminController::addPage');
    $routes->get('edit-page/(:num)', 'AdminController::editPage/$1');
    $routes->get('navigation', 'AdminController::navigation');
    $routes->get('edit-menu-link/(:num)', 'AdminController::editMenuLink/$1');
    //posts
    $routes->get('post-format', 'PostController::postFormat');
    $routes->get('add-post', 'PostController::addPost');
    $routes->get('posts', 'PostController::posts');
    $routes->get('slider-posts', 'PostController::sliderPosts');
    $routes->get('featured-posts', 'PostController::featuredPosts');
    $routes->get('breaking-news', 'PostController::breakingNews');
    $routes->get('recommended-posts', 'PostController::recommendedPosts');
    $routes->get('pending-posts', 'PostController::pendingPosts');
    $routes->get('scheduled-posts', 'PostController::scheduledPosts');
    $routes->get('drafts', 'PostController::drafts');
    $routes->get('bulk-post-upload', 'PostController::bulkPostUpload');
    $routes->get('edit-post/(:num)', 'PostController::editPost/$1');
    //content AI center
    $routes->get('content-ai', 'ContentAIController::index');
    $routes->post('content-ai/settings', 'ContentAIController::saveSettingsPost');
    $routes->post('content-ai/calendar/add', 'ContentAIController::addCalendarItemPost');
    $routes->post('content-ai/calendar/delete', 'ContentAIController::deleteCalendarItemPost');
    $routes->post('content-ai/calendar/approve', 'ContentAIController::approveCalendarItemPost');
    $routes->post('content-ai/calendar/retry', 'ContentAIController::retryCalendarItemPost');
    $routes->post('content-ai/run-now', 'ContentAIController::runNowPost');
    $routes->post('content-ai/trends/fetch', 'ContentAIController::fetchTrendsPost');
    $routes->post('content-ai/trends/update', 'ContentAIController::updateTrendFlagsPost');
    $routes->post('content-ai/trends/add', 'ContentAIController::addSelectedTrendsToCalendarPost');
    $routes->post('content-ai/x-pulse/run', 'ContentAIController::runXPulseNowPost');
    //SEO analysis (keyword ranking tracker)
    $routes->get('seo-analysis', 'SeoAnalysisController::index');
    $routes->get('seo-analysis/keywords', 'SeoAnalysisController::keywords');
    $routes->get('seo-analysis/keyword/(:num)', 'SeoAnalysisController::keywordDetail/$1');
    $routes->post('seo-analysis/keyword/add', 'SeoAnalysisController::addKeywordPost');
    $routes->post('seo-analysis/keyword/update', 'SeoAnalysisController::updateKeywordPost');
    $routes->post('seo-analysis/keyword/toggle', 'SeoAnalysisController::toggleKeywordPost');
    $routes->post('seo-analysis/keyword/delete', 'SeoAnalysisController::deleteKeywordPost');
    $routes->post('seo-analysis/keywords/sync', 'SeoAnalysisController::syncKeywordsPost');
    $routes->post('seo-analysis/fetch-now', 'SeoAnalysisController::fetchNowPost');
    //rss feeds
    $routes->get('feeds', 'RssController::feeds');
    $routes->get('import-feed', 'RssController::importFeed');
    $routes->get('edit-feed/(:num)', 'RssController::editFeed/$1');
    //categories
    $routes->get('add-category', 'CategoryController::addCategory');
    $routes->get('categories', 'CategoryController::categories');
    $routes->get('edit-category/(:num)', 'CategoryController::editCategory/$1');
    //widgets
    $routes->get('widgets', 'AdminController::widgets');
    $routes->get('add-widget', 'AdminController::addWidget');
    // CMS helper
    $routes->get('cms-pages/seed-wealth', 'PagesAdminController::seedWealthLp');
    $routes->get('cms-pages/restore/(:num)', 'PagesAdminController::restore/$1');
    $routes->get('edit-widget/(:num)', 'AdminController::editWidget/$1');
    //polls
    $routes->get('polls', 'AdminController::polls');
    $routes->get('add-poll', 'AdminController::addPoll');
    $routes->get('edit-poll/(:num)', 'AdminController::editPoll/$1');
    //gallery
    $routes->get('gallery-images', 'GalleryController::images');
    $routes->get('gallery-add-image', 'GalleryController::addImage');
    $routes->get('edit-gallery-image/(:num)', 'GalleryController::editImage/$1');
    $routes->get('gallery-albums', 'GalleryController::albums');
    $routes->get('edit-gallery-album/(:num)', 'GalleryController::editAlbum/$1');
    $routes->get('gallery-categories', 'GalleryController::categories');
    $routes->get('edit-gallery-category/(:num)', 'GalleryController::editCategory/$1');
    //contact
    $routes->get('contact-messages', 'AdminController::contactMessages');
    $routes->get('simulator-leads', 'AdminController::simulatorLeads');
    //comments
    $routes->get('comments', 'AdminController::comments');
    $routes->get('pending-comments', 'AdminController::pendingComments');
    //newsletter
    $routes->get('newsletter', 'AdminController::newsletter');
    $routes->post('newsletter-send-email', 'AdminController::newsletterSendEmail');
    //newsletter IA (editorial lines + queue + analytics)
    $routes->get('newsletter/editorial-lines', 'NewsletterAdminController::editorialLines');
    $routes->get('newsletter/editorial-lines/new', 'NewsletterAdminController::editorialLineForm');
    $routes->get('newsletter/editorial-lines/edit/(:num)', 'NewsletterAdminController::editorialLineForm/$1');
    $routes->post('newsletter/editorial-lines/save', 'NewsletterAdminController::editorialLineSave');
    $routes->post('newsletter/editorial-lines/delete/(:num)', 'NewsletterAdminController::editorialLineDelete/$1');
    $routes->post('newsletter/editorial-lines/generate/(:num)', 'NewsletterAdminController::editorialLineGenerate/$1');
    $routes->get('newsletter/queue', 'NewsletterAdminController::queue');
    $routes->get('newsletter/queue/view/(:num)', 'NewsletterAdminController::queueView/$1');
    $routes->post('newsletter/queue/update/(:num)', 'NewsletterAdminController::queueUpdate/$1');
    $routes->post('newsletter/queue/approve/(:num)', 'NewsletterAdminController::queueApprove/$1');
    $routes->post('newsletter/queue/cancel/(:num)', 'NewsletterAdminController::queueCancel/$1');
    $routes->post('newsletter/queue/dispatch/(:num)', 'NewsletterAdminController::queueDispatch/$1');
    $routes->get('newsletter/analytics', 'NewsletterAdminController::analytics');
    //newsletter settings + magnets
    $routes->get('newsletter/settings', 'NewsletterAdminController::settings');
    $routes->post('newsletter/settings/save', 'NewsletterAdminController::settingsSave');
    $routes->get('newsletter/magnets', 'NewsletterAdminController::magnets');
    $routes->get('newsletter/magnets/new', 'NewsletterAdminController::magnetForm');
    $routes->get('newsletter/magnets/edit/(:num)', 'NewsletterAdminController::magnetForm/$1');
    $routes->post('newsletter/magnets/save', 'NewsletterAdminController::magnetSave');
    $routes->post('newsletter/magnets/delete/(:num)', 'NewsletterAdminController::magnetDelete/$1');
    //newsletter CRM sync
    $routes->get('newsletter/crm-sync', 'NewsletterAdminController::crmSync');
    $routes->post('newsletter/crm-sync/run', 'NewsletterAdminController::crmSyncRun');
    $routes->get('newsletter/crm-sync/view/(:num)', 'NewsletterAdminController::crmSyncView/$1');
    //reward-system
    $routes->get('reward-system', 'RewardController::rewardSystem');
    $routes->get('reward-system/earnings', 'RewardController::earnings');
    $routes->get('reward-system/payouts', 'RewardController::payouts');
    $routes->get('reward-system/add-payout', 'RewardController::addPayout');
    $routes->get('reward-system/pageviews', 'RewardController::pageviews');
    $routes->get('author-earnings', 'EarningsController::authorEarnings');
    $routes->get('set-payout-account', 'EarningsController::setPayoutAccount');

    //ad spaces
    $routes->get('ad-spaces', 'AdminController::adSpaces');
    //users
    $routes->get('users', 'AdminController::users');
    $routes->get('edit-user/(:num)', 'AdminController::editUser/$1');
    $routes->get('add-user', 'AdminController::addUser');
    //roles permissions
    $routes->get('roles-permissions', 'AdminController::rolesPermissions');
    $routes->get('add-role', 'AdminController::addRole');
    $routes->get('edit-role/(:num)', 'AdminController::editRole/$1');
    //seo tools
    $routes->get('seo-tools', 'AdminController::seoTools');
    //storage
    $routes->get('storage', 'AdminController::storage');
    //cache system
    $routes->get('cache-system', 'AdminController::cacheSystem');
    //google news
    $routes->get('google-news', 'AdminController::googleNews');
    //settings
    $routes->get('preferences', 'AdminController::preferences');
    $routes->get('route-settings', 'AdminController::routeSettings');
    $routes->get('email-settings', 'AdminController::emailSettings');
    $routes->get('font-settings', 'AdminController::fontSettings');
    $routes->get('edit-font/(:num)', 'AdminController::editFont/$1');
    $routes->get('social-login-settings', 'AdminController::socialLoginSettings');
    $routes->get('general-settings', 'AdminController::generalSettings');
    //dashboard
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('dashboard/google-analytics', 'DashboardController::googleAnalytics');
    $routes->post('dashboard/google-analytics/credentials', 'DashboardController::saveGoogleAnalyticsCredentials');
    $routes->get('dashboard/google-analytics/connect', 'DashboardController::connectGoogleAnalytics');
    $routes->get('dashboard/google-analytics/callback', 'DashboardController::googleAnalyticsCallback');
    $routes->post('dashboard/google-analytics/property', 'DashboardController::saveGoogleAnalyticsProperty');
    $routes->post('dashboard/google-analytics/disconnect', 'DashboardController::disconnectGoogleAnalytics');
    $routes->get('dashboard/widgets', 'DashboardController::widgets');
    $routes->post('dashboard/save-widget-config', 'DashboardController::saveWidgetConfig');
    $routes->get('dashboard/live-data', 'DashboardController::liveData');
    $routes->post('dashboard/get-analytics-data', 'DashboardController::getAnalyticsData');
    $routes->get('dashboard/export-data', 'DashboardController::exportData');
    //language
    $routes->get('language-settings', 'LanguageController::languages');
    $routes->get('edit-language/(:num)', 'LanguageController::editLanguage/$1');
    $routes->get('edit-translations/(:num)', 'LanguageController::editTranslations/$1');
    //tags
    $routes->get('tags', 'CategoryController::tags');
    //bio links
    $routes->get('bio-links', 'BioLinksController::admin');
    $routes->get('bio-links/add', 'BioLinksController::adminAdd');
    $routes->post('bio-links/add', 'BioLinksController::adminAdd');
    $routes->get('bio-links/edit/(:num)', 'BioLinksController::adminEdit/$1');
    $routes->post('bio-links/edit/(:num)', 'BioLinksController::adminEdit/$1');
    $routes->get('bio-links/delete/(:num)', 'BioLinksController::adminDelete/$1');
    $routes->post('bio-links/delete/(:num)', 'BioLinksController::adminDelete/$1');
    $routes->get('bio-links/toggle/(:num)', 'BioLinksController::adminToggle/$1');
    $routes->post('bio-links/update-order', 'BioLinksController::adminUpdateOrder');
    $routes->post('bio-links/update-settings', 'BioLinksController::updateBioSettings');
    $routes->get('bio-links/analytics', 'BioLinksController::adminAnalytics');
    //web stories
    $routes->get('web-stories', 'WebStoriesController::admin');
    $routes->get('web-stories/add', 'WebStoriesController::adminAdd');
    $routes->post('web-stories/add', 'WebStoriesController::adminAddPost');
    $routes->get('web-stories/edit/(:num)', 'WebStoriesController::adminEdit/$1');
    $routes->post('web-stories/edit/(:num)', 'WebStoriesController::adminEditPost/$1');
    $routes->get('web-stories/delete/(:num)', 'WebStoriesController::adminDelete/$1');
    $routes->post('web-stories/delete/(:num)', 'WebStoriesController::adminDelete/$1');
    $routes->get('web-stories/toggle/(:num)', 'WebStoriesController::adminToggle/$1');
    // wealth manager (admin)
    $routes->get('wealth', 'WealthAdminController::index');
    $routes->get('wealth/settings', 'WealthAdminController::settings');
    $routes->post('WealthManager/adminSettingsPost', 'WealthAdminController::settingsPost');
    $routes->get('wealth/tokens', 'WealthAdminController::tokens');
    $routes->post('WealthManager/adminTokensPost', 'WealthAdminController::tokensPost');
    $routes->get('wealth/appointments', 'WealthAdminController::appointments');
    $routes->post('WealthManager/adminAppointmentStatusPost', 'WealthAdminController::appointmentStatusPost');
    $routes->get('wealth/cms', 'WealthAdminController::cms');
    $routes->post('WealthManager/adminCMSPost', 'WealthAdminController::cmsPost');
    $routes->get('wealth/export', 'WealthAdminController::export');
    $routes->post('WealthManager/adminExportCsv', 'WealthAdminController::exportCsv');
    $routes->get('wealth/view-result/(:num)', 'WealthAdminController::viewResult/$1');
    $routes->get('wealth/logs', 'WealthAdminController::logs');
    $routes->get('wealth/run-setup', 'WealthAdminController::runSetup');
    $routes->get('wealth/diagnostics', 'WealthAdminController::diagnostics');
    $routes->get('wealth/sessions', 'WealthAdminController::sessions');
    $routes->get('wealth/session/(:num)', 'WealthAdminController::session/$1');

    // marketing home cms
    $routes->get('marketing/home-cms', 'MarketingAdminController::homeCms');
    $routes->post('marketing/home-cms', 'MarketingAdminController::homeCmsPost');
    $routes->get('marketing/simulators-cms', 'MarketingAdminController::simulatorsCms');
    $routes->post('marketing/simulators-cms', 'MarketingAdminController::simulatorsCmsPost');
    $routes->get('marketing/consorcio-cms', 'MarketingAdminController::consorcioCms');
    $routes->post('marketing/consorcio-cms', 'MarketingAdminController::consorcioCmsPost');

    // CMS Pages (admin) - visual builder (avoid conflict with AdminController::pages)
    $routes->get('cms-pages', 'PagesAdminController::index');
    $routes->post('cms-pages/add', 'PagesAdminController::add');
    $routes->get('cms-pages/edit/(:num)', 'PagesAdminController::edit/$1');
    $routes->post('cms-pages/edit/(:num)', 'PagesAdminController::editPost/$1');
    $routes->get('cms-pages/builder/(:num)', 'PagesAdminController::builder/$1');
    $routes->post('cms-pages/save-builder/(:num)', 'PagesAdminController::saveBuilder/$1');
    $routes->get('cms-pages/publish/(:num)', 'PagesAdminController::publish/$1');
    $routes->get('cms-pages/delete/(:num)', 'PagesAdminController::delete/$1');
    $routes->post('cms-pages/save-template', 'PagesAdminController::saveTemplate');
    $routes->get('cms-pages/delete-template/(:num)', 'PagesAdminController::deleteTemplate/$1');
    $routes->get('cms-pages/run-setup', 'PagesAdminController::runSetup');
    // link legacy page to visual builder
    $routes->get('pages/visual/(:num)', 'PagesAdminController::linkLegacy/$1');
});


/*
 * --------------------------------------------------------------------
 * Static POST Routes
 * --------------------------------------------------------------------
 */

$postRoutesArray = [
    //Admin
    'Admin/deleteNavigationPost',
    'Admin/deletePagePost',
    'Admin/adSpacesPost',
    'Admin/googleAdsenseCodePost',
    'Admin/cacheSystemPost',
    'Admin/approveCommentPost',
    'Admin/deleteCommentPost',
    'Admin/deleteContactMessagePost',
    'Admin/googleNewsPost',
    'Admin/seoToolsPost',
    'Admin/googleIndexingApiPost',
    'Admin/indexNowSettingsPost',
    'Post/testIndexNowApiPost',
    'Admin/sitemapSettingsPost',
    'Admin/sitemapPost',
    'Admin/socialLoginSettingsPost',
    'Admin/storagePost',
    'Admin/awsS3Post',
    'Admin/setThemePost',
    'Admin/setThemeSettingsPost',
    'Admin/editFontPost',
    'Admin/setSiteFontPost',
    'Admin/addFontPost',
    'Admin/deleteFontPost',
    'Admin/setActiveLanguagePost',
    'Admin/downloadDatabaseBackup',
    'Admin/editMenuLinkPost',
    'Admin/addMenuLinkPost',
    'Admin/menuLimitPost',
    'Admin/sortMenuItems',
    'Admin/hideShowHomeLink',
    'Admin/deleteSubscriberPost',
    'Admin/newsletterSettingsPost',
    'Admin/newsletterSendEmailPost',
    'Admin/addPagePost',
    'Admin/editPagePost',
    'Admin/deletePagePost',
    'Admin/addPollPost',
    'Admin/editPollPost',
    'Admin/deletePollPost',
    'Admin/emailSettingsPost',
    'Admin/emailVerificationSettingsPost',
    'Admin/contactEmailSettingsPost',
    'Admin/sendTestEmailPost',
    'Admin/generalSettingsPost',
    'Admin/contactSettingsPost',
    'Admin/socialMediaSettingsPost', 
    'Admin/customHeaderCodesPost',
    'Admin/cookiesWarningPost',
    'Admin/metaConversionsApiPost',
    'Admin/recaptchaSettingsPost',
    'Admin/maintenanceModePost',
    'Admin/preferencesPost',
    'Admin/aiWriterPost',
    'Admin/validateOpenAIKeyPost',
    'Admin/validateAIWriterKeyPost',
    'Admin/fileUploadSettingsPost',
    'Admin/routeSettingsPost',
    'Admin/addUserPost',
    'Admin/userOptionsPost',
    'Admin/deleteUserPost',
    'Admin/addRolePost',
    'Admin/editRolePost',
    'Admin/editUserPost',
    'Admin/deleteRolePost',
    'Admin/loadUsersDropdown',
    'Admin/changeUserRolePost',
    'Admin/addWidgetPost',
    'Admin/editWidgetPost',
    'Admin/deleteWidgetPost',
    'Admin/getMenuLinksByLang',
    'Admin/approveSelectedComments',
    'Admin/deleteSelectedComments',
    'Admin/deleteSelectedContactMessages',
    'Admin/deleteSimulatorLeadPost',
    'Admin/deleteSelectedSimulatorLeads',
    'Admin/updateSimulatorLeadStatus',
    //Ajax
    'Ajax/setThemeModePost',
    'Ajax/incrementPostViews',
    'Ajax/runOnPageLoad',
    'Ajax/addPollVote',
    'Ajax/loadMorePosts',
    'Ajax/loadMoreUsers',
    'Ajax/loadMoreSubscribers',
    'Ajax/addRemoveReadingListItem',
    'Ajax/addReaction',
    'Ajax/addCommentPost',
    'Ajax/addCommentPost',
    'Ajax/addCommentPost',
    'Ajax/addCommentPost',
    'Ajax/loadSubcommentBox',
    'Ajax/likeCommentPost',
    'Ajax/loadMoreComments',
    'Ajax/deleteCommentPost',
    'Ajax/addRemoveReadingListItem',
    'Ajax/getQuizAnswers',
    'Ajax/getQuizResults',
    'Ajax/addPostPollVote',
    'Ajax/generateTextAI',
    'Ajax/getTagSuggestions',
    'Ajax/addSimulatorLeadPost',
    //Auth
    'Auth/loginPost',
    //WebStories
    'WebStories/adminUpdateOrder',
    'WebStories/generateImage',
    'WebStories/uploadImage',
    'WebStories/generateFromArticle',
    'WebStories/generateImagesStep',
    'WebStories/bulkVisibility',
    'WebStories/bulkDelete',
    'WebStories/testConnection',
    'WebStories/checkImageStatus',
    //Category
    'Category/deleteCategoryPost',
    'Category/addCategoryPost',
    'Category/deleteCategoryPost',
    'Category/editCategoryPost',
    'Category/getParentCategoriesByLang',
    'Category/getSubCategories',
    'Category/addTagPost',
    'Category/editTagPost',
    'Category/deleteTagPost',
    //Earnings
    'Earnings/setPayoutAccountPost',
    'Earnings/newPayoutRequestPost',
    //File
    'File/uploadFile',
    'File/uploadAudio',
    'File/uploadImage',
    'File/uploadQuizImageFile',
    'File/uploadVideo',
    'File/getImages',
    'File/deleteImage',
    'File/loadMoreImages',
    'File/searchImage',
    'File/getQuizImages',
    'File/deleteQuizImage',
    'File/loadMoreQuizImages',
    'File/searchQuizImage',
    'File/uploadRecipeImage',
    'File/getRecipeImages',
    'File/deleteRecipeImage',
    'File/loadMoreRecipeImages',
    'File/searchRecipeImage',
    'File/deleteFile',
    'File/getFiles',
    'File/loadMoreFiles',
    'File/searchFiles',
    'File/deleteVideo',
    'File/getVideos',
    'File/loadMoreVideos',
    'File/searchVideos',
    'File/deleteAudio',
    'File/getAudios',
    'File/loadMoreAudios',
    'File/searchAudios',
    //Gallery
    'Gallery/addImagePost',
    'Gallery/addAlbumPost',
    'Gallery/deleteAlbumPost',
    'Gallery/addCategoryPost',
    'Gallery/deleteCategoryPost',
    'Gallery/editAlbumPost',
    'Gallery/editCategoryPost',
    'Gallery/editImagePost',
    'Gallery/deleteImagePost',
    'Gallery/setAsAlbumCover',
    'Gallery/getAlbumsByLang',
    'Gallery/getCategoriesByAlbum',
    //Language
    'Language/addLanguagePost',
    'Language/editLanguagePost',
    'Language/setDefaultLanguagePost',
    'Language/exportLanguagePost',
    'Language/deleteLanguagePost',
    'Language/importLanguagePost',
    'Language/editTranslationsPost',
    //Post
    'Post/addPostPost',
    'Post/downloadCSVFilePost',
    'Post/generateCSVObjectPost',
    'Post/importCSVItemPost',
    'Post/postOptionsPost',
    'Post/deletePost',
    'Post/editPostPost',
    'Post/deletePostMainImage',
    'Post/deletePostAdditionalImage',
    'Post/setHomeSliderPostOrderPost',
    'Post/setFeaturedPostOrderPost',
    'Post/deleteSelectedPosts',
    'Post/postBulkOptionsPost',
    'Post/getVideoFromURL',
    'Post/deletePostVideo',
    'Post/deletePostAudio',
    'Post/deletePostFile',
    'Post/generateCoverImageAI',
    'Post/getListItemHTML',
    'Post/addListItem',
    'Post/deletePostListItemPost',
    'Post/getQuizQuestionHTML',
    'Post/addQuizQuestion',
    'Post/getQuizAnswerHTML',
    'Post/addQuizQuestionAnswer',
    'Post/deleteQuizQuestion',
    'Post/deleteQuizQuestionAnswer',
    'Post/getQuizResultHTML',
    'Post/addQuizResult',
    'Post/deleteQuizResult',
    'Post/testGoogleIndexingApiPost',
    //Reward
    'Reward/addPayoutPost',
    'Reward/deletePayoutPost',
    'Reward/updateSettingsPost',
    'Reward/updatePayoutPost',
    'Reward/updateCurrencyPost',
    'Reward/approvePayoutPost',
    //Rss
    'Rss/editFeedPost',
    'Rss/checkFeedPosts',
    'Rss/deleteFeedPost',
    'Rss/importFeedPost',
];

foreach ($postRoutesArray as $item) {
    $array = explode('/', $item);
    $routes->post($item, $array[0] . 'Controller::' . $array[1]);
}

/*
 * --------------------------------------------------------------------
 * Dynamic Routes
 * --------------------------------------------------------------------
 */

if (!empty($languages)) {
    foreach ($languages as $language) {
        $key = '';
        if ($generalSettings->site_lang != $language->id) {
            $key = $language->short_form . '/';
            $routes->get($language->short_form, 'HomeController::index');
            $routes->get($key . 'blog', 'HomeController::blog');
            $routes->get($key . 'simuladores', 'HomeController::simulatorsHub');
            $routes->get($key . 'simuladores/cambio', 'HomeController::simulatorsFxHub');
            $routes->get($key . 'simuladores-cambio', 'HomeController::simulatorsFxLegacyRedirect');
            $routes->get($key . 'simulador-de-risco-cambial', 'HomeController::simulatorsFxLegacyRedirect');
            $routes->get($key . 'fx-loan', 'HomeController::simulatorsFxLegacyRedirect');
            $routes->get($key . 'simulador-seguro-resgatavel', 'HomeController::simuladorSeguroResgatavel');
        }
        $routes->get($key . $customRoutes->register, 'AuthController::register');
        $routes->get($key . $customRoutes->forgot_password, 'AuthController::forgotPassword');
        $routes->get($key . $customRoutes->logout, 'CommonController::logout');
        $routes->get($key . $customRoutes->posts, 'HomeController::posts');
        $routes->get($key . $customRoutes->tag . '/(:any)', 'HomeController::tag/$1');
        $routes->get($key . $customRoutes->gallery_album . '/(:num)', 'HomeController::galleryAlbum/$1');
        $routes->get($key . $customRoutes->search, 'HomeController::search');
        $routes->get($key . $customRoutes->profile . '/(:any)', 'ProfileController::profile/$1');
        $routes->get($key . $customRoutes->settings, 'ProfileController::editProfile', ['filter' => 'auth']);
        $routes->get($key . $customRoutes->settings . '/' . $customRoutes->social_accounts, 'ProfileController::socialAccounts', ['filter' => 'auth']);
        $routes->get($key . $customRoutes->settings . '/' . $customRoutes->preferences, 'ProfileController::preferences', ['filter' => 'auth']);
        $routes->get($key . $customRoutes->settings . '/' . $customRoutes->change_password, 'ProfileController::changePassword', ['filter' => 'auth']);
        $routes->get($key . $customRoutes->settings . '/' . $customRoutes->delete_account, 'ProfileController::deleteAccount', ['filter' => 'auth']);
        $routes->get($key . $customRoutes->reading_list, 'HomeController::readingList', ['filter' => 'auth']);
        $routes->get($key . $customRoutes->rss_feeds, 'HomeController::rssFeeds');
        $routes->get($key . 'rss/latest-posts', 'HomeController::rssLatestPosts');
        $routes->get($key . 'rss/category/(:any)', 'HomeController::rssByCategory/$1');
        $routes->get($key . 'rss/author/(:any)', 'HomeController::rssByUser/$1');
        $routes->get($key . 'preview/(:any)', 'HomeController::preview/$1');
        $routes->get($key . 'reset-password', 'AuthController::resetPassword');
        $routes->get($key . 'confirm-email', 'AuthController::confirmEmail');
        if ($generalSettings->site_lang != $language->id) {
            $routes->get($key . '(:any)/(:any)/(:any)', 'HomeController::error404');
            $routes->get($key . '(:any)/(:any)', 'HomeController::subCategory/$1/$2');
            $routes->get($key . '(:any)', 'HomeController::any/$1');
        }
    }
}

$routes->get('(:any)/(:any)/(:any)', 'HomeController::error404');
$routes->get('(:any)/(:any)', 'HomeController::subCategory/$1/$2');
$routes->get('(:any)', 'HomeController::any/$1');

// CLI utility for Wealth Manager setup (unified under WealthAdminController)
$routes->cli($customRoutes->admin . '/wealth/run-setup', 'WealthAdminController::runSetup');
