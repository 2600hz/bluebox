<?php
    jquery::$skinName = 'dot-luv';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <?php echo $meta;?>

        <title><?php echo $title;?></title>
        
        <link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>skins/darkness/assets/css/reset.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>skins/darkness/assets/css/layout.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>skins/darkness/assets/css/navigation.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>skins/darkness/assets/css/screen.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>skins/darkness/assets/css/forms.css" media="screen" />
        <?php echo $css ?>
        <link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>skins/darkness/assets/css/jquery.custom.css" media="screen" />

        <!--[if IE]>
            <link rel="stylesheet" href="<?php echo url::base(); ?>skins/darkness/assets/css/ie.css" type="text/css" media="screen, projection">
        <![endif]-->
        <?php jquery::addPlugin(array('growl', 'qtip')); ?>
        <?php echo $js; ?>
    </head>
    <body id="bluebox-com">
    <?php echo $header;?>
    <div class="container">

        <div class="header">

            <div class="topbar">

                <?php if (users::getAttr('user_id')) : ?>
                    <div>
                        Welcome <?php echo users::getAttr('full_name'); ?>!
                    </div>
                                        <div class="quiet"><?php echo html::anchor('user/logout', 'logout');?></div>
                <?php endif; ?>
            </div>


            <div class="bottom hide">&nbsp;</div>

        </div>
        <!-- END OF HEADER -->


        <div class="content">

            <div class="wrapper">

                <div class="nav">
                    <ul>
                    <?php
                        $navTree = navigation::getNavTree(0);
                        foreach($navTree as $branch => $navStructure) {
                            if (empty($navStructure['currentNavItem'])) {
                                echo '<li>';
                                echo html::anchor($navStructure['navURL'], '<span>' . $navStructure['navLabel'] . '</span>', array(
                                    'title' => $navStructure['navSummary'],
                                    'class' => navigation::getNavClasses($navStructure)
                                ));
                                echo '</li>';
                            } else {
                                echo '<li class="currentNavItem">';
                                echo '<span>' . $navStructure['navLabel'] . '</span>';
                                if (!empty($navStructure['navSubmenu'])) {
                                    echo '<ul>';

                                    foreach ($navStructure['navSubmenu'] as $label => $options) {
                                        if (!is_array($options)) {
                                            $options = array('url' => $options);
                                        }
                                        $currentNavItem = navigation::atUrl($options['url']);
                                        if (!empty($options['disabled']) && !$currentNavItem) continue;
                                        echo '<li ';
                                        if ($currentNavItem)
                                            echo 'class="currentNavItem"';
                                        echo '>';
                                        echo html::anchor($options['url'], '<span>' . $label . '</span>');
                                        echo '</li>';
                                    }

                                    echo '</ul>';
                                }
                                echo '</li>';
                            }
                        }
                    ?>
                    </ul>
                </div>

                <div class="main">
                    <?php message::render(array(), array('html' => FALSE, 'growl' => TRUE)); ?>
                    <!-- MAIN CONTENT -->
                    <?php echo $content ?>
                    <!-- END MAIN CONTENT -->
                </div>

            </div>
        </div>
        <div class="clear"></div>
        <!-- END OF CONTENT -->

        <div class="footer">

            <div class="topbar hide">&nbsp;</div>

            <div class="nav hide">&nbsp;</div>

            <div class="bottom txt-right">
                <p> &copy; <?php echo date('Y');?></p>
            </div>

        </div>
        <!-- END OF FOOTER -->

    </div>
    <!-- END OF WRAPPER -->
    <?php jquery::buildResponse(); ?>

    <script type="text/javascript">
        //<![CDATA[
        $(document).ready(function () {
            $('form.form input.checkbox').not('form.blueboxmanager input.checkbox').each(function() {
                label = $(this).parent().find('.label');
                $(label).css({
                    'display': 'inline',
                    'float': 'left',
                    'margin-right': '5px'
                });
            });
            $('.help').each(function() {
                $(this).qtip({
                    content: {
                        title: {
                            text: 'Additional Help...'
                        },
                        text: $(this).attr('tooltip')
                    },
                    position: {
                        corner: {
                            tooltip: 'topLeft',
                            target: 'bottomRight'
                        }
                    },
                    show: {
                        solo: true // And hide all other tooltips
                    },
                    style: {
                        width: 550,
                        padding: '8px',
                        title: {
                            'background-color': '#555555',
                            color: '#ffffff',
                            padding: '3px 10px 5px 10px',
                            'font-size': '110%'
                        },
                        border: {
                            width: 8,
                            color: '#555555'
                        }
                    }
                });

            });
        });
        //]]>
    </script>
    </body>
</html>
