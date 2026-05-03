# [2.0.0](https://github.com/audunru/reporting-api/compare/v1.2.2...v2.0.0) (2026-05-03)


### Features

* exclude CSP violations from LogReport by default and log at warning level ([#12](https://github.com/audunru/reporting-api/issues/12)) ([680e885](https://github.com/audunru/reporting-api/commit/680e8858bd2889bcacdb0e2e34af74193642c0cb)), closes [#11](https://github.com/audunru/reporting-api/issues/11)


### BREAKING CHANGES

* LogReport no longer logs CSP violations by default, and log
level changed from info to warning. Override shouldExclude() returning false
to restore the previous CSP logging behavior.

## [1.2.2](https://github.com/audunru/reporting-api/compare/v1.2.1...v1.2.2) (2026-05-03)


### Bug Fixes

* use PHP string interpolation in log messages ([#8](https://github.com/audunru/reporting-api/issues/8)) ([3ba58d9](https://github.com/audunru/reporting-api/commit/3ba58d9874d4a49b47f91e3f62c5bd3dc8218d32)), closes [#2](https://github.com/audunru/reporting-api/issues/2) [#3](https://github.com/audunru/reporting-api/issues/3)

## [1.2.1](https://github.com/audunru/reporting-api/compare/v1.2.0...v1.2.1) (2026-05-03)


### Bug Fixes

* exclude all CSRF middleware variants from reports route ([#7](https://github.com/audunru/reporting-api/issues/7)) ([2d57754](https://github.com/audunru/reporting-api/commit/2d577542c2f4e59427bfb94d7f451e859dcc3dbf)), closes [#2](https://github.com/audunru/reporting-api/issues/2)

# [1.2.0](https://github.com/audunru/reporting-api/compare/v1.1.0...v1.2.0) (2026-05-03)


### Features

* add middleware to set Reporting-Endpoints response header ([#6](https://github.com/audunru/reporting-api/issues/6)) ([c6ebea9](https://github.com/audunru/reporting-api/commit/c6ebea962fabb9d3547f7b3ca7fedb9fcbcb4742))

# [1.1.0](https://github.com/audunru/reporting-api/compare/v1.0.0...v1.1.0) (2026-05-03)


### Features

* add report listeners and error handling ([22fe192](https://github.com/audunru/reporting-api/commit/22fe19229fdbc8717c6e7090b3f117952fda7a81))

# 1.0.0 (2026-05-01)


### Features

* initial package scaffold ([ff39237](https://github.com/audunru/reporting-api/commit/ff39237e2d5e89c55184c29e35d64110f15fed37))
