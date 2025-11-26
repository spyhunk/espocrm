# EspoCRM Customizations Summary

## Lead – Find Contacts Action
- Added dropdown item on Lead detail view to search contacts by matching `emailAddress`.
- UI wiring:
  - `application/Espo/Modules/Crm/Resources/metadata/clientDefs/Lead.json:9` adds `menu.detail.dropdown` with label `Find contacts` and handler `crm:handlers/lead/detail-actions`.
  - `client/modules/crm/src/handlers/lead/detail-actions.js:1` defines `findContacts` that:
    - Reads current lead email.
    - Calls `Lead/action/findContacts`.
    - Shows an in-app notification with found contact names.
- Backend endpoint:
  - `application/Espo/Modules/Crm/Controllers/Lead.php:106` adds `postActionFindContacts` that:
    - Loads Lead by `id`, retrieves `emailAddress`.
    - Finds `Contact` records with the same `emailAddress`.
    - Returns `{ names: string[] }` to the client.

## Contact – Boolean Filter: Has Phone Number
- New boolean filter to only show contacts that have a primary phone number.
- Filter implementation:
  - `application/Espo/Modules/Crm/Classes/Select/Contact/BoolFilters/HasPhoneNumber.php:10` joins `phoneNumbers` (`primary = true`) and applies `phoneNumbers.name != NULL`.
- Filter registration:
  - `application/Espo/Modules/Crm/Resources/metadata/selectDefs/Contact.json:1` maps `hasPhoneNumber` to the filter class.
- UI exposure:
  - `application/Espo/Modules/Crm/Resources/metadata/clientDefs/Contact.json:93` adds `hasPhoneNumber` to `boolFilterList`.

## Account – Description Auto-Populate Hook
- Populates Account `description` with related Contacts including their role.
- Hook class:
  - `application/Espo/Modules/Crm/Classes/RecordHooks/Account/BeforeUpdatePopulateDescription.php:10` fetches related `contacts`, builds lines `Name - Role`, and sets `description`.
- Hook wiring:
  - `application/Espo/Modules/Crm/Resources/metadata/recordDefs/Account.json:3` registers the hook for `beforeCreate` and `beforeUpdate`.

## Usage Notes
- Lead: open a Lead with an email and use `Find contacts` from the detail dropdown; results appear in notifications.
- Contact: toggle `hasPhoneNumber` boolean filter in list view to show only contacts with phones.
- Account: on create/update, `description` is filled with related Contacts and their roles.

## Validation
- PHP syntax validated for modified/new PHP files.