


 /*
`Created by nhatpd@luci.vn on 27/03/2020`
`Copyright (c) 2020 nhatpd@luci.vn`
 */

## Require:
- có kiến thức về redux, redux-saga 
- tìm hiểu thêm về kiến trúc của React boilerplate
- tìm hiểu về cách sử dụng Antdesign 

## Cài đặt:

- `npm install`
- run: `npm start`
- build release: 
    + staging : `npm run build:staging`
    + production : `npm run build`
- deploy: copy thư mục /build lên websever 
 
 
## Cấu trúc project:
- Tìm hiểu React boilerplate rồi tính tiếp.


## Command Util
- lessc --js my-theme.less result.css
..good luck...

## Muốn thay đổi màu giao diện bắt buộc phải thay đổi theo hướng dẫn sau:

1. Thay đổi màu trong file default.less:
# Ví dụ:
+ @color-active-tab-label: ;       // Thay đổi màu chữ khi 1 tab menu đang active       
+ @background-color-active-tab: ;  // Thay đổi màu nền khi 1 tab menu đang active  
+ @primary-color: ;                // Màu chung của hệ thống

2. Thay đổi màu trong file result.css:
- Thay đổi --color-global trong html mục đích thay đổi màu chung cả hệ thống, màu này bắt buộc trùng với @primary-color ở file default.less trên.
- Thay đổi --color-event trong html mục đích thay đổi màu của input, button, calendar khi focus hoặc hover.
- Thay đổi --color-event-active trong html mục đích thay đổi màu của button khi active.
- Thay đổi --color-boxshadow trong html mục đích thay đổi màu boxshadow của input, button, calendar khi active.
# Ví dụ:
html {
  --color-global: ;        // Màu chung của hệ thống
  --color-event: ;         // Màu chung của các input, button
  --color-event-active: ;  // Màu chung của các button khi active
  --color-boxshadow: ;     // Màu boxshadow của các input, button
} 

3. Thay đổi màu trong biến GLOBAL_COLOR file utils/constants.js màu này được dùng chung trong các component
- export const GLOBAL_COLOR = '#008789';  // Màu chung của hệ thống

## Màu global của các dự án đang triển khai

2. LUCI:
+ @primary-color, --color-global, --color-event, --color-boxshadow : #009B71;
+ @color-active-tab-label: #F8CD4A;
+ @background-color-active-tab: #018360;
+ --color-event-active: #00755A;

3. WATER-POINT:
+ @primary-color, --color-global, --color-event, --color-boxshadow : #008789;
+ @color-active-tab-label: #F8CD4A;
+ @background-color-active-tab: #006466;
+ --color-event-active: #006466;