<?php
namespace KnpU\CodeBattle\Model;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *   "self", 
 *    href = @Hateoas\Route(
 *      "api_homepage"
 *    ),
 *    attributes = {"title": "Your API starting point"}
 * )
 * @Hateoas\Relation(
 *   "programmers", 
 *    href = @Hateoas\Route(
 *      "api_programmers_list"
 *    ),
 *    attributes = {"title": "All the programmers in the system"}
 * )
 */
class Homepage {
  private $message = 'Welcome to the CodeBattle API! Weee! For human documentation visit http://api/..';
}
