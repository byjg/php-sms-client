# Changelog - Version 6.0

## Overview

Version 6.0 is a major modernization release that updates the project to support the latest PHP versions (8.3-8.5), PHPUnit 11, and Psalm 6. This release includes breaking changes to minimum requirements and several code quality improvements.

## New Features

### Enhanced Type Safety
- Added stricter type hints and annotations throughout the codebase
- Added `#[Override]` attributes to all overridden methods for better IDE support
- Added PHPDoc annotations with `non-empty-string` types for PhoneFormat properties
- Improved return type declarations across all classes

### Comprehensive Documentation
- Created extensive documentation in the `docs/` folder:
  - **basic-usage.md**: Complete usage examples and ProviderFactory guide
  - **phone-formatting.md**: Phone number validation and formatting guide
  - **providers.md**: Detailed provider documentation and comparison table
  - **custom-providers.md**: Step-by-step guide for creating custom providers
- Updated README.md with documentation links and improved structure
- Added mermaid diagram for dependency visualization

### Modern Testing Standards
- PHPUnit 11 full compatibility
- Converted `@dataProvider` doc-comments to `#[DataProvider]` attributes
- Made all data provider methods static (PHPUnit 11 requirement)
- All 67 tests pass without errors or deprecations

### Improved Static Analysis
- Psalm 6 support with zero errors
- 97.28% type coverage
- Added cacheDirectory to psalm.xml for better performance
- Fixed InvalidNullableReturnType issues

### Enhanced CI/CD
- Updated GitHub Actions workflow for PHP 8.3-8.4 testing
- Improved container configuration
- Upgraded actions/checkout to v5
- Added Composer scripts for `test` and `psalm` commands

## Bug Fixes

- Fixed Psalm static analysis issues:
  - Resolved InvalidNullableReturnType in `Phone::format()`
  - Fixed missing return type declarations
  - Fixed missing parameter type declarations
  - Removed trailing whitespace from type annotations
- Fixed class_implements() error handling in ProviderFactory
- Fixed preg_replace null return handling in Phone constructor

## Breaking Changes

| Before (5.x) | After (6.0) | Description |
|--------------|-------------|-------------|
| PHP >= 8.1 < 8.4 | PHP >= 8.3 < 8.6 | **Minimum PHP version increased to 8.3** |
| byjg/webrequest ^5.0 | byjg/webrequest ^6.0 | Updated dependency to major version 6 |
| phpunit ^9.6 | phpunit ^10.5\|^11.5 | PHPUnit upgraded to versions 10/11 |
| psalm ^5.9 | psalm ^5.9\|^6.13 | Psalm 6 support added |
| class Phone | final class Phone | Phone class is now final (cannot be extended) |
| class ProviderFactory | final class ProviderFactory | ProviderFactory class is now final |
| class FakeProvider | final class FakeProvider | FakeProvider class is now final |
| class TwilioVerifyProvider | final class TwilioVerifyProvider | Provider classes are now final |
| Data provider methods | Static data provider methods | All PHPUnit data providers must be static |
| Phone::format(): string | Phone::format(): string\|null | Return type now allows null |
| @dataProvider docblock | #[DataProvider] attribute | PHPUnit attributes replace docblocks |
| No #[Override] | #[Override] on methods | Override attributes required for interface methods |

## Migration Guide from 5.x to 6.x

### Step 1: Update PHP Version
Ensure your environment is running **PHP 8.3 or higher**:
```bash
php --version  # Should show 8.3.0 or higher
```

### Step 2: Update Dependencies
Update your `composer.json`:
```bash
composer require byjg/sms-client:^6.0
composer update
```

### Step 3: Check Class Extensions
If you extended any of these classes in your code, you'll need to refactor:
- `Phone` - Now final, use composition instead of inheritance
- `ProviderFactory` - Now final, use static methods directly
- Provider classes - Now final, implement `ProviderInterface` directly

**Before:**
```php
class MyCustomPhone extends Phone {
    // Your custom logic
}
```

**After:**
```php
class MyCustomPhone {
    private Phone $phone;

    public function __construct(Phone $phone) {
        $this->phone = $phone;
    }

    // Your custom logic using $this->phone
}
```

### Step 4: Update PHPUnit Tests (if applicable)
If you have custom tests using data providers:

**Before:**
```php
/**
 * @dataProvider phoneProvider
 */
public function testPhone($number) { }

public function phoneProvider() { }
```

**After:**
```php
#[DataProvider('phoneProvider')]
public function testPhone($number) { }

public static function phoneProvider() { }  // Must be static
```

### Step 5: Handle Nullable Return Types
The `Phone::format()` method now returns `string|null`:

**Before:**
```php
$formatted = $phone->format();  // Always string
```

**After:**
```php
$formatted = $phone->format();  // May be null
if ($formatted !== null) {
    echo $formatted;
}
```

### Step 6: Update Type Declarations (if using static analysis)
If you're using Psalm or PHPStan, run analysis and fix any new type errors:
```bash
composer psalm
```

### Step 7: Test Your Application
Run your full test suite to ensure everything works:
```bash
composer test
```

## Additional Notes

- All provider classes are now marked as `final` to prevent unintended inheritance
- The codebase now has comprehensive documentation in the `docs/` folder
- GitHub Actions now tests against PHP 8.3 and 8.4
- The project description has been updated to better reflect its purpose
- A Sponsor badge has been added to the README

## Upgrade Checklist

- [ ] Verify PHP version is 8.3 or higher
- [ ] Run `composer update` to get version 6.0
- [ ] Remove any class extensions of Phone, ProviderFactory, or Provider classes
- [ ] Update PHPUnit data providers to be static (if applicable)
- [ ] Handle nullable return from `Phone::format()`
- [ ] Run static analysis tools (Psalm/PHPStan)
- [ ] Run your test suite
- [ ] Review new documentation in `docs/` folder

## Resources

- [Basic Usage Guide](docs/basic-usage.md)
- [Phone Formatting Guide](docs/phone-formatting.md)
- [Providers Documentation](docs/providers.md)
- [Custom Providers Guide](docs/custom-providers.md)
