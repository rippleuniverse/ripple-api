<?php

function currencyFormat($amount, $currency = '₦')
{
    return $currency . ' ' . number_format($amount, 2);
}
