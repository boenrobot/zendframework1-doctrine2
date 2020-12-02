<?php

namespace Bisna\Application\Resource;

use Bisna\Doctrine\Container as DoctrineContainer;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;

/**
 * Zend Application Resource Doctrine class
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 */
class Doctrine extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Bisna\Doctrine\Container
     */
    protected $container;

    /**
     * Initializes Doctrine Context.
     *
     * @return Bisna\Doctrine\Container
     * @throws \Bisna\Exception\NameNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function init()
    {
        $config = $this->getOptions();
        
        // Starting Doctrine container
        $this->container = new DoctrineContainer($config);

        if (!empty($repositoryFactory = $config['orm']['entityManagers']['default']['repositoryFactory'])) {
            $this->container->getEntityManager()->getConfiguration()->setRepositoryFactory(
                new $repositoryFactory
            );
        }

        /* @var AnnotationDriver $annotationReader */
        $annotationReader = $this->container->getEntityManager()->getConfiguration()->getMetadataDriverImpl();

        \Gedmo\DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
            new MappingDriverChain,
            $annotationReader->getReader()
        );

        // Add to Zend Registry
        \Zend_Registry::set('doctrine', $this->container);

        return $this->container;
    }
    
    /**
     * Retrieve the Doctrine Container.
     *
     * @return Bisna\Doctrine\Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}