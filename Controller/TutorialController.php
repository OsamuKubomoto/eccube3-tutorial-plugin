<?php

/*
 * This file is part of the Tutorial
 *
 * Copyright (C) 2016 kubomoto
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Tutorial\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TutorialController extends AbstractController
{
    const LIST_ORDER = 'desc'; // ★ソート順切り替えに定数を宣言

    /**
     * index
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

        // Tutorialエンティティをインスタンス化
        $Tutorial = new \Plugin\Tutorial\Entity\Tutorial(); // ★前章で作成した、dtb_tutorial用のエンティティ(データモデルオブジェクト)をインスタンス化します。

        $builder = $app['form.factory']->createBuilder('tutorial', $Tutorial);

        $form = $builder->getForm();

        $defaultForm = clone $form; // ★処理成功時の画面で保持値をクリアするために、空のエンティティを格納したフォームを一時保存

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $saveStatus = $app['eccube.plugin.tutorial.repository.tutorial']->save($Tutorial); // ★サブミット値をエンティティマネージャーではなくレポジトリのメソッドに渡す

            if ($saveStatus) {
                $app->addSuccess('データーが保存されました'); // ★メッセージをフラッシュメッセージに変更
                $form = $defaultForm; // ★フォームオブジェクトを一時保存しておいた空エンティティのフォームオブジェクトで上書き
            } else {
                $app->addError('データーベースの保存中にエラーが発生しました'); // ★登録後一覧情報が取得出来ない際は、エラーメッセージを表示
            }
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $app->addError('入力内容をご確認ください'); // ★メッセージをフラッシュメッセージに変更
        }

        $crudList = $app['eccube.plugin.tutorial.repository.tutorial']->getAllDataSortByUpdateDate(self::LIST_ORDER); // ★レポジトリに作成したメソッドで更新日時降順の全レコード取得

        return $app->render(
            'Tutorial/Resource/template/default/Tutorial/crud_top.twig',
            [
                'forms' => $form->createView(),
                'crudList' => $crudList, // ★データーベース取得値をビューに渡す
            ]
        );
    }

    /**
     * edit
     *
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Application $app, Request $request, $id) // ★引数に「$id(URLパラメーター)」を追記
    {
        $Tutorial = $app['eccube.plugin.tutorial.repository.tutorial']->getDataById($id); // ★該当レポジトリから「id」をキーに編集対象レコードを取得

        if (!$Tutorial) { // ★エラー判定、データーが一件もない場合は登録画面へ遷移
            return $app->redirect($app->url('plugin_tutorial_crud'));
        } 

        $builder = $app['form.factory']->createBuilder('tutorial', $Tutorial); // ★取得エンティティをもとに、フォームビルダーを生成
        $builder->remove('save'); // ★フォームタイプに設定した項目「save」ボタンを削除
        $builder->add( // ★ビルダーに編集確定用ボタンを追加
            'update',
            'submit',
            array(
                'label' => '編集を確定する',
                'attr' => array(
                    'style' => 'float:left;',
                )
            )
        )
        ->add( // ★ビルダーに「戻る」ボタンを追加
            'back',
            'button',
            array(
                'label' => '戻る',
                'attr' => array(
                    'style' => 'float:left;',
                    'onClick' => 'javascript:history.back();'
                )
            )
        );

        $form = $builder->getForm(); // ★再構築したフォームビルダーからフォームを取得

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $saveStatus = $app['eccube.plugin.tutorial.repository.tutorial']->save($Tutorial); // ★サブミットデーターの更新(登録処理と同内容)

            if ($saveStatus) {
                $app->addSuccess('データーが保存されました');
                return $app->redirect($app->url('plugin_tutorial_crud')); // ★更新成功時は、登録画面へ遷移
            } else {
                $app->addError('データーベースの保存中にエラーが発生いたしました');
            }
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $app->addError('入力内容をご確認ください');
        }

        return $app->render(
            'Tutorial/Resource/template/default/Tutorial/crud_edit.twig',
            array(
                'forms' => $form->createView(),
                'crud' => $Tutorial,
            )
        );
    }

    /**
     * 削除画面
     * 引数を元に該当レコードを削除
     * 問題がなければ、登録画面に遷移
     *
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $Tutorial = $app['eccube.plugin.tutorial.repository.tutorial']->find($id);

       if (is_null($Tutorial)) {
            $app->addError('該当IDのデーターが見つかりません');
            return $app->redirect($app->url('plugin_tutorial_crud'));
       }

        $app['orm.em']->remove($Tutorial);
        $app['orm.em']->flush($Tutorial);

        return $app->redirect($app->url('plugin_tutorial_crud'));
     }
}
