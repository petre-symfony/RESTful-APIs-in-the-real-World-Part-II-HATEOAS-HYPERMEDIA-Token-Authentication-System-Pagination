<?php
namespace KnpU\CodeBattle\Controller\Api;

use KnpU\CodeBattle\Controller\BaseController;
use KnpU\CodeBattle\Security\Token\ApiToken;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

class TokenController extends BaseController{
  protected function addRoutes(\Silex\ControllerCollection $controllers) {
    $controllers->post('/api/tokens', array($this, 'newAction'));
  }
  
  public function newAction(Request $request){
    $username = $request->headers->get('PHP_AUTH_USER');
    $user = $this->getUserRepository()->findUserByUsername($username);
    
    $data = json_decode($request->getContent(), true);
    $token = new ApiToken($user->id);
    $token->notes = $data['notes'];
    
    $this->getApiTokenRepository()->save($token);
    
    return $this->createApiResponse($token, 201);
  }
}
