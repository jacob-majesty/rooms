<?php
// app/Core/BaseController.php

abstract class BaseController {

    /**
     * Construtor do BaseController.
     * Pode ser usado para inicializar propriedades comuns, como o caminho base das views.
     */
    public function __construct() {
        // Nada específico aqui por enquanto, mas pode ser expandido.
        // Por exemplo, para iniciar a sessão se não fosse feito no index.php
        // ou para carregar configurações comuns.
    }

    /**
     * Renderiza um arquivo de view, passando dados para ele.
     * @param string $viewPath O caminho da view a partir da pasta 'app/Views/' (ex: 'auth/login').
     * @param array $data Um array associativo de dados a serem passados para a view.
     */
    protected function render($viewPath, $data = []) {
        // Transforma o array $data em variáveis individuais que a view pode acessar.
        // Por exemplo, se $data = ['name' => 'João'], $name estará disponível na view.
        extract($data);

        // Constrói o caminho completo para o arquivo da view.
        // Assumindo que as views estão em 'project/app/Views/'
        $fullViewPath = __DIR__ . '/../../app/Views/' . $viewPath . '.php';

        // Verifica se o arquivo da view existe
        if (file_exists($fullViewPath)) {
            // Inclui o arquivo da view.
            // As variáveis extraídas ($data) estarão disponíveis aqui.
            require_once $fullViewPath;
        } else {
            // Lida com o erro se a view não for encontrada (ex: página 404)
            // Em um ambiente de produção, você pode ter uma página de erro mais robusta.
            http_response_code(500); // Erro interno do servidor
            echo "<h1>Erro 500: View não encontrada!</h1><p>O arquivo de view '{$viewPath}.php' não foi localizado.</p>";
            // Ou redirecionar para uma página de erro genérica
            // header('Location: /error');
            exit();
        }
    }

    // Você pode adicionar outros métodos comuns aqui, por exemplo:
    // protected function redirect($url) { /* ... */ }
    // protected function getParam($name) { /* ... */ }
}