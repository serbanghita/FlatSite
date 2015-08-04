# PHP gotcha: echo AND print

_Output surprises from using echo and print._

Date: Sep 21st, 2011 8:51:00pm

You will probably freak out because you have never saw [echo](http://php.net/manual/en/function.echo.php) and [print](http://www.php.net/manual/en/function.print.php) working together, it's highly uncommon to see this in a production environment. Yet ... I've discovered an interesting PHP gotcha in the Zend Certified Engineer test question:

    // What is the output of the following code?
    echo '1' + print '2';

*Answer: 22*

    // What is the output of the following code?
    echo print '4';

*Answer:41*

    // What is the output of the following code?
    echo print print 2;

*Answer: 211*

    // What is the output of the following code?
    echo 1, print '2';

*Answer: 121*

**The explanation:**

First of all keep in mind that echo and print are both language constructs and not functions. For any web developer this 
means that we don't have to use parentheses, but in the same time we have to keep in mind that everything that follows 
to echo, in the examples above are it's argument(s)! This is the key of interpreting this kind of code.

In the example:


    echo '1' + print '2';


*   `'1' + print '2'` is the argument
*   `print '2'` outputs `2` and returns `1`
*   finally echo outputs `'1' + 1`, which is `2`

Hence the final output `22`.

This example is my favorite (imagine doing this in an interview):

    echo 1, print '2';

* `1, print '2'` are two separate arguments (echo supports `echo(1,2,3,4...);`)
* `1` is outputed instantly
* `print '2'` outputs `2` and returns `1`
* finally echo outputs 1


Hence the final output `121`.

**References**

* [Comparing PHP's print and echo](http://stackoverflow.com/questions/7094118/reference-comparing-phps-print-and-echo/7095292#7095292)
* [Language constructs vs. Build-in functions](http://www.phpknowhow.com/basics/language-constructs-vs-built-in-functions/)
* zend_do_print() and zend_do_echo() from [PHP source code](http://lxr.sweon.net/php/http/source/Zend/zend_compile.c?v=5.1.0#L532)