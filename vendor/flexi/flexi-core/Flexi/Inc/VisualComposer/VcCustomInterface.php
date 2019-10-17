<?php

namespace Flexi\Inc\VisualComposer;

interface VcCustomInterface
{
    public function setMap();

    public function setPaths();

    public function getHtml($args);
}