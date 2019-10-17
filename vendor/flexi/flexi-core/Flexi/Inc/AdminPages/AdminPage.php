<?php

namespace Flexi\Inc\AdminPages;

use Flexi\Core\View;

class AdminPage
{
    public $title;
    public $menuTitle;
    public $iconUrl;

    protected $slug;

    private $position;
    private $template;
    private $capability;
    private $subPages = [];

    function __construct($title, $menuTitle, $template, $slug = '', $cap = 'manage_options', $iconUrl = '', $position = null, $subPage = false)
    {
        $this->title = $title;
        $this->menuTitle = $menuTitle;
        $this->iconUrl = $iconUrl;

        $this->position = $position;
        $this->slug = $this->generateSlug($slug);

        $this->template = $template;
        $this->capability = $cap;

        $this->setupPage($subPage);
    }

    protected function generateSlug($slug)
    {
        return $slug ? sanitize_title($slug) : sanitize_title($this->menuTitle);
    }

    private function setupPage($subPage)
    {
        if (!$subPage) {
            add_menu_page($this->title, $this->menuTitle, $this->capability, $this->slug, [$this, 'getPageView'], $this->iconUrl, $this->position);
        } else {
            add_submenu_page($subPage, $this->title, $this->menuTitle, $this->capability, $this->slug, [$this, 'getPageView']);
        }

    }

    public function getPageView()
    {
        $page = $this;
        View::create($this->template, compact('page'));
    }

    public function addSubPage($title, $menuTitle, $template, $slug = '', $cap = 'manage_options')
    {
        $this->subPages[] = new AdminPage($title, $menuTitle, $template, $slug, $cap, '', null, $this->slug);
    }
}