<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Application;
use Eccube\Entity\PageLayout;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160819173130 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $app = Application::getInstance(); // ★エンティティマネージャーの取得のためにApplicationを取得します
        $em = $app['orm.em']; // ★エンティティマネージャーを取得します

        if (!$schema->hasTable('plg_tutorial')) {
            $entities = [
                'Plugin\Tutorial\Entity\Tutorial' // ★テーブル作成を行うエンティティを指定します
            ];
            $classes = [];

            foreach ($entities as $entity) {
                $classes[] = $em->getMetadataFactory()->getMetadataFor($entity); // ★エンティティからカラム情報を取得します。
            }

            $tool = new SchemaTool($em); // ★テーブル生成のためにスキーマツールをインスタンス化します
            $tool->createSchema($classes); // ★テーブルを生成します
        }

        $qb = $em->createQueryBuilder(); // ★クエリビルダーを取得

        $qb->select('pl') // ★該当情報が登録済みかどうかを確認するためのSQLを構築
            ->from('\Eccube\Entity\PageLayout', 'pl')
            ->where('pl.url = :Url')
            ->setParameter('Url', 'plugin_tutorial_crud');

        $res = $qb->getQuery()->getResult(); // ★SQL結果を取得

        if(count($res) < 1){ // ★結果がなければ、以下情報を書き込み
            $PageLayout = new PageLayout(); // ★登録するためのエンティティをインスタンス化
            $DeviceType = $em->getRepository('\Eccube\Entity\Master\DeviceType')->find(10); // ★格納するデバイスタイプをDBから取得
            $PageLayout->setDeviceType($DeviceType); // ★以下登録エンティティに必要情報を格納
            $PageLayout->setName('チュートリアル/CRUD');
            $PageLayout->setUrl('plugin_tutorial_crud');
            $PageLayout->setFileName('Tutorial/crud_top');
            $PageLayout->setEditFlg(2);

            $em->persist($PageLayout); // ★エンティティマネージャーの管理化に登録エンティティ追加
            $em->flush($PageLayout); // ★登録エンティティを対象に保存
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        if (!$schema->hasTable('plg_tutorial')) {
            $schema->dropTable('plg_tutorial');
        }

        $app = \Eccube\Application::getInstance(); // ★EC-CUBEのアプリケーションクラスを取得
        $em = $app['orm.em']; // ★エンティティマネージャーを取得
        $qb = $em->createQueryBuilder(); // ★クエリビルダーを取得

        $qb->select('pl') // ★該当画面情報が保存されているかを確認するためのSQLを生成
            ->from('\Eccube\Entity\PageLayout', 'pl')
            ->where('pl.url = :Url')
            ->setParameter('Url', 'plugin_tutorial_crud');

        $res = $Point = $qb->getQuery()->getResult(); // ★情報取得

        if(count($res) > 0){ // ★該当情報が保存されていれば、削除処理
            $qb->delete() // ★該当画面情報を削除するための、SQLを生成
                ->from('\Eccube\Entity\PageLayout', 'pl')
                ->where('pl.url = :Url')
                ->setParamater('Url', 'plugin_tutorial_crud');
            $res = $Point = $qb->getQuery()->execute(); // ★削除処理実行
        }
    }
}
