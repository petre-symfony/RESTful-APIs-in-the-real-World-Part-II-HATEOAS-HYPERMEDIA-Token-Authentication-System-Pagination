<?php
namespace KnpU\CodeBattle\Controller\Api;

use KnpU\CodeBattle\Controller\BaseController;
use KnpU\CodeBattle\Security\Token\ApiToken;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

class BattleController extends BaseController{
  protected function addRoutes(\Silex\ControllerCollection $controllers) {
    $controllers->post('/api/battles', array($this, 'newAction'));
  }
  
  public function newAction(Request $request){
    $this->enforceUserSecurity();
    
    $data = $this->decodeRequestBodyIntoParameters($request);
    
    $projectId = $data->get('projectId'); 
    $programmerId = $data->get('programmerId');
    
    $project = $this->getProjectRepository()->find($projectId);
    $programmer = $this->getProgrammerRepository()->find($programmerId);
    
    return $this->createApiResponse($token, 201);
  }
}
