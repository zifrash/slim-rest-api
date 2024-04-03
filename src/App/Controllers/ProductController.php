<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\ProductEntity;
use App\Exceptions\MyException;
use App\Repositories\ProductRepository;
use App\ViewInterface;
use Override;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * https://symfony.com/doc/current/validation.html
 * https://symfony.com/doc/current/reference/constraints/Type.html
 */

class ProductController extends Controller
{
    public function __construct(
        protected LoggerInterface $logger,
        protected ViewInterface $view
    ) {
        $this->repository = new ProductRepository();
        $this->exception = new MyException();
        $this->entity = new ProductEntity();
        $this->viewTemplate = 'product.twig';
    }

    #[Override]
    function getConstraintCreate(): Constraint
    {
        return new Assert\Collection([
            'name' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 2]),
                new Assert\Type('string')
            ],
            'price' => [
                new Assert\NotBlank(),
                new Assert\Type('float')
            ],
            'quantity' => [
                new Assert\NotBlank(),
                new Assert\Type('int')
            ]
        ]);
    }

    #[Override]
    function getConstraintUpdate(): Constraint
    {
        return new Assert\Collection([
            'name' => new Assert\Optional([
                new Assert\Length(['min' => 2]),
                new Assert\Type('string')
            ]),
            'price' => new Assert\Optional([
                new Assert\Type('float')
            ]),
            'quantity' => new Assert\Optional([
                new Assert\Type('int')
            ]),
        ]);
    }

    #[Override]
    protected function getConstraintGetById(): Constraint
    {
        return new Assert\Type('numeric', 'Element id is not numeric!');
    }

    #[Override]
    protected function getConstraintDelete(): Constraint
    {
        return $this->getConstraintGetById();
    }
}