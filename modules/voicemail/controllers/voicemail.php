<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * voicemails.php - Voicemail Controller Class
 *
 * @author Karl Anderson
 * @license MPL
 * @package FreePBX3
 * @subpackage Voicemail
 */
class Voicemail_Controller extends FreePbx_Controller
{
    protected $writable = array('mailbox', 'password', 'email_all_messages', 'email_address', 'delete_file', 'attach_audio_file', 'name');

    protected $baseModel = 'Voicemail';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Build a grid with a hidden conference_id, class_type, and add an option for the user to select the display columns
        $this->grid = jgrid::grid($this->baseModel, array('caption' => 'Voicemail Boxes', 'multiselect' => true))
            ->add('voicemail_id', 'ID', array('hidden' => true, 'key' => true))
            ->add('mailbox', 'Mailbox')
            ->add('name', 'Mailbox Name')
            ->add('email_address', 'Email')
            ->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
            ))
            ->addAction('voicemail/edit', 'Edit', array(
            'arguments' => 'voicemail_id',
            'width' => '120'
            ))
            ->addAction('voicemail/delete', 'Delete', array(
            'arguments' => 'voicemail_id',
            'width' => '20'
            ))
            ->navGrid(array('del' => true));

        // dont foget to let the plugins add to the grid!
        plugins::views($this);

        // Produces the grid markup or JSON, respectively
        $this->view->grid = $this->grid->produce();
    }

    public function add() {
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = ucfirst(Router::$method) .' a Voicemail Box';

        $this->voicemail = new $this->baseModel();
        if ($this->submitted()) {
            $this->voicemail->account_id = users::$user->Location->account_id;
            if ($this->formSave($this->voicemail)) {
                url::redirect(Router_Core::$controller);
            }
        }

        $this->view->voicemail = $this->voicemail;
        plugins::views($this);
    }

    public function edit($id) {
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = ucfirst(Router::$method) .' a Voicemail Box';

        $this->voicemail = Doctrine::getTable($this->baseModel)->find($id);
        if (! $this->voicemail) {
            Kohana::show_404();
        }

        if ($this->submitted()) {
            if ($this->formSave($this->voicemail)) {
                url::redirect(Router_Core::$controller);
            }
        }

        $this->view->voicemail = $this->voicemail;
        plugins::views($this);
    }

    public function delete($id) {
        $this->stdDelete($id);
    }
}