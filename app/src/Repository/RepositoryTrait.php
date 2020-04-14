<?php

namespace App\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

/**
 * Trait RepositoryTrait
 * @package App\Repository
 * @property EntityManagerInterface $_em
 */
trait RepositoryTrait
{
    /**
     * @var Connection[]
     */
    private $connections;

    /**
     * @param object $entity
     * @param bool $flush
     */
    public function delete(object $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if($flush) {
            $this->_em->flush();
        }

        return;
    }

    /**
     * @param object $entity
     * @param bool $flush
     * @return object|null
     */
    public function insert(object $entity, bool $flush = true): ?object
    {
        $this->_em->persist($entity);
        if($flush) {
            $this->_em->flush();
        }

        return $entity;
    }

    /**
     * @param object $entity
     * @param bool $flush
     * @return object
     */
    public function update(object $entity, bool $flush = true): object
    {
        $this->_em->persist($entity);
        if($flush) {
            $this->_em->flush();
        }

        return $entity;
    }

    /**
     * @param string $class
     * @param string $id
     * @param bool $flush
     * @throws ORMException
     */
    public function removeById(string $class, string $id, bool $flush = true)
    {
        $entity = $this->_em->getReference($class, $id);
        $this->_em->remove($entity);

        if($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param object $entity
     * @param bool $flush
     * @return object|null
     */
    public function remove(object $entity, bool $flush = true): ?object
    {
        $this->_em->remove($entity);
        if($flush) {
            $this->_em->flush();
        }

        return $entity;
    }

    /**
     * @param object $entity
     * @param bool $flush
     * @return object
     */
    public function merge(object $entity, bool $flush = true): object
    {
        $this->_em->merge($entity);
        if($flush) {
            $this->_em->flush();
        }

        return $entity;
    }

    /**
     * Flushes all changes to objects that have been queued up to now to the database.
     */
    public function flush(): void
    {
        $this->_em->flush();
        if(FALSE === $this->_em->getConnection()->ping()){
            $this->_em->getConnection()->close();
            $this->_em->getConnection()->connect();
        }
        return;
    }

    /**
     * @param string|null $objectName
     */
    public function clear(string $objectName = null): void
    {
        $this->_em->clear($objectName);
        if(FALSE === $this->_em->getConnection()->ping()){
            $this->_em->getConnection()->close();
            $this->_em->getConnection()->connect();
        }
        return;
    }

    /**
     * @required
     * @param ManagerRegistry $managerRegistry
     */
    public function setSecurity(
        ManagerRegistry $managerRegistry
    ) {
        $this->connections = $managerRegistry->getConnections();
    }

    /**
     * @param string $connectionName
     * @return Connection|object
     */
    protected function getConnection(string $connectionName) : Connection
    {
        return $this->connections[$connectionName];
    }

    /**
     * @param string $connectionName
     * @return string
     */
    protected function getDatabase(string $connectionName) : string
    {
        return $this->connections[$connectionName]->getDatabase();
    }

}