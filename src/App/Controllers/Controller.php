<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\EntityInterface;
use App\Exceptions\ExceptionInterface;
use App\Helpers\JsonHelper;
use App\Repositories\RepositoryInterface;
use App\ViewInterface;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

abstract class Controller
{
    protected RepositoryInterface $repository;
    protected LoggerInterface $logger;
    protected Exception $exception;
    protected EntityInterface $entity;
    protected ViewInterface $view;
    protected string $viewTemplate;

    public function getList(Request $request, Response $response): Response
    {
        $entities = $this->repository->getAll();

        $this->logger->log(LogLevel::INFO, "{$response->getStatusCode()} getAll");

        $response->getBody()->write(JsonHelper::toJson($entities));

        return $response;
    }

    abstract protected function getConstraintGetById(): Constraint;
    public function getById(Request $request, Response $response, string $id): Response
    {
        try {
            $this->validate($id, $this->getConstraintGetById());

            $entity = $this->repository->getById($id);
        } catch (ExceptionInterface $e) {
            $this->logger->log(LogLevel::ERROR, "{$e->getCode()} getById {$e->getMessage()}");

            $response->getBody()->write($e->getErrorTemplateJson());
            return $response->withStatus($e->getCode());
        }

        $this->logger->log(LogLevel::INFO, "{$response->getStatusCode()} getById");

        $response->getBody()->write(JsonHelper::toJson($entity));

        return $response;
    }

    abstract protected function getConstraintCreate(): Constraint;
    public function create(Request $request, Response $response): Response
    {
        try {
            $this->validate($request->getParsedBody(), $this->getConstraintCreate());

            $newEntity = $this->entity::create($request->getParsedBody());
            $entity = $this->repository->create($newEntity);
        } catch (ExceptionInterface $e) {
            $this->logger->log(LogLevel::ERROR, "{$e->getCode()} create {$e->getMessage()}");

            $response->getBody()->write($e->getErrorTemplateJson());
            return $response->withStatus($e->getCode());
        }

        $this->logger->log(LogLevel::INFO, "{$response->getStatusCode()} create");

        $response->getBody()->write(JsonHelper::toJson($entity));

        return $response->withStatus(201);
    }

    abstract protected function getConstraintUpdate(): Constraint;
    public function update(Request $request, Response $response, string $id): Response
    {
        try {
            $this->validate($id, $this->getConstraintGetById());
            $this->validate($request->getParsedBody(), $this->getConstraintUpdate());

            $entity = $this->repository->update($id, $request->getParsedBody());
        } catch (ExceptionInterface $e) {
            $this->logger->log(LogLevel::ERROR, "{$e->getCode()} update {$e->getMessage()}");

            $response->getBody()->write($e->getErrorTemplateJson());
            return $response->withStatus($e->getCode());
        }

        $this->logger->log(LogLevel::INFO, "{$response->getStatusCode()} update");

        $response->getBody()->write(JsonHelper::toJson($entity));

        return $response;
    }

    abstract protected function getConstraintDelete(): Constraint;
    public function delete(Request $request, Response $response, string $id): Response
    {
        try {
            $this->validate($id, $this->getConstraintDelete());

            $isDelete = $this->repository->delete($id);
        } catch(ExceptionInterface $e) {
            $this->logger->log(LogLevel::ERROR, "{$e->getCode()} delete {$e->getMessage()}");

            $response->getBody()->write($e->getErrorTemplateJson());
            return $response->withStatus($e->getCode());
        }

        $this->logger->log(LogLevel::INFO, "{$response->getStatusCode()} delete");

        $response->getBody()->write(JsonHelper::toJson($isDelete));

        return $response;
    }

    private function validate(mixed $value, Constraint $constraint): void
    {
        $validator = Validation::createValidator();
        $error = $validator->validate($value, $constraint);

        if (count($error) > 0) {
            throw new $this->exception($error->get(0)->getMessage(), 422);
        }
    }
}