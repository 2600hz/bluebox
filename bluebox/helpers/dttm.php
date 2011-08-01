<?php defined('SYSPATH') or die('No direct access allowed.');

class dttm
{

        public static function pluralize( $count, $text )
        {
                return $count . $text . ( $count == 1 ? '' :  's' );
        }


        public static function timestampdiff($d1, $d2, $returnformat = 'colon')
        {
                if ($d1 === 'now') $d1 = time();
                if ($d2 === 'now') $d2 = time();
                if (is_string($d1)) $d1 = (int) $d1;
                if (is_string($d2)) $d2 = (int) $d2;
                if (!($d1 > 0 && $d2 > 0)) return false;
                return self::datetimediff( date('c', $d1), date('c', $d2), $returnformat);
        }

        public static function datetimediff($d1, $d2, $returnformat = 'colon')
        {
                kohana::Log('debug', 'd1: ' . print_r($d1, true) . ' d2: ' . print_r($d2, true));
                if ($d1 === '' || $d2 === '') return false;
                try {
                        $date1 = new DateTime($d1);
                        $date2 = new DateTime($d2);
                }
                catch (Exception $e)
                {
                        return false;
                }

                $interval = $date1->diff($date2);
                $interval_str = '';
                switch ($returnformat) {
                        case 'colon':
                                if ($interval->invert) $interval_str .= '-';
                                if ( $v = $interval->y >= 1 ) $interval_str .= $interval->y . 'y';
                                if ( $v = $interval->m >= 1 ) $interval_str .= $interval->m . 'm';
                                if ( $v = $interval->d >= 1 ) $interval_str .= $interval->d . 'd';
                                $interval_str .= ' ';
                                if ( $v = $interval->h >= 1 ) $interval_str .= str_pad($interval->h, 2, '0', STR_PAD_LEFT) . ':';
                                else $interval_str .= '00:';
                                if ( $v = $interval->i >= 1 ) $interval_str .= str_pad($interval->i, 2, '0', STR_PAD_LEFT) . ':';
                                else $interval_str .= '00:';
                                if ( $v = $interval->s >= 1 ) $interval_str .= str_pad($interval->s, 2, '0', STR_PAD_LEFT) . ':';
                                else $interval_str .= '00:';
                                $interval_str = substr($interval_str, 0, -1);
                                break;
                        case 'text':
                                if ( $v = $interval->y >= 1 ) $interval_str .= self::pluralize( $interval->y, ' year' ) . ', ';
                                if ( $v = $interval->m >= 1 ) $interval_str .= self::pluralize( $interval->m, ' month' ) . ', ';
                                if ( $v = $interval->d >= 1 ) $interval_str .= self::pluralize( $interval->d, ' day' ) . ', ';
                                if ( $v = $interval->h >= 1 ) $interval_str .= self::pluralize( $interval->h, ' hour' ) . ', ';
                                if ( $v = $interval->i >= 1 ) $interval_str .= self::pluralize( $interval->i, ' minute' ) . ', ';
                                if ( $v = $interval->s >= 1 ) $interval_str .= self::pluralize( $interval->s, ' second' ) . ', ';
                                $interval_str = substr($interval_str, 0, -1);
                                $interval_str .= ' ';
                                if ($interval->invert)
                                        $interval_str .= _('from now');
                                else
                                        $interval_str .= _('ago');
                                break;
                        default:
                                $interval_str = $interval->format($returnformat);
                                break;
                }
                return $interval_str;
        }
}