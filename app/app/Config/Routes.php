<?php

namespace Config;

$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

$routes->get('testroute', 'TestRoute::index');
// Frontend Routes
$routes->get('/', 'Home::index');
$routes->get('/paket-makeup', 'Home::paketMakeup');
$routes->get('/sewa-kostum', 'Home::sewaKostum');
$routes->get('sewa-kostum/(:segment)', 'Home::detailKostum/$1');
$routes->get('/lokasi', 'Home::lokasi');
$routes->post('/kirim-pesan', 'Home::kirimPesan');
$routes->get('mitra', 'Home::mitra');
$routes->get('mitra/(:segment)', 'Home::detailMitra/$1');
$routes->get('gallery', 'Home::gallery');
$routes->get('gallery/(:num)', 'Home::detailGallery/$1');


// Admin Routes
$routes->get('login', 'Auth::login');
$routes->post('login/proses', 'Auth::prosesLogin');
$routes->get('register', 'Auth::register');
$routes->post('register/proses', 'Auth::prosesRegister');
$routes->get('logout', 'Auth::logout');

$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('dashboard/chart-data/(:num)', 'Admin\Dashboard::getChartData/$1');
    
    
    $routes->get('paket', 'Admin\Paket::index');
    $routes->get('paket/tambah', 'Admin\Paket::tambah');
    $routes->post('paket/simpan', 'Admin\Paket::simpan');
    $routes->get('paket/edit/(:num)', 'Admin\Paket::edit/$1');
    $routes->post('paket/update/(:num)', 'Admin\Paket::update/$1');
    $routes->get('paket/hapus/(:num)', 'Admin\Paket::hapus/$1');
    $routes->get('paket/toggle-status/(:num)', 'Admin\Paket::toggleStatus/$1');
    $routes->get('paket/toggle-featured/(:num)', 'Admin\Paket::toggleFeatured/$1');
    $routes->post('paket/bulk-action', 'Admin\Paket::bulkAction');
    
    $routes->get('kostum', 'Admin\Kostum::index');
    $routes->get('kostum/tambah', 'Admin\Kostum::tambah');
    $routes->post('kostum/simpan', 'Admin\Kostum::simpan');
    $routes->get('kostum/view/(:num)', 'Admin\Kostum::view/$1');
    $routes->get('kostum/edit/(:num)', 'Admin\Kostum::edit/$1');
    $routes->post('kostum/update/(:num)', 'Admin\Kostum::update/$1');
    $routes->get('kostum/hapus/(:num)', 'Admin\Kostum::hapus/$1');
    $routes->get('kostum/toggle-status/(:num)', 'Admin\Kostum::toggleStatus/$1');
    $routes->get('kostum/toggle-featured/(:num)', 'Admin\Kostum::toggleFeatured/$1');
    $routes->post('kostum/bulk-action', 'Admin\Kostum::bulkAction');
    $routes->get('kostum/export', 'Admin\Kostum::export');
    $routes->get('kostum/import', 'Admin\Kostum::import');
    $routes->post('kostum/process-import', 'Admin\Kostum::processImport');

    $routes->get('kostum-ajax', 'Admin\KostumAjax::index');
    $routes->post('kostum-ajax/getKostum', 'Admin\KostumAjax::getKostum');
    $routes->get('kostum-ajax/getKostumDetail/(:num)', 'Admin\KostumAjax::getKostumDetail/$1');
    $routes->post('kostum-ajax/save', 'Admin\KostumAjax::save');
    $routes->post('kostum-ajax/update/(:num)', 'Admin\KostumAjax::update/$1');
    $routes->post('kostum-ajax/delete/(:num)', 'Admin\KostumAjax::delete/$1');
    $routes->post('kostum-ajax/toggleStatus/(:num)', 'Admin\KostumAjax::toggleStatus/$1');
    $routes->post('kostum-ajax/toggleFeatured/(:num)', 'Admin\KostumAjax::toggleFeatured/$1');
    $routes->post('kostum-ajax/bulkAction', 'Admin\KostumAjax::bulkAction');
    
    $routes->get('pesanan', 'Admin\Pesanan::index');
    $routes->get('pesanan/detail/(:num)', 'Admin\Pesanan::detail/$1');
    $routes->get('pesanan/ubah-status/(:num)/(:any)', 'Admin\Pesanan::ubahStatus/$1/$2');
    
    $routes->get('pengaturan', 'Admin\Pengaturan::index');
    $routes->post('pengaturan/simpan', 'Admin\Pengaturan::simpan');
    
    $routes->get('area', 'Admin\Area::index');
    $routes->get('area/tambah', 'Admin\Area::tambah');
    $routes->post('area/simpan', 'Admin\Area::simpan');
    $routes->get('area/edit/(:num)', 'Admin\Area::edit/$1');
    $routes->post('area/update/(:num)', 'Admin\Area::update/$1');
    $routes->get('area/hapus/(:num)', 'Admin\Area::hapus/$1');
    $routes->get('area/toggle-status/(:num)', 'Admin\Area::toggleStatus/$1');

    $routes->get('mitra', 'Admin\Mitra::index');
    $routes->get('mitra/tambah', 'Admin\Mitra::tambah');
    $routes->post('mitra/simpan', 'Admin\Mitra::simpan');
    $routes->get('mitra/edit/(:num)', 'Admin\Mitra::edit/$1');
    $routes->post('mitra/update/(:num)', 'Admin\Mitra::update/$1');
    $routes->get('mitra/hapus/(:num)', 'Admin\Mitra::hapus/$1');
    $routes->get('mitra/toggle-status/(:num)', 'Admin\Mitra::toggleStatus/$1');
    $routes->get('mitra/toggle-featured/(:num)', 'Admin\Mitra::toggleFeatured/$1');

    $routes->get('gallery', 'Admin\Gallery::index');
    $routes->get('gallery/tambah', 'Admin\Gallery::tambah');
    $routes->post('gallery/simpan', 'Admin\Gallery::simpan');
    $routes->get('gallery/edit/(:num)', 'Admin\Gallery::edit/$1');
    $routes->post('gallery/update/(:num)', 'Admin\Gallery::update/$1');
    $routes->get('gallery/hapus/(:num)', 'Admin\Gallery::hapus/$1');
    $routes->get('gallery/toggle-status/(:num)', 'Admin\Gallery::toggleStatus/$1');
    $routes->get('gallery/toggle-featured/(:num)', 'Admin\Gallery::toggleFeatured/$1');
});

// Auth Routes
$routes->get('login', 'Auth::login');
$routes->post('login/proses', 'Auth::prosesLogin');
$routes->get('logout', 'Auth::logout');