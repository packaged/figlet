<?php
namespace Packaged\Figlet;

class Figlet
{
  protected $_currentFont;
  protected $_fontFile;
  protected $_signature;
  protected $_hardblank;
  protected $_height;
  protected $_baseline;
  protected $_maxWidth;
  protected $_defaultSmush;
  protected $_commentLines;
  protected $_rightToLeft;
  protected $_fontSmush;
  protected $_loaded = false;
  protected $_defaultFont;

  /**
   * @param null   $loadFont
   * @param string $defaultFont
   */
  public function __construct($loadFont = null, $defaultFont = 'standard')
  {
    $this->_defaultFont = $defaultFont;
    $this->_currentFont = $defaultFont;

    if($loadFont !== null)
    {
      $this->loadFont($loadFont);
    }
  }

  /**
   * @return bool
   */
  public function isFontLoaded()
  {
    return (bool)$this->_loaded;
  }

  /**
   * @return string current loaded font
   */
  public function currentFont()
  {
    return $this->_currentFont;
  }

  /**
   * Load a font which has been bundled with this package (found in .fonts)
   *
   * @param        $name
   * @param null   $fontDir
   * @param string $ext
   */
  public function loadFont($name, $fontDir = null, $ext = '.flf')
  {
    if($fontDir === null)
    {
      $fontDir = __DIR__ . DIRECTORY_SEPARATOR . '.fonts' . DIRECTORY_SEPARATOR;
    }
    $this->_currentFont = $name;
    $this->loadFontFromPath($fontDir . $name . $ext, false);
  }

  /**
   * Load a custom figlet font
   *
   * @param $fontFile   string full path to figlet font file
   * @param $setCurrent bool to set the current font to the full path
   *
   * @throws \Exception
   */
  public function loadFontFromPath($fontFile, $setCurrent = true)
  {
    $this->_loaded = false;
    if(!file_exists($fontFile))
    {
      throw new \Exception(
        "Could not load figlet font '" .
        ($setCurrent ? $fontFile : $this->_currentFont) . "'",
        404
      );
    }

    $this->_fontFile = file($fontFile);

    $definitions = sscanf(
      $this->_fontFile[0],
      '%5s%c %d %*d %d %d %d %d %d',
      $this->_signature,
      $this->_hardblank,
      $this->_height,
      $this->_maxWidth,
      $this->_defaultSmush,
      $this->_commentLines,
      $this->_rightToLeft,
      $this->_fontSmush
    );

    if($this->_signature != "flf2a" || $definitions < 5)
    {
      throw new \Exception("Invalid figlet font file provided", 500);
    }

    if($setCurrent)
    {
      $this->_currentFont = $fontFile;
    }

    $this->_loaded = true;
  }

  /**
   * Get a single character from the loaded font
   *
   * @param $character
   *
   * @return array
   */
  public function getCharacter($character)
  {
    if(!$this->_loaded)
    {
      $this->loadFont($this->_defaultFont);
    }

    $final     = array();
    $offset    = ((ord($character) - 32) * $this->_height);
    $startLine = $this->_commentLines + 1 + $offset;
    $lines     = array_slice($this->_fontFile, $startLine, $this->_height);
    foreach($lines as $line)
    {
      $final[] = str_replace(
        array('@', $this->_hardblank, "\n"),
        array('', ' ', ''),
        $line
      );
    }
    return $final;
  }

  /**
   * Create a figlet string
   *
   * @param $string string text to generate
   *
   * @return string output content with new lines
   */
  public function render($string)
  {
    $out        = "";
    $characters = str_split($string);
    $chars      = array();
    foreach($characters as $char)
    {
      $chars[] = $this->getCharacter($char);
    }
    for($line = 0; $line < $this->_height; $line++)
    {
      foreach($chars as $charLines)
      {
        $out .= $charLines[$line];
      }
      $out .= "\n";
    }
    return $out;
  }

  /**
   * Create a figlet output string
   *
   * @param      $string
   * @param null $font
   *
   * @return string
   */
  public static function create($string, $font = null)
  {
    $figlet = new Figlet($font);
    return $figlet->render($string);
  }
}
