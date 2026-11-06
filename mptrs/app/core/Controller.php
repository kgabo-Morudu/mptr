<?php
abstract class Controller {
    public function __construct() {
        // Base constructor is intentionally empty.
    }
    protected function view(string $template, array $data = [], string $layout = 'main'): void {
        extract($data, EXTR_SKIP);
        $viewFile = dirname(__DIR__, 2) . '/views/' . $template . '.php';
        if (!file_exists($viewFile)) die("View not found: {$template}");

        if ($layout === null) { require $viewFile; return; }

        $layoutFile = dirname(__DIR__, 2) . '/views/layouts/' . $layout . '.php';
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require $layoutFile;
    }

    protected function json($data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $route, array $params = []): void {
        $qs = $params ? '&' . http_build_query($params) : '';
        header("Location: " . BASE_URL . "/index.php?route={$route}{$qs}");
        exit;
    }

    protected function input(string $key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
}