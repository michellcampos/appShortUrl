<?php

require __DIR__.'/../model/UserModel.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UserController
{


    public function userModel()
    {
        $model = new UserModel();
        return $model;
    }

    public function getAllUsers(Request $request, Response $response)
    {
        $data = array();
        $model = self::userModel();
        foreach ($model->getAllUsers() as $key => $value) {
            $data['hits'] += $value['hits'];
            $data['urlCount'] += 1;
            if($data['urlCount'] <= 10) {
                $data['topUrls'][$key]['id'] = $value['url_id'];
                $data['topUrls'][$key]['hits'] = $value['hits'];
                $data['topUrls'][$key]['url'] = $value['url'];
                $data['topUrls'][$key]['shortUrl'] = "http://{$_SERVER['HTTP_HOST']}/{$value['shortUrl']}";
            }
        }

        return $response->withJson($data)->withStatus(200);
    }

    public function createUser(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        $name = filter_var(trim($post['name']), FILTER_SANITIZE_STRING);
        if(!empty($name)) {
            $model = self::userModel();
            $dataObject = $model->getUserByName($name);
            $data['id'] = $dataObject->name;
            if(empty($dataObject)) {
                $model->createUser($name);
                $httpStatusCode = 201;
                $data['id'] = $name;
            } else {
                $httpStatusCode = 409;
            }

        } else {
            $httpStatusCode = 411;
            $data['message'] = '411 Length Required';
        }
        return $response->withJson($data)->withStatus($httpStatusCode);
    }

    public function getStatsByUserId(Request $request, Response $response)
    {
        $userId = $request->getAttribute('userId');

        $model = self::userModel();
        $obj = $model->getUrlsFromUserId($userId);

        $data = array();

        if(!empty($obj)) {

            foreach ($obj as $key => $value) {
                $data[$key]['id'] = $value['url_id'];
                $data[$key]['hits'] = $value['hits'];
                $data[$key]['url'] = $value['url'];
                $data[$key]['shortUrl'] = "http://{$_SERVER['HTTP_HOST']}/{$value['shortUrl']}";
            }
            $httpStatusCode = 200;

        } else {
            $httpStatusCode = 404;
            $data['message'] = 'user not found';
        }

        $db = null;

        return $response->withJson($data)->withStatus($httpStatusCode);
    }
}