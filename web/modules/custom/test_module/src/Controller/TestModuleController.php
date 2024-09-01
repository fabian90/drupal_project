<?php
namespace Drupal\test_module\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\test_module\Service\TestModuleService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class TestModuleController extends ControllerBase {

  protected $testModuleService;

  public function __construct(TestModuleService $testModuleService) {
    $this->testModuleService = $testModuleService;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('test_module.test_module_service') // Asegúrate de que el servicio se registre correctamente.
    );
  }

    /**
   * Redirige a la página de creación de registro.
   */

  public function listData() {
    // Obtén los datos para mostrar en la tabla.
    $data = $this->testModuleService->getData();

    return [
      '#theme' => 'test_module_data', // Usa un tema específico para los datos.
      '#data' => $data,
      '#form' => $this->formBuilder()->getForm('Drupal\test_module\Form\TestModuleForm'), // Asumiendo que tienes un formulario definido.
    ];
  }
  /**
   * Muestra la página de datos.
   */
  public function dataPage() {
    // Obtiene los datos utilizando el servicio.
    $data = $this->testModuleService->getData();

    // Mensaje si no hay datos.
    if (empty($data)) {
      \Drupal::messenger()->addWarning($this->t('No hay datos registrados.'));
    }
   else {
    // Obtiene el nombre de los términos de vocabulario.
    foreach ($data as &$record) {
        // Asumiendo que tienes un campo para el ID del término en tu registro.
        $record->tipo_documento_nombre = $this->getTermName($record->tipo_documento);
        $record->pais_nombre = $this->getTermName($record->pais);
    }
}

  // Devuelve el render array con la biblioteca adjunta.
      return [
        '#theme' => 'test_module_data',
        '#data' => $data,
        '#attached' => [
            'library' => [
                'test_module/global-styling',
            ],
        ],
    ];
  }

  /**
   * Retrieves the name of a taxonomy term by its ID.
   */
  private function getTermName($tid) {
    if ($term = \Drupal\taxonomy\Entity\Term::load($tid)) {
      return $term->getName();
    }
    return $this->t('Desconocido');
  }

  // public function dataPage() {
  //   return [
  //     '#markup' => $this->t('Esta es la página de datos.'),
  //   ];
  // }

  public function apiData() {
    try {
        // Obtén los datos de la base de datos
        $data = $this->testModuleService->getData();

        // Verifica si se obtuvieron datos
        if (empty($data)) {
            return new JsonResponse(['message' => 'No se encontraron registros.'], Response::HTTP_NOT_FOUND);
        }

        // Formatea los datos para una respuesta JSON
        $response_data = [];
        foreach ($data as $record) {
            // Asegúrate de que cada registro contenga los campos esperados
            $response_data[] = [
                'apellido' => isset($record->apellido) ? $record->apellido : null,
                'nombre' => isset($record->nombre) ? $record->nombre : null,
                'tipo_documento' => isset($record->tipo_documento) ? $this->getTermName($record->tipo_documento) : null,
                'numero_documento' => isset($record->numero_documento) ? $record->numero_documento : null,
                'correo' => isset($record->correo) ? $record->correo : null,
                'telefono' => isset($record->telefono) ? $record->telefono : null,
                'pais' => isset($record->pais) ? $this->getTermName($record->pais) : null,
            ];
        }

        // Devuelve la respuesta JSON
        return new JsonResponse($response_data, Response::HTTP_OK);
    } catch (\Exception $e) {
        // Manejo de errores: devuelve un mensaje de error JSON
        return new JsonResponse(['error' => 'Ocurrió un error al procesar la solicitud. ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

}

