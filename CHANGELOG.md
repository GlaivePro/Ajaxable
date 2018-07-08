# Changelog

## 0.5.0

### Removed
- Methods that are called when creating or deleting models. Native events are good enough.

### Changed
- Simplified putting and removing files.

### Updated
- Readme according to changes.
- Interface according to changes.


## 0.4.0

### Added
- Possibility to specify position of the newly created row by `data-ajaxable-list-position`.
- Possibility to specify name of the view on the model.

### Removed
- Need to create a list view.

### Changed
- Moved some functionality from Controller to trait.
- The responses are now created with model methods (implemented by the trait). This allows new points to inject changegs and removes the need of explicit list view. Just override the `drawList()` method if you need to.
- The creator now clears inputs on succesful creation.

### Updated
- Readme according to changes.
- Interface according to changes.

## 0.3.0

### Changed
- Highlighting class to prevent conflicts.
- Specifying list in creating and reordering tools - you should now use selector instead of id.

### Updated
- Readme according to changes.

### Fixed
- Errors in Controller with regards to creating and reordering.

## 0.2.3

### Added
- Toggling `d-none` class along with `hidden`.

### Updated
- Rewrote readme focusing on the handier parts first.

## 0.2.1

### Fixed
- Some syntax fixes.

## 0.2.0

### Added
- Added functionality to update or create.

## Initial commit

Experimental version, first time these functions are extracted as a package for easier re-use.
