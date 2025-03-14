<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLCZVendor\Symfony\Component\Validator\Context;

use PPLCZVendor\Symfony\Component\Validator\Validator\ValidatorInterface;
use PPLCZVendor\Symfony\Contracts\Translation\TranslatorInterface;
/**
 * Creates new {@link ExecutionContext} instances.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @internal version 2.5. Code against ExecutionContextFactoryInterface instead.
 */
class ExecutionContextFactory implements ExecutionContextFactoryInterface
{
    private $translator;
    private $translationDomain;
    public function __construct(TranslatorInterface $translator, ?string $translationDomain = null)
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }
    /**
     * {@inheritdoc}
     */
    public function createContext(ValidatorInterface $validator, $root)
    {
        return new ExecutionContext($validator, $root, $this->translator, $this->translationDomain);
    }
}
