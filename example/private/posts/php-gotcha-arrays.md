# PHP gotcha of the day: tricky arrays

_Indexes, keys, values ... they all have some surprises for you_

Date: Oct 5th, 2011 9:29:00pm

As a PHP programmer you are dealing with [arrays](http://www.php.net/manual/en/language.types.array.php) every single day, meaning that there is a high probability of hitting some "unexpected" results. In this article we will not deal with array operations and functions.

Try to answer the following questions, we'll explain the results at the end of this article:

a) Problem:

    <?php // a.) What is the output of count($array)?
    $array = array(1 => "Serban", "1" =&gt; "Ghita", "John", 2 =&gt; "Doe");
    ?&gt;

Answer: `2`

b) Problem:

    <?php // b.) What will the following script display? (1 answer)
    $array = array('Serban' => 'Ghita', 'John' =&gt; 'Smith');
    function myName(){
      return 'Serban';
    }
    echo $array[myName()];
    // 1. Parse error
    // 2. Ghita
    // 3. NULL
    // 4. Notice: Use of undefined constant myName + NULL
    ?&gt;

Answer: `Ghita`

c) Problem:

    <?php // c.) What is the output of strlen($text) ?
    // d.) What is the output of count($text) ?
    $text = 'Serban';
    $text[7] = 'Ghita';
    ?>

Answer:

    8
    1
    // (ok I know this is not an array question, I'm playing with your mind)

d) Problem:

    <?php // e.) What is the resulting key of the value 'Serban' ?
    $array = array(0 => 'Octav', 1 =&gt; 'Bogdan', 2 =&gt; 'Marius', 3 =&gt; 'Ciprian', 4 =&gt; 'Iulian');
    
    foreach($array as $key =&gt; $value){
      unset($array[$key]);
    }
    
    $array[] = 'Serban';
    ?&gt;

Answer: `5`

e) Problem:

    <?php // f.) What is the result of the var_dump() function?
    $a = array(false => 'Serban', true =&gt; 'Ghita');
    $b = array('Serban', 'Ghita');
    
    var_dump($a===$b);
    ?&gt;

Answer: `true`

f) Problem:

    <?php // g.) What is the output?
    $a = array(1, 2, 3);
    $b = &$a[0];
    $a2 = $a;
    $a2[0]++;
    
    echo( $a[0] );
    ?>

Answer: `2`

**Answers explained**

a) Array keys can only be of `integer` and `string` type. `'1'` will be interpreted as `1`, but `'01'` will be interpreted as `'01'`. If you provide an array with two identical keys the latter will override the former. If a key is not specified for a value, the maximum of the integer indices is taken and the new key will be that value plus 1.

b) In an array the key between the square brackets `[]` can be an expression, that means both `$arr[test()]` and `$arr[test($var)]` is valid code.

c) and d.) `Serban` string has a length of `6`. `$text[7]` adds `G` character on the 8th position and automatically pads the 7th position with a space, resulting `Serban G`. Obviously `count($string)` that is not null is always `1`. Note that `count(null)` is `0`.

d) Unsetting keys in `$array` doesn't resets the index value. Doing `reset($array)` will reset the index, doing `unset($array)` will destroy the array and reset the index.

e) Remember that array keys can only be strings or integers, so `false` evaluates to `0` and true to `1`. The arrays are identical and the keys are in the same order.

Note that:

    <?php $a = array(0 => 'Serban', 1 =&gt; 'Ghita');
    $b = array(1 =&gt; 'Ghita', 0 =&gt; 'Serban');
    
    var_dump($a==$b); // bool(true)
    var_dump($a===$b); // bool(false)
    ?&gt;

f) When arrays are copied, the "reference status" of their members is preserved.