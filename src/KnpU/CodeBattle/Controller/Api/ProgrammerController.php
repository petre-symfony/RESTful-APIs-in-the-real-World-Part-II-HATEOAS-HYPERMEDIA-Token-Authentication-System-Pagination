<?php

namespace KnpU\CodeBattle\Controller\Api;

use KnpU\CodeBattle\Api\ApiProblem;
use KnpU\CodeBattle\Api\ApiProblemException;
use KnpU\CodeBattle\Controller\BaseController;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use KnpU\CodeBattle\Model\Programmer;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProgrammerController extends BaseController {
  protected function addRoutes(ControllerCollection $controllers) {
    $controllers->post('/api/programmers', array($this, 'newAction'));

    $controllers->get('/api/programmers/{nickname}', array($this, 'showAction'))
      ->bind('api_programmers_show');

    $controllers->get('/api/programmers', array($this, 'listAction'));

    // point PUT and PATCH at the same controller
    $controllers->put('/api/programmers/{nickname}', array($this, 'updateAction'));
    // PATCH isn't natively supported, hence the different syntax
    $controllers->match('/api/programmers/{nickname}', array($this, 'updateAction'))
      ->method('PATCH');

    $controllers->delete('/api/programmers/{nickname}', array($this, 'deleteAction'));
  }

  public function newAction(Request $request) {
    if (!$this->getLoggedInUser()){
      throw new AccessDeniedException();  
    }
     
    $programmer = new Programmer();
    $this->handleRequest($request, $programmer);

    if ($errors = $this->validate($programmer)) {
      $this->throwApiProblemValidationException($errors);
    }

    $this->save($programmer);
    
    $response = $this->createApiResponse($programmer, 201);
    $programmerUrl = $this->generateUrl(
      'api_programmers_show',
      ['nickname' => $programmer->nickname]
    );
    $response->headers->set('Location', $programmerUrl);

    return $response;
  }

  public function showAction($nickname) {
    $programmer = $this->getProgrammerRepository()->findOneByNickname($nickname);

    if (!$programmer) {
      $this->throw404('Oh no! This programmer has deserted! We\'ll send a search party!');
    }

    $response = $this->createApiResponse($programmer, 200);

    return $response;
  }

  public function listAction() {
    $programmers = $this->getProgrammerRepository()->findAll();
    $data = array('programmers' => $programmers);
    
    $response = $this->createApiResponse($data, 200);

    return $response;
  }

  public function updateAction($nickname, Request $request) {
    $programmer = $this->getProgrammerRepository()->findOneByNickname($nickname);

    if (!$programmer) {
      $this->throw404('Oh no! This programmer has deserted! We\'ll send a search party!');
    }
    
    if ($programmer->userId != $this->getLoggedInUser()->id){
      throw new AccessDeniedException();
    }

    $this->handleRequest($request, $programmer);

    if ($errors = $this->validate($programmer)) {
      $this->throwApiProblemValidationException($errors);
    }

    $this->save($programmer);

    $response = $this->createApiResponse($programmer, 200);

    return $response;
  }

  public function deleteAction($nickname) {
    $programmer = $this->getProgrammerRepository()->findOneByNickname($nickname);

    if ($programmer) {
      $this->delete($programmer);
    }

    return new Response(null, 204);
  }

  /**
   * Reads data from the Request, updates the Programmer and saves it.
   *
   * @param Request $request
   * @param Programmer $programmer
   */
  private function handleRequest(Request $request, Programmer $programmer) {
    $data = json_decode($request->getContent(), true);
    $isNew = !$programmer->id;

    if ($data === null) {
      $problem = new ApiProblem(
        400,
        ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
      );
      throw new ApiProblemException($problem);
    }

    // determine which properties should be changeable on this request
    $apiProperties = array('avatarNumber', 'tagLine');
    if ($isNew) {
      $apiProperties[] = 'nickname';
    }

    // update the properties
    foreach ($apiProperties as $property) {
      // if a property is missing on PATCH, that's ok - just skip it
      if (!isset($data[$property]) && $request->isMethod('PATCH')) {
        continue;
      }

      $val = isset($data[$property]) ? $data[$property] : null;
      $programmer->$property = $val;
    }

    $programmer->userId = $this->getLoggedInUser()->id;
  }


  private function throwApiProblemValidationException(array $errors) {
    $apiProblem = new ApiProblem(
      400,
      ApiProblem::TYPE_VALIDATION_ERROR
    );
    $apiProblem->set('errors', $errors);

    throw new ApiProblemException($apiProblem);
  }
}
