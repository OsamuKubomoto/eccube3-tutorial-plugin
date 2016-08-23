<?php

/*
 * This file is part of the Tutorial
 *
 * Copyright (C) 2016 kubomoto
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Tutorial\ServiceProvider;

use Eccube\Application;
use Plugin\Tutorial\Form\Type\TutorialConfigType;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;


class TutorialServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // プラグイン用設定画面
        // $app->match('/' . $app['config']['admin_route'] . '/plugin/Tutorial/config', 'Plugin\Tutorial\Controller\ConfigController::index')->bind('plugin_Tutorial_config');

        // 独自コントローラ
        $app->match('/tutorial/crud', '\Plugin\Tutorial\Controller\TutorialController::index')->bind('plugin_tutorial_crud');
        $app->match('/tutorial/crud/edit/{id}', '\Plugin\Tutorial\Controller\TutorialController::edit')->bind('plugin_tutorial_crud_edit')->assert('id', '^[1-9]+[0]?$'); // ★ルーティングとURLパラメーターの設定を追記
        $app->delete('/tutorial/crud/delete/{id}', '\Plugin\Tutorial\Controller\TutorialController::delete')->bind('plugin_tutorial_crud_delete')->assert('id', '^[1-9]+[0]?$'); // ★ルーティングとURLパラメーターの設定を追記

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new TutorialConfigType($app);
            $types[] = new \Plugin\Tutorial\Form\Type\Front\TutorialType($app);
            return $types;
        }));

        // Form Extension

        // Repository
        $app['eccube.plugin.tutorial.repository.tutorial'] = $app->share(function () use ($app) { //★この行を追加
            return $app['orm.em']->getRepository('Plugin\Tutorial\Entity\Tutorial');
        });

        // Service
    }

    public function boot(BaseApplication $app)
    {
    }
}
