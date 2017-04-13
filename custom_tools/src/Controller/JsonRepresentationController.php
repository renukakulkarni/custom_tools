<?php

namespace Drupal\custom_tools\Controller;

use Symfony\Component\Serializer\SerializerInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Response;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @file
 * Contains \Drupal\custom_tools\Controller\JSONViewAccess.
 */
class JsonRepresentationController extends ControllerBase {

  /**
   * Serialization variable.
   *
   * @var Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * The serialization class to use.
   *
   * @param Symfony\Component\Serializer\SerializerInterface $serializer
   *   The serializer service.
   */
  public function __construct(SerializerInterface $serializer) {
    $this->serializer = $serializer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('serializer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function jsonViewAccess($site_api_key, NodeInterface $node) {
    $system_siteapikey = $this->config('system.site')->get('siteapikey');
    if (!empty($node)) {
      if ($site_api_key === $system_siteapikey && ($node->getType() === 'page')) {
        $data = $this->serializer->serialize($node, 'json', ['plugin_id' => 'entity']);
        return new Response($data);
      }
      else {
        throw new AccessDeniedHttpException();
      }
    }
    else {
      throw new AccessDeniedHttpException();
    }
  }

}
