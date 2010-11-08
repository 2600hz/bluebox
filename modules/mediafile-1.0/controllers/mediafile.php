<?php defined('SYSPATH') or die('No direct access allowed.');

class MediaFile_Controller extends Bluebox_Controller
{
    protected $baseModel = 'MediaFile';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Media Files'
            )
        );

        // Add the base model columns to the grid
        $grid->add('mediafile_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Name');
        $grid->add('type', 'Type');

        if (!kohana::config('mediafile.hide_rate_folders'))
        {
            $grid->add('rates', 'Rate');
            $grid->add('channels', 'Channels');
            $grid->add('length', 'Length');
        }
        
        $grid->add('updated_at', 'Updated');

        // Add the actions to the grid
        $grid->addAction('mediafile/edit', 'Edit', array(
                'arguments' => 'mediafile_id',
                'attributes' => array('class' => 'qtipAjaxForm')
            )
        );
        $grid->addAction('mediafile/delete', 'Delete', array(
                'arguments' => 'mediafile_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        if (kohana::config('mediafile.hide_rate_folders'))
        {
            $q = $this->grid->getQuery()->GroupBy('file');
        }
        else
        {
            $q = $this->grid->getQuery();
        }

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce(array('doctrine_query' => $q));
    }

    /**
     * This generic delete function will remove entries of $baseModel
     */
    public function delete($id = NULL)
    {
        $this->template->content = new View('mediafile/delete');

        $this->view->title = 'Delete Media File';

        $this->loadBaseModel($id);

        if ($action = $this->submitted(array('submitString' => 'delete')))
        {
            Event::run('bluebox.deleteOnSubmit', $action);

            if (($action == self::SUBMIT_CONFIRM))
            {
                $success = TRUE;

                if(kohana::config('mediafile.hide_rate_folders', FALSE) AND !empty($_POST['delete_media_files']))
                {
                    foreach ($_POST['delete_media_files'] as $mediafile_id)
                    {
                        $mediafile = Doctrine::getTable('MediaFile')->find($mediafile_id);

                        if (!$mediafile)
                        {
                            continue;
                        }

                        if (!$this->formDelete($mediafile))
                        {
                            $success = FALSE;
                            
                            break;
                        }
                    }
                }
                else
                {
                    $success = $this->formDelete($this->mediafile);
                }
                
                if ($success)
                {
                    $this->returnQtipAjaxForm(NULL);

                    url::redirect(Router_Core::$controller);
                }
            }
            else if ($action == self::SUBMIT_DENY)
            {
                $this->exitQtipAjaxForm();

                url::redirect(Router_Core::$controller);
            }
        }

        $this->view->set_global('mediafile', $this->mediafile);

        Event::run('bluebox.prepare_delete_view', $this->view);

        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    protected function prepareUpdateView($baseModel = NULL)
    {
        $sampleRates = array();

        if (kohana::config('mediafile.hide_rate_folders'))
        {
            $this->view->sample_rates = $this->mediafile->get_resampled();
        }
        else 
        {
            $this->view->sample_rates = array($this->mediafile);
        }

        parent::prepareUpdateView($baseModel);
    }

    protected function save_prepare(&$mediafile)
    {
        if (!strcasecmp(Router::$method, 'create'))
        {
            if (($error = $mediafile->prepare_upload()))
            {
                Bluebox_Controller::$validation->add_error('mediafile[upload]', $error);

                throw new Bluebox_Exception('Upload error ' .$error);
            }
        }

        parent::save_prepare($object);
    }

    public function description($mediafile_id)
    {
        $mediafile = Doctrine::getTable('MediaFile')->find($mediafile_id);

        if (isset($mediafile['description']))
        {
            echo $mediafile['description'];
        }
        
        flush();

        die();
    }

    public function download($mediafile_id, $stream = FALSE)
    {
        $mediafile = Doctrine::getTable('MediaFile')->find($mediafile_id);

        if (!$mediafile)
        {
            die();
        }

        header('Expires: 0');

        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        header('Cache-Control: public');

        header(sprintf('Content-type: %s', $mediafile->contentType()));

        header('Content-Transfer-Encoding: binary');

        if (!$stream)
        {
            // Include filename and attachment disposition only if we don't want to stream
            header(sprintf('Content-Disposition: attachment; filename="%s"', $mediafile->downloadName()));
        }

        readfile($mediafile->filepath(TRUE));

        flush();
        
        die();
    }

    public function qtipAjaxReturn($data)
    {
        if ($data instanceof MediaFile)
        {
            $id = $data['mediafile_id'];

            $catalog = MediaFile::catalog();

            $value = $catalog[$id];

            jquery::addPlugin('growl');

            Session::instance()->get_once('bluebox_message', array());

            javascript::codeBlock('
                $(\'#media_widget_file_list\')
                    .prepend($("<option></option>")
                    .attr("selected", "selected")
                    .attr("value", "' .$id .'")
                    .text("' .$value .'"))
                    .trigger("change");
            ');
        }

        parent::qtipAjaxReturn($data);
    }
}