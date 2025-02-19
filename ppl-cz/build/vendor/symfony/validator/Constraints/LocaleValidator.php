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

use PPLCZVendor\Symfony\Component\Intl\Locales;
use PPLCZVendor\Symfony\Component\Validator\Constraint;
use PPLCZVendor\Symfony\Component\Validator\ConstraintValidator;
use PPLCZVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use PPLCZVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
/**
 * Validates whether a value is a valid locale code.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class LocaleValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Locale) {
            throw new UnexpectedTypeException($constraint, Locale::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }
        $inputValue = (string) $value;
        $value = $inputValue;
        if ($constraint->canonicalize) {
            $value = \Locale::canonicalize($value);
        }
        if (!Locales::exists($value)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($inputValue))->setCode(Locale::NO_SUCH_LOCALE_ERROR)->addViolation();
        }
    }
}
