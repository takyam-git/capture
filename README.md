#takyam/capture
PhantomJSを用いてスクリーンショットを取るPHP用のライブラリです。
中でphantomjsコマンドをExecしてるだけの簡単仕様です。

#install
composer.json に以下を追加

```json
{
	"require-dev": {
		"takyam/capture": "dev-master"
	}
}
```

あとはいつもどおり php composer.phar install/update でいけるはず

## Usage

```php
<?php
$capture = new Capture('http://www.yahoo.co.jp');
$capture->set_width(1920, 1280);
$capture->save('/tmp/hoge/fuga.png');
```