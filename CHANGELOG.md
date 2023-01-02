# Changelog

## Versions 

### 0.5.1

- Fixed `View` implemented `ViewInterface`. 

### 0.5.0

- Adjusted `Service::executePost()` allowed send query params.
- Adjusted `App::createResponse()` and `App::createView()` added type hints.
- Introduced new `ViewInterface`. 

### 0.4.0 

- Introduced abastract Enum class  

### 0.3.0

- Introduced data transfer object based on [spatie/data-transfer-object v1](https://github.com/spatie/data-transfer-object/tree/1.14.1) 

### 0.2.0

- Introduced validator based on [rakit/validation](https://github.com/rakit/validation) 

### 0.1.2

- Changed default charset for mysql connection utf8mb4 
- Added environment variable `PDO_CHARSET` for set default charset 


### 0.1.0

- Added update  functional for active record model 
- Added basic service class for call external services 

