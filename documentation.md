# How to use the API
All request with their details are listed in this document. Please keep this document up-to-date with any changes made to the API.

To test/debug the api use: [Postman](https://www.getpostman.com/)

## Table of contents:
[Example](#example)

## Example
### Get example
`/example/{ID}`
- ID: id of example **required**

Request type `Get`
### Create new example
`/example/`

Request type `Post`
#### Parameters:

Name | Description | Datatype | Required
---- | ----------- | -------- | --------
Firstname | Firstname of user | String | Yes
Lastname | Firstname of user | String | Yes

### Update example
`/example/{ID}`
- ID: id of example **required**

Request type `Put`
#### Parameters:

Name | Description | Datatype | Required
---- | ----------- | -------- | --------
Firstname | Firstname of user | String | Yes
Lastname | Firstname of user | String | Yes

### Delete example
`/example/{ID}`
- ID: id of example **required**

Request type `Delete`
