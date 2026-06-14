<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Auth Routes
$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::login');
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::register');
    $routes->get('verify/(:any)', 'Auth::verify/$1');
    $routes->get('logout', 'Auth::logout');
});

// SuperAdmin Routes
$routes->group('superadmin', function($routes) {
    $routes->get('dashboard', 'SuperAdmin::dashboard');
    $routes->post('createConference', 'SuperAdmin::createConference');
    $routes->get('toggleConference/(:num)', 'SuperAdmin::toggleConference/$1');
    $routes->post('assignAdmin', 'SuperAdmin::assignAdmin');
    $routes->get('removeAdmin/(:num)', 'SuperAdmin::removeAdmin/$1');
    $routes->get('deleteConference/(:num)', 'SuperAdmin::deleteConference/$1');
    $routes->match(['get', 'post'], 'editConference/(:num)', 'SuperAdmin::editConference/$1');
});

// Admin Routes
$routes->group('admin', function($routes) {
    $routes->get('selectConference/(:num)', 'Admin::selectConference/$1');
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('disciplines', 'Admin::disciplines');
    $routes->post('addDiscipline', 'Admin::addDiscipline');
    $routes->get('deleteDiscipline/(:num)', 'Admin::deleteDiscipline/$1');
    $routes->get('criteria', 'Admin::criteria');
    $routes->post('addCriteria', 'Admin::addCriteria');
    $routes->get('deleteCriteria/(:num)', 'Admin::deleteCriteria/$1');
    $routes->post('saveAsTemplate', 'Admin::saveAsTemplate');
    $routes->post('importTemplate', 'Admin::importTemplate');
    $routes->get('deleteTemplate/(:num)', 'Admin::deleteTemplate/$1');
    $routes->post('assignReviewers', 'Admin::assignReviewers');
    $routes->get('rooms', 'Admin::rooms');
    $routes->post('createRoom', 'Admin::createRoom');
    $routes->get('deleteRoom/(:num)', 'Admin::deleteRoom/$1');
    $routes->post('assignCommittee', 'Admin::assignCommittee');
    $routes->get('removeCommittee/(:num)', 'Admin::removeCommittee/$1');
    $routes->post('assignPaper', 'Admin::assignPaper');
    $routes->get('removePaper/(:num)', 'Admin::removePaper/$1');
    $routes->get('payments', 'Admin::payments');
    $routes->get('approvePayment/(:num)', 'Admin::approvePayment/$1');
    $routes->get('rejectPayment/(:num)', 'Admin::rejectPayment/$1');
    $routes->post('setRevisionDeadline', 'Admin::setRevisionDeadline');
    $routes->get('approveRevision/(:num)', 'Admin::approveRevision/$1');
    $routes->post('autoAssignRooms', 'Admin::autoAssignRooms');
    $routes->get('reports', 'Admin::reports');
    $routes->get('pending-reviews', 'Admin::pendingReviews');
});

// Author Routes
$routes->group('author', function($routes) {
    $routes->get('dashboard', 'Author::dashboard');
    $routes->post('submitPaper', 'Author::submitPaper');
    $routes->post('submitRevision', 'Author::submitRevision');
    $routes->get('payment', 'Author::payment');
    $routes->post('payBank', 'Author::payBank');
    $routes->post('payGateway', 'Author::payGateway');
});

// Reviewer Routes
$routes->group('reviewer', function($routes) {
    $routes->get('dashboard', 'Reviewer::dashboard');
    $routes->get('evaluate/(:num)', 'Reviewer::evaluate/$1');
    $routes->post('submitEvaluation/(:num)', 'Reviewer::submitEvaluation/$1');
});

// Committee Routes
$routes->group('committee', function($routes) {
    $routes->get('dashboard', 'Committee::dashboard');
    $routes->get('evaluate/(:num)', 'Committee::evaluate/$1');
    $routes->post('submitEvaluation/(:num)', 'Committee::submitEvaluation/$1');
});
