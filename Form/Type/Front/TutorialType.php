<?php
/*
 * This file is part of the Tutorial
 *
 * Copyright (C) 2016 kubomoto
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Tutorial\Form\Type\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TutorialType extends AbstractType
{
    public $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $post_type = [
            '1' => '質問',
            '2' => '提案',
        ];

        $builder->add(
            'reason',
            'choice',
            [
                'label' => '投稿種別',
                'required' => true,
                'choices' => $post_type,
                'mapped' => true,
                'expanded' => false,
                'multiple' => false,
            ]
        )
        ->add(
            'name',
            'text',
            [
                'label' => '投稿者ハンドルネーム',
                'required' => true,
                'mapped' => true,
                'constraints' => [
                    new Assert\Regex(
                        [
                            'pattern' => "/^[\da-zA-Z]+$/u",
                            'message' => '半角英数字で入力してください'
                        ]
                    ),
                ],
            ]
        )
        ->add(
            'title',
            'text',
            [
                'label' => '投稿のタイトル',
                'required' => false,
                'mapped' => true,
                'constraints' => [
                    new Assert\Length(
                        [
                            'min' => '0',
                            'max' => '100',
                            'maxMessage' => '100文字以内で入力してください',
                        ]
                    ),
                ]
            ]
        )
        ->add(
            'notes',
            'textarea',
            array(
                'label' => '内容',
                'required' => false,
                'mapped' => true,
                'empty_data' => null,
                'attr' => [
                    'style' => 'height:100px;',
                ],
            )
        )
        ->add(
            'save',
            'submit',
            [
                'label' => 'この内容で保存する',
            ]
        )
        ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tutorial';
    }
}
