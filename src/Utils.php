<?php

namespace AdBoard\Utils;

function cryptPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}

function checkPassword($password, $hash)
{
    return password_verify($password, $hash);
}
