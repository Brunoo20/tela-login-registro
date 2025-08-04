<?php

namespace Core;

/**
 * Carrega a classe do framewok
 */
class classLoader
{
    protected $prefixes = [];

    public function register()
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    public function addNamespace($prefix, $base_dir, $prepend = false)
    {
        // normalizar prefixo de namespace
        $prefix = trim($prefix, '\\') . '\\';

        // normaliza o diretório base com um separador final
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

        // inicializa a matriz de prefixos de namespace
        if (isset($this->prefixes[$prefix]) === false) {
            $this->prefixes[$prefix] = [];
        }

        // mantém o diretório base para o prefixo do namespace
        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            array_push($this->prefixes[$prefix], $base_dir);
        }
    }

    public function loadClass($class)
    {
        // o prefixo do namespace atual
        $prefix = $class;

        // trabalhar de trás para frente através dos nomes de namespace do nome de classe totalmente qualificado
        // para encontrar um nome de arquivo mapeado
        while (false !== $pos = strrpos($prefix, '\\')) {
            // mantém o separador de namespace final no prefixo
            $prefix = substr($class, 0, $pos + 1);

            // o resto é o nome da classe relativa
            $relative_class = substr($class, $pos + 1);

            // tenta carregar um arquivo mapeado para o prefixo e a classe relativa
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }

            // remove o separador de namespace final para a próxima iteração
            // de strrpos()
            $prefix = rtrim($prefix, '\\');
        }

        // nunca encontrou um arquivo mapeado
        return false;
    }

    protected function loadMappedFile($prefix, $relative_class)
    {
        // há algum diretório base para esse prefixo de namespace?
        if (isset($this->prefixes[$prefix]) === false) {
            return false;
        }

        // procure nos diretórios base por este prefixo de namespace
        foreach ($this->prefixes[$prefix] as $base_dir) {
            // substitui o prefixo do namespace pelo diretório base,
            // substitui os separadores de namespace pelos separadores de diretório
            // no nome da classe relativa, acrescenta .php
            $file = $base_dir
                . str_replace('\\', '/', $relative_class)
                . '.php';

            // se o arquivo mapeado existir, solicite-o
            if ($this->requireFile($file)) {
                // sim, terminamos
                return $file;
            }
        }

        // nunca encontrou isso
        return false;
    }

    protected function requireFile($file)
    {
        if (file_exists($file)) {
            require $file;

            return true;
        }

        return false;
    }
}
