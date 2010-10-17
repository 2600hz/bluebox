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

        <?php jquery::addPlugin(array('persistent', 'scrollTo', 'growl', 'qtip', 'infiniteCarousel', 'dropdowns', 'blockUI', 'form')); ?>
        <?php javascript::add('bluebox'); ?>

        <?php echo $css ?>
        <?php echo $js ?>

        <?php echo html::link('skins/bluebox/assets/img/favicon.ico', 'icon', 'image/x-icon'); ?>
    </head>

    <body>
        <div id="lang_bar"></div>
        <div class="container">

            <div class="header">

                <div class="topbar">

                    <!-- Logo for the page -->
                    <div id="logo_container">
                    <?php
                        $logoImg = html::image('assets/img/logo.png','the Bluebox Project!');
                        echo html::anchor('/welcome',  $logoImg, array(
                            'id' => 'logo'
                        ));
                    ?>
                    </div>

                    <!-- Profile/experience related quicklinks, like login/logout, language, etc. -->
                    <div id="profileLinks">

                        <!-- Login manager -->
                        <div class="loginManager">
                        <?php if (!empty(users::$user)): ?>
                                <?php  echo __('Welcome') . ' ' . users::$user->first_name . ' ' . users::$user->last_name; ?>
                            |
                            <?php echo html::anchor('user/logout', __('Logout')); ?>
       
                            <?php
                                echo ' | ';
                                echo html::anchor('#', __('Language') , array('id' => 'change_lang'));
                                echo form::dropdown(array(
                                    'name' => 'lang',
                                    'id' => 'lang',
                                    'style' => 'display:none;',
                                    'translate' => false
                                ) , i18n::$langs, Session::instance()->get('lang', 'en'));
                            ?>

                            <?php endif; ?>

                            <?php if (!empty(users::$user)): ?>
                            <!-- dash board -->
                            <div class="dash">
                                <?php if (class_exists('DashManager', TRUE)) echo DashManager::renderActions(); ?>
                            </div>
                        <?php endif; ?>
                            
                        </div>
                        
                    </div>

                    <!-- Quick access bar, for module access after login -->
                    <div id="quickAccess"></div>
                </div>

                <div class="bottom"></div>

            </div>
            <!-- END OF HEADER -->

            <div class="content">

                <div class="wrapper">
      
                    <div class="nav">
                        <?php if (!empty(users::$user)): ?>
                        <div id="navigation">
                            
                            <div class="navCategorySelection" >
                                <div class="wrapper">
                                    <ul id="navCategories">
                                    <?php
                                        $navTree = navigation::getNavTree(1);
                                        foreach($navTree as $branch => $leafs) {
                                            echo '<li id="category_' . strtolower($branch) .'">' . ucfirst($branch) . '</li>';
                                        }
                                    ?>
                                    </ul>
                                </div>
                            </div>

                            <?php foreach($navTree as $branch => $leafs) : ?>
                            <div id="navGroup<?php echo ucfirst($branch); ?>" class="navGroup">
                                <div class="wrapper">
                                    <ul>
                                    <?php
                                        foreach($leafs as $navStructure) {
                                            // determine if the nav items has a menu icon
                                            if($img = navigation::getNavIcon($navStructure)) {
                                                $icon = '<span class="navIcon ' . pathinfo($img, PATHINFO_FILENAME) . '" style="background-image: url(' . $img . ');">&nbsp;</span>';
                                                $text = $icon . '<span>' . $navStructure['navLabel'] . '</span>';
                                            } else {
                                                $text = '<span>' . $navStructure['navLabel'] . '</span>';
                                            }

                                            // create the link for this item
                                            echo '<li>';
                                            echo html::anchor($navStructure['navURL'], $text, array(
                                                'title' => (isset($navStructure['navSummary']) ? $navStructure['navSummary'] : ''),
                                                'class' => navigation::getNavClasses($navStructure)
                                            ));
                                            echo '</li>';
                                        }
                                    ?>
                                    </ul>
                                </div>
                            </div>
                            <?php endforeach; ?>

                            <div id="navCap"></div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="main">
                        
                        <?php message::render(); ?>

                        <div class="sub_menu">
                        <?php
                            $submenu = navigation::getCurrentSubMenu();
                            if (!empty($submenu)&& navigation::atUrl()) {
                                foreach ($submenu as $menuItem => $parameters) {
                                    if (!empty($parameters['disabled'])) {
                                        continue;
                                    }
                                    if (navigation::atUrl($parameters['url'])) {
                                        continue;
                                    }
                                    echo html::anchor($parameters['url'], __($menuItem));
                                }
                            }
                        ?>
                        </div>
                        
                        <?php echo $content; ?>

                    </div>

                </div>

            </div>
            <!-- END OF CONTENT -->

            <div class="footer">
                <small><a href="http://www.2600hz.org">powered by blue.box</a></small>
            </div>
            <!-- END OF FOOTER -->

        </div>
        <?php
            if (class_exists('DashManager', TRUE) && isset(users::$user)) {
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
