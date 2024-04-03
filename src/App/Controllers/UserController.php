<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\UserEntity;
use App\Exceptions\MyException;
use App\Repositories\UserRepository;
use App\ViewInterface;
use Override;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * https://symfony.com/doc/current/validation.html
 * https://symfony.com/doc/current/reference/constraints/Type.html
 */

class UserController extends Controller
{
    public function __construct(
        protected LoggerInterface $logger,
        protected ViewInterface $view
    ) {
        $this->repository = new UserRepository();
        $this->exception = new MyException();
        $this->entity = new UserEntity();
        $this->viewTemplate = 'user.twig';
    }

    #[Override]
    protected function getConstraintGetById(): Constraint
    {
        return new Assert\Type('numeric', 'Element id is not numeric!');
    }

    #[Override]
    protected function getConstraintCreate(): Constraint
    {
        return new Assert\Collection([
            'name' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 2]),
                new Assert\Type('string')
            ],
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email()
            ],
            'phone' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 11]),
                new Assert\Type('string')
            ],
            'password' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 6]),
                new Assert\Type('string')
            ]
        ]);
    }

    #[Override]
    protected function getConstraintUpdate(): Constraint
    {
        return new Assert\Collection([
            'name' => new Assert\Optional([
                new Assert\Length(['min' => 2]),
                new Assert\Type('string')
            ]),
            'email' => new Assert\Optional([
                new Assert\Email()
            ]),
            'phone' => new Assert\Optional([
                new Assert\Length(['min' => 11]),
                new Assert\Type('string')
            ]),
            'password' => new Assert\Optional([
                new Assert\Length(['min' => 6]),
                new Assert\Type('string')
            ]),
            'is_admin' => new Assert\Optional([
                new Assert\Type('bool')
            ])
        ]);
    }

    #[Override]
    protected function getConstraintDelete(): Constraint
    {
        return $this->getConstraintGetById();
    }
}