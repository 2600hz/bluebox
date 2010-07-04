<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/* removed since it now use the doctrine autoload feature
 * require_once(dirname(__FILE__).'/Yml_Inline.class.php');
 */

/**
 * YamlSfParser class.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: YamlSfParser.class.php 8869 2008-05-09 00:22:57Z dwhittle $
 */
class Doctrine_Parser_YamlSf_Parser
{
  protected
    $value         = '',
    $offset        = 0,
    $lines         = array(),
    $currentLineNb = -1,
    $currentLine   = '',
    $refs          = array();

  /**
   * Constructor
   *
   * @param integer The offset of YAML document (used for line numbers in error messages)
   */
  public function __construct($offset = 0)
  {
    $this->offset = $offset;
  }

  /**
   * Parses a YAML string to a PHP value.
   *
   * @param  string A YAML string
   *
   * @return mixed  A PHP value
   */
  public function parse($value)
  {
    $this->value = $this->cleanup($value);
    $this->currentLineNb = -1;
    $this->currentLine = '';
    $this->lines = explode("\n", $this->value);

    $data = array();
    while ($this->moveToNextLine())
    {
      if ($this->isCurrentLineEmpty())
      {
        continue;
      }

      // tab?
      if (preg_match('#^\t+#', $this->currentLine))
      {
        throw new InvalidArgumentException(sprintf('A YAML file cannot contain tabs as indentation at line %d (%s).', $this->getRealCurrentLineNb(), $this->currentLine));
      }

      $isRef = $isInPlace = false;
      if (preg_match('#^\-(\s+(?P<value>.+?))?\s*$#', $this->currentLine, $values))
      {
        if (isset($values['value']) && preg_match('#^&(?P<ref>[^ ]+) *(?P<value>.*)#', $values['value'], $matches))
        {
          $isRef = $matches['ref'];
          $values['value'] = $matches['value'];
        }

        // array
        if ( !isset($values['value']) || '' == trim($values['value'], ' ') || 0 === strpos(ltrim($values['value'], ' '), '#'))
        {
          $c = $this->getRealCurrentLineNb() + 1;
          $parser = new Doctrine_Parser_YamlSf_Parser($c);
          $parser->refs =& $this->refs;
          $data[] = $parser->parse($this->getNextEmbedBlock());
        }
        else
        {
          if (preg_match('/^([^ ]+)\: +({.*?)$/', $values['value'], $matches))
          {
            $data[] = array($matches[1] => Doctrine_Parser_YamlSf_Inline::load($matches[2]));
          }
          else
          {
            $data[] = $this->parseValue($values['value']);
          }
        }
      }
      else if (preg_match('#^(?P<key>[^ ].*?) *\:(\s+(?P<value>.+?))?\s*$#', $this->currentLine, $values))
      {
        $key = Doctrine_Parser_YamlSf_Inline::parseScalar($values['key']);

        if ('<<' === $key)
        {
          if (isset($values['value']) && '*' === substr($values['value'], 0, 1))
          {
            $isInPlace = substr($values['value'], 1);
            if ( !array_key_exists($isInPlace, $this->refs))
            {
              throw new InvalidArgumentException(sprintf('Reference "%s" does not exist on line %s.', $isInPlace, $this->currentLine));
            }
          }
          else
          {
            throw new InvalidArgumentException(sprintf('In place substitution must point to a reference on line %s.', $this->currentLine));
          }
        }
        else if (isset($values['value']) && preg_match('#^&(?P<ref>[^ ]+) *(?P<value>.*)#', $values['value'], $matches))
        {
          $isRef = $matches['ref'];
          $values['value'] = $matches['value'];
        }

        // hash
        if ( !isset($values['value']) || '' == trim($values['value'], ' ') || 0 === strpos(ltrim($values['value'], ' '), '#'))
        {
          // if next line is less indented or equal, then it means that the current value is null
          if ($this->isNextLineIndented())
          {
            $data[$key] = null;
          }
          else
          {
            $c = $this->getRealCurrentLineNb() + 1;
            $parser = new Doctrine_Parser_YamlSf_Parser($c);
            $parser->refs =& $this->refs;
            $data[$key] = $parser->parse($this->getNextEmbedBlock());
          }
        }
        else
        {
          if ($isInPlace)
          {
            $data = $this->refs[$isInPlace];
          }
          else
          {
            $data[$key] = $this->parseValue($values['value']);
          }
        }
      }
      else
      {
        // one liner?
        if (1 == count(explode("\n", rtrim($this->value, "\n"))))
        {
          return Doctrine_Parser_YamlSf_Inline::load($this->lines[0]);
        }

        throw new InvalidArgumentException(sprintf('Unable to parse line %d (%s).', $this->getRealCurrentLineNb(), $this->currentLine));
      }

      if ($isRef)
      {
        $this->refs[$isRef] = end($data);
      }
    }

    return empty($data) ? null : $data;
  }

  /**
   * Returns the current line number (takes the offset into account).
   *
   * @return integer The current line number
   */
  protected function getRealCurrentLineNb()
  {
    return $this->currentLineNb + $this->offset;
  }

  /**
   * Returns the current line indentation.
   *
   * @returns integer The current line indentation
   */
  protected function getCurrentLineIndentation()
  {
    return strlen($this->currentLine) - strlen(ltrim($this->currentLine, ' '));
  }

  /**
   * Returns the next embed block of YAML.
   *
   * @param string A YAML string
   */
  protected function getNextEmbedBlock()
  {
    $this->moveToNextLine();

    $newIndent = $this->getCurrentLineIndentation();

    if ( !$this->isCurrentLineEmpty() && 0 == $newIndent)
    {
      throw new InvalidArgumentException(sprintf('Indentation problem at line %d (%s)', $this->getRealCurrentLineNb(), $this->currentLine));
    }

    $data = array(substr($this->currentLine, $newIndent));

    while ($this->moveToNextLine())
    {
      if ($this->isCurrentLineEmpty())
      {
        if ($this->isCurrentLineBlank())
        {
          $data[] = substr($this->currentLine, $newIndent);
        }

        continue;
      }

      $indent = $this->getCurrentLineIndentation();

      if (preg_match('#^(?P<text> *)$#', $this->currentLine, $match))
      {
        // empty line
        $data[] = $match['text'];
      }
      else if ($indent >= $newIndent)
      {
        $data[] = substr($this->currentLine, $newIndent);
      }
      else if (0 == $indent)
      {
        $this->moveToPreviousLine();

        break;
      }
      else
      {
        throw new InvalidArgumentException(sprintf('Indentation problem at line %d (%s)', $this->getRealCurrentLineNb(), $this->currentLine));
      }
    }

    return implode("\n", $data);
  }

  /**
   * Moves the parser to the next line.
   */
  protected function moveToNextLine()
  {
    if ($this->currentLineNb >= count($this->lines) - 1)
    {
      return false;
    }

    $this->currentLine = $this->lines[++$this->currentLineNb];

    return true;
  }

  /**
   * Moves the parser to the previous line.
   */
  protected function moveToPreviousLine()
  {
    $this->currentLine = $this->lines[--$this->currentLineNb];
  }

  /**
   * Parses a YAML value.
   *
   * @param  string A YAML value
   *
   * @return mixed  A PHP value
   */
  protected function parseValue($value)
  {
    if ('*' === substr($value, 0, 1))
    {
      if (false !== $pos = strpos($value, '#'))
      {
        $value = substr($value, 1, $pos - 2);
      }
      else
      {
        $value = substr($value, 1);
      }

      if ( !array_key_exists($value, $this->refs))
      {
        throw new InvalidArgumentException(sprintf('Reference "%s" does not exist (%s).', $value, $this->currentLine));
      }
      return $this->refs[$value];
    }

    if (preg_match('/^(?P<separator>\||>)(?P<modifiers>\+|\-|\d+|\+\d+|\-\d+|\d+\+|\d+\-)?(?P<comments> +#.*)?$/', $value, $matches))
    {
      $modifiers = isset($matches['modifiers']) ? $matches['modifiers'] : '';

      return $this->parseFoldedScalar($matches['separator'], preg_replace('#\d+#', '', $modifiers), intval(abs($modifiers)));
    }
    else
    {
      return Doctrine_Parser_YamlSf_Inline::load($value);
    }
  }

  /**
   * Parses a folded scalar.
   *
   * @param  string  The separator that was used to begin this folded scalar (| or >)
   * @param  string  The indicator that was used to begin this folded scalar (+ or -)
   * @param  integer The indentation that was used to begin this folded scalar
   *
   * @return string  The text value
   */
  protected function parseFoldedScalar($separator, $indicator = '', $indentation = 0)
  {
    $separator = '|' == $separator ? "\n" : ' ';
    $text = '';

    $notEOF = $this->moveToNextLine();

    while ($notEOF && $this->isCurrentLineBlank())
    {
      $text .= "\n";

      $notEOF = $this->moveToNextLine();
    }

    if ( !$notEOF)
    {
      return '';
    }

    if ( !preg_match('#^(?P<indent>'.($indentation ? str_repeat(' ', $indentation) : ' +').')(?P<text>.*)$#', $this->currentLine, $matches))
    {
      $this->moveToPreviousLine();

      return '';
    }

    $textIndent = $matches['indent'];
    $previousIndent = 0;

    $text .= $matches['text'].$separator;
    while ($this->currentLineNb + 1 < count($this->lines))
    {
      $this->moveToNextLine();

      if (preg_match('#^(?P<indent> {'.strlen($textIndent).',})(?P<text>.+)$#', $this->currentLine, $matches))
      {
        if (' ' == $separator && $previousIndent != $matches['indent'])
        {
          $text = substr($text, 0, -1)."\n";
        }
        $previousIndent = $matches['indent'];

        $text .= str_repeat(' ', $diff = strlen($matches['indent']) - strlen($textIndent)).$matches['text'].($diff ? "\n" : $separator);
      }
      else if (preg_match('#^(?P<text> *)$#', $this->currentLine, $matches))
      {
        $text .= preg_replace('#^ {1,'.strlen($textIndent).'}#', '', $matches['text'])."\n";
      }
      else
      {
        $this->moveToPreviousLine();

        break;
      }
    }

    if (' ' == $separator)
    {
      // replace last separator by a newline
      $text = preg_replace('/ (\n*)$/', "\n$1", $text);
    }

    switch ($indicator)
    {
      case '':
        $text = preg_replace('#\n+$#s', "\n", $text);
        break;
      case '+':
        break;
      case '-':
        $text = preg_replace('#\n+$#s', '', $text);
        break;
    }

    return $text;
  }

  /**
   * Returns true if the next line is indented.
   *
   * @return Boolean Returns true if the next line is indented, false otherwise
   */
  protected function isNextLineIndented()
  {
    $currentIndentation = $this->getCurrentLineIndentation();
    $notEOF = $this->moveToNextLine();

    while ($notEOF && $this->isCurrentLineEmpty())
    {
      $notEOF = $this->moveToNextLine();
    }

    if (false === $notEOF)
    {
      return false;
    }

    $ret = false;
    if ($this->getCurrentLineIndentation() <= $currentIndentation)
    {
      $ret = true;
    }

    $this->moveToPreviousLine();

    return $ret;
  }

  /**
   * Returns true if the current line is blank or if it is a comment line.
   *
   * @return Boolean Returns true if the current line is empty or if it is a comment line, false otherwise
   */
  protected function isCurrentLineEmpty()
  {
    return $this->isCurrentLineBlank() || $this->isCurrentLineComment();
  }

  /**
   * Returns true if the current line is blank.
   *
   * @return Boolean Returns true if the current line is blank, false otherwise
   */
  protected function isCurrentLineBlank()
  {
    return '' == trim($this->currentLine, ' ');
  }

  /**
   * Returns true if the current line is a comment line.
   *
   * @return Boolean Returns true if the current line is a comment line, false otherwise
   */
  protected function isCurrentLineComment()
  {
    return 0 === strpos(ltrim($this->currentLine, ' '), '#');
  }

  /**
   * Cleanups a YAML string to be parsed.
   *
   * @param  string The input YAML string
   *
   * @return string A cleaned up YAML string
   */
  protected function cleanup($value)
  {
    $value = str_replace(array("\r\n", "\r"), "\n", $value);

    if ( !preg_match("#\n$#", $value))
    {
      $value .= "\n";
    }

    // strip YAML header
    preg_replace('#^\%YAML[: ][\d\.]+.*\n#s', '', $value);

    // remove ---
    $value = preg_replace('#^\-\-\-.*?\n#s', '', $value);

    return $value;
  }
}