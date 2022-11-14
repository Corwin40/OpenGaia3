<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof Member) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function vueFront()
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.type = :type')
            ->setParameter('val', 'membre')
            ->orderBy('m.lastName', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function listMembersOnFront()
    {
        return $this->createQueryBuilder('m')
            ->select('
                m.id as id, 
                m.firstName as firstName, 
                m.lastName as lastName, 
                m.avatarName AS avatarName, 
                m.email AS email, 
                s.name AS structure,
                m.isVerified AS isVerified,
                s.id AS idStruct, 
                s.FirstActivity AS FirstActivity, 
                s.city AS city, 
                s.urlWeb AS web, 
                s.urlFacebook AS Facebook, 
                s.urlLinkedin AS Linkedin, 
                s.urlInstagram AS Instagram,
                s.logoStructureName AS logoStructureName,
                t.id As idteam,
                t.name AS team
                ')
            ->join('m.structure', 's')
            ->join('s.Team', 't')
            ->where('m.isVerified = :isVerified')
            ->andWhere('m.type = :type')
            ->setParameter('isVerified', 1)
            ->setParameter('type', 'member')
            ->orderBy('m.lastName', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function MemberOnFront($id)
    {
        return $this->createQueryBuilder('m')
            ->select('
                m.id as id,
                m.firstName as firstName,
                m.lastName as lastName,
                s.description AS description,
                m.avatarName AS avatarName, 
                m.email AS adherent_email,
                m.phoneDesk AS adherent_desk,
                m.phoneGsm AS adherent_gsm,
                s.id as idstructure,
                s.name as structure,
                s.respFirstName AS respFirstName,
                s.respLastName AS respLastName,
                s.EmailStruct AS EmailStruct,
                s.phoneDesk AS phoneDesk,
                s.phoneGsm AS phoneGsm,
                s.address AS address,
                s.complement AS complement,
                s.zipcode AS zipcode,
                s.FirstActivity as FirstActivity,
                s.secondActivity AS secondActivity,
                s.city as city,
                s.urlWeb AS urlWeb,
                s.urlFacebook AS Facebook, 
                s.urlLinkedin AS Linkedin, 
                s.urlInstagram AS Instagram,
                s.logoStructureName AS logoStructureName,
                s.illustrationName AS illustrationName,
                s.illustrationtwoName AS illustrationtwoName,
                s.illustrationthirdName AS illustrationthirdName
                ')
            ->join('m.structure', 's')
            ->where('m.id = :member')
            ->setParameter('member', $id)
            ->orderBy('m.lastName', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return member[] Returns an array of member objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?member
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
