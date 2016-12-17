# Introduction

Polder Knowledge EntityService is a service layer to interact with any data storage you might want to use.
By using this extra abstraction layer you will be able to create a loos binding with your data storage.
In many situations an ORM is used to connect with a database for example. But what if your users are moved to a
micro-service? You will have to adapt your full application? This is where this library comes in.

It gives you the option to interact with any storage like it was a database. Extra functionality like sending 
an email when a user was created can be added by attaching a listener to the required service.

## Usage

The example below shows a very basic setup of a service.

```php
<?php
  //Container is your applications InteroperableContainer    
  $entityManager = $container->get(Doctrine\ORM\EntityManager::class);
  //Repository can be the provided ORMRepository or any other custom repository you want to use.
  $repository = new \PolderKnowledge\EntityService\Repository\Doctrine\ORMRepository($entityManager, MyEntity::class);
  
  //Setup the service
  $entityService = new \PolderKnowledge\EntityService\EntityService($repository);
  
  //Ready to fetch the required entity.
  $myObject = $entityService->find(1);
```

To attach a new event handler simply create your handler class like the example below.
And attach it using the `attach` method of the EntityService

```php
<?php

use Zend\EventManager\EventManagerInterface;

final class myHandler extends \Zend\EventManager\AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        //Use positive priorities to attach your handler before execution.
        //Use negative priorities to attach your handler after execution.
        $this->listeners[] = $events->attach('persist', array($this, 'onPersist'), 10);
    }
    
    public function onPersist(\PolderKnowledge\EntityService\Event\EntityEvent $event)
    {
        //Do your magic
        
        //Fetch the current entity
        $entity = $event->getParam('entity');
        
        //Call stop propagation to abort
        //$event->stopPropagation();
    }
}
```
For more information about the available events look at the [events] page. When you are not 
familiar with [zend-eventmanager] take a look at their docs.

[events]: ./events
[zend-eventmanager]: https://docs.zendframework.com/zend-eventmanager/
