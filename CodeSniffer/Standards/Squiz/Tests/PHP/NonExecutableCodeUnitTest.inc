<?php
switch ($var) {
    case '1':
        return;
        echo 'hi';
    break;
    case '2':
    case '3':
        if ($something === true) {
            break;
            echo 'hi';
        }
    break;
    default:
        return;

        if ($something === true) {
            break;
            echo 'hi';
        }
    break;
}

function myFunction($var)
{
    if ($something === TRUE) {
        return;
        echo 'hi';
    }

    return;
    return FALSE;
    if ($something === TRUE) {
        return TRUE;
    }

}//end myFunction()

foreach ($vars as $var) {
    if ($something === TRUE) {
        break;
        break;
    } else {
        break;
        echo 'hi';
    }

    echo $var."\n";
}

switch ($lowerVarType) {
    case 'bool':
        return 'boolean';
        echo 'hi';
    case 'double':
    case 'real':
        return 'float';
        echo 'hi';
}

exit();

// Errors are thrown from here down from the exit() above.
// No lines before should have duplicate errors.
foreach ($vars as $var) {
    if ($something === TRUE) {
        break;
        break;
    }
}

exit();

?>