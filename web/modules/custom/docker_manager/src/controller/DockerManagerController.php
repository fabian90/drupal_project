<?php
namespace Drupal\docker_manager\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DockerManagerController {

  public function getContainers() {
    $output = shell_exec('docker ps -a --format "{{.ID}} {{.Names}} {{.Status}}"');
    $containers = explode("\n", trim($output));
    $data = [];
    foreach ($containers as $container) {
      list($id, $name, $status) = explode(" ", $container, 3);
      $data[] = ['id' => $id, 'name' => $name, 'status' => $status];
    }
    return new JsonResponse($data);
  }

  public function startContainer($id) {
    $output = shell_exec("docker start $id");
    return new JsonResponse(['status' => $output ? 'started' : 'failed']);
  }

  public function stopContainer($id) {
    $output = shell_exec("docker stop $id");
    return new JsonResponse(['status' => $output ? 'stopped' : 'failed']);
  }

  public function removeContainer($id) {
    $output = shell_exec("docker rm $id");
    return new JsonResponse(['status' => $output ? 'removed' : 'failed']);
  }

  public function getContainerLogs($id) {
    $output = shell_exec("docker logs $id");
    return new JsonResponse(['logs' => $output]);
  }
}
