<?php

namespace Widgets\Base;

/**
 * Representa um elemento HTML genérico (como <div>, <span>, etc.)
 */
class Element
{
    protected $tagname; // Nome da tag HTML (ex: div, span)
    protected $properties; // Atributos HTML (ex: class, id, style)
    protected $children; // Filhos (outros objetos ou textos)


    /**
     *  Construtor: define o nome da tag e inicializa atributos e filhos
     */
    public function __construct($name)
    {
        // define o nome do elemento
        $this->tagname = $name;
    }

    /**
     * Método mágico __set: define um atributo (ex: $div->class = 'box';)
     */
    public function __set($name, $value)
    {
        // Valida o nome do atributo
        // armazena os valores atribuídos ao array properties
        $this->properties[$name] = $value;
    }

    /**
     * Método mágico __get: recupera um atributo
     */
    public function __get($name)
    {
        // retorna os valores atribuídos ao array properties
        return isset($this->properties[$name]) ? $this->properties[$name] : null;
    }

    /**
     * Adiciona um filho ao elemento (texto ou outro objeto Element)
     */
    public function add($child)
    {
        $this->children[] = $child;
    }

    /**
     * Exibe o elemento na tela
     */
    public function show()
    {
        // abre a tag

        $this->open();
        echo "\n";

        if ($this->children) {
            foreach ($this->children as $child) {
                // se for objeto
                if (is_object($child)) {
                    $child->show();
                } elseif (is_string($child) or (is_numeric(($child)))) {
                    // se for texto
                    echo $child;
                }
            }
            // fecha a tag
            $this->close();
        }
    }

    /**
     * Exibe a tag de abertura (ex: <div class="box">)
     */
    private function open()
    {
        // exibe a tag de abertura
        echo "<{$this->tagname}";

        if ($this->properties) {
            // percorre as propriedades
            foreach ($this->properties as $name => $value) {
                if (is_scalar($value)) {
                    echo " {$name}=\"{$value}\"";
                }
            }
        }

        echo '>';
    }

    /**
     *  Exibe a tag de fechamento (ex: </div>)
     */
    private function close()
    {
        echo "</{$this->tagname}>\n";
    }

    /**
     * Permite converter o objeto em string automaticamente (ex: echo $div;)
     */
    public function __toString()
    {
        ob_start();
        $this->show();
        $content = ob_get_clean();

        return $content;
    }
}
