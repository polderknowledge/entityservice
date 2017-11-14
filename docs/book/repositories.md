# Repositories

This library exist out of two layers the first layer of abstraction is the `EntityServiceInterface`
this interface provides a easy to use CRUD interface. Which will work for all services the same way.
The actual connection with your datastorage is done via the Repository.

All Repositories MUST implement the `EntityRepositoryInterface` depending on your needs you SHOULD implement one of the 
feature interfaces. The `AbstractEntityService` will check for this features and throws an exeception when a method 
is not supported by the used Repository.

## Implementations

Currently 2 repositories are implementated

1. Repository backed by a Doctrine EntityManager
2. Repository backed by a Doctrine Collection
