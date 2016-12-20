# Events

One of the most important ways to extend the behavior of an EntityService are the
events triggered by the service. The `AbstractEntityService` class is fully event
driven. Even the repository used by the service is triggered via the same event.
This allows the user to prevent the real execution of the repository. Or modify the
data that will be processed. E.g. add extra criteria to a find event to fetch only
elements that are owned by the current signed in user. Or add the id of the current
persisted entity.

Each event handler has a execution priority, the repository is executed on priority `0`. It
is not recommended to attach other listeners at the same priority since the order of execution 
is not guaranteed by zend-eventmanager. Each method triggers a single event so priority is required
to manipulate the order of execution. Use positive numbers to be able to execute your handler before the
repository or negative numbers to execute your handler post repository. 

> Be aware of the fact that you won't be able to stop propagation after the repository was executed. Validation or any other
shortcut to prevent the repository from execution have to be done pre-repository.

## Available events

In this chapter all default events of the `AbstractEntityServices` are described.

### countBy

| Parameter | description |
| --------- | ----------- |
| criteria  | Can be a Criteria object or an array |

### delete

| Parameter | description |
| --------- | ----------- |
| entity    | Object to remove |


### deleteBy

| Parameter | description |
| --------- | ----------- |
| criteria  | Can be a Criteria object or an array |

### find

| Parameter | description |
| --------- | ----------- |
| id        | Id of the entity to find |

### findAll

No parameters available

### findOneBy

| Parameter | description |
| --------- | ----------- |
| criteria  | Can be a Criteria object or an array |

### findBy

| Parameter | description |
| --------- | ----------- |
| criteria  | Can be a Criteria object or an array |

### flush

| Parameter | description |
| --------- | ----------- |
| entity    | Entity to flush, can be null |


### persist

| Parameter | description |
| --------- | ----------- |
| entity    | Entity to persist |


### multiPersist

| Parameter | description |
| --------- | ----------- |
| entities    | Entities to flush, can be null |
