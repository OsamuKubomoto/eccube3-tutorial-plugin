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
        if (!$schema->hasTable('plg_tutorial')) {
            $schema->dropTable('plg_tutorial');
        }
        $app = \Eccube\Application::getInstance();
        $em = $app['orm.em'];
        $qb = $em->createQueryBuilder();
        $qb->select('pl')
            ->from('\Eccube\Entity\PageLayout', 'pl')
            ->where('pl.url = :Url')
            ->setParameter('Url', 'plugin_tutorial_crud');
        $res = $qb->getQuery()->getResult();
        if(count($res) > 0){
            $qb->delete('p')
                ->from('\Eccube\Entity\PageLayout', 'p')
                ->where('p.url = :Url')
                ->setParameter('Url', 'plugin_tutorial_crud');
            $res = $qb->getQuery()->execute();
        }
    }
}
