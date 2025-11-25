# Testing Guide

## Setup

1. Install PHPUnit:
```bash
composer require --dev phpunit/phpunit
```

2. Run tests:
```bash
vendor/bin/phpunit
```

3. Run specific test suite:
```bash
vendor/bin/phpunit tests/Unit
vendor/bin/phpunit tests/Integration
```

## Writing Tests

### Unit Tests
- Test individual classes/methods in isolation
- Mock dependencies
- Fast execution
- Location: `tests/Unit/`

### Integration Tests
- Test interactions between components
- May use test database
- Slower execution
- Location: `tests/Integration/`

## Example Test Structure

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tuezy\ValidationHelper;

class ValidationHelperTest extends TestCase
{
    public function testRequiredValidation(): void
    {
        $func = $this->createMock(\stdClass::class);
        $validator = new ValidationHelper($func);
        
        $result = $validator->required('value', 'Field');
        $this->assertTrue($result);
        $this->assertEmpty($validator->getErrors());
    }
}
```

## Test Coverage

Run with coverage:
```bash
vendor/bin/phpunit --coverage-html coverage/
```

