<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('HomeController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override(function() {
	return view('404');
});
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.



$routes->get('/', 'HomeController::index');
$routes->get('/index', 'HomeController::index');
$routes->get('/species/(:segment)', 'SpeciesportalController::index/$1');
$routes->get('/download_species_gene_list/(:segment)/(:segment)', 'SpeciesportalController::download_species_csv/$1/$2');

$routes->get('/grasstfdb', 'GrassdbController::index/TF');
$routes->get('/grasscoregdb', 'GrassdbController::index/Coreg');


$routes->get('/tfomecollection.php', 'TfomecollectionController::index/Maize'); //support old links on abrc.osu.edu
$routes->get('/tfomecollection', 'TfomecollectionController::index/Maize');
$routes->get('/tfomecollection/datatable', 'TfomecollectionController::crop_datatable/Maize');

$routes->get('/RiceTfome', 'TfomecollectionController::index/Rice');
$routes->get('/rice_tfome/datatable', 'TfomecollectionController::crop_datatable/Rice');

// tfome information page
$routes->get('/tfomeinfor/(:segment)', 'TfomeinforController::index/$1');

// support old links to tfome information page e.g. http://grassius.org/tfomeinfor.php?clone=pUT1103
$routes->get('/tfomeinfor.php', 'TfomeinforController::legacy_endpoint');

$routes->get('/regcollection/filtered_datatable/(:segment)', 'Regnet\RegnetController::filtered_datatable/$1');
$routes->get('/regnet/get_vis_json/(:segment)/(:segment)', 'Regnet\RegnetController::get_vis_json/$1/$2');
$routes->get('/regnet/autocomplete', 'Regnet\RegnetController::autocomplete');
$routes->get('/pdinetwork', 'Regnet\RegnetController::index');

$routes->get('/pdicollection', 'PdicollectionController::pdicollection_page');
$routes->get('/pdicollection/datatable', 'PdicollectionController::default_datatable');
$routes->get('/pdicollection/filtered_datatable/(:segment)', 'PdicollectionController::filtered_datatable/$1');

$routes->get('/pdicollection/download_table/(:segment)', 'PdicollectionController::download_table/$1');
$routes->get('/pdicollection/filtered_histogram/(:segment)', 'PdicollectionController::filtered_histogram/$1' );
$routes->get('/pdicollection/autocomplete/(:segment)', 'PdicollectionController::autocomplete/$1' );

$routes->get('/pdicollection/get_vis_json/(:segment)', 'PdicollectionController::get_vis_json/$1' );



$routes->get('/browsefamily/(:segment)/(:segment)', 'BrowsefamilyController::index/$1/$2');

$routes->get('/transcripts', 'TranscriptsController::index');
$routes->get('/transcripts_datatable', 'TranscriptsController::datatable');


$routes->get('/customfamily_autocomplete', 'CustomfamilyController::customfamily_autocomplete');
$routes->get('/customfamily/Maize', 'CustomfamilyController::index/Maize');
$routes->get('/customfamily_datatable/Maize/(:segment)/(:segment)', 'CustomfamilyController::customfamily_datatable/Maize/$1/$2');


$routes->get('/family/(:segment)/(:any)', 'FamilyController::index/$1/$2');
$routes->get('/family_datatable/(:segment)/(:any)', 'FamilyController::family_datatable/$1/$2');
$routes->get('/family_datatable_debug/(:segment)/(:any)', 'FamilyController::family_datatable_debug/$1/$2');
$routes->get('/download_family_gene_list/(:segment)/(:segment)/(:segment)', 
             'FamilyController::download_family_csv/$1/$2/$3');
$routes->get('/download_family_gene_list/(:segment)/(:segment)/(:segment)/(:segment)', 
             'FamilyController::download_family_csv/$1/$2/$3/$4');

// download links for fasta files given /species/version
$routes->get('/download_sequences_csv/(:segment)/(:segment)', 
             'FastaDownloadController::download_seq_fasta/1/$1/$2');
$routes->get('/download_sequences_fasta/(:segment)/(:segment)', 
             'FastaDownloadController::download_seq_fasta/0/$1/$2');



// download links for fasta files given /species/version/class/family
$routes->get('/download_sequences_csv/(:segment)/(:segment)/(:segment)/(:segment)', 
             'FastaDownloadController::download_seq_fasta/1/$1/$2/$3/$4');
$routes->get('/download_sequences_csv/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 
             'FastaDownloadController::download_seq_fasta/1/$1/$2/$3/$4/$5');
$routes->get('/download_sequences_fasta/(:segment)/(:segment)/(:segment)/(:segment)', 
             'FastaDownloadController::download_seq_fasta/0/$1/$2/$3/$4');
$routes->get('/download_sequences_fasta/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 
             'FastaDownloadController::download_seq_fasta/0/$1/$2/$3/$4/$5');



// support front-end interaction tables on proteininfor page
$routes->get('/proteininfor/datatable_filter_by_regulator/(:segment)','ProteininforController::datatable_filter_by_regulator/$1');
$routes->get('/proteininfor/datatable_filter_by_target/(:segment)','ProteininforController::datatable_filter_by_target/$1');

// download interaction tables on proteininfor page
$routes->get('/proteininfor/download_table_filter_by_regulator/(:segment)','ProteininforController::download_table_filter_by_regulator/$1');
$routes->get('/proteininfor/download_table_filter_by_target/(:segment)','ProteininforController::download_table_filter_by_target/$1');

// serve proteininfor page
$routes->get('/proteininfor/(:segment)/(:segment)', 'ProteininforController::proteininfor_page/$1/$2');

// get and set certain session variables
$routes->get('/set_session_var/(:segment)/(:segment)', 'BaseController::set_session_var/$1/$2');
$routes->get('/get_session_var/(:segment)', 'BaseController::get_session_var/$1');


// main search bar at the top of the screen
$routes->get('/search/(:any)', 'Topsearch\TopsearchController::search/$1');

// traditional search on coregdb or tfdb
$routes->get('/autocomplete/(:segment)/(:any)', 'SearchController::autocomplete/$1/$2');
$routes->get('/search_results/(:any)', 'SearchController::search_results/$1');

// tools
$routes->get('/translation_tool', 'TranslationToolController::translation_tool');
$routes->post('/translation_tool/translation', 'TranslationToolController::submit');

// simple pages
$routes->get('/downloads', 'InfoController::downloads');
$routes->get('/people', 'InfoController::people');
$routes->get('/links', 'InfoController::links');
$routes->get('/info', 'InfoController::info');
$routes->get('/about', 'InfoController::about');
$routes->get('/tutorial', 'InfoController::tutorial');
$routes->get('/contact', 'InfoController::contact');


// admin pages
$routes->get('/login', 'Admin\AdminController::login');
$routes->post('/attempt_login', 'Admin\AdminController::attempt_login');

$routes->get('/family_admin', 'Admin\FamilyCrudController::index');
$routes->get('/family_admin/datatable', 'Admin\FamilyCrudController::datatable');
$routes->get('/family_admin/edit/(:segment)', 'Admin\FamilyCrudController::edit/$1');

$routes->get('/gene_admin', 'Admin\GeneCrudController::index');
$routes->get('/gene_admin/datatable', 'Admin\GeneCrudController::datatable');
$routes->get('/gene_admin/edit/(:segment)', 'Admin\GeneCrudController::edit/$1');

$routes->get('/clone_admin', 'Admin\CloneCrudController::index');
$routes->get('/clone_admin/datatable', 'Admin\CloneCrudController::datatable');
$routes->get('/clone_admin/edit/(:segment)', 'Admin\CloneCrudController::edit/$1');

$routes->get('/logout', 'Admin\AdminController::logout');

$routes->get('/edit_family/(:any)', 'Admin\AdminController::edit_family/$1/$2');
$routes->post('/post_edit_family/(:any)', 'Admin\AdminController::post_edit_family/$1/$2');

// dev pages
//$routes->get('/test', 'TestController::test');
$routes->get('/get_json', 'TestController::get_json');
$routes->get('/get_edges', 'TestController::get_edges');


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
