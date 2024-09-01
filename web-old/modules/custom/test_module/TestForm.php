<?php

namespace Drupal\test_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class TestForm extends FormBase {

  public function getFormId() {
    return 'test_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['lastname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Apellido'),
      '#required' => TRUE,
    ];
    $form['firstname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre'),
      '#required' => TRUE,
    ];
    $form['document_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Tipo de Documento'),
      '#options' => $this->getTaxonomyOptions('document_type'),
      '#required' => TRUE,
    ];
    $form['document_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Numero de documento'),
      '#required' => TRUE,
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Correo electrónico'),
      '#required' => TRUE,
    ];
    $form['phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Teléfono'),
      '#required' => TRUE,
    ];
    $form['country'] = [
      '#type' => 'select',
      '#title' => $this->t('País'),
      '#options' => $this->getTaxonomyOptions('country'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Aquí almacenamos los datos.
    \Drupal::messenger()->addMessage($this->t('Formulario enviado.'));
  }

  private function getTaxonomyOptions($vocabulary) {
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vocabulary);
    $options = [];
    foreach ($terms as $term) {
      $options[$term->tid] = $term->name;
    }
    return $options;
  }
}
