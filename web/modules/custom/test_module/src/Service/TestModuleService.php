<?php
namespace Drupal\test_module\Service;

use Drupal\Core\Database\Connection;

class TestModuleService {

  protected $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public function getData() {
    $query = $this->database->select('test_module_data', 'd');
    $query->fields('d');
    return $query->execute()->fetchAll();
  }

  public function getRecordById($id) {
    $query = $this->database->select('test_module_data', 'd')
      ->fields('d')
      ->condition('id', $id) // Suponiendo que hay una columna 'id' en la tabla.
      ->range(0, 1); // Solo queremos un registro.

    return $query->execute()->fetchObject();
  }
}
