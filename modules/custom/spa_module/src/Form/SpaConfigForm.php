<?php

/**
 * @file
 * Contains Drupal\spa_module\Form\SpaConfigForm.
 */

namespace Drupal\spa_module\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SpaConfigForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'spa_module.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'spa_module_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('spa_module.settings');

    $form['spa_module_settings'] = [
        '#type' => 'details',
        '#title' => t('Spa Module Configuration'),
        '#open' => TRUE,
    ];
    $form['spa_module_settings']['name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('React App name'),
        '#default_value' => $config->get('spa_module.spa_module_settings'),
        '#description' => $this->t('Enter a React app name you wish to create'),
    ];
    $form['spa_module_settings']['path'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Path'),
        '#field_prefix' => '/',
        '#maxlength' => '255',
      ];
    $form['spa_module_settings']['submit'] = [
        '#type' => 'submit',
        '#button_type' => 'primary',
        '#value' => $this->t('Create a React App'),
        '#submit' => ['::submitForm'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    exec('npm create react-app mynewapp', $return_var);
    if ($return_var !== 0) {
        \Drupal::messenger()->addMessage($return_var);
    } else {
        \Drupal::messenger()->addMessage('The React app was successfully created.');
    }
  }
}