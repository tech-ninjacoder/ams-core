## Version 1.0.6

### `<card-view>`
1.  `showClearFilter` props added for clearing filter in `card-view`
   - send `true` for enabling this feature.

### `<app-table>`

1. `sortByFilter` added. if you want to use send props in table `options`
    ```
   sortByFilter: {
       isVisible: true,
       label: 'Sort By',
       key: 'sort_by',
       options: ['most_recent','most_viewed','top_rated'],
   }
   ```
2. `paginationBlockClass` default `mt-primary` if wrapper `false` then default `p-primary`

### `<app-input>`

1. `minimal` props added in `text-editor`
    - default `false`.
    - Send `true` if you want to see a minimal version of Text Editor

## Version 1.0.5 - `9 February, 2021`

### `<app-table>`

1. `enableRowSelect` & `bulkSelect` are combined. (`app-table`)
    - You have to send props only `enableRowSelect: true` for Those option
    - When you select a row it will trigger `event` named `getRows` with 2 parameter (selectedRows: `array`,
      bulkSelect: `Boolean`)
2. `showCount` props added in `app-table`
    - send `true` for showing counting of items
3. `showClearFilter` props added for clearing filter in `app-table`
    - send `true` for enabling this feature.
4. `multi-create` type added in `app-input`
5. `app-tag-manager` new component created.
6. `paginationOptions` type `array` default `[10,20,30]`
7. `managePagination` type `Boolean` default `true`
    - send `false` for turned off this feature
8. `enableCookie` type `Boolean` default `true`
    - send `false` for turned off saving or getting columns from cookies
9. `dataKey` type `String` default `data`
    - the `key` which is going to hold your `dataSet` for `table` in your paginate response

### `<app-table>` with `card-view`

- Only Applicable if you use card view

1. `cardViewEmptyBlock` type `Boolean` default `true`
    - send `false` for turned off empty block show
2. `cardViewPagination` type `Boolean` default `true`
    - send `false` for turned off showing pagination
3. `cardViewQueryParams` type `Object` default `{}`
    - send `{key: value}` if you want this in your backend query-string
