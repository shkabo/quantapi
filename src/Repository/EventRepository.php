<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function topFiveLastSevenDays()
    {
        // find top 5 countries to use them in IN clause of query
        $countryCodes = $this->findTopCountries(5);

        $expr = $this->getEntityManager()->getExpressionBuilder();
        $select = $this->createQueryBuilder('e')
            ->select(['e.datum', 'e.countryCode', 'e.eventType', 'SUM(e.ammount) as CNT'])
            ->where('e.datum >= :datum')
            ->setParameter('datum', (new \DateTime('-7 days'))->format('Y-m-d'))
            ->andWhere($expr->in('e.countryCode', ':countryCodes'))
            ->setParameter('countryCodes', $countryCodes)
            ->groupBy('e.datum')
            ->addGroupBy('e.countryCode')
            ->addGroupBy('e.eventType')
            ->orderBy('e.datum', 'DESC')
            ->addOrderBy('e.countryCode', 'DESC')
            ->addOrderBy('e.eventType', 'DESC');

        return $select->getQuery()->getResult();

//          select e.datum
//                    ,e.countryCode
//                        ,e.eventType
//                    ,sum(e.ammount) as cnt
//                from event as e
//                join (
//                    select countryCode
//                        ,sum(ammount) as cnt
//                    from event
//                    group by countryCode
//                    order by cnt desc
//                    limit 5
//                ) as v
//                on v.countryCode = e.countryCode
//                where datum >= current_date() - 7
//                and e.countryCode = v.countryCode
//                group by datum, countryCode, eventType
//                order by v.cnt desc, e.datum desc, e.eventType
    }


    /**
     * Find top n Countries by summarized events
     * @param int $limit
     * @return array
     */
    public function findTopCountries(int $limit): array
    {
        $subSelect = $this->createQueryBuilder('q')
            ->select(['q.countryCode', 'SUM(q.ammount) as CNT'])
            ->groupBy('q.countryCode')
            ->orderBy('CNT', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);

        $countryCodes = [];
        foreach($subSelect as $row) {
            $countryCodes[] = $row['countryCode'];
        }
        return $countryCodes;
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
