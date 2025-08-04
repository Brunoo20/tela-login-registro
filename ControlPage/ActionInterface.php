<?php

namespace ControlPage;

interface ActionInterface
{
    public function setParameter($param, $value);
    public function serialize();
}
