<?php

namespace Album\Controller;

use Album\Model\AlbumTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Album\Form\AlbumForm;
use Album\Model\Album;

class AlbumController extends AbstractActionController
{
    // Add this property:
    private $table;

    // Add this constructor:
    public function __construct(AlbumTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        return new ViewModel([
            'albums' => $this->table->fetchAll(),
        ]);
    }

    public function addAction()
    {
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if (!$request->isPost()) {
            return ['form' => $form];
        }

        $album = new Album();
        $form->setInputFilter($album->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form];
        }

        $album->exchangeArray($form->getData());
        $this->table->saveAlbum($album);

        return $this->redirect()->toRoute('album');
    }

    public function editAction()
    {
        // Step 1: Retrieve the 'id' parameter from the route
        $id = (int) $this->params()->fromRoute('id', 0);

        // Step 2: If the 'id' is 0, redirect to the 'add' action
        if (0 === $id) {
            return $this->redirect()->toRoute('album', ['action' => 'add']);
        }

        // Step 3: Retrieve the album with the specified 'id'
        try {
            $album = $this->table->getAlbum($id);
        } catch (\Exception $e) {
            // Step 4: If album not found, redirect to the 'index' action
            return $this->redirect()->toRoute('album', ['action' => 'index']);
        }

        // Step 5: Create a new AlbumForm instance
        $form = new AlbumForm();

        // Step 6: Bind the album data to the form
        $form->bind($album);

        // Step 7: Set the value of the submit button to 'Edit'
        $form->get('submit')->setAttribute('value', 'Edit');

        // Step 8: Get the current request
        $request = $this->getRequest();

        // Step 9: Prepare view data with 'id' and 'form'
        $viewData = ['id' => $id, 'form' => $form];

        // Step 10: If the request is not a POST request, return the view data
        if (!$request->isPost()) {
            return $viewData;
        }

        // Step 11: Set input filter and data for the form
        $form->setInputFilter($album->getInputFilter());
        $form->setData($request->getPost());

        // Step 12: If the form is not valid, return the view data
        if (!$form->isValid()) {
            return $viewData;
        }

        // Step 13: Try to save the edited album
        try {
            $this->table->saveAlbum($album);
        } catch (\Exception $e) {
            // Handle any exceptions here
        }

        // Step 14: Redirect to the 'index' action
        return $this->redirect()->toRoute('album', ['action' => 'index']);
    }


    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteAlbum($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }

        return [
            'id'    => $id,
            'album' => $this->table->getAlbum($id),
        ];
    }
}
