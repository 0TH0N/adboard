<?php

namespace AdBoard\Tests;

use PHPUnit\Framework\TestCase;
use AdBoard\Validator;

class ValidatorTest extends TestCase
{
    public function testValidateAdData()
    {
        $rightAdData = [
            'ad-text' => 'Super puper ad dgdsf sf gs fgsd',
            'user-name' => 'Mikhail',
            'password' => 'password',
            'phone' => '+79506512684'
        ];
        $violations = Validator::validateAdData($rightAdData);
        $allViolations = [];
        foreach ($violations as $group) {
            foreach ($group as $violation) {
                $allViolations[] = $violation;
            }
        }
        $this->assertEmpty($allViolations);
        $wrongAdData = [
            'ad-text' => 'Su',
            'user-name' => 'M',
            'password' => 'p',
            'phone' => '+7950'
        ];
        $violations = Validator::validateAdData($wrongAdData);
        $allViolations = [];
        foreach ($violations as $group) {
            foreach ($group as $violation) {
                $allViolations[] = $violation;
            }
        }
        $this->assertNotEmpty($allViolations);
    }

    public function testValidateNumberFakeAds()
    {
        $violations = Validator::validateNumberFakeAds(-3);
        $allViolations = [];
        foreach ($violations as $violation) {
            $allViolations[] = $violation;
        }
        $this->assertNotEmpty($allViolations);

        $violations = Validator::validateNumberFakeAds(5);
        $allViolations = [];
        foreach ($violations as $violation) {
            $allViolations[] = $violation;
        }
        $this->assertEmpty($allViolations);

        $violations = Validator::validateNumberFakeAds(35);
        $allViolations = [];
        foreach ($violations as $violation) {
            $allViolations[] = $violation;
        }
        $this->assertNotEmpty($allViolations);
    }
}
