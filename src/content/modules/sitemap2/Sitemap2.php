<?php

class Sitemap2 extends Controller
{

    private $moduleName = "sitemap2";

    public function render()
    {
        return Template::executeModuleTemplate($this->moduleName, "sitemap.php");
    }

    public function getShowNotInMenu()
    {
        return Settings::get("sitemap2_show_not_in_menu", "bool");
    }

    public function settings()
    {
        if (Request::isPost()) {
            Settings::set("sitemap2_show_not_in_menu", Request::getVar("sitemap2_show_not_in_menu"), "bool");
        }
        return Template::executeModuleTemplate($this->moduleName, "settings.php");
    }

    public function getMenu($name = "top", $parent = null, $recursive = true, $order = "position")
    {
        $language = $_SESSION["language"];
        $query = db_query("SELECT * FROM " . tbname("content") . " WHERE menu ='$name' AND language = '$language' AND active = 1 AND `deleted_at` IS NULL AND parent IS NULL and `type` <> 'node' and `type` <> 'snippet' and `type` <> 'link' ORDER by position");
        
        $html_output = "<ul>\n";
        while ($row = db_fetch_object($query)) {
            $html_output .= "  <li>";
            if (! startsWith($row->redirection, "#")) {
                
                if (get_requested_pagename() != $row->systemname) {
                    $html_output .= "<a href='" . buildSEOUrl($row->systemname) . "' target='" . $row->target . "'>";
                } else {
                    $html_output .= "<a href='" . buildSEOUrl($row->systemname) . "' target='" . $row->target . "'>";
                }
            }
            
            $html_output .= $row->title;
            if (! startsWith($row->redirection, "#")) {
                $html_output .= "</a>\n";
            }
            
            // Unterebene 1
            $query2 = db_query("SELECT * FROM " . tbname("content") . " WHERE active = 1 AND `deleted_at` IS NULL AND language = '$language' AND parent=" . $row->id . " and `type` <> 'node' and `type` <> 'snippet' and `type` <> 'link' ORDER by position");
            if (db_num_rows($query2) > 0) {
                $html_output .= "<ul>\n";
                while ($row2 = db_fetch_object($query2)) {
                    $html_output .= "      <li>";
                    
                    if (! startsWith($row2->redirection, "#")) {
                        
                        if (get_requested_pagename() != $row2->systemname) {
                            $html_output .= "<a href='" . buildSEOUrl($row2->systemname) . "' target='" . $row->target . "'>";
                        } else {
                            $html_output .= "<a href='" . buildSEOUrl($row2->systemname) . "' target='" . $row->target . "'>";
                        }
                    }
                    $html_output .= $row2->title;
                    
                    if (! startsWith($row2->redirection, "#")) {
                        $html_output .= '</a>';
                    }
                    
                    // Unterebene 2
                    $query3 = db_query("SELECT * FROM " . tbname("content") . " WHERE active = 1 AND `deleted_at` IS NULL AND language = '$language' AND parent=" . $row2->id . " and `type` <> 'node' and `type` <> 'snippet' and `type` <> 'link' ORDER by position");
                    if (db_num_rows($query3) > 0) {
                        $html_output .= "  <ul>\n";
                        while ($row3 = db_fetch_object($query3)) {
                            $html_output .= "      <li>";
                            if (! startsWith($row3->redirection, "#")) {
                                
                                if (get_requested_pagename() != $row3->systemname) {
                                    $html_output .= "<a href='" . buildSEOUrl($row3->systemname) . "' target='" . $row3->target . "'>";
                                } else {
                                    $html_output .= "<a href='" . buildSEOUrl($row3->systemname) . "' target='" . $row3->target . "'>";
                                }
                            }
                            $html_output .= $row3->title;
                            
                            if (! startsWith($row3->redirection, "#")) {
                                $html_output .= '</a>';
                            }
                            
                            // Unterebene 3
                            $query4 = db_query("SELECT * FROM " . tbname("content") . " WHERE active = 1 AND `deleted_at` IS NULL AND language = '$language' AND parent=" . $row3->id . " and `type` <> 'node' and `type` <> 'snippet' and `type` <> 'link' ORDER by position");
                            if (db_num_rows($query4) > 0) {
                                $html_output .= "  <ul>\n";
                                while ($row4 = db_fetch_object($query4)) {
                                    $html_output .= "<li>";
                                    if (! startsWith($row4->redirection, "#")) {
                                        if (get_requested_pagename() != $row4->systemname) {
                                            $html_output .= "<a href='" . buildSEOUrl($row4->systemname) . "' target='" . $row4->target . "'>";
                                        } else {
                                            $html_output .= "<a href='" . buildSEOUrl($row4->systemname) . "' target='" . $row4->target . "'>";
                                        }
                                    }
                                    $html_output .= $row4->title;
                                    
                                    if (! startsWith($row4->redirection, "#")) {
                                        $html_output .= '</a>';
                                    }
                                    $html_output .= "</li>\n";
                                }
                                
                                $html_output .= "  </ul></li>\n";
                            }
                        }
                        $html_output .= "  </ul></li>\n";
                    } else {
                        $html_output .= "</li>\n";
                    }
                }
                $html_output .= "  </ul></li>\n";
            } else {
                $html_output .= "</li>\n";
            }
        }
        
        $html_output .= "</ul>";
        return $html_output;
    }
}