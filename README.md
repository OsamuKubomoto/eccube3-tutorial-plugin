# README

EC-CUBE3のチュートリアルを参考にプラグイン化してみました。

## インストール手順

app/Pluginディレクトリ直下で下記コマンドを実行

    $ git clone https://github.com/OsamuKubomoto/eccube3-tutorial-plugin.git　Tutorial

プラグインをインストール

    $ app/console plugin:develop install --code=Tutorial

プラグインを有効化

    $ app/console plugin:develop enable --code=Tutorial

## 注意

なぜかコントローラー名にCrudとつけるとエラーで先にすすめなかったので、しかたなくTutorialに名前を変えて作りました。
ですので、チュートリアル通りには作ってませんが一応動作はできてます。