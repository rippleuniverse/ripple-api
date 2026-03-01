<?php

function currencyFormat($amount, $currency = '₦')
{
    return $currency . ' ' . number_format($amount, 2);
}

function sanitizedJsonDecode($val, $assoc = null)
{
    return gettype($val) === 'string' ? json_decode($val, $assoc) : $val;
}
