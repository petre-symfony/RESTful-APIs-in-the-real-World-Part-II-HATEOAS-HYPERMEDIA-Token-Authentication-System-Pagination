<?php

namespace KnpU\CodeBattle\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class Battle {
  /* All public properties are persisted */
  /**
   * @Serializer\Expose()
   */
  public $id;

  /**
   * @var Programmer
   */
  public $programmer;

  /**
   * @var Project
   */
  public $project;

  /**
   * @Serializer\Expose()
   */
  public $didProgrammerWin;

  /**
   * @Serializer\Expose()
   */
  public $foughtAt;

  /**
   * @Serializer\Expose()
   */
  public $notes;
}
