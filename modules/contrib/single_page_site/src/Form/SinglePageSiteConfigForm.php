<?php

namespace Drupal\single_page_site\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SinglePageSiteConfigForm.
 *
 * @package Drupal\single_page_site\Form
 */
class SinglePageSiteConfigForm extends ConfigFormBase {

  /**
   * The core module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * SinglePageSiteConfigForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory to load/save config.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Core module handler.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler) {
    parent::__construct($config_factory);
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'single_page_settings_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['single_page_site.config', 'system.site'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $settings = $this->config('single_page_site.config');
    $menus = $this->getMenus();

    $form['general-settings'] = [
      '#type' => 'details',
      '#title' => t('General settings'),
      '#open' => TRUE,
    ];

    $form['general-settings']['menu'] = [
      '#title' => t('Menu'),
      '#type' => 'select',
      '#options' => $menus,
      '#default_value' => !empty($settings->get('menu')) ? $settings->get('menu') : NULL,
      '#required' => TRUE,
      '#description' => t('Which menu should be used for the single page navigation?'),
    ];

    $form['general-settings']['menuclass'] = [
      '#title' => t('Menu Class/Id'),
      '#type' => 'textfield',
      '#default_value' => !empty($settings->get('menuclass')) ? $settings->get('menuclass') : NULL,
      '#required' => TRUE,
      '#description' => t('Define the class/id of the menu wrapper. Eg: #main-menu or .main-menu'),
    ];

    $description = $this->t('You need to install the "link attributes module" to use this feature');
    if ($this->moduleHandler->moduleExists('link_attributes')) {
      $description = $this->t('Define the class(es) that menu item(s) should contain in order to be rendered on the single page. Eg: single-page-item. Leave blank if you want to use all the menu items.');
    }

    $form['general-settings']['class'] = [
      '#title' => t('Menu item selector'),
      '#type' => 'textfield',
      '#default_value' => !empty($settings->get('class')) ? $settings->get('class') : NULL,
      '#required' => FALSE,
      '#disabled' => !$this->moduleHandler->moduleExists('link_attributes'),
      '#description' => $description,
    ];

    $form['general-settings']['title'] = [
      '#title' => t('Title'),
      '#type' => 'textfield',
      '#default_value' => !empty($settings->get('title')) ? $settings->get('title') : NULL,
      '#required' => TRUE,
      '#description' => t('Configure the title of the page'),
    ];

    $form['general-settings']['tag'] = [
      '#title' => t('Tag'),
      '#type' => 'textfield',
      '#default_value' => !empty($settings->get('tag')) ? $settings->get('tag') : NULL,
      '#required' => TRUE,
      '#description' => t('Define the HTML tag which should be used for title-wrapping. Eg: h2 or p'),
    ];

    $description = $settings->get('homepage') ? t('Unchecking this option will not change your homepage setting. You will have to re-configure it manually.') :
      t('I will use this page as my homepage');
    $form['general-settings']['homepage'] = [
      '#type' => 'checkbox',
      '#title' => t('Homepage'),
      '#description' => $description,
      '#default_value' => $settings->get('homepage') !== NULL ? $settings->get('homepage') : 1,
    ];

    $form['scroll-settings'] = [
      '#type' => 'details',
      '#title' => t('Scroll settings'),
      '#open' => FALSE,
    ];

    $form['scroll-settings']['scroll-down'] = [
      '#title' => t('Down'),
      '#type' => 'textfield',
      '#default_value' => !empty($settings->get('down')) ? $settings->get('down') : 50,
      '#required' => TRUE,
      '#description' => t('Define the distance between the item and the viewport (px) when a menu item should be highlighted when scrolling down'),
    ];

    $form['scroll-settings']['scroll-up'] = [
      '#title' => t('Up'),
      '#type' => 'textfield',
      '#default_value' => !empty($settings->get('up')) ? $settings->get('up') : 200,
      '#required' => TRUE,
      '#description' => t('Define the distance between the item and the viewport (px) when a menu item should be highlighted when scrolling up'),
    ];

    $form['advanced-settings'] = [
      '#type' => 'details',
      '#title' => t('Advanced settings'),
      '#open' => FALSE,
    ];

    $form['advanced-settings']['smooth-scrolling'] = [
      '#type' => 'checkbox',
      '#title' => t('Use smooth scrolling'),
      '#default_value' => $settings->get('smoothscrolling') !== NULL ? $settings->get('smoothscrolling') : 1,
    ];

    $form['advanced-settings']['update-hash'] = [
      '#type' => 'checkbox',
      '#title' => t('Update url fragment while scrolling'),
      '#default_value' => $settings->get('updatehash') ? $settings->get('updatehash') : 0,
    ];

    $form['advanced-settings']['offset-selector'] = [
      '#title' => t('Which menu should be used for calculating the offset'),
      '#type' => 'textfield',
      '#default_value' => $settings->get('offsetselector') ? $settings->get('offsetselector') : NULL,
      '#description' => t('The height of this selector will be used to determine the scroll to position. Most of the times this is will be the same selector as your "Menu Class/Id"'),
    ];

    $form['advanced-settings']['filter-url-prefix'] = [
      '#title' => t("Filter url prefixes out of anchor ID's"),
      '#type' => 'checkbox',
      '#default_value' => $settings->get('filterurlprefix') ? $settings->get('filterurlprefix') : 0,
      '#description' => t('This removes the language prefix out of the anchor ID. Enable this option if the menu does not work for multilingual pages.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Validate menu class on special chars.
    $menu_class = $form_state->getValue('menuclass');
    if (preg_match('/[^A-Za-z0-9#.-]/', $menu_class)) {
      $form_state->setErrorByName('menuclass',
        t('"Menu Class/Id" contains forbidden chars. Only a-z, #, ., - allowed.'));
    }

    // Validate class on non alphapetic chars.
    $class = $form_state->getValue('class');
    if (preg_match('/[^A-Za-z0-9-]/', $class)) {
      $form_state->setErrorByName('class',
        t('"Menu item selector"  contains forbidden chars. Only a-z, - allowed.'));
    }

    // Validate tag on special chars.
    $tag = $form_state->getValue('tag');
    if (preg_match('/[^A-Za-z0-9]/', $tag)) {
      $form_state->setErrorByName('tag', t('"Tag" contains special characters.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('single_page_site.config')
      ->set('menu', $form_state->getValue('menu'))
      ->set('menuclass', $form_state->getValue('menuclass'))
      ->set('class', $form_state->getValue('class'))
      ->set('title', $form_state->getValue('title'))
      ->set('tag', $form_state->getValue('tag'))
      ->set('homepage', $form_state->getValue('homepage'))
      ->set('down', $form_state->getValue('scroll-down'))
      ->set('up', $form_state->getValue('scroll-up'))
      ->set('smoothscrolling', $form_state->getValue('smooth-scrolling'))
      ->set('updatehash', $form_state->getValue('update-hash'))
      ->set('offsetselector', $form_state->getValue('offset-selector'))
      ->set('filterurlprefix', $form_state->getValue('filter-url-prefix'))
      ->save();

    if ($form_state->getValue('homepage')) {
      // Set single-page-site as homepage.
      $this->config('system.site')
        ->set('page.front', '/single-page-site')
        ->save();
    }

    $this->messenger()->addMessage(t('Your settings have been saved.'));
  }

  /**
   * Fetches all menus.
   *
   * @return mixed
   *   Return Menus.
   */
  private function getMenus() {
    return \Drupal::entityQuery('menu')->execute();
  }

}
