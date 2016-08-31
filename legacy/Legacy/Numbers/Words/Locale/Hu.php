<?php

namespace NumberToWords\Legacy\Numbers\Words\Locale;

use NumberToWords\Legacy\Numbers\Words;

class Hu extends Words
{

    // {{{ properties

    /**
     * Locale name
     * @var string
     * @access public
     */
    var $defaultLocale = 'hu_HU';

    /**
     * Language name in English
     * @var string
     * @access public
     */
    var $lang = 'Hungarian';

    /**
     * Native language name
     * @var string
     * @access public
     */
    var $lang_native = 'Magyar';

    /**
     * The word for the minus sign
     * @var string
     * @access private
     */
    var $_minus = 'Mínusz '; // minus sign

    /**
     * The suffixes for exponents (singular and plural)
     * Names based on:
     * http://mek.oszk.hu/adatbazis/lexikon/phplex/lexikon/d/kisokos/186.html
     * @var array
     * @access private
     */
    var $_exponent = array(
        0 => array(''),
        3 => array('ezer'),
        6 => array('millió'),
        9 => array('milliárd'),
       12 => array('billió'),
       15 => array('billiárd'),
       18 => array('trillió'),
       21 => array('trilliárd'),
       24 => array('kvadrillió'),
       27 => array('kvadrilliárd'),
       30 => array('kvintillió'),
       33 => array('kvintilliárd'),
       36 => array('szextillió'),
       39 => array('szextilliárd'),
       42 => array('szeptillió'),
       45 => array('szeptilliárd'),
       48 => array('oktillió'),
       51 => array('oktilliárd'),
       54 => array('nonillió'),
       57 => array('nonilliárd'),
       60 => array('decillió'),
       63 => array('decilliárd'),
       600 => array('centillió')
        );

    /**
     * The array containing the digits (indexed by the digits themselves).
     * @var array
     * @access private
     */
    var $_digits = array(
        0 => 'nulla', 'egy', 'kettõ', 'három', 'négy',
        'öt', 'hat', 'hét', 'nyolc', 'kilenc'
    );

    /**
     * The word separator
     * @var string
     * @access private
     */
    var $_sep = '';

    /**
     * The thousands word separator
     * @var string
     * @access private
     */
    var $_thousand_sep = '-';

    /**
     * The currency names (based on the below links,
     * informations from central bank websites and on encyclopedias)
     *
     * @var array
     * @link http://30-03-67.dreamstation.com/currency_alfa.htm World Currency Information
     * @link http://www.jhall.demon.co.uk/currency/by_abbrev.html World currencies
     * @link http://www.shoestring.co.kr/world/p.visa/change.htm Currency names in English
     * @access private
     */
    var $_currency_names = array(
      'ALL' => array(array('lek'), array('qindarka')),
      'AUD' => array(array('Australian dollar'), array('cent')),
      'BAM' => array(array('convertible marka'), array('fenig')),
      'BGN' => array(array('lev'), array('stotinka')),
      'BRL' => array(array('real'), array('centavos')),
      'BYR' => array(array('Belarussian rouble'), array('kopiejka')),
      'CAD' => array(array('Canadian dollar'), array('cent')),
      'CHF' => array(array('Swiss franc'), array('rapp')),
      'CYP' => array(array('Cypriot pound'), array('cent')),
      'CZK' => array(array('Czech koruna'), array('halerz')),
      'DKK' => array(array('Danish krone'), array('ore')),
      'EEK' => array(array('kroon'), array('senti')),
      'EUR' => array(array('euro'), array('euro-cent')),
      'GBP' => array(array('pound', 'pounds'), array('pence', 'pence')),
      'HKD' => array(array('Hong Kong dollar'), array('cent')),
      'HRK' => array(array('Croatian kuna'), array('lipa')),
      'HUF' => array(array('forint'), array('filler')),
      'ILS' => array(array('new sheqel','new sheqels'), array('agora','agorot')),
      'ISK' => array(array('Icelandic króna'), array('aurar')),
      'JPY' => array(array('yen'), array('sen')),
      'LTL' => array(array('litas'), array('cent')),
      'LVL' => array(array('lat'), array('sentim')),
      'MKD' => array(array('Macedonian dinar'), array('deni')),
      'MTL' => array(array('Maltese lira'), array('centym')),
      'NOK' => array(array('Norwegian krone'), array('oere')),
      'PLN' => array(array('zloty', 'zlotys'), array('grosz')),
      'ROL' => array(array('Romanian leu'), array('bani')),
      'RUB' => array(array('Russian Federation rouble'), array('kopiejka')),
      'SEK' => array(array('Swedish krona'), array('oere')),
      'SIT' => array(array('Tolar'), array('stotinia')),
      'SKK' => array(array('Slovak koruna'), array()),
      'TRL' => array(array('lira'), array('kuruþ')),
      'UAH' => array(array('hryvna'), array('cent')),
      'USD' => array(array('dollar'), array('cent')),
      'YUM' => array(array('dinars'), array('para')),
      'ZAR' => array(array('rand'), array('cent'))
    );

    /**
     * The default currency name
     * @var string
     * @access public
     */
    var $def_currency = 'HUF'; // forint

    // }}}
    // {{{ _toWords()

    /**
     * Converts a number to its word representation
     * in the Hungarian language
     *
     * @param integer $num       An integer between -infinity and infinity inclusive :)
     *                           that need to be converted to words
     * @param integer $power     The power of ten for the rest of the number to the right.
     *                           Optional, defaults to 0.
     * @param integer $powsuffix The power name to be added to the end of the return string.
     *                           Used internally. Optional, defaults to ''.
     *
     * @return string  The corresponding word representation
     *
     * @access protected
     * @author Nils Homp
     * @since  Words 0.16.3
     */
    function _toWords($num, $options = array(), $power = 0, $powsuffix = '', $gt2000 = false)
    {
        $chk_gt2000 = true;

        /**
         * Loads user options
         */
        extract($options, EXTR_IF_EXISTS);

        /**
         * Return string
         */
    	$ret = '';

        // add a minus sign
        if (substr($num, 0, 1) == '-') {
            $ret = $this->_sep . $this->_minus;
            $num = substr($num, 1);
        }

        // strip excessive zero signs and spaces
        $num = trim($num);
        $num = preg_replace('/^0+/', '', $num);

        if ($chk_gt2000) $gt2000 = $num > 2000;

        if (strlen($num) > 3) {
            $maxp = strlen($num)-1;
            $curp = $maxp;
            for ($p = $maxp; $p > 0; --$p) { // power

                // check for highest power
                if (isset($this->_exponent[$p])) {
                    // send substr from $curp to $p
                    $snum = substr($num, $maxp - $curp, $curp - $p + 1);
                    $snum = preg_replace('/^0+/', '', $snum);
                    if ($snum !== '') {
                        $cursuffix = $this->_exponent[$power][count($this->_exponent[$power])-1];
                        if ($powsuffix != '') {
                            $cursuffix .= $this->_sep . $powsuffix;
                        }

                        $ret .= $this->_toWords(
                            $snum,
                            array('chk_gt2000' => false),
                            $p,
                            $cursuffix,
                            $gt2000
                        );

                    	if ($gt2000) $ret .= $this->_thousand_sep;
                    }
                    $curp = $p - 1;
                    continue;
                }
            }
            $num = substr($num, $maxp - $curp, $curp - $p + 1);
            if ($num == 0) {
                return rtrim($ret, $this->_thousand_sep);
            }
        } elseif ($num == 0 || $num == '') {
            return $this->_sep . $this->_digits[0];
        }

        $h = $t = $d = 0;

        switch(strlen($num)) {
        case 3:
            $h = (int)substr($num, -3, 1);

        case 2:
            $t = (int)substr($num, -2, 1);

        case 1:
            $d = (int)substr($num, -1, 1);
            break;

        case 0:
            return;
            break;
        }

        if ($h) {
            $ret .= $this->_sep . $this->_digits[$h] . $this->_sep . 'száz';
        }

        // ten, twenty etc.
        switch ($t) {
        case 9:
        case 5:
            $ret .= $this->_sep . $this->_digits[$t] . 'ven';
            break;
        case 8:
        case 6:
            $ret .= $this->_sep . $this->_digits[$t] . 'van';
            break;
        case 7:
            $ret .= $this->_sep . 'hetven';
            break;
        case 3:
            $ret .= $this->_sep . 'harminc';
            break;
        case 4:
            $ret .= $this->_sep . 'negyven';
            break;
        case 2:
            switch ($d) {
            case 0:
                $ret .= $this->_sep . 'húsz';
                break;
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
                $ret .= $this->_sep . 'huszon';
                break;
            }
            break;
        case 1:
            switch ($d) {
            case 0:
                $ret .= $this->_sep . 'tíz';
                break;
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
                $ret .= $this->_sep . 'tizen';
                break;
            }
            break;
        }

        if ($d > 0) { // add digits only in <0> and <1,inf)
            $ret .= $this->_sep . $this->_digits[$d];
        }

        if ($power > 0) {
            if (isset($this->_exponent[$power])) {
                $lev = $this->_exponent[$power];
            }

            if (!isset($lev) || !is_array($lev)) {
                return null;
            }

            $ret .= $this->_sep . $lev[0];
        }

        if ($powsuffix != '') {
            $ret .= $this->_sep . $powsuffix;
        }

        return $ret;
    }
    // }}}
    // {{{ toCurrencyWords()

    /**
     * Converts a currency value to its word representation
     * (with monetary units) in English language
     *
     * @param integer $int_curr         An international currency symbol
     *                                  as defined by the ISO 4217 standard (three characters)
     * @param integer $decimal          A money total amount without fraction part (e.g. amount of dollars)
     * @param integer $fraction         Fractional part of the money amount (e.g. amount of cents)
     *                                  Optional. Defaults to false.
     * @param integer $convert_fraction Convert fraction to words (left as numeric if set to false).
     *                                  Optional. Defaults to true.
     *
     * @return string  The corresponding word representation for the currency
     *
     * @access public
     * @author Piotr Klaban <makler@man.torun.pl>
     * @since  Words 0.4
     */
    function toCurrencyWords($int_curr, $decimal, $fraction = false, $convert_fraction = true)
    {
        $int_curr = strtoupper($int_curr);
        if (!isset($this->_currency_names[$int_curr])) {
            $int_curr = $this->def_currency;
        }
        $curr_names = $this->_currency_names[$int_curr];

        $ret = trim($this->_toWords($decimal));
        $lev = ($decimal == 1) ? 0 : 1;
        if ($lev > 0) {
            if (count($curr_names[0]) > 1) {
                $ret .= $this->_sep . $curr_names[0][$lev];
            } else {
                $ret .= $this->_sep . $curr_names[0][0] . 's';
            }
        } else {
            $ret .= $this->_sep . $curr_names[0][0];
        }

        if ($fraction !== false) {
            if ($convert_fraction) {
                $ret .= $this->_sep . trim($this->_toWords($fraction));
            } else {
                $ret .= $this->_sep . $fraction;
            }
            $lev = ($fraction == 1) ? 0 : 1;
            if ($lev > 0) {
                if (count($curr_names[1]) > 1) {
                    $ret .= $this->_sep . $curr_names[1][$lev];
                } else {
                    $ret .= $this->_sep . $curr_names[1][0] . 's';
                }
            } else {
                $ret .= $this->_sep . $curr_names[1][0];
            }
        }
        return $ret;
    }
    // }}}

}
