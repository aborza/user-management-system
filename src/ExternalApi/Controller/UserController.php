<?php

namespace App\ExternalApi\Controller;

use App\ExternalApi\Model\Group\Create\Request as GroupCreateRequest;
use App\ExternalApi\Model\User\Create\Request as UserCreateRequest;
use App\ExternalApi\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/external-api")
 */
class UserController extends AbstractController
{
    private UserService $userService;
    private ControllerHelper $controllerHelper;

    public function __construct(UserService $userService, ControllerHelper $controllerHelper)
    {
        $this->userService = $userService;
        $this->controllerHelper = $controllerHelper;
    }

    /**
     * @Route("/users", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        return $this->json($this->userService->getUsers($request));
    }

    /**
     * @Route("/users/{id}", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getUserById(int $id): Response
    {
        return $this->json($this->controllerHelper->getSerializer()->normalize($this->userService->getUser($id)));
    }

    /**
     * @Route("/users", methods={"POST"})
     * @IsGranted("ROLE_ADMIN_API")
     */
    public function create(Request $request): Response
    {
        try {
            $userCreateRequest = $this->controllerHelper->deserializeAndValidate($request, UserCreateRequest::class);
        } catch (BadRequestHttpException $e) {
            return $this->controllerHelper->jsonFromException($e);
        }

        $this->userService->createUser($userCreateRequest);

        return $this->json([], Response::HTTP_CREATED);
    }

    /**
     * @Route("/users/{id}", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN_API")
     */
    public function delete(int $id): Response
    {
        try {
           $this->userService->removeUser($id);
        } catch (NotFoundHttpException $e) {
            return $this->controllerHelper->jsonFromException($e);
        }

        return $this->json("User with id {$id} was successfully deleted");

    }

    /**
     * @Route("/groups/{groupId}/users/{userId}", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN_API")
     */
    public function addUserToGroup(int $groupId, int $userId): Response
    {
        try {
            $this->userService->addUserToGroup($userId, $groupId);
        } catch (NotFoundHttpException|ConflictHttpException $e) {
            return $this->controllerHelper->jsonFromException($e);
        }
        return $this->json("User with id {$userId} was successfully added to group {$groupId}");

    }

    /**
     * @Route("/groups/{groupId}/users/{userId}", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN_API")
     */
    public function removeUserFromGroup(int $groupId, int $userId): Response
    {
        try {
            $this->userService->removeUserFromGroup($userId, $groupId);
        } catch (NotFoundHttpException|BadRequestHttpException $e) {
            return $this->controllerHelper->jsonFromException($e);
        }

        return $this->json("User with id {$userId} was successfully removed from group {$groupId}");
    }

    /**
     * @Route("/groups", methods={"POST"})
     * @IsGranted("ROLE_ADMIN_API")
     */
    public function createGroup(Request $request): Response
    {
        try {
            $groupCreateRequest = $this->controllerHelper->deserializeAndValidate($request, GroupCreateRequest::class);
        } catch (BadRequestHttpException $e) {
            return $this->controllerHelper->jsonFromException($e);
        }

        $this->userService->createGroup($groupCreateRequest);

        return $this->json([], Response::HTTP_CREATED);
    }

    /**
     * @Route("/groups/{id}", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN_API")
     */
    public function deleteGroup(int $id): Response
    {
        try {
            $this->userService->removeGroup($id);
        } catch (BadRequestHttpException|NotFoundHttpException $e) {
            return $this->controllerHelper->jsonFromException($e);
        }

        return $this->json("Group with id {$id} was successfully deleted");
    }
}
