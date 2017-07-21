<?php
$menus = getAllMenus(true);
$controller = ModuleHelper::getMainController("sitemap2");
if (! $controller->getShowNotInMenu() and faster_in_array("not_in_menu", $menus)) {
    $menus = array_flip($menus);
    unset($menus["not_in_menu"]);
    $menus = array_flip($menus);
}
?>
<?php
foreach ($menus as $menu) {
    
    $items = ContentFactory::getAllByMenu($menu);
    if (count($items) > 0) {
        ?>
<h3><?php translate($menu);?></h3>
<?php echo $controller->getMenu($menu);?>
<?php
    }
}