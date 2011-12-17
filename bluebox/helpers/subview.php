<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Subview
 * @author     Darren Schreiber <d@d-man.org>
 * @license    Mozilla Public License (MPL)
 */
class subview
{
    /**
     * Renders all subviews passed in. Handles nested views within arrays. Uses recursion / No depth restriction.
     * 
     * @param array $subviews An array of subviews. Can contain nested arrays with additional views.
     * @return string All HTML from the subviews returned as a big string of HTML.
     */
    public function render($subviews, $section = NULL)
    {
        $html = '';

        if (!is_array($subviews))
        {
            $subviews = array($subviews);
        }

        if (!is_null($section) AND !is_array($section))
        {
            $section = array($section);
        }

        if (is_array($section))
        {
            $section = array_map('strtolower', $section);
        }

        foreach ($subviews as $subview) {

            // Handle nested views
            if (is_array($subview))
            {
                self::render($subview, $section);

            } 
            else
            {
                if (!($subview instanceof View))
                {
                    kohana::log('error', 'Can not render a subview that is not an instance of View');
                    continue;
                }

                if (isset($subview->render_conditional))
                {
                    $renderConditions = $subview->render_conditional;

                    if (!is_array($renderConditions))
                    {
                        $renderConditions = array($renderConditions => FALSE);
                    }

                    if (Request::is_ajax())
                    {
                        if (isset($renderConditions['ajax']) AND $renderConditions['ajax'] === FALSE)
                        {
                            kohana::log('debug', 'Not rendering view due to ajax request');
                            continue;
                        }
                    }

                    if (isset($_REQUEST['qtipAjaxForm']) AND $renderConditions['qtipAjaxForm'] === FALSE)
                    {
                        kohana::log('debug', 'Not rendering view due to qtipAjaxForm request');
                        continue;
                    }
                    
                    if (isset($renderConditions['mode']))
                    {
                    	if ( (!is_array($renderConditions['mode'])))
                    		$renderConditions['mode'] = array($renderConditions['mode']);
                    		
                    	# get mode of controller and check in renderConditions['renderMode] to see if it exists
                    	if (!in_array(Bluebox_Controller::getControllerMode(), $renderConditions['mode']))
                    		continue;
                    }

                }

                if (is_null($section))
                {
                    $html .= $subview->render();
                    continue;
                }

                if (!isset($subview->section))
                {
                    continue;
                }

                $subviewSection = strtolower($subview->section);

                if (in_array($subviewSection, $section))
                {
                    $html .= $subview->render();
                }

            }

        }

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
        $html = '';

        // Only process subviews if it's an array
        if (is_array($subviews))
        {

            $renderedViews = array();

            // Process subview sections and place rendering results into array, ordered by section
            foreach ($subviews as $subview)
            {

                if (!empty($subview->section))
                {
                    $section = $subview->section;
                } 
                else
                {
                    $section = 'NULL';
                }

                $rendered = self::render($subview);

                if (!empty($rendered))
                {
                    $renderedViews[$section][] = $rendered;
                }
            }

            // Was a specific order for rendering specified?
            if ($order)
            {
                foreach ($order as $sectionTitle)
                {
                    if (isset($renderedViews[$sectionTitle]))
                    {
                        //$html .= form::open_section(ucfirst($sectionTitle));

                        $html .= implode("\n", $renderedViews[$sectionTitle]);

                        //$html .= form::close_section();
                        
                        unset($renderedViews[$sectionTitle]);
                    }
                }
            }

            foreach ($renderedViews as $sectionTitle => $renderedSection)
            {
                //$html .= form::open_section(ucfirst($sectionTitle));

                $html .= implode("\n", $renderedSection);

                //$html .= form::close_section();
            }
        }

        return $html;
    }
}