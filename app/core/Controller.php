<?php
/*
 * Base Controller
 * Loads the models and views
 */
class Controller {
    // Load model
    public function model($model) {
        // Require model file
        require_once '../app/models/' . $model . '.php';

        // Instantiate model
        return new $model();
    }

    // Load view
    public function view($view, $data = []) {
        // Extract data to make variables available in view
        if (!empty($data)) {
            extract($data);
        }
        // Check for view file
        if (file_exists('../app/views/' . $view . '.php')) {
            require '../app/views/' . $view . '.php';
        } else {
            die('View ' . $view . ' does not exist');
        }
    }
}
