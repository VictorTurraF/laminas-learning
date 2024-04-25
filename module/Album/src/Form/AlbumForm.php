<?php

namespace Album\Form;

use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;

class AlbumForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('album');

        $this->add([
            'name' => 'id',
            'type' => Hidden::class,
        ]);
        $this->add([
            'name' => 'title',
            'type' => Text::class,
            'options' => [
                'label' => 'Title',
            ],
        ]);
        $this->add([
            'name' => 'artist',
            'type' => Text::class,
            'options' => [
                'label' => 'Artist',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => Submit::class,
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}
