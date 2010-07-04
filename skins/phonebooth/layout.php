<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <?php echo $meta ?>

        <title>
            Bluebox<?php if (strlen($title) > 0) echo ' : ' . $title; ?>
        </title>

        <?php stylesheet::add(array('reset','layout', 'navigation', 'screen', 'forms'), 10); ?>
        <?php stylesheet::add('jquery.custom.css', 31); ?>
        <?php stylesheet::add('ie', array('cond' => 'ie', 'weight' => 10)); ?>

        <?php jquery::addPlugin(array('tabs', 'multiselect', 'persistent', 'blockUI')); ?>
        <?php javascript::add('phonebooth'); ?>

        <?php echo $css ?>
        <?php echo $js ?>

        <?php echo html::link('skins/phonebooth/assets/img/favicon.ico', 'icon', 'image/x-icon'); ?>
    </head>
    
    <body id="bluebox-com">
    <div id="lang_bar"></div>

    <div class="container">
        <div class="header">
            <div class="topbar clearfix">
                
            <?php
                $logoImg = html::image('assets/img/logo.png','Bluebox v3 Let freedom ring!');
                echo html::anchor('/welcome',  $logoImg, array(
                    'id' => 'logo'
                ));
            ?>

            <?php if (!empty(users::$user->first_name)) : ?>
                <?php echo __('Welcome') . ' ' . users::$user->first_name . ' ' . users::$user->last_name; ?>

                |
                <?php
                    echo html::anchor('user/logout', __('Logout'));
                ?>
                |
            <?php endif; ?>

            <?php
                echo html::anchor('#', __('Language') , array(
                    'id' => 'change_lang'
                ));
                echo form::dropdown(array(
                    'name' => 'lang',
                    'translate' => false
                ), i18n::$langs, Session::instance()->get('lang', 'en'));
            ?>

            <?php if (!empty(users::$user)) {
                if (class_exists('DashManager', TRUE)) echo DashManager::renderActions();
            }
            ?>
                
            </div>
            <div class="bottom hide">&nbsp;</div>
        </div>
        <!-- END OF HEADER -->

        <div class="content">
            <div class="wrapper">

                <div class="left-corner-dot">
                    <?php echo html::image(skins::getSkin() . 'assets/img/corner_dot.jpg', '*'); ?>
                </div>
                <div class="right-corner-dot">
                    <?php echo html::image(skins::getSkin() . 'assets/img/corner_dot.jpg', '*'); ?>
                </div>

                <div class="nav navMenuContainer">
                    <div class="navMenuList">
                    <?php
                        // get a multideminsional array of the navigation structure, but limit depth to 1 level
                        $navTree = navigation::getNavTree(1);
                        // loop each of the nav branches
                        foreach($navTree as $branch => $leafs) {
                            // generate markup to group these nav items
                            echo '<div class="navMenuGroup navGroup' . $branch . '"><h1>';
                            echo $branch != 'unset' ? ucfirst($branch) : '&nbsp;';
                            echo '</h1>';

                            // loop each nav item in this group
                            foreach($leafs as $navStructure) {
                                // determine if the nav items has a menu icon
                                if($img = navigation::getNavIcon($navStructure)) {
                                    $icon = '<span class="navIcon ' . pathinfo($img, PATHINFO_FILENAME) . '" style="background-image: url(' . $img . ');">&nbsp;</span>';
                                    $text = $icon . $navStructure['navLabel'];
                                } else {
                                    $text = '<span class="navIcon">&nbsp;</span>' . $navStructure['navLabel'];
                                }

                                // create the link for this item
                                echo html::anchor($navStructure['navURL'], $text, array(
                                    'title' => $navStructure['navSummary'],
                                    'class' => navigation::getNavClasses($navStructure)
                                ));
                            }
                            echo '</div>';
                        }
                    ?>
                    </div>
                </div>

                <div id="tab-panel" class="main">
                <?php
                    // display submenu items
                    $submenu = navigation::getCurrentSubMenu();
                    if (!empty($submenu)) {

                        $disabled = array_flip(array_keys($submenu));
                        $tabPanels = $selected ='';

                        echo '<ul class="subMenuList">';
                        foreach($submenu as $name => $navStructure) {
                            
                            $displayName = '<span>' .$navStructure['translatedName'] .'</span>';
                            $tabName = trim(preg_replace('/[^a-zA-Z0-9_]+/imx', '_', $name), '_');
                            $url = $navStructure['url'];

                            if (navigation::atUrl($url)) {
                                echo '<li class="subItem' .$tabName .' currentSubItem ">';
                                echo html::anchor('#'. $tabName, $displayName, array(
                                    'title' => $tabName
                                ));
                                echo '</li>';

                                $tabPanels .= '<div id="' .$tabName .'" class="main_content_inner_tab_panel">';

                                $selected = 'selected: ' . $disabled[$name] .',';
                                
                                unset($disabled[$name]);
                            } else {
                                echo '<li class="subItem' .$tabName .'">';
                                echo html::anchor($url, $displayName, array(
                                    'title' => $tabName
                                ));
                                echo '</li>';
                                
                                $tabPanels = '<div id="' .$tabName .'" class="main_content_inner_tab_panel"></div>' .$tabPanels;

                                if (empty($navStructure['disabled'])) {
                                    unset($disabled[$name]);
                                }
                            }
                        }
                        echo '</ul>';

                        $disabled = empty($disabled) ? '' : ' disabled: [' . implode(', ', $disabled) . '], ';
                        jquery::addQuery('#tab-panel')->tabs('{ ' .$selected .$disabled . 'cache: true,  ajaxOptions: { cache: true }, spinner: \'<em>' . __('Loading') . '&#8230;</em>\'}');

                        echo $tabPanels;
                    }
                ?>
                <?php message::render(); ?>
                <?php echo $content; ?>
                <?php if (!empty($submenu)) echo '</div>'; ?>
                </div>

                <div class="left-corner-dot">
                    <?php echo html::image(skins::getSkin() . 'assets/img/corner_dot.jpg', '*'); ?>
                </div>
                <div class="right-corner-dot" style="text-align:right;">
                    <?php echo html::image(skins::getSkin() . 'assets/img/corner_dot.jpg', '*'); ?>
                </div>

            </div>
        </div>
        <!-- END OF CONTENT -->

        <div class="footer">
        </div>
        <!-- END OF FOOTER -->
    </div>

    <!-- END OF WRAPPER -->
    <?php
        if (class_exists('DashManager', TRUE) && !empty($this->user->first_name)) {
            echo DashManager::renderDialogs();
        }
    ?>
    <?php javascript::renderCodeBlocks(); ?>
    </body>
</html>

<?php if (Kohana::config('core.render_stats') === TRUE) { ?>
<!-- Kohana Version: {kohana_version} -->
<!-- Kohana Codename: {kohana_codename} -->
<!-- Execution Time: {execution_time} -->
<!-- Memory Usage: {memory_usage} -->
<!-- Included Files: {included_files} -->
<?php } ?>
