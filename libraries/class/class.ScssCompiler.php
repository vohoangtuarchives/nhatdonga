<?php

class ScssCompiler
{
	private $scssPath = '';
	private $cssPath = '';
	private $compiler = null;
	private $sourceMap = true;
	private $available = false;

	public function __construct()
	{
		// Kiểm tra xem thư viện scssphp có được cài đặt không
		if (!class_exists('ScssPhp\ScssPhp\Compiler')) {
			$this->available = false;
			return;
		}

		$this->available = true;
		$this->scssPath = ROOT . 'assets/scss/';
		$this->cssPath = ROOT . 'assets/css/';
		
		$compilerClass = 'ScssPhp\ScssPhp\Compiler';
		$outputStyleClass = 'ScssPhp\ScssPhp\OutputStyle';
		
		$this->compiler = new $compilerClass();
		
		// Cấu hình compiler
		$this->compiler->setImportPaths([$this->scssPath]);
		$this->compiler->setOutputStyle($outputStyleClass::EXPANDED);
		
		// Cấu hình source map
		if ($this->sourceMap) {
			$this->compiler->setSourceMap($compilerClass::SOURCE_MAP_FILE);
		} else {
			$this->compiler->setSourceMap($compilerClass::SOURCE_MAP_NONE);
		}
	}

	/**
	 * Compile file SCSS sang CSS
	 * 
	 * @param string $scssFile Đường dẫn file SCSS (từ assets/scss/)
	 * @param string $cssFile Đường dẫn file CSS output (từ assets/css/)
	 * @return bool True nếu thành công, False nếu có lỗi
	 */
	public function compile($scssFile, $cssFile = null)
	{
		if (!$this->available) {
			throw new Exception("Thư viện scssphp chưa được cài đặt. Vui lòng chạy: composer require scssphp/scssphp");
		}

		try {
			// Nếu không chỉ định file CSS output, tự động tạo tên
			if ($cssFile === null) {
				$cssFile = str_replace('.scss', '.css', $scssFile);
			}

			$scssFilePath = $this->scssPath . $scssFile;
			$cssFilePath = $this->cssPath . $cssFile;
			$mapFilePath = $cssFilePath . '.map';

			// Kiểm tra file SCSS có tồn tại không
			if (!file_exists($scssFilePath)) {
				throw new Exception("File SCSS không tồn tại: {$scssFilePath}");
			}

			// Kiểm tra thư mục CSS có tồn tại không
			$cssDir = dirname($cssFilePath);
			if (!is_dir($cssDir)) {
				if (!mkdir($cssDir, 0777, true)) {
					throw new Exception("Không thể tạo thư mục: {$cssDir}");
				}
			}

			// Đọc nội dung SCSS
			$scssContent = file_get_contents($scssFilePath);

			// Compile SCSS sang CSS
			$result = $this->compiler->compileString($scssContent, $scssFilePath);
			$cssContent = $result->getCss();

			// Ghi file CSS
			if (file_put_contents($cssFilePath, $cssContent) === false) {
				throw new Exception("Không thể ghi file CSS: {$cssFilePath}");
			}

			// Source map được embed tự động trong CSS bởi scssphp khi SOURCE_MAP_FILE được bật

			return true;

		} catch (Exception $e) {
			error_log("SCSS Compile Error: " . $e->getMessage());
			return false;
		}
	}

	/**
	 * Compile file main.scss (file chính)
	 * 
	 * @return bool
	 */
	public function compileMain()
	{
		return $this->compile('main.scss', 'main.css');
	}

	/**
	 * Kiểm tra file SCSS có cần compile lại không
	 * (so sánh thời gian modify)
	 * 
	 * @param string $scssFile
	 * @param string $cssFile
	 * @return bool
	 */
	public function needsCompile($scssFile, $cssFile = null)
	{
		if ($cssFile === null) {
			$cssFile = str_replace('.scss', '.css', $scssFile);
		}

		$scssFilePath = $this->scssPath . $scssFile;
		$cssFilePath = $this->cssPath . $cssFile;

		// Nếu file CSS không tồn tại, cần compile
		if (!file_exists($cssFilePath)) {
			return true;
		}

		// Nếu file SCSS mới hơn file CSS, cần compile lại
		if (file_exists($scssFilePath)) {
			$scssTime = filemtime($scssFilePath);
			$cssTime = filemtime($cssFilePath);
			
			return $scssTime > $cssTime;
		}

		return false;
	}

	/**
	 * Compile tự động nếu cần
	 * 
	 * @param string $scssFile
	 * @param string $cssFile
	 * @return bool
	 */
	public function autoCompile($scssFile, $cssFile = null)
	{
		if ($this->needsCompile($scssFile, $cssFile)) {
			return $this->compile($scssFile, $cssFile);
		}
		return true;
	}

	/**
	 * Bật/tắt source map
	 * 
	 * @param bool $enable
	 */
	public function setSourceMap($enable)
	{
		if (!$this->available) {
			return;
		}

		$this->sourceMap = $enable;
		$compilerClass = 'ScssPhp\ScssPhp\Compiler';
		
		if ($enable) {
			$this->compiler->setSourceMap($compilerClass::SOURCE_MAP_FILE);
		} else {
			$this->compiler->setSourceMap($compilerClass::SOURCE_MAP_NONE);
		}
	}

	/**
	 * Thiết lập output style
	 * 
	 * @param string $style 'expanded', 'compressed', 'compact', 'nested'
	 */
	public function setOutputStyle($style = 'expanded')
	{
		if (!$this->available) {
			return;
		}

		$outputStyleClass = 'ScssPhp\ScssPhp\OutputStyle';
		
		switch (strtolower($style)) {
			case 'compressed':
				$this->compiler->setOutputStyle($outputStyleClass::COMPRESSED);
				break;
			case 'compact':
				$this->compiler->setOutputStyle($outputStyleClass::COMPACT);
				break;
			case 'nested':
				$this->compiler->setOutputStyle($outputStyleClass::NESTED);
				break;
			case 'expanded':
			default:
				$this->compiler->setOutputStyle($outputStyleClass::EXPANDED);
				break;
		}
	}

	/**
	 * Kiểm tra xem SCSS compiler có sẵn sàng không
	 * 
	 * @return bool
	 */
	public function isAvailable()
	{
		return $this->available;
	}
}

