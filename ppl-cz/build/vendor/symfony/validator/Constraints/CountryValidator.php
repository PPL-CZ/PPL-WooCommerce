<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLCZVendor\Symfony\Component\Validator\Constraints;

use PPLCZVendor\Symfony\Component\Intl\Countries;
use PPLCZVendor\Symfony\Component\Validator\Constraint;
use PPLCZVendor\Symfony\Component\Validator\ConstraintValidator;
use PPLCZVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use PPLCZVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
/**
 * Validates whether a value is a valid country code.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class CountryValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Country) {
            throw new UnexpectedTypeException($constraint, Country::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }
        $value = (string) $value;
        if ($constraint->alpha3 ? !Countries::alpha3CodeExists($value) : !Countries::exists($value)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Country::NO_SUCH_COUNTRY_ERROR)->addViolation();
        }
    }
}
