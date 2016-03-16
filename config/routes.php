<?php
/**
 * Created by PhpStorm.
 * User: michell
 * Date: 16/03/16
 * Time: 00:49
 */

$app = new \Slim\App;

$app->post('/users', 'UserController::createUser');
$app->post('/users/{userid}/urls', 'UrlController::createUrlByUser');

$app->get('/stats', 'UserController::getAllUsers');
$app->get('/stats/{id}', 'UrlController::getUrlById');

$app->get('/urls/{id}', 'UrlController::urlRedirectById');

$app->get('/users/{userId}/stats', 'UserController::getStatsByUserId');


$app->get('/{shorUrl}', 'UrlController::redirectByHash');



$app->run();