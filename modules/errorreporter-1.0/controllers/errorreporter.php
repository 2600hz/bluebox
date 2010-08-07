<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/ErrorReporter
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class ErrorReporter_Controller extends Bluebox_Controller
{
    public function inform($hash = NULL)
    {
        $error = $this->session->get($hash, '');

        $report['issue'] = empty($_POST['report']['issue']) ? '' : $_POST['report']['issue'];

        $report['while'] = empty($_POST['report']['while']) ? '' : $_POST['report']['while'];

        $report['contact'] = empty($_POST['report']['contact']) ? users::$user['email_address'] : $_POST['report']['contact'];

        $report['error'] = empty($_POST['report']['error']) ? $error : $_POST['report']['error'];

        $report['log'] = isset($_POST['report']['log']) ? $_POST['report']['log'] : TRUE;

        if ($action = $this->submitted(array('submitString' => 'Send')))
        {
            if (($action == self::SUBMIT_CONFIRM) AND ($this->submitReport($report)))
            {
                message::set('Request was submitted, your ticket will be worked on.', 'success');
                
                $this->returnQtipAjaxForm(NULL);

                url::redirect(Router_Core::$controller .'/thankYou');
            }
            else if ($action == self::SUBMIT_DENY)
            {
                $this->exitQtipAjaxForm();
            }
        }

        $this->view->report = $report;
    }

    public function thankYou()
    {
        
    }

    protected function submitReport($report)
    {
        $valid = TRUE;

        $validation = Bluebox_Controller::$validation;

        if (empty($report['issue']))
        {
            $validation->add_error('report[issue]', 'Please describe the issue');

            $valid = FALSE;
        }

        if (empty($report['while']))
        {
            $validation->add_error('report[while]', 'Please describe the cause');

            $valid = FALSE;
        }

        if (empty($report['contact']))
        {
            $validation->add_error('report[contact]', 'Please provide a method to contact you');

            $valid = FALSE;
        }

        if (empty($report['error']))
        {
            $validation->add_error('report[error]', 'Please provide the error message');

            $valid = FALSE;
        }

        if (!$valid)
        {
            return FALSE;
        }

        if (!empty($report['log']))
        {
            $filename = Kohana::log_directory().date('Y-m-d').'.log'.EXT;

            $offset = -150 * 120;

            $rs = @fopen($filename, 'r');

            $report['log'] = '';

            if ( $rs !== FALSE )
            {
                fseek($rs, $offset, SEEK_END);

                fgets($rs);

                while(!feof($rs))
                {
                    $buffer = fgets($rs);

                    $report['log'] .= htmlspecialchars($buffer ."\n");
                }

                fclose($rs);
            }
        }

        $allowStats = Kohana::config('core.anonymous_statistics');
        
        if (!empty($allowStats))
        {
            $report['anonymous_id'] = Kohana::config('core.anonymous_id');

            Package_Catalog::disableRemote();

            Package_Catalog::buildCatalog();

            $report['catalog'] = Package_Catalog::getPackageList();
        }

        try
        {
            $errorCollector = Kohana::config('errorreporter.collector');

            $this->do_post_request($errorCollector, $report);
        }
        catch (Exception $e)
        {
            message::set($e->getMessage());

            $this->returnQtipAjaxForm(NULL);

            return FALSE;
        }

        return TRUE;
    }

    protected function do_post_request($url, $data)
    {
        $params = array('http' => array(
            'method' => 'POST',
            'timeout' => 5,
            'content' => http_build_query($data),
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n"
//            'header'  => sprintf("Authorization: Basic %s\r\n", base64_encode($username.':'.$password)).
//                               "Content-type: application/x-www-form-urlencoded\r\n",
        ));

        $context = stream_context_create($params);

        $ret = @file_get_contents($url, FALSE, $context);

        if ($ret === FALSE)
        {
            throw new Exception('Failed to transmit error report.');
        }

        return TRUE;
    }
}