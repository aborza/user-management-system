<?php

namespace App\ExternalApi\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ControllerHelper extends AbstractController
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    public function deserializeAndValidate(Request $request, string $class)
    {
        $model = $this->serializer->deserialize($request->getContent(), $class, JsonEncoder::FORMAT);

        $errors = $this->validator->validate($model);

        if (count($errors) > 0) {
            $errMessage['errors'] = [];
            foreach ($errors as $error) {
                $errMessage['errors'][] = [
                    'field' => $error->getPropertyPath(),
                    'error' => $error->getMessage()
                ];
            }

            throw new BadRequestHttpException(json_encode($errMessage));
        }

        return $model;
    }

    public function jsonFromException(HttpException $httpException): Response
    {
        $message = $httpException->getMessage();

        if ($httpException instanceof BadRequestHttpException) {
            $decodedMessage = json_decode($message);
            $message = is_object($decodedMessage) && isset($decodedMessage->errors) ? json_decode($message) : $message;
        }

        return $this->json($message, $httpException->getStatusCode());
    }
}