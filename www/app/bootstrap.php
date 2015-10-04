<?php



// Step 1: Load Nette Framework
// this allows load Nette Framework classes automatically so that
// you don't have to litter your code with 'require' statements
// require LIBS_DIR . '/Nette/loader.php';
require LIBS_DIR . '/Nette/loader.php';

// Step 2: Configure environment
// 2a) enable Debug for better exception and error visualisation
// Debug::enable();

// 2b) load configuration from config.ini file
Environment::loadConfig();



// Step 3: Configure application
$application = Environment::getApplication();

dibi::connect((array)Environment::getConfig('database'));


// Step 4: Setup application router
$router = $application->getRouter();

// mod_rewrite detection
//if (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) {

  Route::setStyleProperty('action', Route::FILTER_TABLE, array(
        'nejctenejsich' => 'mostread',
        'nejnovejsich' => 'newest',
        'editovat' => 'work',
        'praci' => 'works',
        'autoru' => 'authors',
        'hledat' => 'search',
        'autora' => 'author',

  ));
  Route::setStyleProperty('presenter', Route::FILTER_TABLE, array(
        'seznam' => 'Default',
        'autor' => 'Author',
        'prace' => 'Work',
        'pridat' => 'Editor'
  ));

  $router[] = new Route('admin/<presenter>-<action>', array(
      'module' => 'Admin',
      'presenter' => 'Default',
      'action' => 'default'
  ));

  $router[] = new Route('<presenter autor|prace>/<url>', array(
      'module' => 'Front',
      'action' => 'default'
  ));

	$router[] = new Route('<presenter>-<action>[/maturita/<class>][/rok/<year>][/cena/<award>][/typ/<type>][/rocnik/<grade>][/kategorie/<category>]', array(
      'module' => 'Front',
      'presenter' => 'Default',
      'action' => 'default'
  ));



//}
	//$router[] = new SimpleRouter('Front:Default:default');




// Step 5: Run the application!
$application->run();

