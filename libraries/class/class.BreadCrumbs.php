<?php
use Tuezy\BreadcrumbHelper;

class BreadCrumbs extends BreadcrumbHelper
{
    public function set($slug = '', $name = '')
    {
        // Legacy 'set' had ($slug, $name)
        // New 'add' has ($name, $slug)
        parent::add($name, $slug);
    }

    public function get($configBase = null)
    {
        // Legacy get() returned rendered HTML
        return parent::render();
    }
}