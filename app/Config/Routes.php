<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('auth', function($routes) {
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');
    $routes->post('verify', 'AuthController::verifyEmail');
    $routes->get('oauth/google/start', 'AuthController::googleStart');
    $routes->get('oauth/google/callback', 'AuthController::googleCallback');
    $routes->get('oauth/linkedin/start', 'AuthController::linkedinStart');
    $routes->get('oauth/linkedin/callback', 'AuthController::linkedinCallback');
    $routes->post('refresh', 'AuthController::refreshToken');
    $routes->post('logout', 'AuthController::logout');
});

$routes->group('profiles', function($routes) {
    $routes->get('me', 'ProfileController::me');
    $routes->put('me', 'ProfileController::update');
    $routes->put('me/opentowork', 'ProfileController::toggleOpenToWork');
    $routes->post('me/avatar', 'ProfileController::uploadAvatar');

    $routes->get('me/experience', 'ExperienceController::list');
    $routes->post('me/experience', 'ExperienceController::add');
    $routes->put('me/experience/(:num)', 'ExperienceController::update/$1');
    $routes->delete('me/experience/(:num)', 'ExperienceController::delete/$1');

    $routes->get('me/achievements', 'AchievementController::list');
    $routes->post('me/achievements', 'AchievementController::add');

    $routes->get('me/portfolio', 'PortfolioController::list');
    $routes->post('me/portfolio', 'PortfolioController::add');

    $routes->put('me/strength/recalculate', 'ProfileController::recalculateStrength');
    $routes->get('(:segment)', 'ProfileController::view/$1');
});

$routes->group('onboarding', function($routes){
    $routes->post('start', 'OnboardingController::start');
    $routes->put('roles', 'OnboardingController::setRoles');
    $routes->put('specializations', 'OnboardingController::setSpecializations');
    $routes->put('chapter', 'OnboardingController::pickChapter');
    $routes->post('cv', 'OnboardingController::uploadCV');
    $routes->put('links', 'OnboardingController::addLinks');
    $routes->post('survey/submissions', 'OnboardingController::submitSurvey');
    $routes->post('complete', 'OnboardingController::complete');
});

$routes->group('', function($routes) {
    $routes->get('policies/bundle', 'PoliciesController::bundle');
    $routes->post('policies/accept', 'PoliciesController::accept');
});


