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
            $key = '0';
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
        array_pop($active);
        array_pop($active);

        static::$active = implode('.', $active);
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

    /**
     * Temporaty function to generate the HTML of the Benchmark
     *
     * @return string The html
     */
    public static function toHtml()
    {
        foreach (static::$index as $key => $velue) {
            $indent = substr_count($key, '.') * static::$config['indent_size'];
            $mark = static::getArrayValue(static::$marks, $key);
            ?>
            <tr>
                <td style="text-indent:<?php echo $indent; ?>px">
                    <?php echo $mark['label']; ?>
                </td>
                <td><?php echo $mark['start']; ?></td>
                <td><?php echo $mark['end']; ?></td>
                <td><?php echo number_format($mark['end']-$mark['start'], 4); ?>µs</td>
            </tr>
            <?php
        }
    }
}