<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * Bluebox Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is Bluebox Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 * K Anderson
 *
 */

/**
 * subview.php - Subviews are views contained within a view.
 *
 * The helper functions here allow for easily displaying subviews, including in named sections
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Core
 */

class subview {
    /**
     * Renders all subviews passed in. Handles nested views within arrays. Uses recursion / No depth restriction.
     * @param array $subviews An array of subviews. Can contain nested arrays with additional views.
     * @return string All HTML from the subviews returned as a big string of HTML.
     */
    public function render($subviews, $section = NULL)
    {
        $html = '';

        if (!is_array($subviews))
            $subviews = array($subviews);

        foreach ($subviews as $subview) {
            // Handle nested views
            if (is_array($subview)) {
                self::render($subview, $section);
            } else {
                // See if we're supposed to be rendering only a specific view
                if (($section == NULL) or (($section) && isset($subview->section) && (strcasecmp($section, $subview->section) == 0))) {
                    // Render the current view and return it's HTML
                    $html .= $subview->render();
                }
            }
        }

        return $html;
    }

    /**
     * Renders all subviews passed in (using render()) and attaches a standard header and footer to the views along with a section title.
     * @param array | Bluebox_View $subviews Array of nested views
     * Last modified by K Anderson on 06/08/09
     * 	 
     * @param string $sectionTitle
     * @return string HTML of rendered subviews, with header and footer attached
     */
    public function renderSection($subviews, $sectionTitle)
    {
        $html = '<!-- ' .$sectionTitle .' add-on modules -->';
        //$html .= form::open_fieldset(array('class' => 'section ' .strtolower($sectionTitle)));
        //$html .= form::legend($sectionTitle);

        // Add the HTML from all subviews (including nested ones)
        $html .= self::render($subviews);

        $html .= "<!-- End $sectionTitle modules -->\n\n";
        return $html;
    }

    /**
     * Renders an array of nested views, using the values of 'tab' and 'section' within the view object as the section title
     * Also processes nested views.
     * Last modified by K Anderson on 06/08/09
     * 
     * @param array | Bluebox_View $subviews Array of nested views
     * @param array $order (optional) An array of section names to process, in the order they are given.
     * @return string HTML string of all rendered sections, with titles for each section.
     */
    public function renderAsSections($subviews, $order = NULL)
    {
        $html = ' ';

        // Only process subviews if it's an array
        if (is_array($subviews)) {
            $renderedViews = array();
            // Process subview sections and place rendering results into array, ordered by section
            foreach ($subviews as $subview) {
                $section = (isset($subview->section) ? $subview->section : 'other');

                // Does section already exist? If so, don't add title to section again
                if (isset($renderedViews[$section])) {
                    $renderedViews[$section][] = self::render($subview);
                } else {
                    $renderedViews[$section][] = self::renderSection($subview, ucfirst($section));
                }
            }


            /**
             * TODO: Change from jquery to toggles
             */
            // Only load the accordion if we need it IE: there is more than one plugin and jquery is avaliable
            if (!empty($renderedViews) && count($renderedViews) > 1 && class_exists('jquery'))
            {
                    //jquery::addPlugin('accordion');
                    //jquery::addQuery('#accordion') -> accordion('{ autoHeight: false}');
            }
			
            // Was a specific order for rendering specified?
            if ($order) {
                foreach ($order as $sectionTitle) if (isset($renderedViews[$sectionTitle])) {
                    $html .= form::open_section(ucfirst($sectionTitle));
                    $html .= implode("\n", $renderedViews[$sectionTitle]);
                    $html .= form::close_section();
                }
            } else {
                foreach ($renderedViews as $sectionTitle => $renderedSection) {
                    $html .= form::open_section(ucfirst($sectionTitle));
                    $html .= implode("\n", $renderedSection);
                    $html .= form::close_section();
                }
            }
        }

        return $html;
    }

}

