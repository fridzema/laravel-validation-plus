# Changelog

All notable changes to `laravel-validation-plus` will be documented in this file.

## 1.0.2 - 2026-03-03

### [1.0.2](https://github.com/fridzema/laravel-validation-plus/compare/v1.0.1...v1.0.2) (2026-03-03)


### Bug Fixes

* **ci:** drop PHP 8.2 from test matrix (Pest 4 requires ^8.3) ([82a97e5](https://github.com/fridzema/laravel-validation-plus/commit/82a97e5e4b119587f893a425f7aa0f8d27aaae00))



## 1.0.1 - 2026-03-03

### [1.0.1](https://github.com/fridzema/laravel-validation-plus/compare/v1.0.0...v1.0.1) (2026-03-03)


### Styles

* fix not operator spacing in ShareWarnings ([c80885b](https://github.com/fridzema/laravel-validation-plus/commit/c80885bf8a47b3e6aee08780260a415ddde52325))




## 1.0.0 - 2026-03-03

## [1.0.0](https://github.com/fridzema/laravel-validation-plus/compare/v0.0.0...v1.0.0) (2026-03-03)


### ⚠ BREAKING CHANGES

* first stable release

### Features

* add CI/CD suite with automatic release flow ([febd08e](https://github.com/fridzema/laravel-validation-plus/commit/febd08ebc3ec14e038432a18463af37b248348f7))
* add HasWarningRules trait for FormRequest ([13739c7](https://github.com/fridzema/laravel-validation-plus/commit/13739c74681559f1c14a2c7f01eb7f013cf6805d))
* add optional Blade warnings component ([dfae98f](https://github.com/fridzema/laravel-validation-plus/commit/dfae98f16673de45236abb46db9710ce2f86c2f0))
* add service provider, config, global helper, and facade ([0cb51dc](https://github.com/fridzema/laravel-validation-plus/commit/0cb51dc384bc8d3b629280fd4c81dcc31a2ede59))
* add ShareWarnings middleware for session/API integration ([f64cfd6](https://github.com/fridzema/laravel-validation-plus/commit/f64cfd60249ba23f8d1bb4e5a396e2a9ce6f08dc))
* add WarningBag extending MessageBag ([1dd5c85](https://github.com/fridzema/laravel-validation-plus/commit/1dd5c852d994d6973d0691ea1e420ce5d48cbc14))
* add WarningValidator that runs rules without throwing ([1724f88](https://github.com/fridzema/laravel-validation-plus/commit/1724f883c9b58ca076abe59e442828ed939a101c))
* add X-Validation-Warnings-Data JSON header to responses ([aac9a4e](https://github.com/fridzema/laravel-validation-plus/commit/aac9a4eba8e0ef241ce3afdac30201bbf04cbfda))
* move warning evaluation to withValidator after callback for Precognition support ([d13fa4f](https://github.com/fridzema/laravel-validation-plus/commit/d13fa4f1d53a68c7007409b76447f4f79faa6038))


### Bug Fixes

* add header fallback to test macro warning resolution ([bcc46e9](https://github.com/fridzema/laravel-validation-plus/commit/bcc46e9f99406ee6f8f21c97a948038df5cd9579))
* guard list arrays and merge existing warnings in ShareWarnings ([3eb5817](https://github.com/fridzema/laravel-validation-plus/commit/3eb58174a30f38ab0c4251e0359c6f2128e39c70))
* guard scalar JSON payloads in ShareWarnings middleware ([5ef181c](https://github.com/fridzema/laravel-validation-plus/commit/5ef181c766c2bc244be2ea950c0122393e440e10))
* use scoped binding and view composer for Octane safety ([20fcd32](https://github.com/fridzema/laravel-validation-plus/commit/20fcd3244db93e525dbb9fa2a586d6117b90890f))
* **ci:** enable fetch_all_tags for correct first-release version bump ([c57379f](https://github.com/fridzema/laravel-validation-plus/commit/c57379f9d8594ce497b6a06fb75a284840d37e26))


### Code Refactoring

* replace after() with getValidatorInstance() to avoid trait conflicts ([9a1de6b](https://github.com/fridzema/laravel-validation-plus/commit/9a1de6beab4cac8bbf6d944984357073d47e4c45))
* replace withValidator with after() hook and deduplicate getMessages call ([a1ea8ac](https://github.com/fridzema/laravel-validation-plus/commit/a1ea8ac43539608d2a172f0ea3b379014cfdc751))


### Documentation

* add README with installation and usage guide ([a02dc2c](https://github.com/fridzema/laravel-validation-plus/commit/a02dc2c6fa2bfac749c8bd946bb6f504f48a87c9))
* add Precognition integration docs and screenshots ([8eb6184](https://github.com/fridzema/laravel-validation-plus/commit/8eb618498e7356021d215dc4d90fc9aae820f2c8))


### Tests

* add failing Precognition integration tests ([c1ae32d](https://github.com/fridzema/laravel-validation-plus/commit/c1ae32d3e429742586cd5fb7c716c215fe24b385))
* add integration tests for FormRequest and TestResponse macros ([fd88427](https://github.com/fridzema/laravel-validation-plus/commit/fd8842782e25266e443ad5bcc941418ab849a461))


