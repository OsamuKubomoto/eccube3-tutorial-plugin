<?php

/*
 * This file is part of the Tutorial
 *
 * Copyright (C) 2016 kubomoto
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Tutorial\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TutorialConfigType extends AbstractType
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $builder
        //     ->add('name', 'text', array(
        //         'constraints' => array(
        //             new Assert\NotBlank(),
        //         ),
        //     ));
    }

    public function getName()
    {
        return 'tutorial_config';
    }
}
