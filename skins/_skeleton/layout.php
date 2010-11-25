<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <?php echo $meta;?>

        <title><?php echo $title;?></title>
        <?php $skin = url::base() . skins::getSkin(); ?>

        <link rel="stylesheet" type="text/css" href="<?php echo $skin; ?>assets/css/reset.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo $skin; ?>assets/css/layout.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>skins/bluebox/assets/css/navigation.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo $skin; ?>assets/css/screen.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo $skin; ?>assets/css/forms.css" media="screen" />

        <?php echo $css;?>

        <link rel="stylesheet" type="text/css" href="<?php echo $skin; ?>assets/css/jquery.custom.css" media="screen" />

        <!--[if IE]>
            <link rel="stylesheet" href="<?php echo $skin; ?>assets/css/ie.css" type="text/css" media="screen, projection">
        <![endif]-->

        <?php echo $js; ?>
    </head>
    <body id="bluebox-com">

    <?php echo $header;?>
    <div class="container">

        <div class="header">

            <div class="topbar">

                <?php if (users::getAttr('user_id')): ?>

                    <?php echo __('Welcome') . ' ' .users::getAttr('full_name'); ?>
                    <?php echo html::anchor('user/logout', __('Logout')); ?>

                <?php endif; ?>
                
            </div>


            <div class="bottom">&nbsp;</div>

        </div>
        <!-- END OF HEADER -->


        <div class="content">

            <div class="wrapper">
                <div class="nav">
                    <ul>
                    <?php
                        $navTree = navigation::getNavTree(0);
                        foreach($navTree as $navStructure) {
                            // determine if the nav items has a menu icon
                            if($img = navigation::getNavIcon($navStructure)) {
                                $icon = '<span class="navIcon ' . pathinfo($img, PATHINFO_FILENAME) . '" style="background-image: url(' . $img . ');">&nbsp;</span>';
                            } else {
                                $icon = '';
                            }

                            // create the link for this item
                            echo '<li>';
                            echo html::anchor($navStructure['navURL'], $icon . '<span>' . $navStructure['navLabel'] . '</span>', array(
                                'title' => $navStructure['navSummary'],
                                'class' => navigation::getNavClasses($navStructure)
                            ));
                            echo '</li>';
                        }
                    ?>
                    </ul>
                </div>

                <div class="main">
                    <?php echo $content ?>
                </div>

            </div>

        </div>
        <!-- END OF CONTENT -->


        <div class="footer">
        </div>
        <!-- END OF FOOTER -->

    </div>

    <!-- END OF WRAPPER -->
    <?php jquery::buildResponse(); ?>
    </body>
</html>
