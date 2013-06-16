<?php
class Capture
{
	const BIN_PATH_WINDOWS = 'bin\\win\\phantomjs.exe';
	const BIN_PATH_MAC = 'bin/mac/phantomjs';
	const BIN_PATH_LINUX_I686 = 'bin/linux/i686/phantomjs';
	const BIN_PATH_LINUX_X86_64 = 'bin/linux/x86_64/phantomjs';
	const SCRIPT_NAME = 'capture.coffee';

	protected $phantom_js_root_path = null;
	protected $bin = null;

	protected $url = null;

	protected $screen_width = 1280;
	protected $screen_height = 1024;

	/**
	 * コンストラクタ
	 */
	public function __construct($url = null)
	{
		if (!is_null($url)) {
			$this->set_url($url);
		}

		//phantom_jsのルートディレクトリへのパス
		$this->phantom_js_root_path = __DIR__ . '/../../lib/phantomjs';

		//OS毎にバイナリのパスを切り替える
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { //Win
			$this->bin = static::BIN_PATH_WINDOWS;
		} elseif (strtoupper(PHP_OS) === 'DARWIN') { //OS X
			$this->bin = static::BIN_PATH_MAC;
		} elseif (strtoupper(PHP_OS) === 'LINUX') { //Linux
			if (preg_match('/x86_64/', php_uname())) { //x86_64
				$this->bin = static::BIN_PATH_LINUX_X86_64;
			} else { //i686
				$this->bin = static::BIN_PATH_LINUX_I686;
			}
		} else { //非対応
			throw new OutOfBoundsException();
		}
	}

	/**
	 * キャプチャ対象のURLをセットする
	 * @param $url
	 * @throws InvalidArgumentException
	 * @return $this
	 */
	public function set_url($url)
	{
		if (filter_var($url, FILTER_VALIDATE_URL) === false) {
			throw new InvalidArgumentException();
		}
		$this->url = $url;
		return $this;
	}

	/**
	 * スクリーンサイズをセットする
	 * @param int $width
	 * @param int $height
	 * @return $this
	 * @throws InvalidArgumentException
	 */
	public function set_screen_size($width = null, $height = null)
	{
		if (!is_null($width)) {
			$this->set_screen_width($width);
		}
		if (!is_null($height)) {
			$this->set_screen_height($height);
		}
		return $this;
	}

	/**
	 * スクリーンサイズの幅をセットする
	 * @param int $width
	 * @return $this
	 * @throws InvalidArgumentException
	 */
	public function set_screen_width($width)
	{
		if (!is_numeric($width)) {
			throw new InvalidArgumentException();
		}
		$this->screen_width = (int)$width;
		return $this;
	}

	/**
	 * スクリーンサイズの高さをセットする
	 * @param int $height
	 * @return $this
	 * @throws InvalidArgumentException
	 */
	public function set_screen_height($height)
	{
		if (!is_numeric($height)) {
			throw new InvalidArgumentException();
		}
		$this->screen_height = (int)$height;
		return $this;
	}

	/**
	 * 指定したURLにキャプチャを保存する
	 * @param null|string $save_path
	 * @throws OutOfBoundsException
	 * @throws ErrorException
	 * @return null|string
	 */
	public function save($save_path = null)
	{
		//URLが存在しない場合はエラー
		if(is_null($this->url)){
			throw new OutOfBoundsException();
		}

		//パスの指定が無い場合は適当なディレクトリに設置
		if (is_null($save_path)) {
			$save_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'takyam_capture_' . uniqid(md5($this->url)) . '.png';
		}

		//phantomjsを叩くコマンドを生成
		$command = implode(' ', array(
			$this->phantom_js_root_path . DIRECTORY_SEPARATOR . $this->bin,
			$this->phantom_js_root_path . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . static::SCRIPT_NAME,
			$this->url,
			$save_path,
			$this->screen_width,
			$this->screen_height,
		));

		//コマンドを実行
		exec($command, $std_out, $return_code);

		//エラーでこけたらException
		if($return_code !== 0){
			throw new ErrorException();
		}

		//保存に成功したら保存したパスを返す
		return $save_path;
	}
}