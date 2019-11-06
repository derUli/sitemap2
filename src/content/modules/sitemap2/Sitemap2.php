<?php

use UliCMS\Models\Content\Language;

class Sitemap2 extends Controller {

    private $moduleName = "sitemap2";

    public function render() {
        return Template::executeModuleTemplate($this->moduleName, "sitemap.php");
    }

    public function getShowNotInMenu() {
        return Settings::get("sitemap2_show_not_in_menu", "bool");
    }

    public function settings() {
        if (Request::isPost()) {
            Settings::set("sitemap2_show_not_in_menu", Request::getVar("sitemap2_show_not_in_menu"), "bool");
        }
        return Template::executeModuleTemplate($this->moduleName, "settings.php");
    }

    public function getMenu($name = "top", $parent_id = null, $recursive = true, $order = "position") {
        $html = "";
        $name = db_escape($name);
        $language = $_SESSION["language"];
        $sql = "SELECT id, slug, access, link_url, title, alternate_title, menu_image, target, type, link_to_language FROM " . tbname("content") .
                " WHERE menu='$name' AND language = '$language' AND active = 1 AND `deleted_at` IS NULL AND hidden = 0 and type <> 'snippet' and parent_id ";

        if (is_null($parent_id)) {
            $sql .= " IS NULL ";
        } else {
            $sql .= " = " . intval($parent_id) . " ";
        }
        $sql .= " ORDER by " . $order;
        $query = db_query($sql);

        if (db_num_rows($query) == 0) {
            return $html;
        }
        $containsCurrentItem = parent_item_contains_current_page($parent_id);
        $html .= "<ul>\n";

        while ($row = db_fetch_object($query)) {
            if (checkAccess($row->access)) {
                $html .= "<li>";
                $title = $row->title;
                $link_url = $row->link_url;
                if ($row->type == "language_link" && !is_null($row->link_to_language)) {
                    $language = new Language($row->link_to_language);
                    $link_url = $language->getLanguageLink();
                }
                $html .= "<a href='" . buildSEOUrl($row->slug, $link_url) . "'>";
                $html .= htmlentities($title, ENT_QUOTES, "UTF-8");
                $html .= "</a>\n";

                if ($recursive) {
                    $html .= $this->getMenu($name, $row->id, true, $order);
                }

                $html .= "</li>";
            }
        }
        $html .= "</ul>";
        return $html;
    }

}
