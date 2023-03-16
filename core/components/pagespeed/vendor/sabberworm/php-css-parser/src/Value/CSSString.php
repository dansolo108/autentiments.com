<?php

namespace Sabberworm\CSS\Value;

use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\SourceException;
use Sabberworm\CSS\Parsing\UnexpectedEOFException;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;

class CSSString extends PrimitiveValue
{
    /**
     * @var string
     */
    private $sString;

    /**
     * @param string $sString
     * @param int $iLineNo
     */
    public function __construct($sString, $iLineNo = 0)
    {
        $this->sString = $sString;
        parent::__construct($iLineNo);
    }

    /**
     * @return CSSString
     *
     * @throws SourceException
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    public static function parse(ParserState $oParserState)
    {
        $sBegin = $oParserState->peek();
        $sQuote = null;
        if ($sBegin === "'") {
            $sQuote = "'";
        } elseif ($sBegin === '"') {
            $sQuote = '"';
        }
        if ($sQuote !== null) {
            $oParserState->consume($sQuote);
        }
        $sResult = "";
        $sContent = null;
        if ($sQuote === null) {
            // Unquoted strings end in whitespace or with braces, brackets, parentheses
            while (!preg_match('/[\\s{}()<>\\[\\]]/isu', $oParserState->peek())) {
                $sResult .= $oParserState->parseCharacter(false);
            }
        } else {
            while (!$oParserState->comes($sQuote)) {
                $sContent = $oParserState->parseCharacter(false);
                if ($sContent === null) {
                    throw new SourceException(
                        "Non-well-formed quoted string {$oParserState->peek(3)}",
                        $oParserState->currentLine()
                    );
                }
                $sResult .= $sContent;
            }
            $oParserState->consume($sQuote);
        }
        return new CSSString($sResult, $oParserState->currentLine());
    }

    /**
     * @param string $sString
     *
     * @return void
     */
    public function setString($sString)
    {
        $this->sString = $sString;
    }

    /**
     * @return string
     */
    public function getString()
    {
        return $this->sString;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render(new OutputFormat());
    }

    /**
     * @return string
     */
    public function render(OutputFormat $oOutputFormat)
    {
		$sQuote = $oOutputFormat->getStringQuotingType();
		$aString = preg_split('//u', $this->sString, null, PREG_SPLIT_NO_EMPTY);
		$iLength = count($aString);
		foreach ($aString as $i => $sChar) {
			if (strlen($sChar) === 1) {
				if ($sChar === $sQuote || $sChar === '\\') {
					// Encode quoting related characters as hex values
				} else {
					$iOrd = ord($sChar);
					if ($iOrd > 31 && $iOrd < 127) {
						// Keep only human readable ascii characters
						continue;
					}
				}
			}

			$sHex = '';
			$sUtf32 = iconv('utf-8', 'utf-32le', $sChar);
			$aBytes = str_split($sUtf32);
			foreach (array_reverse($aBytes) as $sByte) {
				$sHex .= str_pad(dechex(ord($sByte)), 2, '0', STR_PAD_LEFT);
			}
			$sHex = ltrim($sHex, '0');
			if ($i + 1 < $iLength && strlen($sHex) < 6) {
				// Add space after incomplete unicode escape if there can be any confusion
				$sNextChar = $aString[$i + 1];
				if (preg_match('/^[a-fA-F0-9\s]/u', $sNextChar)) {
					$sHex .= ' ';
				}
			}
			$aString[$i] = '\\' . $sHex;
		}

		return $sQuote . implode($aString) . $sQuote;
    }
}
