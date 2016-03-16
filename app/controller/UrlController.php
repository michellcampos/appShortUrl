<?php

require __DIR__.'/../model/UrlModel.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UrlController
{

    public function urlModel()
    {
        $model = new UrlModel();
        return $model;
    }

    public function redirectByHash(Request $request, Response $response)
    {
        $shorUrl = $request->getAttribute('shorUrl');
        $model = self::urlModel();
        if(!empty($shorUrl)) {
            $urlData = $model->getUrlByHash($shorUrl);
            $model->updateHitsByUrlId($urlData->url_id);
            return $response->withStatus(301)->withHeader('Location', $urlData->url);
        }
    }

    public function urlRedirectById(Request $request, Response $response)
    {
        $urlId = $request->getAttribute('id');
        $model = self::urlModel();
        $urlObj = $model->getUrlById($urlId);
        if(!empty($urlObj)) {
            $model->updateHitsByUrlId($urlId);
            $response = $response->withStatus(301)->withHeader('Location', $urlObj->url);
        } else {
            $response = $response->withStatus(404);
        }
        $db = null;
        return $response;
    }

    public function getUrlById(Request $request, Response $response)
    {
        $urlId = $request->getAttribute('id');
        $model = self::urlModel();
        $data = $model->getUrlById($urlId);
        $data->shortUrl = "http://{$_SERVER['HTTP_HOST']}/{$data->shortUrl}";
        return $response->withJson($data)->withStatus(200);
    }

    public function createUrlByUser(Request $request, Response $response)
    {
        $userid = $request->getAttribute('userid');
        $post = $request->getParsedBody();
        $shortUrl = substr(strtolower(preg_replace('/[0-9_\/]+/','',base64_encode(sha1($post['url'].$userid.microtime())))),0,8);
        $model = self::urlModel();
        $obj = $model->getUserById($userid);

        if(!empty($obj)) {
            $lastInsertId = $model->createUrl(array(
                'user_id' => $userid,
                'url' => $post['url'],
                'short_url' => $shortUrl
            ));
            $data['id'] = $lastInsertId;
            $data['hits'] = 0;
            $data['url'] = $post['url'];
            $data['shortUrl'] = "http://{$_SERVER['HTTP_HOST']}/{$shortUrl}";
            $httpStatusCode = 201;
        } else {
            $httpStatusCode = 404;
            $data['message'] = 'user not found!';
        }
        return $response->withJson($data)->withStatus($httpStatusCode);
    }
}