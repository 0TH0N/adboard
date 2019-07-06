<?php

namespace AdBoard;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class Validator
{
    public static function validateAdData($adData)
    {
        $validator = Validation::createValidator();
        $violations = [];

        $constraintAdText = [
            new Assert\Length(['min' => 20, 'max' => 1000])
        ];
        $adTextViolations = $validator->validate($adData['adText'], $constraintAdText);
        if (!empty($adTextViolations)) {
            $violations['adText'] = $adTextViolations;
        }

        $constraintUserName = [
            new Assert\Length(['min' => 2, 'max' => 30])
        ];
        $userNameViolations = $validator->validate($adData['userName'], $constraintUserName);
        if (!empty($userNameViolations)) {
            $violations['userName'] = $userNameViolations;
        }

        $constraintPassword = [
            new Assert\Length(['min' => 6, 'max' => 30])
        ];
        $passwordViolations = $validator->validate($adData['password'], $constraintPassword);
        if (!empty($passwordViolations)) {
            $violations['password'] = $passwordViolations;
        }

        $constraintPhone = [
            new Assert\Length(['min' => 6, 'max' => 30])
        ];
        $phoneViolations = $validator->validate($adData['phone'], $constraintPhone);
        if (!empty($phoneViolations)) {
            $violations['phone'] = $phoneViolations;
        }

        return $violations;
    }

    public static function validateNumberFakeAds($number)
    {
        $validator = Validation::createValidator();
        $constraints = [
            new Assert\GreaterThan(0),
            new Assert\LessThan(31)
        ];
        $violations = $validator->validate($number, $constraints);
        return $violations;
    }
}
