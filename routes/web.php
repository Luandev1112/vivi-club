<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Upgrading
|--------------------------------------------------------------------------
|
| The upgrading process routes
|
*/
Route::group([
	'namespace' => 'App\Http\Controllers\Install',
	'middleware' => ['web', 'no.http.cache']
], function () {
	Route::get('upgrade', 'UpdateController@index');
	Route::post('upgrade/run', 'UpdateController@run');
});


/*
|--------------------------------------------------------------------------
| Installation
|--------------------------------------------------------------------------
|
| The installation process routes
|
*/
Route::group([
	'namespace'  => 'App\Http\Controllers\Install',
	'middleware' => ['web', 'install.checker', 'no.http.cache'],
	'prefix'     => 'install',
], function () {
	Route::get('/', 'InstallController@starting');
	Route::get('site_info', 'InstallController@siteInfo');
	Route::post('site_info', 'InstallController@siteInfo');
	Route::get('system_compatibility', 'InstallController@systemCompatibility');
	Route::get('database', 'InstallController@database');
	Route::post('database', 'InstallController@database');
	Route::get('database_import', 'InstallController@databaseImport');
	Route::get('cron_jobs', 'InstallController@cronJobs');
	Route::get('finish', 'InstallController@finish');
});


/*
|--------------------------------------------------------------------------
| Back-end
|--------------------------------------------------------------------------
|
| The admin panel routes
|
*/
Route::group([
	'namespace'  => 'App\Http\Controllers\Admin',
	'middleware' => ['web', 'install.checker'],
	'prefix'     => config('larapen.admin.route_prefix', 'admin'),
], function ($router) {
	// Auth
	// Authentication Routes...
	Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
	Route::post('login', 'Auth\LoginController@login');
	Route::get('logout', 'Auth\LoginController@logout')->name('logout');
	
	// Registration Routes...
	Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
	Route::post('register', 'Auth\RegisterController@register');
	
	// Password Reset Routes...
	Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
	Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
	Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
	Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
	
	// Admin Panel Area
	Route::group([
		'middleware' => ['admin', 'clearance', 'banned.user', 'no.http.cache'],
	], function ($router) {
		// Dashboard
		Route::get('dashboard', 'DashboardController@dashboard');
		Route::get('/', 'DashboardController@redirect');
		
		// Extra (must be called before CRUD)
		Route::get('homepage/{action}', 'HomeSectionController@reset')->where('action', 'reset_(.*)');
		Route::get('languages/sync_files', 'LanguageController@syncFilesLines');
		Route::get('languages/texts/{lang?}/{file?}', 'LanguageController@showTexts');
		Route::post('languages/texts/{lang}/{file}', 'LanguageController@updateTexts');
		Route::get('permissions/create_default_entries', 'PermissionController@createDefaultEntries');
		Route::get('blacklists/add', 'BlacklistController@banUserByEmail');
		Route::get('categories/rebuild-nested-set-nodes', 'CategoryController@rebuildNestedSetNodes');
		
		// CRUD
		CRUD::resource('advertisings', 'AdvertisingController');
		CRUD::resource('blacklists', 'BlacklistController');
		CRUD::resource('categories', 'CategoryController');
		CRUD::resource('categories/{catId}/subcategories', 'CategoryController');
		CRUD::resource('cities', 'CityController');
		CRUD::resource('companies', 'CompanyController');
		CRUD::resource('countries', 'CountryController');
		CRUD::resource('countries/{countryCode}/cities', 'CityController');
		CRUD::resource('countries/{countryCode}/admins1', 'SubAdmin1Controller');
		CRUD::resource('currencies', 'CurrencyController');
		CRUD::resource('genders', 'GenderController');
		CRUD::resource('homepage', 'HomeSectionController');
		CRUD::resource('admins1/{admin1Code}/cities', 'CityController');
		CRUD::resource('admins1/{admin1Code}/admins2', 'SubAdmin2Controller');
		CRUD::resource('admins2/{admin2Code}/cities', 'CityController');
		CRUD::resource('languages', 'LanguageController');
		CRUD::resource('meta_tags', 'MetaTagController');
		CRUD::resource('packages', 'PackageController');
		CRUD::resource('pages', 'PageController');
		CRUD::resource('payments', 'PaymentController');
		CRUD::resource('payment_methods', 'PaymentMethodController');
		CRUD::resource('permissions', 'PermissionController');
		CRUD::resource('pictures', 'PictureController');
		CRUD::resource('posts', 'PostController');
		CRUD::resource('p_types', 'PostTypeController');
		CRUD::resource('report_types', 'ReportTypeController');
		CRUD::resource('roles', 'RoleController');
		CRUD::resource('salary_types', 'SalaryTypeController');
		CRUD::resource('settings', 'SettingController');
		CRUD::resource('time_zones', 'TimeZoneController');
		CRUD::resource('users', 'UserController');
		
		// Others
		Route::get('account', 'UserController@account');
		Route::post('ajax/{table}/{field}', 'InlineRequestController@make');
		
		// Backup
		Route::get('backups', 'BackupController@index');
		Route::put('backups/create', 'BackupController@create');
		Route::get('backups/download/{file_name?}', 'BackupController@download');
		Route::delete('backups/delete/{file_name?}', 'BackupController@delete')->where('file_name', '(.*)');
		
		// Actions
		Route::get('actions/clear_cache', 'ActionController@clearCache');
		Route::get('actions/clear_images_thumbnails', 'ActionController@clearImagesThumbnails');
		Route::get('actions/maintenance/{mode}', 'ActionController@maintenance')->where('mode', '(down|up)');
		
		// Re-send Email or Phone verification message
		Route::get('verify/user/{id}/resend/email', 'UserController@reSendVerificationEmail');
		Route::get('verify/user/{id}/resend/sms', 'UserController@reSendVerificationSms');
		Route::get('verify/post/{id}/resend/email', 'PostController@reSendVerificationEmail');
		Route::get('verify/post/{id}/resend/sms', 'PostController@reSendVerificationSms');
		
		// Plugins
		Route::get('plugins', 'PluginController@index');
		Route::post('plugins/{plugin}/install', 'PluginController@install');
		Route::get('plugins/{plugin}/install', 'PluginController@install');
		Route::get('plugins/{plugin}/uninstall', 'PluginController@uninstall');
		Route::get('plugins/{plugin}/delete', 'PluginController@delete');
		
		// System Info
		Route::get('system', 'SystemController@systemInfo');
	});
});


/*
|--------------------------------------------------------------------------
| Front-end
|--------------------------------------------------------------------------
|
| The not translated front-end routes
|
*/
Route::group([
	'namespace'  => 'App\Http\Controllers',
	'middleware' => ['web', 'install.checker'],
], function ($router) {
	// Select Language
	Route::get('lang/{code}', 'Locale\SetLocaleController@redirect');
	
	// FILES
	Route::get('file', 'FileController@show');
	Route::get('js/fileinput/locales/{code}.js', 'FileController@fileInputLocales');
	
	// SEO
	Route::get('sitemaps.xml', 'SitemapsController@index');
	
	// Impersonate (As admin user, login as an another user)
	Route::group(['middleware' => 'auth'], function ($router) {
		Route::impersonate();
	});
});


/*
|--------------------------------------------------------------------------
| Front-end
|--------------------------------------------------------------------------
|
| The translated front-end routes
|
*/
Route::group([
	'namespace'  => 'App\Http\Controllers',
], function ($router) {
	Route::group(['middleware' => ['web', 'install.checker']], function ($router) {
		// Country Code Pattern
		$countryCodePattern = implode('|', array_map('strtolower', array_keys(getCountries())));
		$countryCodePattern = !empty($countryCodePattern) ? $countryCodePattern : 'us';
		/*
		 * NOTE:
		 * '(?i:foo)' : Make 'foo' case-insensitive
		 */
		$countryCodePattern = '(?i:' . $countryCodePattern . ')';
		$router->pattern('countryCode', $countryCodePattern);
		
		
		// HOMEPAGE
		Route::get('/', 'HomeController@index');
		Route::get(dynamicRoute('routes.countries'), 'CountriesController@index');
		
		
		// AUTH
		Route::group(['middleware' => ['guest', 'no.http.cache']], function ($router) {
			// Registration Routes...
			Route::get(dynamicRoute('routes.register'), 'Auth\RegisterController@showRegistrationForm');
			Route::post(dynamicRoute('routes.register'), 'Auth\RegisterController@register');
			Route::get('register/finish', 'Auth\RegisterController@finish');
			
			// Authentication Routes...
			Route::get(dynamicRoute('routes.login'), 'Auth\LoginController@showLoginForm');
			Route::post(dynamicRoute('routes.login'), 'Auth\LoginController@login');
			
			// Forgot Password Routes...
			Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
			Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
			
			// Reset Password using Token
			Route::get('password/token', 'Auth\ForgotPasswordController@showTokenRequestForm');
			Route::post('password/token', 'Auth\ForgotPasswordController@sendResetToken');
			
			// Reset Password using Link (Core Routes...)
			Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
			Route::post('password/reset', 'Auth\ResetPasswordController@reset');
			
			// Social Authentication
			$router->pattern('provider', 'facebook|linkedin|twitter|google');
			Route::get('auth/{provider}', 'Auth\SocialController@redirectToProvider');
			Route::get('auth/{provider}/callback', 'Auth\SocialController@handleProviderCallback');
		});
		
		// Email Address or Phone Number verification
		$router->pattern('field', 'email|phone');
		Route::get('verify/user/{id}/resend/email', 'Auth\RegisterController@reSendVerificationEmail');
		Route::get('verify/user/{id}/resend/sms', 'Auth\RegisterController@reSendVerificationSms');
		Route::get('verify/user/{field}/{token?}', 'Auth\RegisterController@verification');
		Route::post('verify/user/{field}/{token?}', 'Auth\RegisterController@verification');
		
		// User Logout
		Route::get(dynamicRoute('routes.logout'), 'Auth\LoginController@logout');
		
		
		// POSTS
		Route::group(['namespace' => 'Post'], function ($router) {
			$router->pattern('id', '[0-9]+');
			// $router->pattern('slug', '.*');
			$bannedSlugs = collect(config('routes'))->filter(function ($value, $key) {
				return (!Str::contains($key, '.') && !empty($value));
			})->flatten()->toArray();
			if (!empty($bannedSlugs)) {
				/*
				 * NOTE:
				 * '^(?!companies|users)$' : Don't match 'companies' or 'users'
				 * '^(?=.*)$'              : Match any character
				 * '^((?!\/).)*$'          : Match any character, but don't match string with '/'
				 */
				$router->pattern('slug', '^(?!' . implode('|', $bannedSlugs) . ')(?=.*)((?!\/).)*$');
			} else {
				$router->pattern('slug', '^(?=.*)((?!\/).)*$');
			}
			
			// SingleStep Post creation
			Route::group(['namespace' => 'CreateOrEdit\SingleStep'], function ($router) {
				Route::get('create', 'CreateController@getForm');
				Route::post('create', 'CreateController@postForm');
				Route::get('create/finish', 'CreateController@finish');
				
				// Payment Gateway Success & Cancel
				Route::get('create/payment/success', 'CreateController@paymentConfirmation');
				Route::get('create/payment/cancel', 'CreateController@paymentCancel');
				Route::post('create/payment/success', 'CreateController@paymentConfirmation');
				
				// Email Address or Phone Number verification
				$router->pattern('field', 'email|phone');
				Route::get('verify/post/{id}/resend/email', 'CreateController@reSendVerificationEmail');
				Route::get('verify/post/{id}/resend/sms', 'CreateController@reSendVerificationSms');
				Route::get('verify/post/{field}/{token?}', 'CreateController@verification');
				Route::post('verify/post/{field}/{token?}', 'CreateController@verification');
			});
			
			// MultiSteps Post creation
			Route::group(['namespace' => 'CreateOrEdit\MultiSteps'], function ($router) {
				Route::get('posts/create/{tmpToken?}', 'CreateController@getForm');
				Route::post('posts/create', 'CreateController@postForm');
				Route::put('posts/create/{tmpToken}', 'CreateController@postForm');
				Route::get('posts/create/{tmpToken}/payment', 'PaymentController@getForm');
				Route::post('posts/create/{tmpToken}/payment', 'PaymentController@postForm');
				Route::get('posts/create/{tmpToken}/finish', 'CreateController@finish');
				
				// Payment Gateway Success & Cancel
				Route::get('posts/create/{tmpToken}/payment/success', 'PaymentController@paymentConfirmation');
				Route::get('posts/create/{tmpToken}/payment/cancel', 'PaymentController@paymentCancel');
				Route::post('posts/create/{tmpToken}/payment/success', 'PaymentController@paymentConfirmation');
				
				// Email Address or Phone Number verification
				$router->pattern('field', 'email|phone');
				Route::get('verify/post/{id}/resend/email', 'CreateController@reSendVerificationEmail');
				Route::get('verify/post/{id}/resend/sms', 'CreateController@reSendVerificationSms');
				Route::get('verify/post/{field}/{token?}', 'CreateController@verification');
				Route::post('verify/post/{field}/{token?}', 'CreateController@verification');
			});
			
			Route::group(['middleware' => 'auth'], function ($router) {
				$router->pattern('id', '[0-9]+');
				
				// SingleStep Post edition
				Route::group(['namespace' => 'CreateOrEdit\SingleStep'], function ($router) {
					Route::get('edit/{id}', 'EditController@getForm');
					Route::put('edit/{id}', 'EditController@postForm');
					
					// Payment Gateway Success & Cancel
					Route::get('edit/{id}/payment/success', 'EditController@paymentConfirmation');
					Route::get('edit/{id}/payment/cancel', 'EditController@paymentCancel');
					Route::post('edit/{id}/payment/success', 'EditController@paymentConfirmation');
				});
				
				// MultiSteps Post edition
				Route::group(['namespace' => 'CreateOrEdit\MultiSteps'], function ($router) {
					Route::get('posts/{id}/edit', 'EditController@getForm');
					Route::put('posts/{id}/edit', 'EditController@postForm');
					Route::get('posts/{id}/payment', 'PaymentController@getForm');
					Route::post('posts/{id}/payment', 'PaymentController@postForm');
					
					// Payment Gateway Success & Cancel
					Route::get('posts/{id}/payment/success', 'PaymentController@paymentConfirmation');
					Route::get('posts/{id}/payment/cancel', 'PaymentController@paymentCancel');
					Route::post('posts/{id}/payment/success', 'PaymentController@paymentConfirmation');
				});
			});
			
			// Post's Details
			Route::get(dynamicRoute('routes.post'), 'DetailsController@index');
			
			// Contact Job's Author
			Route::post('posts/{id}/contact', 'DetailsController@sendMessage');
			
			// Send report abuse
			Route::get('posts/{id}/report', 'ReportController@showReportForm');
			Route::post('posts/{id}/report', 'ReportController@sendReport');
		});
		Route::post('send-by-email', 'Search\SearchController@sendByEmail');
		
		
		// ACCOUNT
		Route::group(['prefix' => 'account'], function ($router) {
			// Messenger
			// Contact Post's Author
			Route::group([
				'namespace' => 'Account',
				'prefix'    => 'messages',
			], function ($router) {
				Route::post('posts/{id}', 'MessagesController@store');
			});
			
			Route::group([
				'middleware' => ['auth', 'banned.user', 'no.http.cache'],
				'namespace' => 'Account'
			], function ($router) {
				$router->pattern('id', '[0-9]+');
				
				// Users
				Route::get('/', 'EditController@index');
				Route::group(['middleware' => 'impersonate.protect'], function () {
					Route::put('/', 'EditController@updateDetails');
					Route::put('settings', 'EditController@updateSettings');
					Route::put('preferences', 'EditController@updatePreferences');
				});
				Route::get('close', 'CloseController@index');
				Route::group(['middleware' => 'impersonate.protect'], function () {
					Route::post('close', 'CloseController@submit');
				});
				
				// Companies
				Route::group(['prefix' => 'companies'], function ($router) {
					Route::get('/', 'CompanyController@index');
					Route::get('create', 'CompanyController@create');
					Route::post('/', 'CompanyController@store');
					Route::get('{id}', 'CompanyController@show');
					Route::get('{id}/edit', 'CompanyController@edit');
					Route::put('{id}', 'CompanyController@update');
					Route::get('{id}/delete', 'CompanyController@destroy');
					Route::post('delete', 'CompanyController@destroy');
				});
				
				// Resumes
				Route::group(['prefix' => 'resumes'], function ($router) {
					Route::get('/', 'ResumeController@index');
					Route::get('create', 'ResumeController@create');
					Route::post('/', 'ResumeController@store');
					Route::get('{id}', 'ResumeController@show');
					Route::get('{id}/edit', 'ResumeController@edit');
					Route::put('{id}', 'ResumeController@update');
					Route::get('{id}/delete', 'ResumeController@destroy');
					Route::post('delete', 'ResumeController@destroy');
				});
				
				// Posts
				Route::get('saved-search', 'PostsController@getSavedSearch');
				$router->pattern('pagePath', '(my-posts|archived|favourite|pending-approval|saved-search)+');
				Route::get('{pagePath}', 'PostsController@getPage');
				Route::get('my-posts/{id}/offline', 'PostsController@getMyPosts');
				Route::get('archived/{id}/repost', 'PostsController@getArchivedPosts');
				Route::get('{pagePath}/{id}/delete', 'PostsController@destroy');
				Route::post('{pagePath}/delete', 'PostsController@destroy');
				
				// Messenger
				Route::group(['prefix' => 'messages'], function ($router) {
					$router->pattern('id', '[0-9]+');
					Route::post('check-new', 'MessagesController@checkNew');
					Route::get('/', 'MessagesController@index');
					// Route::get('create', 'MessagesController@create');
					Route::post('/', 'MessagesController@store');
					Route::get('{id}', 'MessagesController@show');
					Route::put('{id}', 'MessagesController@update');
					Route::get('{id}/actions', 'MessagesController@actions');
					Route::post('actions', 'MessagesController@actions');
					Route::post('mark-as-read', 'MessagesController@markAllAsRead');
				});
				
				// Transactions
				Route::get('transactions', 'TransactionsController@index');
			});
		});
		
		
		// AJAX
		Route::group(['prefix' => 'ajax'], function ($router) {
			Route::get('countries/{countryCode}/admins/{adminType}', 'Ajax\LocationController@getAdmins');
			Route::get('countries/{countryCode}/admins/{adminType}/{adminCode}/cities', 'Ajax\LocationController@getCities');
			Route::get('countries/{countryCode}/cities/{id}', 'Ajax\LocationController@getSelectedCity');
			Route::post('countries/{countryCode}/cities/autocomplete', 'Ajax\LocationController@searchedCities');
			Route::post('countries/{countryCode}/admin1/cities', 'Ajax\LocationController@getAdmin1WithCities');
			Route::post('category/select-category', 'Ajax\CategoryController@getCategoriesHtml');
			Route::post('save/post', 'Ajax\PostController@savePost');
			Route::post('save/search', 'Ajax\PostController@saveSearch');
			Route::post('post/phone', 'Ajax\PostController@getPhone');
		});
		
		
		// FEEDS
		Route::feeds();
		
		
		// SITEMAPS (XML)
		Route::get('{countryCode}/sitemaps.xml', 'SitemapsController@site');
		Route::get('{countryCode}/sitemaps/pages.xml', 'SitemapsController@pages');
		Route::get('{countryCode}/sitemaps/categories.xml', 'SitemapsController@categories');
		Route::get('{countryCode}/sitemaps/cities.xml', 'SitemapsController@cities');
		Route::get('{countryCode}/sitemaps/posts.xml', 'SitemapsController@posts');
		
		
		// PAGES
		Route::get(dynamicRoute('routes.pricing'), 'PageController@pricing');
		Route::get(dynamicRoute('routes.pageBySlug'), 'PageController@cms');
		Route::get(dynamicRoute('routes.contact'), 'PageController@contact');
		Route::post(dynamicRoute('routes.contact'), 'PageController@contactPost');
		
		
		// SITEMAP (HTML)
		Route::get(dynamicRoute('routes.sitemap'), 'SitemapController@index');
		
		
		// SEARCH
		Route::group(['namespace' => 'Search'], function ($router) {
			$router->pattern('id', '[0-9]+');
			$router->pattern('username', '[a-zA-Z0-9]+');
			Route::get(dynamicRoute('routes.companies'), 'CompanyController@index');
			Route::get(dynamicRoute('routes.search'), 'SearchController@index');
			Route::get(dynamicRoute('routes.searchPostsByUserId'), 'UserController@index');
			Route::get(dynamicRoute('routes.searchPostsByUsername'), 'UserController@profile');
			Route::get(dynamicRoute('routes.searchPostsByCompanyId'), 'CompanyController@profile');
			Route::get(dynamicRoute('routes.searchPostsByTag'), 'TagController@index');
			Route::get(dynamicRoute('routes.searchPostsByCity'), 'CityController@index');
			Route::get(dynamicRoute('routes.searchPostsBySubCat'), 'CategoryController@index');
			Route::get(dynamicRoute('routes.searchPostsByCat'), 'CategoryController@index');
		});
	});
});
