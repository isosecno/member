<?php
/**
 * IT Seniorene - Html class
 *
 * Use of templates to separate HTML from PHP code
 *
 * @author	Terje Bakken
 * @link	https://itseniorene.no
 */
class ISOSEC_Html
{
	/**
	 * Whether to remove unresolved symols from template
	 *
	 * @var bool
	 */
	public $remove_unresolved = true;

    private $dir;
	private $common = null;

/**
 * Class constructor
 *
 * @param string  Path to html directory
 * @return void
 */
    public function __construct($dir = null) {
        $this->dir = $dir;
    }

/**
 * Get template file from file system
 *
 * @param string  Name of file to load - don't include path
 * @param string  Tag to select from file - when NULL, whole file is returned
 * @return string Html template
 */
    public function getTemplate($file, $tag = null)
    {
        $path = $this->dir . '/' . $file;
        $templ = $this->getFile($path);
		if ($tag === null) {
			return $templ;
		}
        return $this->getTemplatePart($templ, $tag, false);
    }

/**
 * Get a template part from a loaded template
 *
 * @param string  Template where the part is taken from
 * @param string  Tag to select
 * @param bool    Whether to remove seleccted tag area from the template
 * @param bool    Whether to replace tag area with a symbolic variable 															.
 * @return string Html template
 */
    public function getTemplatePart(&$templ, $tag, $cut = true, $placeholder = true)
    {
        $pattern = '/^<!--[ ]*#(start|end) ' . $tag . '[ ]+-->.*(\r\n|\n|\r)/m';
        $match;
        preg_match_all($pattern, $templ, $match, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
         if (count($match) != 2 || $match[0][1][0] != 'start' || $match[1][1][0] != 'end') {
            $this->errorExit("Incomplete or missing tag \"$tag\"", 'ITSW_html->getTemplatePart');
         }
         $start = $match[0][2][1] + 1;
         $end = $match[1][0][1];
         $res = substr($templ, $start, $end - $start);
         if ($cut) {
             $end = $match[0][0][1];
             $top = substr($templ, 0, $end);
             $start = $match[1][2][1];
             $bottom = substr($templ, $start);
             if ($placeholder) {
                $templ = $top . '{' . $tag . '}' . $bottom;
             } else {
                $templ = $top . $bottom;
             }
         }
         return $res;
    }

/**
 * Replace symbols in html template
 *
 * Consider using replace() instead - takes multiple dictionaries
 *
 * @param array   Dictionary holding values to replace symbols
 * @param string  Html template
 * @param bool    Whether to remove unresolved symols from template
 * @return string Html with resolved symbols
 */
    public function replaceSymbols($dict, $html, $remove_unresolved = true) {
		$this->remove_unresolved = $remove_unresolved;
		return $this->replace($html, $dict);
    }

/**
 * Replace symbols in html template.
 *
 * @param string	Html template.
 * @param mixed		One or more dictionaries holding values to replace symbols
 * @return string 	Html with resolved symbols
 */
    public function replace(string $html, ...$dictionaries)
	{
		$pos = 0;    // when copying from html doc
		$res = '';
		$matches;
		preg_match_all('/{[\w-]+}/', $html, $matches, PREG_OFFSET_CAPTURE);
		foreach($matches[0] as $match) {
			$res .= substr($html, $pos, $match[1] - $pos);
			$pos = $match[1] + strlen($match[0]);
			$name = trim($match[0], '{}');
			$val = $this->remove_unresolved ? '' : $match[0];
			foreach ($dictionaries as $dict) {
				if (empty($dict)) continue;
				if (gettype($dict) == 'object') {
					if (property_exists($dict, $name)) {
						$val = $dict->$name;
						break;
					}
				} else {
					if (isset($dict[$name])) {
						$val = $dict[$name];
						break;
					}
				}
			}
			if (!is_string($val)) {
				$val = strval($val);   // Make string of non string variables
			}
			$res .= $val;
			// Remove double linefeeds
			if (substr($val, -2) == "\r\n" && substr($html, $pos, 2) == "\r\n") {
				$pos += 2;
			}
			if (substr($val, -1) == "\n" && substr($html, $pos, 1) == "\n") {
				$pos += 1;
			}
		}
		$res .= substr($html, $pos);
        return $res;
    }
/**
 * Gives you a template for a commonly used html part
 *
 * @param string	tag name in common.html.
 * @return string 	common html part
 */
    public function getCommon($tag)
	{
		if (empty($this->common)) {
			$this->common = $this->getTemplate('common.html');
		}
		$res = $this->getTemplatePart($this->common, $tag, false, false);
		return $res;
	}

    private function getFile($path)
    {
        if (!file_exists($path)) {
            $this->errorExit("Missing file:  $path", 'ITSW_html->getFile');
        }
        $res = file_get_contents($path);
        if ($res === false) {
            $this->errorExit("Error reading file:  $path", 'ITSW_html->getFile');
        }
        return $res;
    }

    private function errorExit($msg) {
        throw new Exception($msg);
    }
}
