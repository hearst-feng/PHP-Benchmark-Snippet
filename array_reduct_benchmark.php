/*
 * https://repl.it/@HearstFeng/Array-Reduce-Benchmark
 */
class BaseArrayHelper
{    
    public static function index($array, $key, $groups = [])
    {
        $result = [];
        $groups = (array) $groups;
        foreach ($array as $element) {
            $lastArray = &$result;
            foreach ($groups as $group) {
                $value = static::getValue($element, $group);
                if (!array_key_exists($value, $lastArray)) {
                    $lastArray[$value] = [];
                }
                $lastArray = &$lastArray[$value];
            }
            if ($key === null) {
                if (!empty($groups)) {
                    $lastArray[] = $element;
                }
            } else {
                $value = static::getValue($element, $key);
                if ($value !== null) {
                    if (is_float($value)) {
                        $value = StringHelper::floatToString($value);
                    }
                    $lastArray[$value] = $element;
                }
            }
            unset($lastArray);
        }
        return $result;
    }
    
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }
        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }
        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return $array[$key];
        }
        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }
        if (is_object($array)) {
            // this is expected to fail if the property does not exist, or __get() is not implemented
            // it is not reliably possible to check whether a property is accessible beforehand
            return $array->$key;
        } elseif (is_array($array)) {
            return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        }
        return $default;
    }
}

$arr = [
    [
        "id" => 'abce',
    ],
    [
        "id" => 'def',
    ],
    [
        "id" => 'now',
    ],
    [
        "id" => 'bit',
    ],
];

function test($title, $callback)
{
  echo "$title \n";
  $start = microtime(true);
  
  $result = $callback();
  
  $end = microtime(true);
  echo ($end - $start) * 100000;
  echo "\n";
}

test("array_reduce:", function() use ($arr) {
  return array_reduce($arr,
  function ($products, $product) {
      $products[$product['id']] = $product;
      return $products;
  },
  []);
});

test("foreach:", function() use ($arr) {
  $foreach = [];
  foreach($arr as $item) {
      $foreach[$item['id']] = $item;
  }
  return $foreach;
});

test("foreach:", function() use ($arr) {
  return BaseArrayHelper::index($arr, 'id');
});
