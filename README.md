# php-micro
Simple php micro framework

### DTO 

See more https://github.com/spatie/data-transfer-object/blob/1.14.1/README.md



### Enum 

Class usage example:
```php 
class StatusEnum extends Enum
{

   const ON = 1;
   const OFF = 0;


   public static function getLabels()
    {
        return [
           self::ON   => 'Turn on',
           self::OFF   => 'Turn off',
        ];
    }
}
```
