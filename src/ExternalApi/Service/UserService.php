<?php

namespace App\ExternalApi\Service;

use App\Entity\Group;
use App\Entity\User;
use App\ExternalApi\Factory\GroupFactory;
use App\ExternalApi\Factory\PaginationFactory;
use App\ExternalApi\Factory\UserFactory;
use App\ExternalApi\Factory\UserFilterFactory;
use App\ExternalApi\Model\Group\Create\Request as GroupCreateRequest;
use App\ExternalApi\Model\Pagination;
use App\ExternalApi\Model\User\Create\Request as UserCreateRequest;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\ExternalApi\Model\User\User as UserModel;

class UserService
{
    private UserRepository $userRepository;
    private GroupRepository $groupRepository;
    private UserFilterFactory $userFilterFactory;
    private PaginationFactory $paginationFactory;
    private UserFactory $userFactory;
    private GroupFactory $groupFactory;

    public function __construct(
        UserRepository $userRepository,
        GroupRepository $groupRepository,
        UserFilterFactory $userFilterFactory,
        PaginationFactory $paginationFactory,
        UserFactory $userFactory,
        GroupFactory $groupFactory
    )
    {
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
        $this->userFilterFactory = $userFilterFactory;
        $this->paginationFactory = $paginationFactory;
        $this->userFactory = $userFactory;
        $this->groupFactory = $groupFactory;
    }

    public function getUsers(Request $request): Pagination
    {
        $offset = max(0, $request->get('offset', 0));
        $limit = min(100, $request->get('limit', 10));

        $filter = $this->userFilterFactory->createModel($request);

        return $this->paginationFactory->createModel(
            $this->userRepository->getUsers($offset, $limit, $filter),
            $this->userRepository->getTotalUsers($filter),
        );
    }

    /**
     * @throws NotFoundHttpException
     */
    public function getUser(int $id): UserModel
    {
        return $this->userFactory->createModelFromEntity($this->userRepository->findOrFail($id));
    }

    public function createUser(UserCreateRequest $userCreateRequest): void
    {
        $user = $this->userFactory->createEntityFromModel($userCreateRequest);
        $this->userRepository->add($user, true);
    }

    public function removeUser(int $id): void
    {
        $user = $this->userRepository->findOrFail($id);

        $this->userRepository->remove($user, true);
    }

    public function addUserToGroup(int $userId, int $groupId): void
    {
        $user = $this->userRepository->findOrFail($userId);
        $group = $this->groupRepository->findOrFail($groupId);

        $hasGroup = $user->getGroups()->filter(fn(Group $attachedGroup) => $attachedGroup === $group)->count() > 0;

        if ($hasGroup) {
            throw new ConflictHttpException('User is already in group');
        }

        $user->addGroup($group);
        $this->userRepository->update();
    }

    /**
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function removeUserFromGroup(int $userId, int $groupId): void
    {
        $user = $this->userRepository->findOrFail($userId);
        $group = $this->groupRepository->findOrFail($groupId);
        $hasGroup = $user->getGroups()->filter(fn(Group $attachedGroup) => $attachedGroup === $group)->count() > 0;

        if ($hasGroup === false) {
            throw new BadRequestHttpException('User is not in this group');
        }

        $user->removeGroup($group);
        $this->userRepository->update();
    }

    public function createGroup(GroupCreateRequest $groupCreateRequest): void
    {
        $user = $this->groupFactory->createEntityFromModel($groupCreateRequest);
        $this->groupRepository->add($user, true);
    }

    /**
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function removeGroup(int $groupId)
    {
        $group = $this->groupRepository->findOrFail($groupId);

        if ($group->getUser()->count() > 0) {
            throw new BadRequestHttpException("Group not empty");
        }

        $this->groupRepository->remove($group, true);
    }
}

