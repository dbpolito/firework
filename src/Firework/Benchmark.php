<?php
namespace Firework;

/**
 * A dead simple but powerfull tool that helps you to benchmark your application.
 */
class Benchmark
{

    /**
     * Marks of your application
     * @var array
     */
    protected static $marks = array();

    /**
     * Mark indexes to make it easier to manipulate
     * @var array
     */
    protected static $index = array();

    /**
     * Active Mark
     * @var null|string
     */
    protected static $active = null;

    /**
     * Configuration
     * @var [type]
     */
    protected static $config = array(
        'indent_size' => 15
    );

    /**
     * Start a Mark
     * @param  [type] $label A label for your mark
     *
     * @return void
     */
    public static function start($label = null)
    {
        if (static::$active === null) {
            $key = count(static::$marks);
        } else {
            $key = static::$active.'.marks';

            if (($keys = static::getArrayValue(static::$marks, $key)) !== null) {
                $key .= '.'.count($keys);
            } else {
                $key .= '.0';
            }
        }

        static::$active = $key;
        $label = $label ?: 'Mark '.(count(static::$index)+1);

        $mark = array(
            'label' => $label,
            'start' => static::getTime(),
            'end'   => null,
            'marks' => null
        );

        static::$index[$key] = $label;
        static::setArrayValue(static::$marks, $key, $mark);
    }

    /**
     * End a Started Mark
     *
     * @return void
     */
    public static function end()
    {
        if (static::$active === null) {
            throw new \Exception('There is no more marks to end.');
        }

        static::setArrayValue(static::$marks, static::$active.'.end', static::getTime());

        $active = explode('.', static::$active);
        $active = array_slice($active, 0, -2);

        static::$active = !empty($active) ? implode('.', $active) : null;
    }

    /**
     * Get the current timestamp
     * @return int timestamp
     */
    protected static function getTime()
    {
        return microtime(true);
    }

    /**
     * A helper to iterate arrays with .`s
     * @param  array  $array The array
     * @param  string $key   The index
     *
     * @return mixed  The value of the array index
     */
    protected static function getArrayValue($array, $key)
    {
        if (! is_array($array) and ! $array instanceof \ArrayAccess) {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        foreach (explode('.', $key) as $_key) {
            if (isset($array[$_key]) === false) {
                return null;
            }

            $array = $array[$_key];
        }

        return $array;
    }

    /**
     * A helper to iterate arrays with .`s
     * @param  array  $array The array
     * @param  string $key   The key
     * @param  mixed  $value The value
     *
     * @return void
     */
    protected static function setArrayValue(&$array, $key, $value = null)
    {
        if ( ! is_array($array) and ! $array instanceof \ArrayAccess) {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        foreach (explode('.', $key) as $_key) {
            if (isset($array[$_key]) === false) {
                $array[$_key] = array();
            }

            $array =& $array[$_key];
        }

        $array = $value;
    }

    protected function generateTableContent($marks = array(), $parents = array(), $level = 0)
    {
        $output = '';

        foreach ($marks as $key => $mark) {
            $_parents = array_merge($parents, array($key));
            $elapsed  = ($mark['end']-$mark['start'])*1000;
            $indent   = $level * static::$config['indent_size'];

            $output .= '<tr class="'.implode('_', $parents).($level > 0 ? ' hide' : '').'">
                <td style="text-indent:'.$indent.'px">
                    '.($mark['marks'] !== null ? '<a href="#" class="fw-toggle" onclick="toggleFirework(this, \''.implode('_', $_parents).'\');return false;">+</a>&nbsp;&nbsp;' : '&nbsp;&nbsp;&nbsp;&nbsp;').$mark['label'].'
                </td>
                <td>'.$mark['start'].'</td>
                <td>'.$mark['end'].'</td>
                <td>'.number_format($elapsed, 0).' ms</td>
            </tr>';

            if ($mark['marks'] !== null) {
                $output  .= static::generateTableContent($mark['marks'], $_parents, $level+1);
            }
        }

        return $output;
    }

    /**
     * Temporaty function to generate the HTML of the Benchmark
     *
     * @return string The html
     */
    public static function toHtml()
    {
        $html = '<style type="text/css">
            #firework {width:99%;position:fixed;bottom:0;background-color:#fff;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:14px;line-height:20px;color:#333;background-color:#fff;}
            #fw-btn {color:#333;background-color:#eee;padding:10px 15px;text-decoration:none;display:inline-block;border:2px solid #ddd;border-bottom-color:#eee;font-weight:bold;}
            #fw-wrapper {max-height:300px; overflow:auto;margin-top:-3px;-moz-box-shadow: 0 0 20px #ccc;-webkit-box-shadow: 0 0 20px #ccc;box-shadow: 0 0 20px #ccc;}
            #fw-benchmark {width:100%;text-align:left;border-collapse:collapse;border:2px solid #ddd;}
            #fw-benchmark th {background-color:#eee;}
            #fw-benchmark tr:hover {background-color:#f6f6f6;}
            #fw-benchmark th, #fw-benchmark td {padding:10px;border:2px solid #ddd;}
            #fw-benchmark .fw-toggle {color:#333;text-decoration:none;font-weight:bold;}
            .hide {display:none;}
            </style>
            <div id="firework">
                <a id="fw-btn" href="#" onclick="showFirework();return false;">Benchmark</a>
                <div id="fw-wrapper">
                    <table id="fw-benchmark" class="hide">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Elapsed Time</th>
                            </tr>
                        </thead>
                        <tbody>';

        if (! empty(static::$index)) {
            $html .= static::generateTableContent(static::$marks);
        } else {
            $html .= '<tr><td colspan="4">There is no mark to show.</td></tr>';
        }

        $html .= '</tbody>
                    </table>
                </div>
            </div>
            <script>
            var firework = 0;
            function showFirework() {
                document.getElementById("fw-benchmark").className = (firework === 0) ? "" : "hide";
                firework = (firework === 0) ? 1 : 0;
            }
            function toggleFirework(e, rel) {
                var tmp = document.getElementsByClassName(rel.toString());
                if (e.innerHTML == "+") {
                    for (var i = 0; i < tmp.length; i++) {
                        tmp[i].className = tmp[i].className.replace(" hide", "");
                    }

                    e.innerHTML = "-";
                } else {
                    for (var i = 0; i < tmp.length; i++) {
                        tmp[i].className = tmp[i].className + " hide";
                    }

                    e.innerHTML = "+";
                }
            }
            </script>';

        return $html;
    }
}