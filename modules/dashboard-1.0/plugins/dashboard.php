<?php defined('SYSPATH') or die('No direct access allowed.');

class Dashboard_Plugin extends Bluebox_Plugin
{
    protected $name = 'dashboard';

    public function index()
    {
        $this->subview = new View('generic/blank');

        $this->subview->tab = 'main';

        $this->subview->section = 'general';

        $this->subview->content = $this->request_content('http://www.2600hz.org/bluebox/welcome_page.php');

        $this->views[] = $this->subview;
    }

    protected function request_content($url)
    {
        $data = array(
            'bluebox_remote_request' => 'welcome_page'
        );

        $allowStats = Kohana::config('core.anonymous_statistics');

        if (!empty($allowStats))
        {
            $data['anonymous_id'] = Kohana::config('core.anonymous_id');
        }

        $params = array('http' => array(
            'method' => 'POST',
            'content' => http_build_query($data),
            'timeout' => 5,
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n"
        ));

        $context = stream_context_create($params);

        return @file_get_contents($url, FALSE, $context);
    }
}