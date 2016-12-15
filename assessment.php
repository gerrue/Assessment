<?php

/**
 * Varland Metal Service, Inc. base class for command line programs.
 *
 * @author      Toby Varland <toby.varland@varland.com>
 * @copyright   2016 Varland Metal Service, Inc.
 * @version     1.0
 */
abstract class CLP {

  /**
   * Adds formatting codes for Mac/Linux terminal to string.
   *
   * @param   string    $text     Text to add formatting codes to.
   * @param   string[]  $codes    Formatting codes to add.
   * @return  string
   */
  protected static function format($text, $codes) {
    $os = php_uname('s');
    $windows = (substr_count($os, 'Windows') != 0);
    if (!$windows) {
      $formatCodes = array();
      foreach ($codes as $code) {
        $formatCodes[] = "\033[{$code}m";
      }
      return implode(NULL, $formatCodes) . " {$text} \033[0m";
    } else {
      return $text;
    }
  }

  /**
   * Adds formatting codes for red background/white text to string.
   *
   * @param   string    $text     Text to add formatting codes to.
   * @return  string
   */
  protected static function formatRed($text) {
    return self::format($text, array('41', '1;37'));
  }

  /**
   * Adds formatting codes for blue background/black text to string.
   *
   * @param   string    $text     Text to add formatting codes to.
   * @return  string
   */
  protected static function formatBlue($text) {
    return self::format($text, array('46', '1;30'));
  }

  /**
   * Adds formatting codes for green background/black text to string.
   *
   * @param   string    $text     Text to add formatting codes to.
   * @return  string
   */
  protected static function formatGreen($text) {
    return self::format($text, array('42', '1;30'));
  }

  /**
   * Clears terminal window. Doesn't work very well on Windows - just prints
   * blank lines to clear screen.
   *
   * @return  void
   */
  protected static function clearScreen() {
    $os = php_uname('s');
    $windows = (substr_count($os, 'Windows') != 0);
    if ($windows) {
      for ($i = 0; $i < 200; $i++) { echo("\n"); }
    } else {
      @system('clear');
    }
  }

  /**
   * Waits for user to press enter key to continue.
   *
   * @return  void
   */
  protected static function enterToContinue() {
    echo("Press enter to continue...");
    stream_get_line(STDIN, 1024, PHP_EOL);
  }

  /**
   * Prints program main menu and handles user input.
   *
   * @return  void
   */
  abstract public static function mainMenu();

}

/**
 * Varland Metal Service, Inc. Programming Assessment command line program.
 *
 * @author      Toby Varland <toby.varland@varland.com>
 * @copyright   2016 Varland Metal Service, Inc.
 * @version     1.0
 */
class VMS extends CLP {

  /**
   * Prints program main menu and handles user input.
   *
   * @return  void
   */
  public static function mainMenu() {

    // Initialize response and error message to empty values.
    $response = NULL;
    $message = NULL;

    // Execute in a loop until the quit menu option is selected.
    while ($response != '5') {

      // Clear screen and print out main program menu.
      self::clearScreen();
      echo("Varland Metal Service, Inc. Programming Assessment\n");
      echo(str_repeat('=', 50) . "\n\n");
      if ($message !== NULL) {
        echo(self::formatRed("***** {$message} *****") . "\n\n");
        $message = NULL;
      }
      echo("Main Menu\n---------\n\n");
      echo("1. FizzBuzz\n");
      echo("2. Only Survivor\n");
      echo("3. Number to Text\n");
      echo("4. String Validation\n");
      echo("5. Quit\n\n");
      echo("Enter option number >> ");

      // Read user input and handle.
      $response = stream_get_line(STDIN, 1024, PHP_EOL);
      switch ($response) {
        case '1':
          self::clearScreen();
          echo("Running your FizzBuzz function for n = 20:\n\n");
          ob_start();
          self::fizzbuzz(20);
          $fizzbuzz = trim(ob_get_contents());
          ob_end_clean();
          $fizzbuzzResults = array('1', '2', 'Fizz', '4', 'Buzz', 'Fizz', '7', '8', 'Fizz', 'Buzz',
                                   '11', 'Fizz', '13', '14', 'FizzBuzz', '16', '17', 'Fizz', '19', 'Buzz');
          $pass = ($fizzbuzz == implode("\n", $fizzbuzzResults));
          echo(($pass ? self::formatGreen('PASS') : self::formatRed('FAIL')) . "\n");
          echo("Expected...: " . implode('|', $fizzbuzzResults));
          if (!$pass) {
            echo("\nYour Result: " . str_replace("\n", '|', $fizzbuzz));
            if (!$fizzbuzz) {
              echo("\n\n" . self::formatBlue('Note: your code should output data using "echo", not return data.'));
            } else {
              echo("\n\n" . self::formatBlue('Note: your code should output newline characters, not "|" characters.'));
            }
          }
          echo("\n\n");
          self::enterToContinue();
          break;
        case '2':
          self::clearScreen();
          echo("Running Only Survivor tests:\n\n");
          $tests = array();
          $tests[] = (object)array('players'  =>  10,
                                   'result'   =>  5);
          $tests[] = (object)array('players'  =>  5,
                                   'result'   =>  3);
          $tests[] = (object)array('players'  =>  13,
                                   'result'   =>  11);
          $tests[] = (object)array('players'  =>  96,
                                   'result'   =>  65);
          $tests[] = (object)array('players'  =>  1,
                                   'result'   =>  1);
          foreach ($tests as $index => $test) {
            $result = self::onlySurvivor($test->players);
            echo(($index + 1) . ': ' . ($result == $test->result ? self::formatGreen('PASS') : self::formatRed('FAIL')) . "\n");
            echo("   # Players..: " . number_format($test->players) . "\n");
            echo("   Winner.....: " . number_format($test->result));
            if ($result != $test->result) {
              echo("\n   Your Answer: " . number_format($result));
            }
            echo("\n\n");
          }
          self::enterToContinue();
          break;
        case '3':
          self::clearScreen();
          echo("Running Number to Text tests:\n\n");
          $tests = array();
          $tests[] = (object)array('number'   =>  16,
                                   'result'   =>  'sixteen');
          $tests[] = (object)array('number'   =>  123456789,
                                   'result'   =>  'one hundred twenty-three million four hundred fifty-six thousand seven hundred eighty-nine');
          $tests[] = (object)array('number'   =>  100,
                                   'result'   =>  'one hundred');
          $tests[] = (object)array('number'   =>  101,
                                   'result'   =>  'one hundred one');
          $tests[] = (object)array('number'   =>  1001,
                                   'result'   =>  'one thousand one');
          $tests[] = (object)array('number'   =>  211565768432819,
                                   'result'   =>  'two hundred eleven trillion five hundred sixty-five billion seven hundred sixty-eight million four hundred thirty-two thousand eight hundred nineteen');
          foreach ($tests as $index => $test) {
            $result = self::numberToText($test->number);
            echo(($index + 1) . ': ' . ($result == $test->result ? self::formatGreen('PASS') : self::formatRed('FAIL')) . "\n");
            echo("   Test Value.: " . number_format($test->number) . "\n");
            echo("   Expected...: {$test->result}");
            if ($result != $test->result) {
              echo("\n   Your Answer: {$result}");
            }
            echo("\n\n");
          }
          self::enterToContinue();
          break;
        case '4':
          self::clearScreen();
          echo("Running String Validation tests:\n\n");
          $tests = array();
          $tests[] = (object)array('format'   =>  'abcd@?bcd.ca',
                                   'result'   =>  5);
          $tests[] = (object)array('format'   =>  'a??@???.af',
                                   'result'   =>  0);
          $tests[] = (object)array('format'   =>  '??????????',
                                   'result'   =>  11562500);
          $tests[] = (object)array('format'   =>  'a?c@b?c',
                                   'result'   =>  6);
          $tests[] = (object)array('format'   =>  'a????.?',
                                   'result'   =>  2125);
          $tests[] = (object)array('format'   =>  'a?c@b?c.?',
                                   'result'   =>  180);
          foreach ($tests as $index => $test) {
            $result = self::countValidStrings($test->format);
            echo(($index + 1) . ': ' . ($result == $test->result ? self::formatGreen('PASS') : self::formatRed('FAIL')) . "\n");
            echo("   Test Format: {$test->format}\n");
            echo("   Expected...: " . number_format($test->result));
            if ($result != $test->result) {
              echo("\n   Your Answer: " . number_format($result));
            }
            echo("\n\n");
          }
          self::enterToContinue();
          break;
        case '5':
          self::clearScreen();
          echo(self::formatBlue(str_repeat('*', 75)) . "\n");
          echo(self::formatBlue('* Please submit to Toby Varland <toby.varland@varland.com> when finished. *') . "\n");
          echo(self::formatBlue(str_repeat('*', 75)) . "\n\n");
          self::enterToContinue();
          self::clearScreen();
          break;
        default:
          $message = 'Invalid option selected.';
      }

    }

  }

  /**
   * Prints each number from 1..n on a separate line except in the following
   * cases:
   *
   * 1. If the number is divisible by 3, print "Fizz"
   * 2. If the number is divisible by 5, print "Buzz"
   * 3. If the number is divisible by 3 and 5, print "FizzBuzz"
   *
   * @param   int   $n    Upper limit of range of numbers to print.
   * @return  void
   */
  protected static function fizzbuzz($n) {
    for ($i=1; $i <= $n; $i++){
      if ( $i % 3 == 0 && $i % 5 == 0 ) {
        echo "FizzBuzz";
      } elseif ( $i % 3 == 0 ) {
        echo "Fizz";
      } elseif ( $i % 5 == 0 ) {
        echo "Buzz";
      } else {
        echo $i;
      }
      echo "\n";
    }
  }

  /**
   * Determine the winner of a game where each player knocks out the next player
   * in line until only one survivor remains. When the end of the line is reached,
   * the game loops around (so it's possible for the last player in line to knock
   * out the first player).
   *
   * Consider the example with 9 players:
   *
   *     1 knocks out 2
   *     3 knocks out 4
   *     5 knocks out 6
   *     7 knocks out 8
   *     9 knocks out 1
   *     3 knocks out 5
   *     7 knocks out 9
   *     3 knocks out 7
   *
   *     3 is the winner (and the return value for the function)
   *
   * @param   int   $players    Number of players who start the game.
   * @return  int
   */
  protected static function getLastElement($list,$j){
    foreach($list as $key => $value){
      if($j==2){
        unset($list[$key]);
        $j=1;
      }
      else{
        $j++;
      }
    }
    if(count($list)==1){
      return array_values($list);
    }
    return self::getLastElement($list,$j);
  }

  protected static function onlySurvivor($players) {
    for ($i=1; $i <=$players ; $i++) {
      $list[] = $i;
    }

    $survive = self::getLastElement($list,1);
    return $survive[0];
  }

  /**
   * Converts a number to text. Works for the range of 0 <= $number < 10^15.
   *
   * @param   int     $number     Number to be converted to text.
   * @return  string
   */
  protected static function numberToText($number) {

    // Special case - return result if zero.
    if ($number == 0) return 'zero';

    // Store major number groups.
    $groups = array();
    $groups[] = (object)array('label'   =>  ' trillion',
                              'divisor' =>  1000000000000);
    $groups[] = (object)array('label'   =>  ' billion',
                              'divisor' =>  1000000000);
    $groups[] = (object)array('label'   =>  ' million',
                              'divisor' =>  1000000);
    $groups[] = (object)array('label'   =>  ' thousand',
                              'divisor' =>  1000);
    $groups[] = (object)array('label'   =>  '',
                              'divisor' =>  1);

    // Find results.
    $results = array();
    foreach ($groups as $group) {
      $groupValue = (int)($number / $group->divisor);
      $number -= ($groupValue * $group->divisor);
      if ($groupValue > 0) {
        $results[] = self::threeDigitNumberToText($groupValue) . $group->label;
      }
    }

    // Return result.
    return implode(' ', $results);

  }

  /**
   * Converts a three digit number to text. Works for the range of 0 <= $number < 1000.
   *
   * @param   int     $number     Number to be converted to text.
   * @return  string
   */
  protected static function threeDigitNumberToText($number) {

    // Store single digit cardinal values.
    $ones = array('zero',
                  'one',
                  'two',
                  'three',
                  'four',
                  'five',
                  'six',
                  'seven',
                  'eight',
                  'nine');

    // Store tens cardinal values.
    $tens = array(FALSE,
                  'ten',
                  'twenty',
                  'thirty',
                  'forty',
                  'fifty',
                  'sixty',
                  'seventy',
                  'eighty',
                  'ninety');

    // Store special cases.
    $specialCases = array(11 => 'eleven',
                          12 => 'twelve',
                          13 => 'thirteen',
                          14 => 'fourteen',
                          15 => 'fifteen',
                          16 => 'sixteen',
                          17 => 'seventeen',
                          18 => 'eighteen',
                          19 => 'nineteen');

    // Initialize result.
    $result = array();

    // If number is three digits, add hundreds to result string.
    if ($number >= 100) {
      $hundreds = (int)($number / 100);
      $result[] = "{$ones[$hundreds]} hundred";
      $number -= ($hundreds * 100);
    }

    // If remainder is a special case, add it. Otherwise use standard form.
    if ($number > 0) {
      if (in_array($number, array_keys($specialCases))) {
        $result[] = $specialCases[$number];
      } else {
        $tensPosition = (int)($number / 10);
        $onesPosition = $number % 10;
        if ($tensPosition > 0 && $onesPosition > 0) {
          $result[] = $tens[$tensPosition]
                    . '-'
                    . $ones[$onesPosition];
        } elseif ($tensPosition > 0) {
          $result[] = $tens[$tensPosition];
        } elseif ($onesPosition > 0) {
          $result[] = $ones[$onesPosition];
        }
      }
    }

    // Return full cardinal number.
    return implode(' ', $result);

  }

  /**
   * Counts number of valid strings can be formed using the given format. The given format may
   * contain one or more "?" wildcard characters. The string is valid if it:
   *
   * 1. Starts with one or more non-empty groups of characters a - e separated by
   *    a period,
   * 2. Contains exactly one "@" symbol, and
   * 3. Ends with two or more non-empty groups of characters a - e separated by
   *    a period.
   *
   * @param   string  $format     String format to test.
   * @return  int
   */
  protected static function countValidStrings($format) {

    // Store regex for determining whether given string is valid.
    $regex = '/^[a-e]+(?:\.[a-e]+)*@[a-e]+(?:\.[a-e]+)+$/';

    // If format doesn't contain wildcard, return 1 if valid string.
    if (substr_count($format, '?') == 0) {
      return (preg_match($regex, $format) == 1 ? 1 : 0);
    }

    // Store replacement characters. Only allow "@" if not already present.
    $replacementCharacters = array('a', '.');
    if (substr_count($pattern, '@') == 0) $replacementCharacters[] = '@';

    // Initialize count to 0.
    $countValid = 0;

    // Try matching regular expression with each valid replacement character.
    // Instead of trying a - e, only use a but with 5x multiplier.
    foreach ($replacementCharacters as $char) {

      // Store multiplier.
      $multiplier = ($char == 'a' ? 5 : 1);

      // Generate test string by substituting for first wildcard.
      $test = preg_replace('/\?/', $char, $format, 1);

      // If test string still contains wildcard, add result of recursive function
      // call. Otherwise test string for validity.
      if (substr_count($test, '?') > 0) {
        $countValid += $multiplier * self::countValidStrings($test);
      } else {
        $valid = preg_match($regex, $test);
        if ($valid == 1) {
          $countValid += $multiplier;
        }
      }
    }

    // Return valid matches.
    return $countValid;

  }

}

/** Execute main menu function on script run. */
VMS::mainMenu();

?>
