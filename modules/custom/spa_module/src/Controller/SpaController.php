<?php

namespace Drupal\spa_module\Controller;

use Drupal\Core\Controller\ControllerBase;


class SpaController extends ControllerBase {

    // public function content() {
    // return [
    //     '#theme' => 'spa_module',
    //     '#title' => 'test controller',
    // ]; 
    // }
    public function overview() { 
        $build = [];
        $build['#attached']['library'][] = 'spa_module/my-react-app';
        $build['#markup'] = '<div id="root"></div>'; 
        $build['#attached']['drupalSettings']['myReactApp']['blocks'] = 'testing controller to react';

        return $build;
    }

}