<?php

namespace Drupal\cloudhooks\Acquia;

/**
 * Interface CloudhookInterface.
 *
 * @package lleber
 */
interface CloudhookInterface {

  /**
   * Value of the post-code-update hook.
   *
   * @var string
   */
  const POST_CODE_UPDATE = 'post-code-update';

  /**
   * Value of the post-code-deploy hook.
   *
   * @var string
   */
  const POST_CODE_DEPLOY = 'post-code-deploy';

  /**
   * Value of the post-db-copy hook.
   *
   * @var string
   */
  const POST_DB_COPY = 'post-db-copy';

  /**
   * Value of the post-files-copy hook.
   *
   * @var string
   */
  const POST_FILES_COPY = 'post-files-copy';

  /**
   * Retrieves the application that is being targeted by this hook.
   *
   * @return string
   *   The application name.
   */
  public function getApplication();

  /**
   * Retrieves the environment that is being targeted by this hook.
   *
   * @return string
   *   The environment name.
   */
  public function getEnvironment();

  /**
   * Retrieves the name of the hook that is being executed.
   *
   * @return string
   */
  public function getHook();
}