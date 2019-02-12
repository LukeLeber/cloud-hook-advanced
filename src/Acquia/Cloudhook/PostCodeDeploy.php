<?php

namespace Drupal\cloudhooks\Acquia\Cloudhook;

class PostCodeDeploy extends CloudhookBase {

  protected $sourceBranch;
  protected $deployedTag;
  protected $repoUrl;
  protected $repoType;

  /**
   * {@inheritdoc}
   */
  protected function detectEnvironment() {
    parent::detectEnvironment();

    global $argv;

    $this->sourceBranch = $argv[3];
    $this->deployedTag = $argv[4];
    $this->repoUrl = $argv[5];
    $this->repoType = $argv[6];
  }

  /**
   * Retrieves the name of the hook that is being executed.
   *
   * @return string
   */
  public function getHook() {
    return static::POST_CODE_DEPLOY;
  }

  /**
   * @return mixed
   */
  public function getSourceBranch() {
    return $this->sourceBranch;
  }

  /**
   * @return mixed
   */
  public function getDeployedTag() {
    return $this->deployedTag;
  }

  /**
   * @return mixed
   */
  public function getRepoUrl() {
    return $this->repoUrl;
  }

  /**
   * @return mixed
   */
  public function getRepoType() {
    return $this->repoType;
  }
}