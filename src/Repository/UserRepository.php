<?php

namespace App\Repository;

use App\Entity\User;
use App\ExternalApi\Model\User\Filter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getUsers(int $offset, int $limit, Filter $filter): array
    {
        $queryBuilder = $this->getQueryBuilderWithFilters($filter)
            ->setMaxResults($limit)
            ->setFirstResult($offset);


        return $queryBuilder->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getTotalUsers(Filter $filter): int
    {
        return $this->getQueryBuilderWithFilters($filter)->select('count(u.id)')->getQuery()->getSingleScalarResult();
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @throws NotFoundHttpException
     */
    public function findOrFail(int $id): User
    {
        $user = $this->find($id);

        if ($user === null) {
            throw new NotFoundHttpException("User with id {$id} was not found");
        }

        return $user;
    }

    private function getQueryBuilderWithFilters(Filter $filter): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->select('u.id, u.email, u.firstName, u.lastName, u.active, u.createdAt, u.updatedAt')
            ->where('u.roles like :role')
            ->setParameter('role', '%ROLE_USER%')
        ;

        return $this->addFilters($queryBuilder, $filter);
    }

    private function addFilters(QueryBuilder $queryBuilder, Filter $filter): QueryBuilder
    {
        $this->addFilter($queryBuilder, 'email', $filter->getEmail());
        $this->addFilter($queryBuilder, 'firstName', $filter->getFirstName());
        $this->addFilter($queryBuilder, 'lastName', $filter->getLastName());

        return $queryBuilder;
    }

    private function addFilter(QueryBuilder $queryBuilder, string $columnName, ?string $value)
    {
        if ($value === null) {
            return;
        }

        $queryBuilder->andWhere("u.{$columnName} = :{$columnName}")
            ->setParameter($columnName, $value);
    }
}
