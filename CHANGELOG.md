# Change Log
All notable changes to this project will be documented in this file.

## [0.17.0] - 2022-09-15
- Add `scheme` and `port` keys to the options array property

## [0.16.2] - 2022-05-02
- Update payment initiation schema
- Remove headers from the `getPayment` and `getPaymentStatus` endpoint on v0.3 version

## [0.16.1] - 2022-04-19
- Remove language validation

## [0.16.0] - 2022-03-29
- Remove get bank by card number endpoint implementation
- Fix typo in api domain reference

## [0.15.3] - 2022-03-29
- Fix style using PHP CS Fixer
- Add PHP CS Fixer GitHub action to verify commits

## [0.15.2] - 2022-03-25
- Fix card linking

## [0.15.1] - 2021-11-29
- Support for `Webhook-URL` header on authentication
- New `domain` option to switch between environments easily.

## [0.15.0] - 2021-11-28
- Added support for Account Information Service (AIS)
- Fixes a bug when multiple authentication scopes could not be supplied

## [0.14.5] - 2021-11-16
- Change webhook header names to case insensitive

## [0.14.4] - 2021-10-14
- Get Project Settings endpoint implementation
- Implemented signature algorithm
- Support for UI settings

## [0.14.3] - 2021-09-20
- Fixes a bug when Client-Id/Client-Secret is not passed when using Bearer token
- Updated domain from getkevin.eu to kevin.eu

## [0.14.2] - 2021-06-21
- Added support for 500, 502, 503 and 504 error codes.

## [0.14.1] - 2021-04-28
- Added query parameter paymentMethodPreferred to init payment endpoint

## [0.14.0] - 2021-04-22
- Added support for hybrid payments

## [0.13.3] - 2021-04-15
- Support for data property in unsuccessful responses

## [0.13.2] - 2021-04-14
- Updated documentation

## [0.13.1] - 2021-04-07
- Fixes an undefined index error in getOption method

## [0.13.0] - 2021-04-06
- composer.json author change

## [0.12.0] - 2021-03-30
- Added support for getting payment methods
- Added support for getting bank by card number piece

## [0.11.0] - 2021-03-26
- Added support for initiating and getting payment refunds

## [0.10.0] - 2021-03-19
- Added API v0.3 support
- Added support for specifying plugin integration information

## [0.9.3] - 2021-01-20
- Added option to choose API version.

## [0.9.2] - 2021-01-20
- Added support for `informationStructured` parameter.

## [0.9.1] - 2020-10-27
- Decode url-encoded values.

## [0.9.0] - 2020-08-13
- Reduce PHP requirements to 5.6 version.
- Remove duplicate property definitions.

## [0.8.0] - 2020-07-31
- Use options attribute setting on error responses. Throw exception by default.

## [0.7.0] - 2020-07-29
- Removed cURL dependency.

## [0.6.0] - 2020-07-28
- Added base client class.

## [0.5.0] - 2020-07-24
- Initial release.
