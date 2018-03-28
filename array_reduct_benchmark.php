<?php
/*
 * https://repl.it/@HearstFeng/Array-Reduce-Benchmark
 */
require('vendor/yiisoft/yii2/helpers/BaseArrayHelper.php'); 

use yii\helpers\BaseArrayHelper;

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

test("yii2 ArrayHelper index:", function() use ($arr) {
  return BaseArrayHelper::index($arr, 'id');
});
