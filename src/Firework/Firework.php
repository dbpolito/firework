<?php
namespace Firework;

class Benchmark
{

    protected static $marks = array();

    protected static $index = array();

    protected static $active = null;

    public static function start($label = null)
    {
        if (static::$active === null)
        {
            $key = '0';
        }
        else
        {
            $key = static::$active.'.marks';
            if (($keys = static::getArrayValue(static::$marks, $key)) !== null)
            {
                $keys = array_keys($keys);
                $key .= '.'.count($keys);
            }
            else
            {
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

    protected static function getTime()
    {
        return microtime(true);
    }

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

    public static function toHtml()
    {
        foreach (static::$index as $key => $velue)
        {
            $indent = substr_count($key, '.');
            $mark = static::getArrayValue(static::$marks, $key);
        ?>
        <tr>
            <td style="text-indent:<?php echo $indent*15; ?>px"><?php echo $mark['label']; ?></td>
            <td><?php echo $mark['start']; ?></td>
            <td><?php echo $mark['end']; ?></td>
            <td><?php echo number_format($mark['end']-$mark['start'], 4); ?>µs</td>
        </tr>
        <?php
        }
    }
}