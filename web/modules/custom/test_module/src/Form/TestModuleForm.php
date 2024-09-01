<?php
namespace Drupal\test_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class TestModuleForm extends FormBase {

  public function getFormId() {
    return 'test_module_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['apellido'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Apellido'),
      '#required' => TRUE,
    ];

    $form['nombre'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre'),
      '#required' => TRUE,
    ];

    $form['tipo_documento'] = [
      '#type' => 'select',
      '#title' => $this->t('Tipo de Documento'),
      '#options' => $this->getTaxonomyOptions('tipo_documento'),
      '#required' => TRUE,
    ];

    $form['numero_documento'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Número de Documento'),
      '#required' => TRUE,
    ];

    $form['correo'] = [
      '#type' => 'email',
      '#title' => $this->t('Correo Electrónico'),
      '#required' => TRUE,
    ];

    $form['telefono'] = [
      '#type' => 'tel',
      '#title' => $this->t('Teléfono'),
      '#required' => TRUE,
    ];

    $form['pais'] = [
      '#type' => 'select',
      '#title' => $this->t('País'),
      '#options' => $this->getTaxonomyOptions('pais'),
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Enviar'),
    ];

    $form['actions']['back'] = [
      '#type' => 'button',
      '#value' => $this->t('Regresar a Datos Formulario'),
      '#attributes' => [
          'onclick' => 'window.location.href="' . Url::fromRoute('test_module.data_page')->toString() . '";',
      ],
  ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validación para el campo apellido
    if (strlen($form_state->getValue('apellido')) < 2) {
      $form_state->setErrorByName('apellido', $this->t('El apellido debe tener al menos 2 caracteres.'));
    }

    // Validación para el campo nombre
    if (strlen($form_state->getValue('nombre')) < 2) {
      $form_state->setErrorByName('nombre', $this->t('El nombre debe tener al menos 2 caracteres.'));
    }

    // Validación para el campo número de documento
    if (!is_numeric($form_state->getValue('numero_documento'))) {
      $form_state->setErrorByName('numero_documento', $this->t('El número de documento debe ser un valor numérico.'));
    }

    // Validación para el campo correo electrónico
    if (!filter_var($form_state->getValue('correo'), FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('correo', $this->t('Debe ingresar un correo electrónico válido.'));
    }

    // Validación para el campo teléfono
    if (!preg_match('/^\d{7,10}$/', $form_state->getValue('telefono'))) {
      $form_state->setErrorByName('telefono', $this->t('El teléfono debe contener entre 7 y 10 dígitos.'));
    }

    // Validación para que el número de documento sea único
    $existing = \Drupal::database()->select('test_module_data', 't')
      ->fields('t', ['id'])
      ->condition('numero_documento', $form_state->getValue('numero_documento'))
      ->execute()
      ->fetchField();

    if ($existing) {
      $form_state->setErrorByName('numero_documento', $this->t('El número de documento ya está registrado.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::database()->insert('test_module_data')
      ->fields([
        'apellido' => $form_state->getValue('apellido'),
        'nombre' => $form_state->getValue('nombre'),
        'tipo_documento' => $form_state->getValue('tipo_documento'),
        'numero_documento' => $form_state->getValue('numero_documento'),
        'correo' => $form_state->getValue('correo'),
        'telefono' => $form_state->getValue('telefono'),
        'pais' => $form_state->getValue('pais'),
      ])
      ->execute();

    $this->messenger()->addMessage($this->t('Datos guardados correctamente.'));
     // Redirigir de vuelta al formulario
     $form_state->setRedirect('test_module.form');
  }

  private function getTaxonomyOptions($vocabulary) {
    $options = [];
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vocabulary);
    foreach ($terms as $term) {
      $options[$term->tid] = $term->name;
    }
    return $options;
  }
}
