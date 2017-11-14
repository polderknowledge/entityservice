# Changelog

All Notable changes to `entityservice` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## 4.1.0

- Added a repository backed by a Doctrine Collection

## 4.0.0

- Removed flush method

## 3.0.0 - 2016-12-02

### Added
- New version of the library where all Zend Framework components are moved out of the repository.
- Made the repository ready for open source.

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

## 2.0.6 - 2016-07-26

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Disabled autoAddInvokableClass for ServiceManagers
- Fixed unit tests.
- Added a Gitlab CI build script.

### Removed
- Nothing

### Security
- Nothing

## 2.0.5 - 2016-01-05

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Check for implementation of Identifiable 
- Added a missing use statement

### Removed
- Nothing

### Security
- Nothing

## 2.0.4 - 2015-10-12

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Updated the installation instructions in the README file.
- Made sure we use getOneOrNullResult so that Doctrine doesn't throw an exception.

### Removed
- Nothing

### Security
- Nothing

## 2.0.3 - 2015-10-09

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Fixed the findOneBy method for when no criteria is passed.
- Made sure that the result is limited to one result.

### Removed
- Nothing

### Security
- Nothing

## 2.0.2 - 2015-10-08

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Fixed a bug in the countBy method because the criteria object was modified and should have been cloned. 

### Removed
- Nothing

### Security
- Nothing

## 2.0.1 - 2015-10-06

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- The ORM repository can now also handle the Criteria object correctly.
- The build tools also checks the CS correctly now.

### Removed
- Nothing

### Security
- Nothing

## 2.0.0 - 2015-08-10

### Added
- Simplified the API, no longer returning a ServiceResult.

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- The ServiceProblem class is removed.
- The ServiceResult class is removed.

### Security
- Nothing

## 1.0.0 - 2015-07-06

### Added
- The initial release.

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing
